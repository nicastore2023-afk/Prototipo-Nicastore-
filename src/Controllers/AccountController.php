<?php

declare(strict_types=1);

namespace Nicastore\Controllers;

use Nicastore\Core\Controller;
use Nicastore\Models\User;
use Nicastore\Models\Order;
use Nicastore\Core\Session;
use Nicastore\Config\AppConfig;

class AccountController extends Controller
{
    private User $userModel;
    private Order $orderModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->orderModel = new Order();
        
        if (!Session::isLoggedIn()) {
            $this->redirect(AppConfig::url('/login'));
        }
    }
    
    public function index(): void
    {
        $user = Session::getUser();
        $orders = $this->orderModel->getUserOrders($user['id'], 1, 5);
        
        $this->view('account/index', [
            'title' => 'Mi cuenta',
            'user' => $user,
            'orders' => $orders['data']
        ]);
    }
    
    public function orders(int $page = 1): void
    {
        $user = Session::getUser();
        $orders = $this->orderModel->getUserOrders($user['id'], $page, 10);
        
        $this->view('account/orders', [
            'title' => 'Mis pedidos',
            'orders' => $orders['data'],
            'pagination' => $orders
        ]);
    }
    
    public function orderDetail(int $id): void
    {
        $user = Session::getUser();
        $order = $this->orderModel->findById($id);
        
        if (!$order || $order['user_id'] != $user['id']) {
            $this->redirect(AppConfig::url('/cuenta/pedidos'));
        }
        
        $items = $this->orderModel->getItems($id);
        
        $this->view('account/order_detail', [
            'title' => 'Pedido #' . $id,
            'order' => $order,
            'items' => $items
        ]);
    }
    
    public function profile(): void
    {
        $user = Session::getUser();
        
        $this->view('account/profile', [
            'title' => 'Mi perfil',
            'user' => $user
        ]);
    }
    
    public function updateProfile(): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Token CSRF inválido']);
            $this->back();
        }
        
        $user = Session::getUser();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $newPasswordConfirm = $_POST['new_password_confirm'] ?? '';
        
        $errors = [];
        
        if (empty($name)) {
            $errors[] = 'El nombre es requerido';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }
        
        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser && $existingUser['id'] != $user['id']) {
            $errors[] = 'El email ya está en uso';
        }
        
        $updateData = ['name' => $name, 'email' => $email];
        
        if (!empty($currentPassword)) {
            $dbUser = $this->userModel->find($user['id']);
            if (!$this->userModel->verifyPassword($currentPassword, $dbUser['password'])) {
                $errors[] = 'Contraseña actual incorrecta';
            }
            
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 8) {
                    $errors[] = 'La nueva contraseña debe tener al menos 8 caracteres';
                }
                
                if ($newPassword !== $newPasswordConfirm) {
                    $errors[] = 'Las contraseñas no coinciden';
                }
                
                $updateData['password'] = $newPassword;
            }
        }
        
        if (!empty($errors)) {
            Session::flash('flash', ['type' => 'error', 'message' => implode('<br>', $errors)]);
            $this->back();
        }
        
        if (isset($updateData['password'])) {
            $updateData['password'] = password_hash($updateData['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }
        
        $this->userModel->update($user['id'], $updateData);
        
        Session::set('user', array_merge($user, ['name' => $name, 'email' => $email]));
        
        Session::flash('flash', ['type' => 'success', 'message' => 'Perfil actualizado correctamente']);
        $this->redirect(AppConfig::url('/cuenta/perfil'));
    }
}