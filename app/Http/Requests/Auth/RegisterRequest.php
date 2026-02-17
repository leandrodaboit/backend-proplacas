<?php

namespace App\Http\Requests\Auth;

use App\DTO\Auth\RegisterDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'sobrenome' => ['required', 'string', 'min:2', 'max:100'],
            'empresa' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'device_name' => ['required', 'string', 'max:255'],
            'tipo' => ['nullable', 'string', 'in:admin,operador,cliente'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 2 caracteres.',
            'name.max' => 'O nome deve ter no máximo 100 caracteres.',
            'sobrenome.required' => 'O sobrenome é obrigatório.',
            'sobrenome.min' => 'O sobrenome deve ter pelo menos 2 caracteres.',
            'sobrenome.max' => 'O sobrenome deve ter no máximo 100 caracteres.',
            'empresa.required' => 'A empresa é obrigatória.',
            'empresa.min' => 'A empresa deve ter pelo menos 2 caracteres.',
            'empresa.max' => 'A empresa deve ter no máximo 100 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'device_name.required' => 'O nome do dispositivo é obrigatório.',
            'tipo.in' => 'O tipo deve ser: admin, operador ou cliente.',
        ];
    }

    public function toDTO(): RegisterDTO
    {
        return new RegisterDTO(
            name: $this->validated('name'),
            email: $this->validated('email'),
            password: $this->validated('password'),
            deviceName: $this->validated('device_name'),
            ip: $this->ip() ?? '0.0.0.0',
            sobrenome: $this->validated('sobrenome'),
            empresa: $this->validated('empresa'),
            telefone: $this->validated('telefone'),
            tipo: $this->validated('tipo') ?? 'cliente',
        );
    }
}
