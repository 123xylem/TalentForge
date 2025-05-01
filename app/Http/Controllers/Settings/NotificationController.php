<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Illuminate\Notifications\DatabaseNotification;
//TODO add Caching to notifications
class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('settings/Notifications/Index');
    }

    public function markAsRead(Request $request)
    {
        $notification_id = $request->notification_id ?? $request->route('notification');
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

    public function destroyAll(Request $request)
    {
        $request->user()->notifications()->delete();
        return Inertia::render('settings/Notifications/Index', [
            'flash' => ['success' => 'All notifications deleted successfully'],
        ]);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        $notification_id = $request->route('notification');
        $notification = DatabaseNotification::where('notifiable_id', $user->id)
            ->where('id', $notification_id)
            ->first();

        if ($notification) {
            $notification->delete();
        }
        return Inertia::render('settings/Notifications/Index', [
            'flash' => ['success' => 'Notification deleted successfully'],
        ]);
    }
}
