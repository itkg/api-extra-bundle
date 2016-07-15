<?php

namespace Itkg\ApiExtraBundle\EventSubscriber;

use Itkg\ApiExtraBundle\Cache\Tag\Handler\TagHandlerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * class CacheInvalidateSubscriber 
 */
class CacheInvalidateSubscriber implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $tags;

    /**
     * @var TagHandlerInterface
     */
    private $tagHandler;

    /**
     * @param TagHandlerInterface $tagHandler
     * @param array               $tags
     */
    public function __construct(TagHandlerInterface $tagHandler, array $tags = array())
    {
        $this->tagHandler = $tagHandler;
        $this->tags = $tags;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => 'invalidate'
        );
    }

    /**
     * @param FilterResponseEvent $responseEvent
     */
    public function invalidate(FilterResponseEvent $responseEvent)
    {
        $request = $responseEvent->getRequest();
        $tags = array();
        if ($request->isMethod('POST')) {
            $parameters = array_merge($request->request->all(), $request->query->all(), $request->attributes->all());
            foreach ($this->tags as $key => $associatedTag) {
                foreach ($associatedTag['associated'] as $tag) {
                    if (isset($parameters[$tag])) {
                        $tags[$key] = array(
                            'value' => $parameters[$tag],
                            'type'  => $associatedTag['decache_type']
                        );
                    }
                }
            }
            $this->tagHandler->cleanTags($tags, true);
        }
    }
}
