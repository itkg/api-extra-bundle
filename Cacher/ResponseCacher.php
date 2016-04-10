<?php

namespace Itkg\ApiExtraBundle\Cacher;

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

    const SCOPE_USER = 'user';

    const SCOPE_ALL = 'all';

    /**
     * @param AdapterInterface $cacheAdapter
     */
    public function __construct(AdapterInterface $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
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
        $variantQueryParams = $request->query->all();
        if ((!isset($params['scope']) || $params['scope'] !== self::SCOPE_USER) && isset($variantQueryParams['access_token'])) {
            unset($variantQueryParams['access_token']);
        }

        return sprintf('%s_%s', $request->attributes->get('_route'), implode('_', array_merge($variantQueryParams, $request->attributes->get('_route_params'))));
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
        $cacheableData = new CacheableData($this->createKeyFromRequest($request, $params), isset($params['duration']) ?: null, $content);
        $request->attributes->set('cache_key', $cacheableData->getHashKey());

        return $cacheableData;
    }
}
