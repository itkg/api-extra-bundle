<?php

namespace Itkg\ApiExtraBundle\Cache\Tag\Handler;

/**
 * interface TagHandlerInterface
 */
interface TagHandlerInterface
{
    /**
     * @param string $tag
     * @param string $value
     * @param string $key
     */
    public function createTag($tag, $value, $key);

    /**
     * @param array  $tags
     * @param string $key
     */
    public function createTags(array $tags, $key);

    /**
     * @param string $tag
     * @param string $value
     */
    public function cleanTag($tag, $value);

    /**
     * @param array $tags
     */
    public function cleanTags(array $tags);
}
