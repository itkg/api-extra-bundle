<?php

namespace Itkg\ApiExtraBundle\EventSubscriber;

use Doctrine\Common\Annotations\Reader;
use Itkg\ApiExtraBundle\Exception\UnknownHttpException;
use OpenOrchestra\BaseApi\EventSubscriber\AbstractSubscriber;
use OpenOrchestra\BaseApi\Exceptions\ApiException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class UnknownExceptionSubscriber
 *
 * This subscriber catches all exceptions that where not processed when the API is running
 *
 * This allows to replace the exception with an ApiException
 * so the format of the response will be consistent
 */
class UnknownExceptionSubscriber extends AbstractSubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    private $isDebug = false;

    /**
     * @param Reader                      $annotationReader
     * @param ControllerResolverInterface $resolver
     * @param bool                        $isDebug
     */
    public function __construct(Reader $annotationReader, ControllerResolverInterface $resolver, $isDebug)
    {
        parent::__construct($annotationReader, $resolver);
        $this->isDebug = $isDebug;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    { 
        $exception = $event->getException();
        // do not process ApiException as they will be catched by Orchestra as is
        if ($exception instanceof ApiException || $exception instanceof HttpExceptionInterface) {
            return;
        }
        $annotation = $this->extractAnnotation($event, 'Itkg\ApiExtraBundle\Controller\Annotations\Wrap');
        if (!$annotation) {
            return;
        }
        $message = 'An error occurred during the processing of your request.';
        if ($this->isDebug) {
            $message = $exception->getMessage();
        }
        $exception = new UnknownHttpException($message, $message);

        $event->setException($exception);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        // priority 1005 is set in order to run before
        // OpenOrchestra\BaseApi\EventSubscriber\HttpExceptionSubscriber
        return array(
            KernelEvents::EXCEPTION => array('onKernelException', 1005),
        );
    }
}
