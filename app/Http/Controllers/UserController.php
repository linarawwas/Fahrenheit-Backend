<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function viewUsers()
    {
        $users = User::all();

        return response()->json([
            'users' => $users
        ]);
    }
}
