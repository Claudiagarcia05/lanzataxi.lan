<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Password;
    use Illuminate\Validation\ValidationException;
    use Inertia\Inertia;
    use Inertia\Response;

    class PasswordResetLinkController extends Controller {
        public function create(): Response {

            return Inertia::render('Auth/ForgotPassword', [
                'status' => session('status'),
            ]);
        }

        /**
         * Handle an incoming password reset link request.
         *
         * @throws \Illuminate\Validation\ValidationException
         */
        public function store(Request $solicitud): RedirectResponse {
            $solicitud->validate([
                'email' => 'required|email',
            ]);

            $estado = Password::sendResetLink(
                $solicitud->only('email')
            );

            if ($estado == Password::RESET_LINK_SENT) {

                return back()->with('status', __($estado));
            }

            throw ValidationException::withMessages([
                'email' => [trans($estado)],
            ]);
        }
    }