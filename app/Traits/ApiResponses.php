<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Response;

/**
 * Provides a unified set of methods for returning API responses.
 *
 */
trait ApiResponses {
    protected function respond(string $message, array $payload, int $code) : Response {
        $response       = [
            'success'   => $code === 200,
            'message'   => $message,
        ];

        if (count($payload)) {
            $response['payload']    = $payload;
        }

        return new Response($response, $code);
    }

    /**
     * OK (200) response with the payload array and status message.
     *
     * @param string $message
     * @param array $payload
     * @return Response
     */
    public function respondWithPayload(string $message, array $payload) : Response {
        return $this->respond($message, $payload, 200);
    }

    /**
     * Error (500) response with status message.
     *
     * @param string $message
     * @return Response
     */
    public function respondWithError(string $message) : Response {
        return $this->respond($message, [], 500);
    }
}
