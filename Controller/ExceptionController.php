<?php

namespace Itkg\ApiExtraBundle\Controller;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

/**
 * Class ExceptionController
 */
class ExceptionController extends Controller
{
    /**
     * @param ApiException         $exception
     * @param DebugLoggerInterface $logger
     * @param string               $format
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(ApiException $exception, DebugLoggerInterface $logger = null, $format = 'html')
    {
        $this->container->get('request')->setRequestFormat($format);

        return $this->render('MhpsApiBundle:Exception:show.'.$format.'.twig', array('exception' => $exception));
    }
}
