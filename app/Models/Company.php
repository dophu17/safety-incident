<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'website',
        'size',
        'employee_count',
        'industry',
        'description',
    ];

    /**
     * Get the users for the company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the manager (owner) of the company.
     */
    public function manager()
    {
        return $this->users()->where('role', 'manager')->first();
    }

    /**
     * Get all incidents from this company.
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }
}
