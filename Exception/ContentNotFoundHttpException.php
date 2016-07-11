<?php

namespace Itkg\ApiExtraBundle\Exception;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;

/**
 * Class ContentNotFoundHttpException
 */
class ContentNotFoundHttpException extends ApiException
{
    /**
     * @param string $developerMessage
     * @param string $humanMessage
     */
    public function __construct($developerMessage, $humanMessage)
    {
        parent::__construct(404, null, $developerMessage, $humanMessage);
    }
}
