<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getDataByUuid($uuid){
        $user = User::where('uuid', $uuid)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'user' => null,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }
}
