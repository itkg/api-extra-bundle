<?php

namespace Itkg\ApiExtraBundle\EventSubscriber;

use Doctrine\Common\Annotations\Reader;
use Itkg\ApiExtraBundle\Cacher\ResponseCacherInterface;
use OpenOrchestra\BaseApi\EventSubscriber\AbstractSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * class RequestCacheReaderSubscriber 
 */
class RequestCacheReaderSubscriber extends AbstractSubscriber implements EventSubscriberInterface
{
    /**
     * @var ResponseCacherInterface
     */
    private $responseCacher;

    /**
     * @var array
     */
    private $cachedRoutes = array();
    /**
     * @param Reader $annotationReader
     * @param ControllerResolverInterface $resolver
     * @param ResponseCacherInterface     $responseCacher
     * @param array                       $cachedRoutes
     */
    public function __construct(
        Reader $annotationReader,
        ControllerResolverInterface $resolver,
        ResponseCacherInterface $responseCacher,
        array $cachedRoutes = array()
    ) {
        $this->responseCacher = $responseCacher;
        $this->cachedRoutes = $cachedRoutes;
        parent::__construct($annotationReader, $resolver);
    }

    /**
     * @param GetResponseEvent $event
     */
    public function readCacheData(GetResponseEvent $event)
    {
        if (array_key_exists($route = $event->getRequest()->attributes->get('_route'), $this->cachedRoutes)) {

            $cachedData = $this->responseCacher->readCache($event->getRequest(), $this->cachedRoutes[$route]);
            if (false !== $cachedData) {
                $event->setResponse(new Response($cachedData, 200, array(
                    'Content-Type' => 'application/json',
                    'X-Cache'      => 'Hit',
                    'X-Cache-Key'  => $event->getRequest()->attributes->get('cache_key')
                )));
                $event->getRequest()->attributes->set('cached_data', true);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('readCacheData', 0)
        );
    }
}

