<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);


        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
