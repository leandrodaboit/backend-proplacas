<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sobrenome' => $this->sobrenome,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'tipo' => $this->tipo,
            'status' => $this->status,
            'ativo' => $this->ativo,
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
