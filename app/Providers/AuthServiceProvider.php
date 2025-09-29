<?php

namespace App\Providers;

use App\Models\Incident;
use App\Policies\IncidentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Incident::class => IncidentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}


