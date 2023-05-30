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

    public function getUsername()
    {
        // Get the authenticated user
        $user = Auth::user();

        if ($user) {
            // Retrieve the username
            $username = $user->username;

            // Return the username in the response
            return response()->json(['username' => $username]);
        }

        // If no authenticated user found, return an error response
        return response()->json(['message' => 'User not found'], 404);
    }

    public function getAuthenticatedUser(Request $request)
    {
        $user = Auth::user()->load('readingStreak');

        return response()->json(['user' => $user]);
    }

    public function update(Request $request)
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Validate the request data
        $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
            'password' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update the user's information
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->email = $request->email;

        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture');
            $profilePicturePath = time() . '.' . $profilePicture->extension();

            $profilePicture->move(public_path('profile_picture'), $profilePicturePath);

            $user->profile_picture = $profilePicturePath;
        }

        $user->save();

        // Return the updated user information in the response
        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    public function getReadingRank()
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Get the reading rank
        $readingRank = $user->reading_rank;

        // Return the reading rank in the response
        return response()->json(['reading_rank' => $readingRank]);
    }

    public function getProfilePicture()
    {
        // Get the authenticated user
        $user = Auth::user();

        if ($user) {
            // Retrieve the profile picture URL
            $profilePicture = $user->profile_picture;

            // Return the profile picture URL in the response
            return response()->json(['profile_picture' => $profilePicture]);
        }

        // If no authenticated user found, return an error response
        return response()->json(['message' => 'User not found'], 404);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json(['message' => 'User soft deleted successfully'], 200);
    }
}
