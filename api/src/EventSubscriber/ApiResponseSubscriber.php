<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        // Only apply to JSON responses and API routes
        if (!$response instanceof JsonResponse) {
            return;
        }

        $pathInfo = $request->getPathInfo();
        if (!str_starts_with($pathInfo, '/api/')) {
            return;
        }

        $content = $response->getContent();
        if (!$content) {
            return;
        }

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return;
        }

        // Skip if already wrapped
        if (isset($data['success']) && isset($data['data'])) {
            return;
        }

        $statusCode = $response->getStatusCode();
        $success = $statusCode >= 200 && $statusCode < 300 ? 1 : 0;

        $wrappedData = [
            'success' => $success,
            'data' => $data
        ];

        $response->setContent(json_encode($wrappedData));
    }
}