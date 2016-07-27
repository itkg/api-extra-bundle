<?php

namespace Itkg\ApiExtraBundle\Cacher;

use Itkg\ApiExtraBundle\Cache\Tag\Handler\TagHandlerInterface;
use Itkg\Core\Cache\AdapterInterface;
use Itkg\Core\Cache\CacheableData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ResponseCacher
 */
class ResponseCacher implements ResponseCacherInterface
{
    /**
     * @var AdapterInterface
     */
    private $cacheAdapter;

    /**
     * @var TagHandlerInterface
     */
    private $tagHandler;

    const SCOPE_USER = 'user';

    const SCOPE_ALL = 'all';


    /**
     * @param AdapterInterface $cacheAdapter
     */
    public function __construct(AdapterInterface $cacheAdapter, TagHandlerInterface $tagHandler)
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->tagHandler = $tagHandler;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $params
     *
     * @return string
     */
    public function writeCache(Request $request, Response $response, array $params = array())
    {
        $cacheableData = $this->createCacheableData($request, $params, $response->getContent());
        $this->cacheAdapter->set($cacheableData);

        $variantParams = $this->getVariantParams($request);
        $insersectParams = array_intersect_key($params['tags'], array_keys($variantParams));
        $tags = array();
        foreach ($insersectParams as $key => $tag) {
            $tags[$tag] = $variantParams[$tag];
        }
        $this->tagHandler->createTags($tags, $cacheableData->getHashKey());

        return $cacheableData->getHashKey();
    }

    /**
     * @param Request  $request
     * @param array    $params
     *
     * @return mixed
     */
    public function readCache(Request $request, array $params = array())
    {
        return $this->cacheAdapter->get($this->createCacheableData($request));
    }

    /**
     * @param Request $request
     * @param array   $params
     * @return string
     */
    private function createKeyFromRequest(Request $request, array $params = array())
    {
        return sprintf('%s_%s', $request->attributes->get('_route'), implode('_', $this->getVariantParams($request)));
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getVariantParams(Request $request)
    {
        if ($request->attributes->get('variant_params')) {
            return $request->attributes->get('variant_params');
        }
        $variantQueryParams = $request->query->all();
        if ((!isset($params['scope']) || $params['scope'] !== self::SCOPE_USER) && isset($variantQueryParams['access_token'])) {
            unset($variantQueryParams['access_token']);
        }
        $request->attributes->set('variant_params', array_merge($variantQueryParams, $request->attributes->get('_route_params')));

        return $request->attributes->get('variant_params');
    }
    /**
     * @param Request        $request
     * @param array          $params
     * @param null|string    $content
     *
     * @return CacheableData
     */
    private function createCacheableData(Request $request, array $params = array(), $content = null)
    {
        $cacheableData = new CacheableData($this->createKeyFromRequest($request, $params), isset($params['duration']) ? $params['duration'] : null, $content);
        $request->attributes->set('cache_key', $cacheableData->getHashKey());

        return $cacheableData;
    }
}
