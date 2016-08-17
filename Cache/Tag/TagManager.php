<?php

namespace Itkg\ApiExtraBundle\Cache\Tag;

use Itkg\Core\Cache\AdapterInterface;
use Itkg\Core\Cache\CacheableData;

/**
 * class TagManager 
 */
class TagManager implements TagManagerInterface
{
    const TAG_PREFIX = 'TAG_';

    const STRICT_COMPARISON = 'strict';

    const WILDCARD_COMPARISON = 'wildcard';

    /**
     * @var AdapterInterface
     */
    private $cacheAdapter;

    /**
     * @param AdapterInterface $cacheAdapter
     */
    public function __construct(AdapterInterface $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * @param string $tag
     * @param string $value
     * @param string $key
     */
    public function addKeyToTag($tag, $value, $key)
    {
        $this->registerTag($tag, $value);
        $tagKey = $this->formatTagKey($tag, $value);
        $item = $this->createItem($tagKey);
        $keys = unserialize($this->cacheAdapter->get($item));
        if (!is_array($keys)) $keys = array();

        $keys[$key] = $key;
        $item->setDataFromCache(serialize($keys));
        $this->cacheAdapter->set($item);
    }

    /**
     * @param array  $tags
     * @param string $key
     */
    public function addKeyToTags(array $tags, $key)
    {
        foreach ($tags as $tag => $value) {
            $this->addKeyToTag($tag, $value, $key);
        }
    }

    /**
     * @param string $tag
     * @param string $value
     */
    public function cleanByTag($tag, $value)
    {
        $tagKey = $this->formatTagKey($tag, $value);
        $item = $this->createItem($tagKey);
        $keys = unserialize($this->cacheAdapter->get($item));
        if (is_array($keys)) {
            foreach ($keys as $key) {
                $this->cacheAdapter->remove($this->createItem($key));
            }
        }
        $this->cacheAdapter->remove($item);
    }

    /**
     * @param array $tags
     * @param bool  $intersectionOnly
     */
    public function cleanByTags(array $tags, $intersectionOnly = false)
    {
	if (count($tags) === 1) {
            $intersectionOnly = false;
        }
        $tagsToCheck = $tagsWildcardToCheck = array();
        foreach ($tags as $tag => $conf) {
            $tagMatched = false;
            if ($conf['type'] === self::WILDCARD_COMPARISON) {
                $tagContainerKey = $this->formatTagKey($tag, '');
                $item            = $this->createItem($tagContainerKey);
                $keys            = unserialize($this->cacheAdapter->get($item));
                if (empty($keys)) {
                    $keys = array();
                }

                foreach ($keys as $key) {
                    if (preg_match('#.*' . $this->formatTagKey($tag, $conf['value']) . '.*#', $key)) {
                        $tagsWildcardToCheck[$key] = $key;
                        $tagMatched = true;
                    }
                }
            }
            if (!$tagMatched) {
                $key               = $this->formatTagKey($tag, $conf['value']);
                $tagsToCheck[$key] = $key;
            }
        }

        $wildcardKeys = $this->getKeysByTags($tagsWildcardToCheck, false);
        $strictKeys = $this->getKeysByTags($tagsToCheck, $intersectionOnly);
        if ($intersectionOnly && !empty($wildcardKeys)) {
            $keysToRemove = array_intersect($strictKeys, $wildcardKeys);
        } else {
            $keysToRemove = array_merge($strictKeys, $wildcardKeys);
        }

        foreach ($keysToRemove as $key) {
            $this->cacheAdapter->remove($this->createItem($key));
        }
    }

    /**
     * @param string $tag
     * @param string $value
     *
     * @return array
     */
    public function getKeysByTag($tag, $value = '')
    {
        $tagKey = $this->formatTagKey($tag, $value);
        $item = $this->createItem($tagKey);

        $keys = unserialize($this->cacheAdapter->get($item));
        if (!is_array($keys)) $keys = array();

        return $keys;
    }

    /**
     * @param string $tag
     * @param string $value
     */
    private function registerTag($tag, $value)
    {
        $tagContainerKey = $this->formatTagKey($tag, '');
        $tagKey = $this->formatTagKey($tag, $value);
        $item = $this->createItem($tagContainerKey);
        $keys = unserialize($this->cacheAdapter->get($item));
        if (!is_array($keys)) $keys = array();

        $keys[$tagKey] = $tagKey;
        $item->setDataFromCache(serialize($keys));
        $this->cacheAdapter->set($item);
    }

    /**
     * @param array $tags
     * @param bool  $withIntersection
     *
     * @return array
     */
    private function getKeysByTags(array $tags, $withIntersection = false)
    {
        $index = 0;
        $keys = array();
        foreach ($tags as $tag) {
            $item = $this->createItem($tag);
            $tagKeys = unserialize($this->cacheAdapter->get($item));
            if (!is_array($tagKeys)) $tagKeys = array();
            if($withIntersection) {
                if ($index === 0) {
                    $keys = $tagKeys;
                } else {
                    $keys = array_intersect($keys, $tagKeys);
                }
            } else {
                $keys = array_merge($keys, $tagKeys);
            }
            $index++;
        }

        return $keys;
    }

    /**
     * @param string $tag
     * @param string $value
     *
     * @return string
     */
    private function formatTagKey($tag, $value)
    {
        return sprintf('%s_%s_%s', self::TAG_PREFIX, strtoupper($tag), strtoupper($value));
    }

    /**
     * @param string $key
     * @param array $content
     *
     * @return CacheableData
     */
    private function createItem($key, array $content = array())
    {
        return new CacheableData($key, null, $content);
    }
}
