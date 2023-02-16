<?php
namespace Modules\Auth\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthException extends Exception
{
    /**
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            "message" => $this->getMessage()
        ], Response::HTTP_UNAUTHORIZED);
    }
}
