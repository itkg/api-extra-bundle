<?php

namespace Itkg\ApiExtraBundle\Cacher;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * interface ResponseCacherInterface
 */
interface ResponseCacherInterface
{
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $params
     *
     * @çeturn string
     */
    public function writeCache(Request $request, Response $response, array $params = array());

    /**
     * @param Request  $request
     * @param array    $params
     */
    public function readCache(Request $request, array $params = array());
}
