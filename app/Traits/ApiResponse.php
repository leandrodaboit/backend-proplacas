<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(mixed $data = null, string $message = 'Operação realizada com sucesso', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data ?? new \stdClass(),
        ], $code);
    }

    protected function created(mixed $data = null, string $message = 'Recurso criado com sucesso'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function error(string $message = 'Erro na operação', int $code = 400, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => new \stdClass(),
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    protected function unauthorized(string $message = 'Não autorizado'): JsonResponse
    {
        return $this->error($message, 401);
    }

    protected function forbidden(string $message = 'Acesso negado'): JsonResponse
    {
        return $this->error($message, 403);
    }

    protected function notFound(string $message = 'Recurso não encontrado'): JsonResponse
    {
        return $this->error($message, 404);
    }

    protected function validationError(mixed $errors, string $message = 'Erro de validação'): JsonResponse
    {
        return $this->error($message, 422, $errors);
    }

    protected function serverError(string $message = 'Erro interno do servidor'): JsonResponse
    {
        return $this->error($message, 500);
    }
}
