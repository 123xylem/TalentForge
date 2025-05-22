<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class UserController extends Controller
{
  public function index()
  {
    $users = Cache::remember('users-' . Auth::user()->id, 60 * 120, function () {
      return User::where('id', '!=', Auth::user()->id)
        // ->whereNotIn('id', function ($query) {
        //   $query->select('connected_user_id')
        //     ->from('connections')
        //     ->where('user_id', Auth::id());
        // })
        ->paginate(20);
    });

    $connections = Auth::user()->connections()
      ->where('is_accepted', true)
      ->pluck('connected_user_id')
      ->toArray();

    $pendingRequests = Auth::user()->connections()
      ->where('is_accepted', false)
      ->pluck('connected_user_id')
      ->toArray();

    return response()->json([
      'users' => $users,
      'connections' => array_fill_keys($connections, true),
      'pendingRequests' => array_fill_keys($pendingRequests, true)
    ]);
  }
}
