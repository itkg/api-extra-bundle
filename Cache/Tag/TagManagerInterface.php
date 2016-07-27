<?php

namespace Itkg\ApiExtraBundle\Cache\Tag;

/**
 * interface TagManagerInterface
 */
interface TagManagerInterface
{
    /**
     * @param string $tag
     * @param string $value
     * @param string $key
     */
    public function addKeyToTag($tag, $value, $key);

    /**
     * @param array  $tags
     * @param string $key
     */
    public function addKeyToTags(array $tags, $key);

    /**
     * @param string $tag
     * @param string $value
     */
    public function cleanByTag($tag, $value);

    /**
     * @param array $tags
     * @param bool  $intersectionOnly
     */
    public function cleanByTags(array $tags, $intersectionOnly = false);

    /**
     * @param string $tag
     * @param string $value
     *
     * @return array
     */
    public function getKeysByTag($tag, $value = '');
}
