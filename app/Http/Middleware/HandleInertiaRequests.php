<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use Illuminate\Session\TokenMisMatchException;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {


        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        // Get flash data from session
        $flashData = $request->session()->get('flash', []);
        $flash = [
            'errors' => $request->session()->get('errors') ? $request->session()->get('errors')->getBag('default')->getMessages() : null,
            'success' => $flashData['success'] ?? null,
            'error' => $flashData['error'] ?? null,
        ];

        //Share session data to inertia
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user() ? [
                    // User data
                    ...($request->user()->toArray()),
                    'notifications' => $request->user()->notifications()->latest()->paginate(15),
                    'unread_notifications_count' => $request->user()->unreadNotifications()->count(),
                ] : null,
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => $flash,
            'csrf_token' => csrf_token(),
        ];
    }
    public function handle($request, \Closure $next)
    {
        \Log::info('REQUEST', [
            'REQUEST_URL' => $request->url(),
            'REQUEST_HOST' => $request->getHost(),
            'REQUEST_SCHEME' => $request->getScheme(),
            'REQUEST_PORT' => $request->getPort(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'auth' => auth()->check(),
            // 'session_id' => session()->getId(), // Add this
            // 'session_domain' => config('session.domain'), // Add this
            // 'session_secure' => config('session.secure'), // Add this
            // 'session_same_site' => config('session.same_site'), // Add this
            // // 'cookies' => $request->cookies->all(), // Add this


        ]);

        $response = parent::handle($request, $next);

        \Log::info('RESPONSE', [
            'status' => $response->status(),
            'headers' => $response->headers->all(),
        ]);

        return $response;
    }
}
