<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Feedback;
use App\Models\Ticket;
use App\Policies\ArticlePolicy;
use App\Policies\FeedbackPolicy;
use App\Policies\TicketPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(Article::class, ArticlePolicy::class);
        Gate::policy(Feedback::class, FeedbackPolicy::class);

        // Define gates for role-based permissions
        Gate::define('manage-users', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-agents', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('view-dashboard', function ($user) {
            return $user->isAdmin() || $user->isAgent();
        });

        Gate::define('manage-tickets', function ($user) {
            return $user->isAdmin() || $user->isAgent();
        });

        Gate::define('manage-knowledge-base', function ($user) {
            return $user->isAdmin() || $user->isAgent();
        });
    }
}
