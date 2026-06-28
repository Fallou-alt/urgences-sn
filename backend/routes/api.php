<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VictimeController;
use App\Http\Controllers\StructureController;
use App\Http\Middleware\AuthToken;
use Illuminate\Support\Facades\Route;

Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(AuthToken::class);
Route::patch('/profil/password',  [AuthController::class, 'changePassword'])->middleware(AuthToken::class);
Route::patch('/profil/identite',  [AuthController::class, 'changeProfil'])->middleware(AuthToken::class);

Route::post('/incidents',          [IncidentController::class, 'store']);
Route::get('/stats',               [IncidentController::class, 'stats']);
Route::get('/incidents/{id}/suivi',[IncidentController::class, 'suivi']);

Route::middleware([AuthToken::class . ':pompier'])->group(function () {
    Route::get('/incidents',                    [IncidentController::class, 'index']);
    Route::patch('/incidents/{id}/statut',      [IncidentController::class, 'updateStatut']);
    Route::get('/incidents/{id}/victimes',      [VictimeController::class, 'index']);
    Route::post('/incidents/{id}/victimes',     [VictimeController::class, 'store']);
    Route::delete('/victimes/{id}',             [VictimeController::class, 'destroy']);
});

Route::middleware([AuthToken::class . ':samu'])->group(function () {
    Route::get('/incidents/medical',            [IncidentController::class, 'medical']);
    Route::get('/incidents/{id}/victimes',      [VictimeController::class, 'index']);
    Route::post('/incidents/{id}/victimes',     [VictimeController::class, 'store']);
    Route::delete('/victimes/{id}',             [VictimeController::class, 'destroy']);
});

Route::middleware([AuthToken::class . ':admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard',                    [AdminController::class, 'dashboard']);
    Route::get('/agents',                       [AdminController::class, 'agents']);
    Route::post('/agents',                      [AdminController::class, 'storeAgent']);
    Route::patch('/agents/{id}/toggle',         [AdminController::class, 'toggleAgent']);
    Route::get('/incidents',                    [AdminController::class, 'incidents']);
    Route::delete('/incidents/{id}',            [AdminController::class, 'deleteIncident']);
    // Structures de secours
    Route::get('/structures',                   [StructureController::class, 'index']);
    Route::post('/structures',                  [StructureController::class, 'store']);
    Route::put('/structures/{id}',              [StructureController::class, 'update']);
    Route::patch('/structures/{id}/toggle',     [StructureController::class, 'toggle']);
    // Bilan & export
    Route::get('/bilan',                        [StructureController::class, 'bilan']);
    Route::get('/export-csv',                   [StructureController::class, 'exportCsv']);
});
