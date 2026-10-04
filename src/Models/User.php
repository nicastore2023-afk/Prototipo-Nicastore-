<?php

declare(strict_types=1);

namespace Nicastore\Models;

use Nicastore\Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'password', 'role', 'email_verified_at', 'remember_token'];
    protected array $hidden = ['password', 'remember_token'];
    
    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }
    
    public function createUser(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $data['role'] = $data['role'] ?? 'customer';
        return $this->create($data);
    }
    
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    public function updatePassword(int $userId, string $newPassword): int
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->update($userId, ['password' => $hash]);
    }
    
    public function getCustomers(int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, ['id', 'name', 'email', 'role', 'created_at']);
    }
    
    public function getAdmins(): array
    {
        return $this->where('role', 'admin');
    }
}