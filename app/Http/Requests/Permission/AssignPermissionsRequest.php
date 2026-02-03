<?php

namespace App\Http\Requests\Permission;

use App\DTO\Permission\AssignPermissionsDTO;
use Illuminate\Foundation\Http\FormRequest;

class AssignPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('permissions.manage');
    }

    public function rules(): array
    {
        return [
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'permissions.required' => 'Informe pelo menos uma permissão.',
            'permissions.*.exists' => 'Uma ou mais permissões informadas não existem.',
        ];
    }

    public function toDTO(): AssignPermissionsDTO
    {
        return new AssignPermissionsDTO(
            userId: (int) $this->route('user'),
            permissions: $this->validated('permissions'),
        );
    }
}
