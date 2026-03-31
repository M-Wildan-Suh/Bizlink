<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpanelAccount extends Model
{
    use HasFactory;

    protected $appends = [
        'login_url',
    ];

    protected $fillable = [
        'name',
        'host',
        'port',
        'username',
        'primary_domain',
        'api_token',
        'use_ssl',
        'is_active',
    ];

    protected $hidden = [
        'api_token',
    ];

    protected $casts = [
        'api_token' => 'encrypted',
        'use_ssl' => 'boolean',
        'is_active' => 'boolean',
        'port' => 'integer',
    ];

    public function guardianWebs()
    {
        return $this->hasMany(GuardianWeb::class);
    }

    public function getLoginUrlAttribute(): string
    {
        $host = trim((string) $this->host);

        if ($host === '') {
            return '';
        }

        $parsedHost = parse_url(str_contains($host, '://') ? $host : 'http://' . $host, PHP_URL_HOST);
        $normalizedHost = $parsedHost ?: trim($host, '/');
        $scheme = $this->use_ssl ? 'https' : 'http';
        $port = (int) $this->port;

        return sprintf('%s://%s:%d', $scheme, $normalizedHost, $port);
    }
}
