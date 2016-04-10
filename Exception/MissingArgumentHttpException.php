<?php

namespace Itkg\ApiExtraBundle\Exception;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;

/**
 * Class MissingArgumentHttpException
 */
class MissingArgumentHttpException extends ApiException
{
    /**
     * MissingArgumentHttpException constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct('400', 0, $message, $message);
    }
}
