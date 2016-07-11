<?php

namespace Itkg\ApiExtraBundle\Exception;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;

/**
 * Class RuntimeHttpException
 */
class RuntimeHttpException extends ApiException
{
    /**
     * @param string $developerMessage
     * @param string $humanMessage
     */
    public function __construct($developerMessage, $humanMessage)
    {
        parent::__construct(500, null, $developerMessage, $humanMessage);
    }
}
