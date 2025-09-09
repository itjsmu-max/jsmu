<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, DashboardController, ContractController,
    ProjectController, EmployeeController, ReportsController
};

/* PUBLIC */
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'doLogin'])->name('doLogin');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

/* PROTECTED */
Route::middleware('auth.guard')->group(function () {

    /* Beranda */
    Route::get('/', [DashboardController::class,'index'])->name('home');
    Route::get('/beranda', fn() => redirect()->route('home'))->name('beranda');
    Route::get('/map/projects', [DashboardController::class, 'mapProjects'])->name('map.projects');

    /* Master data hub */
    Route::view('/master-data', 'pages.master-data')->name('master.data');

    /* Projects */
    Route::get('/projects',             [ProjectController::class,'index'])->name('projects.index');
    Route::get('/projects/create',      [ProjectController::class,'create'])->name('projects.create');
    Route::post('/projects',            [ProjectController::class,'store'])->name('projects.store');
    Route::get('/projects/{id}/edit',   [ProjectController::class,'edit'])->name('projects.edit');
    Route::put('/projects/{id}',        [ProjectController::class,'update'])->name('projects.update');
    Route::delete('/projects/{id}',     [ProjectController::class,'destroy'])->name('projects.destroy');

    /* Employees */
    Route::resource('employees', EmployeeController::class)->except(['show']);

    /* ================= Generate PKWT (LIST) ================= */
    // HANYA INI untuk /generate-pkwt
    Route::get('/generate-pkwt', [ContractController::class, 'listForGenerate'])
        ->name('contracts.generate.index');

    // Aksi dari list
    Route::post('/generate-pkwt/generate-one',  [ContractController::class,'generateForEmployee'])->name('contracts.generate.one');
    Route::post('/generate-pkwt/generate-bulk', [ContractController::class,'generateBulk'])->name('contracts.generate.bulk');

    /* ================= Kontrak (Preview, dsb) ================= */
    Route::get('/contracts',          [ContractController::class,'index'])->name('contracts.index');
    Route::get('/contracts/create',   [ContractController::class,'create'])->name('contracts.create');
    Route::post('/contracts',         [ContractController::class,'store'])->name('contracts.store');

    Route::get('/contracts/{id}/preview',     [ContractController::class,'preview'])->name('contracts.preview');
    Route::get('/contracts/{id}/preview.pdf', [ContractController::class,'previewPdf'])->name('contracts.preview.pdf');
    Route::post('/contracts/{id}/generate',   [ContractController::class,'generatePkwt'])->name('contracts.generate');

    Route::get('/contracts/{id}/docx', [ContractController::class,'downloadDocx'])->name('contracts.docx');

    Route::get('/contracts/{id}/sign',           [ContractController::class,'signPage'])->name('contracts.sign.page');
    Route::post('/contracts/{id}/sign/employee', [ContractController::class,'signEmployee'])->name('contracts.sign.employee');

    Route::get('/contracts/{id}', [ContractController::class,'show'])->name('contracts.show');

    /* Monitoring & Laporan */
    Route::view('/monitoring-kontrak', 'pages.monitoring-kontrak')->name('monitoring');
    Route::get('/laporan',        [ReportsController::class,'contracts'])->name('reports.contracts');
    Route::get('/laporan/export', [ReportsController::class,'exportCsv'])->name('reports.contracts.export');
});
