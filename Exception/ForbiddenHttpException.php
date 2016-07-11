<?php

namespace Itkg\ApiExtraBundle\Exception;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;

/**
 * Class ForbiddenHttpException
 */
class ForbiddenHttpException extends ApiException
{
    /**
     * @param string $developerMessage
     * @param string $humanMessage
     */
    public function __construct($developerMessage, $humanMessage)
    {
        parent::__construct(403, null, $developerMessage, $humanMessage);
    }
}
