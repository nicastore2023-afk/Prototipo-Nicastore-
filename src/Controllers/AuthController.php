<?php

declare(strict_types=1);

namespace Nicastore\Controllers;

use Nicastore\Core\Controller;
use Nicastore\Models\User;
use Nicastore\Core\Session;

class AuthController extends Controller
{
    private User $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function showLogin(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect(AppConfig::url('/'));
        }
        $this->view('auth/login', ['title' => 'Iniciar Sesión']);
    }
    
    public function login(): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Token CSRF inválido']);
            $this->back();
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        if (empty($email) || empty($password)) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Email y contraseña son requeridos']);
            $this->back();
        }
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Credenciales inválidas']);
            $this->back();
        }
        
        Session::regenerate();
        Session::setUser([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);
        
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->update($user['id'], ['remember_token' => $token]);
            setcookie('remember_token', $token, time() + 60 * 60 * 24 * 30, '/', '', false, true);
        }
        
        $cart = new \Nicastore\Models\Cart();
        $cart->mergeGuestCart($user['id']);
        
        Session::flash('flash', ['type' => 'success', 'message' => 'Bienvenido, ' . $user['name']]);
        $this->redirect(AppConfig::url('/'));
    }
    
    public function showRegister(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect(AppConfig::url('/'));
        }
        $this->view('auth/register', ['title' => 'Registrarse']);
    }
    
    public function register(): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Token CSRF inválido']);
            $this->back();
        }
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        $errors = [];
        
        if (empty($name)) {
            $errors[] = 'El nombre es requerido';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }
        
        if (strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres';
        }
        
        if ($password !== $passwordConfirm) {
            $errors[] = 'Las contraseñas no coinciden';
        }
        
        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'El email ya está registrado';
        }
        
        if (!empty($errors)) {
            Session::flash('flash', ['type' => 'error', 'message' => implode('<br>', $errors)]);
            $this->back();
        }
        
        $userId = $this->userModel->createUser([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);
        
        $user = $this->userModel->find($userId);
        
        Session::regenerate();
        Session::setUser([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);
        
        $cart = new \Nicastore\Models\Cart();
        $cart->mergeGuestCart($user['id']);
        
        Session::flash('flash', ['type' => 'success', 'message' => 'Cuenta creada exitosamente']);
        $this->redirect(AppConfig::url('/'));
    }
    
    public function logout(): void
    {
        $token = $_COOKIE['remember_token'] ?? null;
        if ($token) {
            $this->userModel->query("UPDATE users SET remember_token = NULL WHERE remember_token = ?", [$token]);
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        Session::logout();
        Session::flash('flash', ['type' => 'success', 'message' => 'Sesión cerrada correctamente']);
        $this->redirect(AppConfig::url('/'));
    }
    
    public function checkRemember(): void
    {
        $token = $_COOKIE['remember_token'] ?? null;
        
        if ($token && !Session::isLoggedIn()) {
            $user = $this->userModel->query("SELECT * FROM users WHERE remember_token = ?", [$token])->fetch();
            
            if ($user) {
                Session::regenerate();
                Session::setUser([
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]);
                
                $cart = new \Nicastore\Models\Cart();
                $cart->mergeGuestCart($user['id']);
            }
        }
    }
}