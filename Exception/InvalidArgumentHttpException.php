<?php

namespace Itkg\ApiExtraBundle\Exception;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;

/**
 * Class InvalidArgumentHttpException
 */
class InvalidArgumentHttpException extends ApiException
{
    /**
     * InvalidArgumentHttpException constructor.
     */
    public function __construct($message)
    {
        parent::__construct('400', 0, $message, $message);
    }
}
