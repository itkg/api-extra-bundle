<?php


namespace Itkg\ApiExtraBundle\EventSubscriber;

use Mhps\MediaBundle\Exceptions\HttpException\MediaNotFoundHttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * class MediaNotFoundExceptionSubscriber
 */
class MediaNotFoundExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $defaultImagePath;

    /**
     * @param string $defaultImagePath
     */
    public function __construct($defaultImagePath)
    {
        $this->defaultImagePath = $defaultImagePath;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!$exception instanceof MediaNotFoundHttpException ) {
            return;
        }

        $content = file_get_contents($this->defaultImagePath);
        $event->setResponse(new StreamedResponse(
            function () use ($content) {
                echo $content;
            },
            404,
            array(
                'Content-Type'        => 'image/png',
                'Content-Disposition' => 'inline; filename="default.png"',
                'Content-Size' => strlen($content),
            )
        ));
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array('onKernelException', 1004),
        );
    }
}
