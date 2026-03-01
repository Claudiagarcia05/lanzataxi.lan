<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use Illuminate\Auth\Events\PasswordReset;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Password;
    use Illuminate\Support\Str;
    use Illuminate\Validation\Rules;
    use Illuminate\Validation\ValidationException;
    use Inertia\Inertia;
    use Inertia\Response;

    class NewPasswordController extends Controller {
        public function create(Request $solicitud): Response {

            return Inertia::render('Auth/ResetPassword', [
                'email' => $solicitud->email,
                'token' => $solicitud->route('token'),
            ]);
        }

        /**
         * Handle an incoming new password request.
         *
         * @throws \Illuminate\Validation\ValidationException
         */
        public function store(Request $solicitud): RedirectResponse {
            $solicitud->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $estado = Password::reset(
                $solicitud->only('email', 'password', 'password_confirmation', 'token'),
                function ($usuario) use ($solicitud) {
                    $usuario->forceFill([
                        'password' => Hash::make($solicitud->password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($usuario));
                }
            );

            if ($estado == Password::PASSWORD_RESET) {

                return redirect()->route('login')->with('status', __($estado));
            }

            throw ValidationException::withMessages([
                'email' => [trans($estado)],
            ]);
        }
    }