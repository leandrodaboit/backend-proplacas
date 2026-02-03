<?php

namespace App\Http\Requests\Permission;

use App\DTO\Permission\CreateRoleDTO;
use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('roles.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:125', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da role é obrigatório.',
            'name.unique' => 'Já existe uma role com este nome.',
            'name.max' => 'O nome deve ter no máximo 125 caracteres.',
            'permissions.*.exists' => 'Uma ou mais permissões informadas não existem.',
        ];
    }

    public function toDTO(): CreateRoleDTO
    {
        return new CreateRoleDTO(
            name: $this->validated('name'),
            description: $this->validated('description'),
            permissions: $this->validated('permissions') ?? [],
        );
    }
}
