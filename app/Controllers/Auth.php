<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/inventory');
        }

        $data = [
            'title' => 'Login'
        ];

        if ($this->request->getMethod() === 'POST') {
            $username = trim((string) $this->request->getPost('username'));
            $password = (string) $this->request->getPost('password');

            $user = $this->userModel->where('username', $username)->first();

            if ($user && hash('sha256', $password) === $user->password) {
                session()->set([
                    'userId' => $user->id,
                    'username' => $user->username,
                    'fullName' => $user->full_name,
                    'isLoggedIn' => true
                ]);

                return redirect()->to('/inventory')->with('success', 'Welcome back, ' . $user->full_name . '!');
            }

            return redirect()->back()->withInput()->with('error', 'Invalid username or password.');
        }

        return view('auth/login', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    }
}
