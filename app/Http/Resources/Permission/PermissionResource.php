<?php

namespace App\Http\Resources\Permission;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'module' => $this->module,
            'guard_name' => $this->guard_name,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
