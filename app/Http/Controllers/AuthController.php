<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Redirect based on user role
            $user = Auth::user();
            if ($user->role === 'manager') {
                return redirect()->intended(route('admin.dashboard'));
            } else {
                // Employee redirects to create incident page
                return redirect()->intended(route('incidents.create'))
                    ->with('welcome', __('messages.Welcome back! You can report safety incidents here.'));
            }
        }

        return back()->withErrors(['email' => __('auth.failed')])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // Create a company for the new user
        $company = Company::create([
            'name' => $data['name'] . "'s Company",
            'size' => 'small',
        ]);

        // New registered users are managers with their own company
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'manager',
            'company_id' => $company->id,
        ]);

        Auth::login($user);
        
        // Redirect to admin dashboard with welcome message
        return redirect()->route('admin.dashboard')
            ->with('status', __('messages.Welcome! Your company has been created. You can now add employees and manage incidents.'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}


