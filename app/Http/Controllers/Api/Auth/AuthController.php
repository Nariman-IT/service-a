<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\AuthLoginRequest;
use App\Http\Requests\Api\Auth\AuthRegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(AuthLoginRequest $request) 
    {
        $data = $request->validated();
    
        $user = User::where('email', $data['email'])->first();
    
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Неверные учетные данные',
            ], 401);
        }
    
        $token = $user->createToken('auth-token')->plainTextToken;
    
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }


    public function register(AuthRegisterRequest $request)
    {
       $data = $request->validated();


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
            
            $cartGuest = DB::table('carts')->where('guest_id', $guest->id)->first();
        }


        $user = User::create($data);
        $token = $user->createToken('auth-token')->plainTextToken;
        
        if (isset($cartGuest)) {
            DB::table('carts')
                ->where('guest_id', $guest->id)
                ->update(['user_id' => $user->id, 'guest_id' => null]);

            DB::table('guests')->where('guest_id', request()->header('X-GUEST-ID'))->delete();
        }

        return response()->json([
            'success' => true,
            'token' => $token,
        ], 201);
    }
}
