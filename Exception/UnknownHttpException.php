<?php

namespace Itkg\ApiExtraBundle\Exception;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;

/**
 * Class UnknownHttpException
 *
 * This exception should be used when a standard exception bubbles up to the kernel
 * while the application is serving the client through the API
 * It can then be rendered properly, following the API excepted format
 */
class UnknownHttpException extends ApiException
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
