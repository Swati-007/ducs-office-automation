<?php

namespace App\Providers;

use App\Models\Scholar;
use App\Policies\RolePolicy;
use App\Policies\ScholarProfilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Role::class => RolePolicy::class,
    ];

    protected $policiesNamespace = 'App\\Policies';

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::guessPolicyNamesUsing(function ($class) {
            return $this->policiesNamespace . '\\' . class_basename($class) . 'Policy';
        });

        $this->registerPolicies();

        Gate::define('scholars.leaves.apply', ScholarProfilePolicy::class . '@applyLeaves');
        Gate::define('scholars.coursework.store', ScholarProfilePolicy::class . '@addCoursework');
        Gate::define('scholars.coursework.complete', ScholarProfilePolicy::class . '@markCourseworkCompleted');
        Gate::define('scholars.advisory_meetings.store', ScholarProfilePolicy::class . '@addAdvisoryMeeting');
        Gate::define('scholars.advisory_committee.manage', ScholarProfilePolicy::class . '@manageAdvisoryCommittee');
        Gate::define('scholars.other_documents.store', ScholarProfilePolicy::class . '@addOtherDocuments');
    }
}
