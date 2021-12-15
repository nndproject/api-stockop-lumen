<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class UserController extends Controller
{
    protected function jwt(User $user) {
        $payload = [
            'iss' => "nndproject", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued. 
            'exp' => time() + 60*60 // Expiration time
        ];
        
        // As you can see we are passing `JWT_SECRET` as the second parameter that will 
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
  
        $email = $request->input('email');
        $password = $request->input('password');
  
        $user = User::select('name', 'email', 'password')->where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => 'Login failed'], 401);
        }
  
        $isValidPassword = Hash::check($password, $user->password);
        if (!$isValidPassword) {
          return response()->json(['message' => 'Login failed'], 401);
        }
  
        $generateToken = bin2hex(random_bytes(40));
        User::where('email', $email)->update(['remember_token' => $generateToken]);

        // $generateToken = $this->jwt($user);
        $user['token'] = $generateToken;
        return response()->json($user, 200);
       /*  return response()->json([
            'success'   => true,
            'data'      => $user
            // 'token'     => $generateToken
        ], 200); */
    }
}
