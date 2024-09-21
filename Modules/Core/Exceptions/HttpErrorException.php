<?php

namespace Modules\Core\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HttpErrorException extends Exception
{

    public function __construct(string $message, protected array $errors = [], $code = 400)
    {
        $this->message = $message;
        $this->code = $code;
    }
    /**
     * Report the exception.
     */
    public function report(): void
    {
        // ...
    }
 
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->message,
            'errors' => (object) $this->errors
        ], $this->code);
    }
}
