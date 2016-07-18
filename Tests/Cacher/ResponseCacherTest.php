<?php

namespace Itkg\ApiExtraBundle\Tests\Cache;

use Itkg\ApiExtraBundle\Cache\Tag\Handler\TagHandlerInterface;
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

    /**
     * @var TagHandlerInterface
     */
    private $tagHandler;

    public function setUp()
    {
        $this->cacheAdapter = new Registry();
        $this->tagHandler = Phake::mock('Itkg\ApiExtraBundle\Cache\Tag\Handler\TagHandlerInterface');
        $this->responseCacher = new ResponseCacher($this->cacheAdapter, $this->tagHandler);
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

        $params = array('tags' => array('version'));
        $this->responseCacher->writeCache($request, new Response($content), $params);
        Phake::verify($this->tagHandler)->createTags(array('version' => 2), '/slide-decks/1_2_3');
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
