<?php

namespace App\Providers;

use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Student;
use App\Models\User;
use App\Observers\AcademicYearObserver;
use App\Observers\BillObserver;
use App\Observers\ParentProfileObserver;
use App\Observers\PaymentObserver;
use App\Observers\PaymentTypeObserver;
use App\Observers\StudentObserver;
use App\Observers\UserObserver;
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
        User::observe(UserObserver::class);
        Student::observe(StudentObserver::class);
        ParentProfile::observe(ParentProfileObserver::class);
        Bill::observe(BillObserver::class);
        Payment::observe(PaymentObserver::class);
        AcademicYear::observe(AcademicYearObserver::class);
        PaymentType::observe(PaymentTypeObserver::class);
    }
}
