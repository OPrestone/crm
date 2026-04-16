<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseApiController extends Controller
{
    protected function apiTenantId(Request $request): int
    {
        return $request->attributes->get('api_app')->tenant_id;
    }

    protected function success(mixed $data, int $status = 200, array $meta = []): JsonResponse
    {
        $payload = ['data' => $data];
        if (!empty($meta)) $payload['meta'] = $meta;
        return response()->json($payload, $status);
    }

    protected function paginate($query, Request $request, callable $transform): JsonResponse
    {
        $perPage = min((int) ($request->query('per_page', 20)), 100);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'data' => $paginated->items() ? array_map($transform, $paginated->items()) : [],
            'meta' => [
                'total'        => $paginated->total(),
                'per_page'     => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem(),
            ],
            'links' => [
                'next' => $paginated->nextPageUrl(),
                'prev' => $paginated->previousPageUrl(),
            ],
        ]);
    }

    protected function notFound(string $resource = 'Resource'): JsonResponse
    {
        return response()->json(['error' => 'Not Found', 'message' => "$resource not found."], 404);
    }

    protected function error(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        $payload = ['error' => 'Bad Request', 'message' => $message];
        if (!empty($errors)) $payload['errors'] = $errors;
        return response()->json($payload, $status);
    }
}
