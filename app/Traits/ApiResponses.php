<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Response;

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

    public function respondWithPayload(string $message, array $payload) : Response {
        return $this->respond($message, $payload, 200);
    }

    public function respondWithError(string $message) : Response {
        return $this->respond($message, [], 500);
    }
}
