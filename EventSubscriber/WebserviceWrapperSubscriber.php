<?php

namespace Itkg\ApiExtraBundle\EventSubscriber;

use OpenOrchestra\BaseApi\EventSubscriber\AbstractSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class WebserviceWrapperSubscriber
 */
class WebserviceWrapperSubscriber extends AbstractSubscriber implements EventSubscriberInterface
{
    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function wrapData(GetResponseForControllerResultEvent $event)
    {
        if (!$this->isEventEligible($event)) {
            return;
        }

        $annot = $this->extractAnnotation($event, 'Itkg\ApiExtraBundle\Controller\Annotations\Wrap');
        if (!$annot) {
            return;
        }

        $data = $event->getControllerResult();
        $result = array(
            'status'  => true,
            'message' => '',
            'data'    => $data
        );
        $event->setControllerResult($result);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array('wrapData', 10)
        );
    }
}
