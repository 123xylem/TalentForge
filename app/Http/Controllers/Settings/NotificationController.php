<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
//TODO add Caching to notifications 
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('settings/Notifications/Index');
    }

    public function markAsRead(Request $request)
    {
        try {
            $notification_id = $request->notification_id ?? $request->route('notification');
            $user = $request->user();
            $user->unreadNotifications->where('id', $notification_id)->markAsRead();
            return back();
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'notification_id' => $request->notification_id ?? $request->route('notification')
            ]);

            return back()->with('error', 'Failed to mark notification as read');
        }
    }

    public function markAllAsRead(Request $request)
    {
        try {
            $request->user()->unreadNotifications->markAsRead();

            return back();
        } catch (\Exception $e) {
            \Log::error([$e, 'Failed to mark all notifications as read']);
            return back()->with('error', 'Failed to mark all notifications as read');
        }
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
