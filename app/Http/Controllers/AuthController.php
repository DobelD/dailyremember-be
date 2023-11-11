<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function register()
    {
        try {
            $validator = Validator::make(request()->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'phone_number' => 'required|string|max:20',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }

            $user = User::create([
                'name' => request('name'),
                'username' => request('username'),
                'email' => request('email'),
                'phone_number' => request('phone_number'),
                'password' => Hash::make(request('password')),
            ]);

            if ($user) {
                return response()->json(['message' => 'Successfully registered'], 200);
            } else {
                return response()->json(['error' => 'Failed to register'], 500);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json(['error' => $error], 500);
        }
    }

    public function login()
    {
        try {
            $credentials = request(['email', 'password']);

            if (!auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $this->respondWithToken(auth()->attempt($credentials));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json(['error' => $error], 500);
        }
    }

    public function me()
    {
        try {
            return response()->json(auth()->user(), 200);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json(['error' => $error], 500);
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json(['error' => $error], 500);
        }
    }

    public function refresh()
    {
        try {
            return $this->respondWithToken(auth()->refresh(), 200);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json(['error' => $error], 500);
        }
    }

    protected function respondWithToken($token)
    {
        try {
            $userId = Auth::id();
            return response()->json([
                'access_token' => $token,
                'user_id'=> $userId
                // 'expires_in' => auth()->factory()->getTTL() * 60
            ], 200);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json(['error' => $error], 500);
        }
    }

    public function updateProfile($id, Request $req)
    {
        try {
            $data = User::find($id);

            if (!$data) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $this->validate($req, [
                "name" => "required|unique:users,name,$id",
                "email" => "email|required|unique:users,email,$id",
                "phone_number" => "required|unique:users,phone_number,$id",
                "username" => "required|unique:users,username,$id",
                "avatar" => "nullable|image|mimes:jpg,png,jpeg,gif,svg",
            ]);

            if ($req->hasFile('avatar')) {
                $username = $data->username;
                $file = $req->file('avatar');
                $extension = $file->getClientOriginalExtension();
                $filename = "{$username}_avatar.{$extension}";
                $path = $file->storeAs('public/avatars', $filename);
                $url = "https://laraveldailyremember.s3.amazonaws.com/$path";
                $data->avatar = $url;
            }

            $data->update($req->all());

            return response()->json(['message' => "Profile updated successfully"], 200);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return response()->json(['error' => $error], 500);
        }
    }

}
