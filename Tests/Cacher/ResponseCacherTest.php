<?php

namespace Itkg\ApiExtraBundle\Tests\Cache;

use Itkg\Core\Cache\Adapter\Registry;
use Itkg\Core\Cache\AdapterInterface;
use Itkg\ApiExtraBundle\Cacher\ResponseCacher;
use Phake;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * class ResponseCacherTest
 */
class ResponseCacherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResponseCacher
     */
    private $responseCacher;

    /**
     * @var AdapterInterface
     */
    private $cacheAdapter;

    public function setUp()
    {
        $this->cacheAdapter = new Registry();
        $this->responseCacher = new ResponseCacher($this->cacheAdapter);

    }

    public function testReadWriteData()
    {
        $content = 'my cached data';
        $request = Request::create('/slide-decks/1');
        $request->attributes->add(array(
            '_route' => '/slide-decks/1',
            '_route_params' => array(
                'version' => 2,
                'language' => 3
            )
        ));

        $this->responseCacher->writeCache($request, new Response($content));
        $this->assertEquals('my cached data', $this->responseCacher->readCache($request));
        $request = Request::create('/slide-decks/2');
        $request->attributes->add(array(
            '_route' => '/slide-decks/2',
            '_route_params' => array(
                'version' => 2,
                'language' => 3
            )
        ));

        $this->assertFalse($this->responseCacher->readCache($request));

        $request = Request::create('/slide-decks/2');
        $request->attributes->add(array(
            '_route' => '/slide-decks/1',
            '_route_params' => array(
                'version' => 3,
                'language' => 3
            )
        ));

        $this->assertFalse($this->responseCacher->readCache($request));
    }
}
