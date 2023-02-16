<?php

namespace Modules\Loan\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UnAuthorizedException extends Exception
{
    /**
     * Custom exception for the loan module
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            "message" => $this->getMessage()
        ], Response::HTTP_UNAUTHORIZED);
    }
}
