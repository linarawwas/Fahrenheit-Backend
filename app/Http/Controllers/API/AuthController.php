<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ReadingStreak;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        $user = User::where('username', $request->username)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }
        return response()->json([
            'user_id' => $user->id,
            'user' => $user,
            'authorization' => [
                'token' => $user->createToken('ApiToken')->plainTextToken,
                'type' => 'bearer',
                'user' => $user,
            ]
        ]);
    }
    public function register(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|unique:users',
                'password' => 'required',
                'email' => 'required|email|unique:users',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $email = $request->email;

            // Check if the email is valid using Mailboxlayer API
            $response = Http::get('http://apilayer.net/api/check', [
                'access_key' => 'ca5aa27e3c13cee1304b031599ba9a92',
                'email' => $email,
            ]);

            if ($response->failed()) {
                throw new \Exception('Failed to validate email. Please try again.');
            }

            $result = $response->json();

            if (isset($result['success']) && !$result['success']) {
                throw new \Exception('Email validation failed. Reason: ' . $result['error']['info']);
            }

            if (!isset($result['format_valid']) || !$result['format_valid'] || !$result['smtp_check']) {
                throw ValidationException::withMessages([
                    'email' => ['The provided email is invalid or does not exist.'],
                ]);
            }

            // Save the user record
            $user = new User();
            $user->username = $request->input('username');
            $user->password = bcrypt($request->input('password'));
            $user->email = $request->input('email');
            if ($request->hasFile('profile_picture')) {
                $profilePicture = $request->file('profile_picture');
                $profilePicturePath = time() . '.' . $profilePicture->extension();
                $profilePicture->move(public_path('profile_picture'), $profilePicturePath);
                $user->profile_picture = $profilePicturePath;
            }
            $user->save();

            // Create a reading streak for the newly registered user
            $streak = new ReadingStreak([
                'last_reading_day' => Carbon::now()->toDateString(),
                'streak' => 1,
                'longest_streak' => 1,
            ]);

            $user->readingStreak()->save($streak);

            return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
