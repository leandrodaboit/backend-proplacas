<?php

namespace App\Http\Requests\Permission;

use App\DTO\Permission\AssignRoleDTO;
use Illuminate\Foundation\Http\FormRequest;

class AssignRolesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('users.manage-roles');
    }

    public function rules(): array
    {
        return [
            'roles' => ['required', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'roles.required' => 'Informe pelo menos uma role.',
            'roles.*.exists' => 'Uma ou mais roles informadas não existem.',
        ];
    }

    public function toDTO(): AssignRoleDTO
    {
        return new AssignRoleDTO(
            userId: (int) $this->route('user'),
            roles: $this->validated('roles'),
        );
    }
}
