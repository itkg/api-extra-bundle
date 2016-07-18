<?php

namespace Itkg\ApiExtraBundle\Tests\Cache\Tag\Handler;

use Itkg\ApiExtraBundle\Cache\Tag\Handler\TagHandler;
use Itkg\ApiExtraBundle\Cache\Tag\TagManagerInterface;

/**
 * class TagHandlerTest 
 */
class TagHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TagManagerInterface
     */
    private $tagManager;

    /**
     * @var TagHandler
     */
    private $tagHandler;

    public function setUp()
    {
        $this->tagManager = \Phake::mock('Itkg\ApiExtraBundle\Cache\Tag\TagManagerInterface');
        $this->tagHandler = new TagHandler($this->tagManager);
    }

    public function testCreateTag()
    {
        $tag = 'test';
        $key = 'key';
        $value = 123;

        $this->tagHandler->createTag($tag, $value, $key);
        \Phake::verify($this->tagManager)->addKeyToTag($tag, $key, $value);
    }

    public function testCreateTags()
    {
        $tags = array('test' => 123, 'version' => 456);
        $key  = 'key';

        $this->tagHandler->createTags($tags, $key);
        \Phake::verify($this->tagManager)->addKeyToTags($tags, $key);
    }

    public function testCleanTag()
    {
        $tag = 'test';
        $value  = 'value';

        $this->tagHandler->cleanTag($tag, $value);
        \Phake::verify($this->tagManager)->cleanByTag($tag, $value);
    }

    public function testCleanTags()
    {
        $tags = array('test' => 123, 'version' => 456);

        $this->tagHandler->cleanTags($tags, true);
        \Phake::verify($this->tagManager)->cleanByTags($tags, true);
    }
}
