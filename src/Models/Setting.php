<?php

declare(strict_types=1);

namespace Nicastore\Models;

use Nicastore\Core\Model;

class Setting extends Model
{
    protected string $table = 'settings';
    protected array $fillable = ['key', 'value'];
    
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = $this->findBy('key', $key);
        return $setting ? $setting['value'] : $default;
    }
    
    public function set(string $key, mixed $value): int
    {
        $existing = $this->findBy('key', $key);
        if ($existing) {
            return $this->update($existing['id'], ['value' => $value]);
        }
        return $this->create(['key' => $key, 'value' => $value]);
    }
    
    public function getAll(): array
    {
        $settings = $this->all(['key', 'value']);
        return array_column($settings, 'value', 'key');
    }
}