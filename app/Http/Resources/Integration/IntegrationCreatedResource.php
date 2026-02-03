<?php

namespace App\Http\Resources\Integration;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IntegrationCreatedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'integration' => new IntegrationTokenResource($this->resource['integration']),
            'token' => $this->resource['token'],
        ];
    }
}
