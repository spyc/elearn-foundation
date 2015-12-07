<?php

namespace Elearn\Foundation\Middleware;

use Closure;
use Elearn\Foundation\Helper\Json;
use Illuminate\Http\Request;
use Illuminate\Redis\Database;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CommonSession extends StartSession
{
    /**
     * @var \Predis\ClientInterface
     */
    private $redis;

    /**
     * @var string
     */
    protected $key;

    public function __construct(SessionManager $manager, Database $db)
    {
        parent::__construct($manager);
        $this->key = env('COMMON_SESSION');
        $this->redis = $db->connection('common');
        if ($this->redis === null) {
            throw new ResourceNotFoundException('Redis Common Connection is not exists.');
        }
    }

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $session = $request->getSession();
        $this->preRequestHandle($session, $request);

        $response = $next($request);

        $session = $request->getSession();
        $this->postRequestHandle($session, $response);

        return $response;
    }

    /**
     * @param SessionInterface $session
     * @param Request $request
     */
    private function preRequestHandle(SessionInterface $session, Request $request)
    {
        $id = $request->cookie($this->key);
        $key = 'session:' .$id;
        if (!Str::equals($key, $session->getId())) {
            $this->redis->del($key);
            return;
        }
        $value = $this->redis->get('session:' . $key);
        $content = Json::parse($value);
        if ($content['last_seen'] > $session->get('last_seen')) {
            foreach ($content as $key => $value) {
                if (!Str::startsWith($key, ['_', 'login_'])) {
                    $session->set($key, $value);
                }
            }
        }
    }

    /**
     * @param SessionInterface $session
     * @param Response $response
     */
    private function postRequestHandle(SessionInterface $session, Response $response)
    {
        if ($this->sessionIsPersistent($config = $this->manager->getSessionConfig())) {
            $id = $session->getId();
            $key = 'session:' . $id;

            $content = $session->all();
            unset($content['_token'], $content['flash']);
            $lastSeen = time();
            $content['last_seen'] = $lastSeen;
            $session->set('last_seen', $lastSeen);

            $value = Json::dump($content);

            $this->redis->watch($key);
            $this->redis->multi();
            $this->redis->set($key, $value);
            $this->redis->expire($key, $this->getSessionLifetimeInSeconds());
            $this->redis->exec();

            $cookie = new Cookie($this->key, $id, $this->getCookieExpirationDate(),
                $config['path'], $config['domain'], Arr::get($config, 'secure', false));
            $response->headers->setCookie($cookie);
        }
    }
}