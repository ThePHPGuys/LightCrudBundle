<?php

namespace TPG\LightCrudBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use TPG\LightCrudBundle\Exception\RequestValidationException;

final class RequestValidationExceptionEventListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof RequestValidationException) {
            $response = new JsonResponse($exception, Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
        }
    }
}