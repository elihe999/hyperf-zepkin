<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;

use Hyperf\Tracer\TracerFactory;

class IndexController extends AbstractController
{
    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();
        $tracer = (new TracerFactory())($this->container);
        $span = $tracer->startSpan('test');
        $span->setTag('http.method', 'GET');
        $span->log(['event' => 'request_received']);
        $scope = $tracer->startActiveSpan('test', [
                  'finish_span_on_close' => false,
             ]);
        $span = $scope->getSpan();
        try {
            $span->setTag(\OpenTracing\Tags\HTTP_METHOD, 'GET');
            // ...
        } finally {
            $scope->close();
        }
        $span->finish();
        return [
            'method' => $method,
            'message' => "Hello {$user}.",
        ];
    }
}
