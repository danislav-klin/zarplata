<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    // Показать форму входа
    public function login()
    {
        echo view('templates/header');
        echo view('auth/login');
        echo view('templates/footer');
    }

    // Обработать форму входа
    public function loginPost()
    {


        $login    = $this->request->getPost('login');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();

        // Ищем по email или логину
        $user = $userModel->groupStart()
            ->where('email', $login)
            ->orWhere('login', $login)
            ->groupEnd()
            ->first();

        //     dd([
        //     'login_input'   => $login,
        //     'user_found'    => $user,
        //     'verify_result' => $user ? password_verify($password, $user['password_hash']) : 'user not found',
        // ]);

        // Проверяем пользователя и пароль
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return redirect()->to('/login')
                ->withInput()
                ->with('errors', ['Неверный логин или пароль.']);
        }

        // Проверяем активен ли аккаунт
        if (!$user['is_active']) {
            return redirect()->to('/login')
                ->withInput()
                ->with('errors', ['Аккаунт заблокирован.']);
        }

        // Записываем данные в сессию
        session()->set([
            'user_id'   => $user['id'],
            'user_name' => $user['first_name'] . ' ' . $user['last_name'],
            'user_role' => $user['role'],
            'user_email' => $user['email'],
            'logged_in' => true,
        ]);

        return redirect()->to('/');
    }

    // Показать форму регистрации
    public function register()
    {
        echo view('templates/header');
        echo view('auth/register');
        echo view('templates/footer');
    }

    // Обработать форму регистрации 
    public function registerPost()
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'surname'    => 'permit_empty|max_length[100]',
            'login'      => 'required|min_length[3]|max_length[100]|is_unique[users.login]',
            'email'      => 'required|valid_email|max_length[255]|is_unique[users.email]',
            'password'   => 'required|min_length[6]',
            'password2'  => 'required|matches[password]',
        ];

        // Человекопонятные названия полей для ошибок
        $messages = [
            'password2' => [
                'matches' => 'Пароли не совпадают.',
            ],
            'login' => [
                'is_unique' => 'Этот логин уже занят.',
            ],
            'email' => [
                'is_unique' => 'Этот email уже зарегистрирован.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->to('/register')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();

        $userModel->insert([
            'first_name'    => $this->request->getPost('first_name'),
            'last_name'     => $this->request->getPost('last_name'),
            'surname'       => $this->request->getPost('surname') ?: null,
            'login'         => $this->request->getPost('login'),
            'email'         => $this->request->getPost('email'),
            'password_hash' => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'role'      => 'manager',
            'is_active' => 1,
        ]);

        return redirect()->to('/login')
            ->with('message', 'Регистрация успешна! Войдите в аккаунт.');
    }

    // Выход
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function profileUpdate()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Сессия истекла, войдите заново');
        }

        $userModel = new \App\Models\UserModel();

        $lastName  = trim((string) $this->request->getPost('last_name'));
        $firstName = trim((string) $this->request->getPost('first_name'));
        $surname   = trim((string) $this->request->getPost('surname'));
        $email     = trim((string) $this->request->getPost('email'));
        $login     = trim((string) $this->request->getPost('login'));
        $pass      = $this->request->getPost('password');
        $conf      = $this->request->getPost('password_confirm');

        if ($lastName === '' || $firstName === '' || $email === '' || $login === '') {
            return redirect()->back()->withInput()->with('error', 'Фамилия, имя, email и логин обязательны');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Некорректный email');
        }

        if ($userModel->where('email', $email)->where('id !=', $userId)->first()) {
            return redirect()->back()->withInput()->with('error', 'Этот email уже занят');
        }

        if ($userModel->where('login', $login)->where('id !=', $userId)->first()) {
            return redirect()->back()->withInput()->with('error', 'Этот логин уже занят');
        }

        $data = [
            'last_name'  => $lastName,
            'first_name' => $firstName,
            'surname'    => $surname !== '' ? $surname : null,
            'email'      => $email,
            'login'      => $login,
        ];

        if (!empty($pass)) {
            if (strlen($pass) < 6) {
                return redirect()->back()->withInput()->with('error', 'Пароль короче 6 символов');
            }
            if ($pass !== $conf) {
                return redirect()->back()->withInput()->with('error', 'Пароли не совпадают');
            }
            $data['password_hash'] = password_hash($pass, PASSWORD_DEFAULT);
        }

        if (!$userModel->update($userId, $data)) {
            return redirect()->back()->withInput()
                ->with('error', 'Ошибка сохранения: ' . json_encode($userModel->errors(), JSON_UNESCAPED_UNICODE));
        }

        session()->set([
            'user_name'  => $lastName . ' ' . $firstName,
            'user_email' => $email,
        ]);

        return redirect()->back()->with('success', 'Профиль обновлён');
    }
}
