<?php
/*
 * This file is part of the feed-io package.
 *
 * (c) Alexandre Debril <alex.debril@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FeedIo;

use \PHPUnit\Framework\TestCase;

class FeedTest extends TestCase
{

    /**
     * @var \FeedIo\Feed
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new Feed();
    }

    /**
     * @covers FeedIo\Feed::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals(new \ArrayIterator(), 'items', $this->object);
    }

    public function testNext()
    {
        $item1 = new Feed\Item();
        $item2 = clone $item1;
        $item2->setTitle('item2');
        $this->object->add($item1);
        $this->object->add($item2);
        $this->object->rewind();
        $this->assertEquals($item1, $this->object->current());
        $this->object->next();
        $this->assertEquals($item2, $this->object->current());
    }

    public function testIsValid()
    {
        $item = new Feed\Item();
        $this->object->add($item);
        $this->object->rewind();

        $this->assertTrue($this->object->valid());
        $this->object->next();
        $this->assertFalse($this->object->valid());
    }

    public function testRewind()
    {
        $item = new Feed\Item();
        $this->object->add($item);

        $this->object->next();
        $this->assertFalse($this->object->valid());
        $this->object->rewind();
        $this->assertEquals($item, $this->object->current());
    }

    public function testKey()
    {
        $this->assertNull($this->object->key());
        $this->object->add(new Feed\Item());
        $this->object->add(new Feed\Item());
        $this->assertEquals(0, $this->object->key());
        $this->object->next();
        $this->assertEquals(1, $this->object->key());
    }

    public function testAdd()
    {
        $item = new Feed\Item();
        $this->object->add($item);

        $this->assertAttributeEquals(new \ArrayIterator(array($item)), 'items', $this->object);
        $this->assertEquals($this->object->current(), $item);

        $feedDate = new \DateTime('-20 days');
        $oldItemDate = new \DateTime('-30 days');
        $newItemDate = new \DateTime('-10 days');

        $this->object->setLastModified($feedDate);
        $item = new Feed\Item();
        $item->setLastModified($oldItemDate);

        $this->object->add($item);
        $this->assertEquals($feedDate, $this->object->getLastModified());

        $item = new Feed\Item();
        $item->setLastModified($newItemDate);

        $this->object->add($item);
        $this->assertEquals($newItemDate, $this->object->getLastModified());
    }
    
    public function testUrl()
    {
        $url = 'http://localhost';
        
        $feed = new Feed;
        $feed->setUrl($url);
        
        $this->assertEquals($url, $feed->getUrl());
    }

    public function testToArray()
    {
        $item = new Feed\Item();
        $item->setTitle('foo-bar');
        $this->object->add($item);

        $out = $this->object->toArray();

        $this->assertEquals('foo-bar', $out['items'][0]['title']);
    }

    public function testJsonSerialize()
    {
        $item = new Feed\Item();
        $item->setTitle('foo-bar');
        $this->object->add($item);
        $this->object->setTitle('hello');
        $this->object->setLastModified(new \DateTime());

        $json = json_encode($this->object);

        $this->assertInternalType('string', $json);
        $this->assertInstanceOf('stdClass', json_decode($json));
    }

    public function testCount()
    {
        $this->assertCount(0, $this->object);

        $this->object->add(new Feed\Item());
        $this->object->add(new Feed\Item());

        $this->assertCount(2, $this->object);
    }
}
