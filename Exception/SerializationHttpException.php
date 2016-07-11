<?php

namespace Itkg\ApiExtraBundle\Exception;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;

/**
 * Class SerializationHttpException
 */
class SerializationHttpException extends ApiException
{
    /**
     * @param string $httpCode
     * @param string $developerMessage
     * @param string $humanMessage
     */
    public function __construct($httpCode, $developerMessage, $humanMessage)
    {
        parent::__construct($httpCode, null, $developerMessage, $humanMessage);
    }
}
