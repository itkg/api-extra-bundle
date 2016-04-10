<?php

namespace Itkg\ApiExtraBundle\EventSubscriber;

use JMS\Serializer\Exception\RuntimeException;
use Itkg\ApiExtraBundle\Exception\SerializationHttpException;
use OpenOrchestra\BaseApi\EventSubscriber\AbstractSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class SerializationExceptionSubscriber
 *
 * This subscriber catches exceptions from the JMS/Serializer package
 * If a Runtime exception is thrown during an Api request, we process it
 * into an ApiException that Orchestra will be able to transform to a JSON response
 *
 * This avoids issues with malformed requests from a client
 * (like malformed JSON and incorrect fields in JSON object)
 */
class SerializationExceptionSubscriber extends AbstractSubscriber implements EventSubscriberInterface
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!($exception = $event->getException()) instanceof RuntimeException ) {
            return;
        }

        $annotation = $this->extractAnnotation($event, 'Itkg\ApiExtraBundle\Controller\Annotations\Wrap');
        if (!$annotation) {
            return;
        }

        $message = 'The data in the request could not be (de)serialized.';

        if (in_array($event->getRequest()->getMethod(), array('POST', 'PUT'))) {
            $httpCode = 400; // we assume that the error is from the client on POST/PUT requests
            $message .= ' ' . $exception->getMessage();
        } else {
            $httpCode = 500;
        }

        $exception = new SerializationHttpException($httpCode, $message, $message);

        $event->setException($exception);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        // priority 1010 is set in order to run before
        // OpenOrchestra\BaseApi\EventSubscriber\HttpExceptionSubscriber
        return array(
            KernelEvents::EXCEPTION => array('onKernelException', 1010),
        );
    }
}
