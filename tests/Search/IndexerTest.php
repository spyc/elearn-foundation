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


use Elearn\Foundation\Search\Indexer;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testPing()
    {
        $client = new Indexer();
        $this->assertInstanceOf(Indexer::class, $client);
        $this->assertTrue($client->ping());
    }
}
