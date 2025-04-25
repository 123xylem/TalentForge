<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Settings/Notifications/Index', [
            'auth' => [
                'user' => [
                    'notifications' => $request->user()->notifications()->paginate(10)
                ]
            ]
        ]);
    }

    public function markAsRead(Request $request)
    {
        $notification_id = $request->notification_id;
        $user = $request->user();
        $user->unreadNotifications->where('id', $notification_id)->markAsRead();

        return back();
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return back();
    }

    public function clear(Request $request)
    {
        $request->user()->notifications()->delete();
        return back();
    }
}
