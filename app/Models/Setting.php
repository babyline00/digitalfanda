<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'label',
        'description',
    ];

    protected $casts = [
        'value' => 'json', // will be handled by accessor
    ];

    public function getValueAttribute($value)
    {
        $type = $this->type;
        if ($type === 'int' || $type === 'integer') {
            return (int) $value;
        }
        if ($type === 'bool' || $type === 'boolean') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        if ($type === 'json' || $type === 'array') {
            return json_decode($value, true);
        }
        return $value;
    }

    public static function get(string $group, string $key, $default = null)
    {
        $setting = self::where('group', $group)->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $group, string $key, $value, string $type = 'string'): self
    {
        return self::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}