<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request) 
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Неверные учетные данные',
            ], 401);
        }
    
        $token = $user->createToken('auth-token')->plainTextToken;
    
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }


    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);


        if (request()->header('X-GUEST-ID')) {
            $guest = DB::table('guests')
                ->where('guest_id', request()->header('X-GUEST-ID'))
                ->first();
            
            if (! $guest) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас исчерпан срок гостевой карты!',
                ], 401);
            }  
            
            $cartGuest = DB::table('cart_items')->where('guest_id', $guest->id)->first();
        }


        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        
        $token = $user->createToken('auth-token')->plainTextToken;
        
        if (isset($cartGuest)) {
            DB::table('cart_items')
                ->where('guest_id', $guest->id)
                ->update(['user_id' => $user->id, 'guest_id' => null]);

            DB::table('guests')->where('guest_id', request()->header('X-GUEST-ID'))->delete();
        }

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }
}
