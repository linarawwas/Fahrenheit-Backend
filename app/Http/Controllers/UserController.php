<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function viewUsers()
    {
        $users = User::all();

        return response()->json([
            'users' => $users
        ]);
    }
    public function getAuthenticatedUser(Request $request)
    {
        $user = Auth::user();

        return response()->json(['user' => $user]);
    }
}
