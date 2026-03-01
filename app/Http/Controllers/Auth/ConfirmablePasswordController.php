<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Validation\ValidationException;
    use Inertia\Inertia;
    use Inertia\Response;

    class ConfirmablePasswordController extends Controller {
        public function show(): Response {

            return Inertia::render('Auth/ConfirmPassword');
        }

        public function store(Request $solicitud): RedirectResponse {
            if (! Auth::guard('web')->validate([
                'email' => $solicitud->user()->email,
                'password' => $solicitud->password,
            ])) {
                throw ValidationException::withMessages([
                    'password' => __('auth.password'),
                ]);
            }

            $solicitud->session()->put('auth.password_confirmed_at', time());

            return redirect()->intended(route('dashboard', absolute: false));
        }
    }