<?php

namespace App\Http\Requests\Permission;

use App\DTO\Permission\UpdateRoleDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('roles.update');
    }

    public function rules(): array
    {
        $roleId = $this->route('role');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:125',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Já existe uma role com este nome.',
            'name.max' => 'O nome deve ter no máximo 125 caracteres.',
            'permissions.*.exists' => 'Uma ou mais permissões informadas não existem.',
        ];
    }

    public function toDTO(): UpdateRoleDTO
    {
        return new UpdateRoleDTO(
            name: $this->validated('name'),
            description: $this->validated('description'),
            permissions: $this->has('permissions') ? $this->validated('permissions') : null,
        );
    }
}
