<?php
/**
 * HHVM
 *
 * Copyright (C) Tony Yip 2015.
 *
 * @author   Tony Yip <tony@opensource.hk>
 * @license  http://opensource.org/licenses/GPL-3.0 GNU General Public License
 */

namespace Elearn\Foundation\Test\Search;

use ReflectionMethod;
use Elearn\Foundation\Search\Indexer;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    private $client;

    public function setUp()
    {
        $this->client = new Indexer();
    }

    public function testClass()
    {
        $this->assertInstanceOf(Indexer::class, $this->client);
    }

    public function testPing()
    {
        $method = new ReflectionMethod($this->client, 'ping');
        $this->assertTrue($method->isPublic());
    }
}