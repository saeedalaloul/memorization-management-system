<?php

namespace App\Http\Controllers;

use App\Models\UserSubscribeNotification;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->checkRoles();
        return view('dashboard');
    }

    private function checkRoles()
    {
        if (auth()->user()->current_role == null || empty(auth()->user()->current_role)) {
            if (count(auth()->user()->roles) > 0) {
                if (count(auth()->user()->roles) == 1) {
                    auth()->user()->update(['current_role' => auth()->user()->roles[0]->name]);
                } else {
                    auth()->user()->update(['current_role' => auth()->user()->roles[1]->name]);
                }
            }
        }
    }

    public function storeCurrentRole(Request $request)
    {
        if ($request->current_role) {
            auth()->user()->update(['current_role' => $request->current_role]);
            return response()->json(['success' => 'Ajax request submitted successfully']);
        }
        return response()->json(['error' => 'Ajax request error']);
    }

    public function checkUserSubscribeNotifications(Request $request)
    {
        if ($request->player_id) {
            $userSubscribeNotification = UserSubscribeNotification::where('id', auth()->id())->first();
            if ($userSubscribeNotification != null) {
                if ($userSubscribeNotification->player_id != $request->player_id) {
                    $userSubscribeNotification->update(['player_id' => $request->player_id]);
                }
            } else {
                UserSubscribeNotification::create(['id' => auth()->id(), 'player_id' => $request->player_id]);
            }

            return response()->json(['success' => 'Ajax request submitted successfully']);
        }
        return response()->json(['error' => 'Ajax request error']);
    }

}
