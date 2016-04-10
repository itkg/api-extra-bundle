<?php

namespace Itkg\ApiExtraBundle\Exception;

/**
 * Class InvalidFacadeException
 */
class InvalidFacadeException extends \InvalidArgumentException
{
    /**
     * InvalidFacadeException constructor.
     *
     * @param string $expectedClass
     */
    public function __construct($expectedClass)
    {
        parent::__construct(sprintf('Facade must be an instance of "%".', $expectedClass));
    }
}
