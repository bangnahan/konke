<?php

use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\AttendanceFormController;
use App\Http\Controllers\CommitteeCheckinController;
use App\Http\Controllers\EventReportAccessController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage redirect
Route::get('/', function () {
    // If logged in as superadmin, go to dashboard
    if (auth()->check() && auth()->user()->isSuperAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    // If an active event exists, show the latest active event form
    $latestEvent = Event::where('is_active', true)->latest()->first();
    if ($latestEvent) {
        return redirect()->route('attendance.form', ['slug' => $latestEvent->slug]);
    }

    abort(404);
})->name('home');

/*
|--------------------------------------------------------------------------
| Public Participant Attendance Routes
|--------------------------------------------------------------------------
*/
Route::prefix('e/{slug}')->group(function () {
    Route::get('/', [AttendanceFormController::class, 'show'])->name('attendance.form');
    Route::post('/', [AttendanceFormController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('attendance.store');
    Route::get('/success/{uuid}', [AttendanceFormController::class, 'success'])->name('attendance.success');
    Route::get('/qrcode/{uuid}/download', [AttendanceFormController::class, 'downloadQr'])->name('attendance.qr.download');

    /*
    |--------------------------------------------------------------------------
    | Dedicated Committee QR Check-in Panel
    |--------------------------------------------------------------------------
    */
    Route::prefix('checkin')->name('event.checkin.')->group(function () {
        Route::get('/login', [CommitteeCheckinController::class, 'showLogin'])->name('login');
        Route::post('/auth', [CommitteeCheckinController::class, 'authenticate'])
            ->middleware('throttle:10,1')
            ->name('auth');
        Route::post('/logout', [CommitteeCheckinController::class, 'logout'])->name('logout');

        Route::middleware('event.passcode')->group(function () {
            Route::get('/', [CommitteeCheckinController::class, 'panel'])->name('panel');
            Route::post('/scan', [CommitteeCheckinController::class, 'scan'])->name('scan');
            Route::post('/toggle/{id}', [CommitteeCheckinController::class, 'toggleManual'])->name('toggle');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Password-Protected Event Report (PIC / Panitia)
    |--------------------------------------------------------------------------
    */
    Route::prefix('report')->name('event.report.')->group(function () {
        Route::get('/login', [EventReportAccessController::class, 'showLogin'])->name('login');
        Route::post('/auth', [EventReportAccessController::class, 'authenticate'])
            ->middleware('throttle:10,1')
            ->name('auth');

        Route::middleware('event.passcode')->group(function () {
            Route::get('/', [EventReportAccessController::class, 'index'])->name('index');
            Route::get('/export', [EventReportAccessController::class, 'export'])->name('export');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Superadmin Authentication Routes (Obfuscated Path)
|--------------------------------------------------------------------------
*/
Route::prefix('adminganteng')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:5,1') // Stricter brute-force protection
        ->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    /*
    |--------------------------------------------------------------------------
    | Protected Superadmin Panel Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('superadmin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/links', [AdminDashboardController::class, 'links'])->name('admin.links');

        // Events CRUD
        Route::resource('events', AdminEventController::class, ['as' => 'admin']);

        // Event Attendances Management & Export
        Route::prefix('events/{event}/attendances')->name('admin.events.attendances.')->group(function () {
            Route::get('/', [AdminAttendanceController::class, 'index'])->name('index');
            Route::post('/{attendance}/toggle', [AdminAttendanceController::class, 'toggleCheckin'])->name('toggle');
            Route::get('/export', [AdminAttendanceController::class, 'export'])->name('export');
            Route::delete('/{attendance}', [AdminAttendanceController::class, 'destroy'])->name('destroy');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Stealth 404 Disguise for Default Admin Probes
|--------------------------------------------------------------------------
*/
Route::any('admin{any?}', function () {
    abort(404);
})->where('any', '.*');
