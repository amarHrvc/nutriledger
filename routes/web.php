<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');

    Route::get('/users/create', function () {
        return view('admin.users.create');
    })->name('users.create');

    // Route model binding automatically loads User by {user} parameter
    Route::get('/users/{user}/edit', function (App\Models\User $user) {
        return view('admin.users.edit', ['user' => $user]);
    })->name('users.edit');
});


// Patient routes - List and Create restricted by role
Route::middleware(['auth'])->group(function () {
    Route::get('/patients', \App\Livewire\Patient\PatientList::class)
        ->can('viewAny', \App\Models\Patient::class)
        ->name('patients.index');

    Route::get('/patients/create', \App\Livewire\Patient\CreatePatient::class)
        ->can('create', \App\Models\Patient::class)
        ->name('patients.create');

    Route::get('/patients/{patient}', \App\Livewire\Patient\ViewPatient::class)
        ->name('patients.show');

    Route::get('/patients/{patient}/edit', \App\Livewire\Patient\EditPatient::class)
        ->name('patients.edit');
});

require __DIR__.'/auth.php';
