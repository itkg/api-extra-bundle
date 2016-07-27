<?php

namespace Itkg\ApiExtraBundle\Tests\Cache\Tag;

use Itkg\ApiExtraBundle\Cache\Tag\TagManager;
use Itkg\ApiExtraBundle\Cache\Tag\TagManagerInterface;
use Itkg\Core\Cache\Adapter\Registry;
use Itkg\Core\Cache\AdapterInterface;
use Itkg\Core\Cache\CacheableData;

/**
 * class TagManagerTest 
 */
class TagManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var TagManagerInterface
     */
    private $manager;

    public function setUp()
    {
        $this->adapter = new Registry();
        $this->manager = new TagManager($this->adapter);
    }

    public function testAddKeyToTag()
    {
        $this->manager->addKeyToTag('version', 123, 'key');

        $item = new CacheableData('TAG__VERSION_', null, null);
        $content = unserialize($this->adapter->get($item));
        $this->assertEquals(array('TAG__VERSION_123' => 'TAG__VERSION_123'), $content);

        $item = new CacheableData('TAG__VERSION_123', null, null);
        $content = unserialize($this->adapter->get($item));
        $this->assertEquals(array('key' => 'key'), $content);
    }

    public function testAddKeyToTags()
    {
        $tags = array('version' => 123, 'language' => 'fr');
        $this->manager->addKeyToTags($tags, 'key');

        $item = new CacheableData('TAG__VERSION_', null, null);
        $content = unserialize($this->adapter->get($item));
        $this->assertEquals(array('TAG__VERSION_123' => 'TAG__VERSION_123'), $content);

        $item = new CacheableData('TAG__VERSION_123', null, null);
        $content = unserialize($this->adapter->get($item));
        $this->assertEquals(array('key' => 'key'), $content);

        $item = new CacheableData('TAG__LANGUAGE_', null, null);
        $content = unserialize($this->adapter->get($item));
        $this->assertEquals(array('TAG__LANGUAGE_FR' => 'TAG__LANGUAGE_FR'), $content);

        $item = new CacheableData('TAG__LANGUAGE_FR', null, null);
        $content = unserialize($this->adapter->get($item));
        $this->assertEquals(array('key' => 'key'), $content);
    }

    public function testCleanByTag()
    {
        $item = new CacheableData('key', null, 213);
        $this->adapter->set($item);
        $this->assertEquals(213, $this->adapter->get($item));
        $this->manager->addKeyToTag('version', 213, 'key');

        $this->manager->cleanByTag('version', 123);
        $this->assertEquals(213, $this->adapter->get($item));

        $this->manager->cleanByTag('version', 213);
        $this->assertFalse($this->adapter->get($item));
    }

    public function testCleanByTagsWithIntersectOnly()
    {
        $item = new CacheableData('key', null, 213);
        $this->adapter->set($item);
        $this->assertEquals(213, $this->adapter->get($item));
        $this->manager->addKeyToTag('version', 213, 'key');
        $this->manager->addKeyToTag('country', 'FR', 'key');

        $this->manager->cleanByTags(array('version' => array('value' => 213, 'type' => 'strict'), 'country' => array('value' => 'EN', 'type' => 'strict')), true);
        $this->assertEquals(213, $this->adapter->get($item));

        $this->manager->cleanByTags(array('version' => array('value' => 213, 'type' => 'strict'), 'country' => array('value' => 'FR', 'type' => 'strict')), true);
        $this->assertFalse($this->adapter->get($item));
    }

    public function testCleanByTagsWithIntersectOnlyAndWildcardComparison()
    {
        $item = new CacheableData('key2', null, 'toto');
        $this->adapter->set($item);
        $item2 = new CacheableData('key3', null, 'toto2');
        $this->adapter->set($item2);
        $this->assertEquals('toto', $this->adapter->get($item));
        $this->manager->addKeyToTag('version', 150, 'key2');
        $this->manager->addKeyToTag('country', 'FR', 'key2');
        $this->manager->addKeyToTag('version', 1500, 'key3');
        $this->manager->addKeyToTag('country', 'FR', 'key3');

        $this->manager->cleanByTags(array('version' => array('value' => 10, 'type' => 'wildcard'), 'country' => array('value' => 'FR', 'type' => 'strict')), true);
        $this->assertEquals('toto', $this->adapter->get($item));
        $this->assertEquals('toto2', $this->adapter->get($item2));
        $this->manager->cleanByTags(array('version' => array('value' => 150, 'type' => 'wildcard'), 'country' => array('value' => 'FR', 'type' => 'strict')), true);
        $this->assertFalse($this->adapter->get($item));
        $this->assertFalse($this->adapter->get($item2));
    }
}
