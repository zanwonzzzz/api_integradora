<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class controlcontroller extends Controller
{
    public function index(int $id = 0)
    {
        $user = User::find($id);
        $user->cuenta = true;
        $user->save();

        return response()->json('cuenta activada');
    }
}
