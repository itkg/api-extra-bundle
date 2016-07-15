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
        $keysToRemove = array();
        $tagsToCheck = array();
        foreach ($tags as $tag => $conf) {
            $tagContainerKey = $this->formatTagKey($tag, '');
            $item = $this->createItem($tagContainerKey);
            $keys = unserialize($this->cacheAdapter->get($item));
            if (!is_array($keys)) $keys = array();
            foreach ($keys as $key) {
               if (($conf['type'] === self::STRICT_COMPARISON && $key === $this->formatTagKey($tag, $conf['value']))
                  || $conf['type'] === self::WILDCARD_COMPARISON && preg_match('#.*'.$this->formatTagKey($tag, $conf['value']).'.*#', $key)) {
                   $tagsToCheck[$key] = $key;
               }
            }
        }

        foreach ($tagsToCheck as $tag) {
            $item = $this->createItem($tag);
            $keys = unserialize($this->cacheAdapter->get($item));
            if (!is_array($keys)) $keys = array();
            if (empty($keysToRemove)) {
                $keysToRemove = $keys;
            } else if($intersectionOnly) {
                $keysToRemove = array_intersect($keysToRemove, $keys);
            } else {
                $keysToRemove = array_merge($keysToRemove, $keys);
            }
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
