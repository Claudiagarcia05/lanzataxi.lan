<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $solicitud): ?string
    {
        return parent::version($solicitud);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $solicitud): array
    {
        return [
            ...parent::share($solicitud),
            'auth' => [
                'user' => $solicitud->user(),
            ],
        ];
    }
}
