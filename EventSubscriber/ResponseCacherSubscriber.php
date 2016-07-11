<?php

namespace Itkg\ApiExtraBundle\EventSubscriber;

use Itkg\ApiExtraBundle\Cacher\ResponseCacherInterface;
use Itkg\ApiExtraBundle\Controller\Annotations\Cache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class WebserviceWrapperSubscriber
 */
class ResponseCacherSubscriber implements EventSubscriberInterface
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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    private $tokenStorage;

    /**
     * @param TokenStorageInterface          $tokenStorage
     * @param AuthorizationCheckerInterface  $authorizationChecker
     * @param ResponseCacherInterface        $responseCacher
     * @param array                          $cachedRoutes
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, ResponseCacherInterface $responseCacher, array $cachedRoutes = array())
    {
        $this->tokenStorage = $tokenStorage;
        $this->responseCacher = $responseCacher;
        $this->cachedRoutes = $cachedRoutes;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param PostResponseEvent $event
     */
    public function cacheData(PostResponseEvent $event)
    {
        if ($event->getRequest()->query->get('no-cache', null) || $this->tokenStorage->getToken() && $this->authorizationChecker->isGranted('MHPS_SLIDE_DECK_FORCE_PREVIEW')) {
            return;
        }
        if (array_key_exists($route = $event->getRequest()->attributes->get('_route'), $this->cachedRoutes)) {
            /** @var Response $response */
            $response = $event->getResponse();

            if ($response->getStatusCode() < 400) {
                if (!$event->getRequest()->attributes->has('cached_data')) {
                    $this->responseCacher->writeCache($event->getRequest(), $event->getResponse(), $this->cachedRoutes[$route]);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::TERMINATE => array('cacheData')
        );
    }


}
