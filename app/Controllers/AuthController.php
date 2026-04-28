<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    public function __construct()
    {
        helper('form');
    }

    public function login()
    {
        if ($this->request->getPost()) {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');

            $dataUser = [
                'username' => 'Disa',
                'password' => '202cb962ac59075b964b07152d234b70', // 123
                'role' => 'admin'
            ];

            if ($username == $dataUser['username']) {
                if (md5($password) == $dataUser['password']) {

                  date_default_timezone_set('Asia/Jakarta'); // biar sesuai dengan waktu
                  session()->set([
                  'username'   => $dataUser['username'],
                  'role'       => $dataUser['role'],
                  'email'      => '111202416041@mhs.dinus.ac.id',
                  'login_time' => date('Y-m-d H:i:s'),
                  'isLoggedIn' => TRUE
]);

                    return redirect()->to('/');
                } else {
                    session()->setFlashdata('failed', 'Username & Password Salah');
                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('failed', 'Username Tidak Ditemukan');
                return redirect()->back();
            }
        } else {
            return view('v_login');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}