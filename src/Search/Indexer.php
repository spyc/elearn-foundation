<?php
/**
 * HHVM
 *
 * Copyright (C) Tony Yip 2015.
 *
 * @author   Tony Yip <tony@opensource.hk>
 * @license  http://opensource.org/licenses/GPL-3.0 GNU General Public License
 */

namespace Elearn\Foundation\Search;

use GuzzleHttp\Client;
use Elearn\Foundation\Helper\Json;

class Indexer
{

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @param string $host
     * @param int $port
     * @param bool|false $https
     */
    public function __construct($host = '127.0.0.1', $port = 9200, $https = false)
    {
        $url = 'http';

        if ($https) {
            $url .= 's';
        }

        $url .= sprintf('://%s:%d/', $host, $port);

        $this->client = new Client([
            'base_uri' => $url,
            'timeout' => 5
        ]);
    }

    /**
     * Ping Test.
     *
     * @return bool
     */
    public function ping()
    {
        $url = '/?hello=elasticsearch';

        $response = $this->client->request('GET', $url);

        return ($response->getStatusCode() === 200
            && Json::parse($response->getBody())['tagline'] === 'You Know, for Search');
    }

    /**
     * Index data.
     *
     * @param string $index
     * @param string $type
     * @param int $id
     * @param array $data
     *
     * @return bool
     */
    public function index($index, $type, $id, array $data)
    {
        $url = static::formUrl($index, $type) . $id;

        $response = $this->client->request('PUT', $url, [
            'json' => $data
        ]);

        $content = Json::parse($response->getBody());

        return $content['created'];
    }

    /**
     * @param string $index
     * @param string $type
     * @param int $id
     * @param array $data
     *
     * @return bool
     */
    public function update($index, $type, $id, array $data)
    {
        $url = static::formUrl($index, $type) . $id . '/_update';

        $response = $this->client->request('POST', $url, [
            'json' => ['doc' => $data]
        ]);

        return 200 === $response->getStatusCode();
    }

    /**
     * Delete indexed data.
     *
     * @param string $index
     * @param string $type
     * @param int $id
     *
     * @return bool
     */
    public function delete($index, $type, $id)
    {
        $url = static::formUrl($index, $type) . $id;

        $response = $this->client->request('DELETE', $url);

        $content = Json::parse($response->getBody());

        return $content['found'];
    }

    public function search($index, $type, array $query)
    {
        $url = static::formUrl($index, $type) . '_search';

        $response = $this->client->request('POST', $url, [
            'json' => $query
        ]);

        $hits = Json::parse($response->getBody())['hits'];

        return $hits['hits'];
    }

    /**
     * Forming request url.
     *
     * @param string $index
     * @param string $type
     *
     * @return string
     */
    private static function formUrl($index, $type)
    {
        return sprintf('%s/%s/', $index, $type);
    }
}