<?php

namespace Itkg\ApiExtraBundle\Cache\Tag\Handler;

use Itkg\ApiExtraBundle\Cache\Tag\TagManagerInterface;

/**
 * class TagHandler 
 */
class TagHandler implements TagHandlerInterface
{
    /**
     * @var TagManagerInterface
     */
    private $tagManager;

    /**
     * @param TagManagerInterface $tagManager
     */
    public function __construct(TagManagerInterface $tagManager)
    {
        $this->tagManager = $tagManager;
    }

    /**
     * @param string $tag
     * @param string $value
     * @param string $key
     */
    public function createTag($tag, $value, $key)
    {
        $this->tagManager->addKeyToTag($tag, $key, $value);
    }

    /**
     * @param array  $tags
     * @param string $key
     */
    public function createTags(array $tags, $key)
    {
        $this->tagManager->addKeyToTags($tags, $key);
    }

    /**
     * @param string $tag
     * @param string $value
     */
    public function cleanTag($tag, $value)
    {
        $this->tagManager->cleanByTag($tag, $value);
    }

    /**
     * @param array $tags
     * @param bool  $intersectOnly
     */
    public function cleanTags(array $tags, $intersectOnly = false)
    {
        $this->tagManager->cleanByTags($tags, $intersectOnly);
    }
}
