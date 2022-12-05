<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\Sanctum;

class UserTokenController extends Controller
{
    //


    public function __invoke(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
            "device_name" => "required"
        ]);

        $user = User::where("email", $request->get("email"))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                "email" => "El email no existe o no coincide con nuestros datos"
            ]);
        }

        return response()->json([
            "token" => $user->createToken($request->device_name)->plainTextToken
        ]);
    }
}
