<?php

namespace App\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Valida el inicio de sesión y bloquea por un minuto
 * si alguien se equivoca 5 veces seguidas (protección contra robots).
 */
class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function autenticar(): void
    {
        $this->verificarIntentos();

        $credenciales = [...$this->only('email', 'password'), 'activo' => true];

        if (! Auth::attempt($credenciales, $this->boolean('recordarme'))) {
            RateLimiter::hit($this->llaveIntentos());

            throw ValidationException::withMessages([
                'email' => 'El correo o la contraseña no son correctos (o el usuario está desactivado).',
            ]);
        }

        RateLimiter::clear($this->llaveIntentos());
    }

    private function verificarIntentos(): void
    {
        if (! RateLimiter::tooManyAttempts($this->llaveIntentos(), 5)) {
            return;
        }

        event(new Lockout($this));
        $segundos = RateLimiter::availableIn($this->llaveIntentos());

        throw ValidationException::withMessages([
            'email' => "Demasiados intentos. Espera {$segundos} segundos e inténtalo otra vez.",
        ]);
    }

    private function llaveIntentos(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
