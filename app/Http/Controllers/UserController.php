<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'      =>  'required|max:255',
            'email'     =>  'required|unique:users',
            'password'  =>  'required',
            'role_id'   =>  'required|' . Rule::in(['1', '2', '3', '4']),
        ]);

        $request['password'] = Hash::make($request->password);
        $user = User::create(
            $request->all()
        );
        unset($user->created_at);
        unset($user->updated_at);
        unset($user->deleted_at);
        return response()->json(['success' => true, 'message' => 'Data Ditambahkan', 'data' => $user]);
    }
}
