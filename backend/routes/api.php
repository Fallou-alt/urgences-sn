<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ResponsableController;
use App\Http\Controllers\AgentController;
use App\Http\Middleware\AuthToken;
use Illuminate\Support\Facades\Route;

// ─── Routes publiques (citoyens) ─────────────────────────────────────────────
Route::post('/incidents',              [IncidentController::class, 'store']);
Route::get('/incidents/{id}/suivi',    [IncidentController::class, 'suivi']);
Route::get('/stats',                   [IncidentController::class, 'stats']);

// ─── Authentification ─────────────────────────────────────────────────────────
Route::post('/login',  [AuthController::class, 'login']);

Route::middleware(AuthToken::class)->group(function () {
    Route::post('/logout',            [AuthController::class, 'logout']);
    Route::patch('/profil/password',  [AuthController::class, 'changePassword']);
    Route::patch('/profil/identite',  [AuthController::class, 'changeProfil']);
});

// ─── ADMIN ───────────────────────────────────────────────────────────────────
Route::middleware(AuthToken::class . ':ADMIN')->prefix('admin')->group(function () {
    Route::get('/dashboard',                        [AdminController::class, 'dashboard']);

    // Structures
    Route::get('/structures',                       [AdminController::class, 'structures']);
    Route::post('/structures',                      [AdminController::class, 'storeStructure']);
    Route::put('/structures/{id}',                  [AdminController::class, 'updateStructure']);
    Route::delete('/structures/{id}',               [AdminController::class, 'deleteStructure']);
    Route::patch('/structures/{id}/toggle',         [AdminController::class, 'toggleStructure']);

    // Responsables
    Route::get('/responsables',                     [AdminController::class, 'responsables']);
    Route::post('/responsables',                    [AdminController::class, 'storeResponsable']);
    Route::patch('/users/{id}/toggle',              [AdminController::class, 'toggleUser']);

    // Incidents (lecture seule)
    Route::get('/incidents',                        [AdminController::class, 'incidents']);

    // Stats & export
    Route::get('/stats',                            [AdminController::class, 'stats']);
    Route::get('/export-csv',                       [AdminController::class, 'exportCsv']);
});

// ─── RESPONSABLE ─────────────────────────────────────────────────────────────
Route::middleware(AuthToken::class . ':RESPONSABLE')->prefix('responsable')->group(function () {
    Route::get('/dashboard',                        [ResponsableController::class, 'dashboard']);

    // Ma structure
    Route::get('/structure',                        [ResponsableController::class, 'maStructure']);
    Route::patch('/structure',                      [ResponsableController::class, 'updateMaStructure']);

    // Agents de ma structure
    Route::get('/agents',                           [ResponsableController::class, 'agents']);
    Route::post('/agents',                          [ResponsableController::class, 'storeAgent']);
    Route::patch('/agents/{id}',                    [ResponsableController::class, 'updateAgent']);
    Route::patch('/agents/{id}/toggle',             [ResponsableController::class, 'toggleAgent']);
    Route::delete('/agents/{id}',                   [ResponsableController::class, 'deleteAgent']);

    // Incidents de ma structure
    Route::get('/incidents',                        [ResponsableController::class, 'incidents']);
    Route::patch('/incidents/{id}/affecter',        [ResponsableController::class, 'affecterAgent']);
    Route::patch('/incidents/{id}/annuler',         [ResponsableController::class, 'annulerIncident']);

    // Victimes
    Route::get('/incidents/{id}/victimes',          [ResponsableController::class, 'victimes']);
    Route::post('/incidents/{id}/victimes',         [ResponsableController::class, 'storeVictime']);
    Route::delete('/victimes/{id}',                 [ResponsableController::class, 'deleteVictime']);

    // Rapport
    Route::get('/rapport',                          [ResponsableController::class, 'rapport']);
});

// ─── AGENT ───────────────────────────────────────────────────────────────────
Route::middleware(AuthToken::class . ':AGENT')->prefix('agent')->group(function () {
    Route::get('/dashboard',                        [AgentController::class, 'dashboard']);
    Route::get('/missions',                         [AgentController::class, 'mesMissions']);
    Route::get('/historique',                       [AgentController::class, 'historique']);
    Route::patch('/missions/{id}/statut',           [AgentController::class, 'updateStatut']);
    Route::patch('/missions/{id}/commentaire',      [AgentController::class, 'ajouterCommentaire']);
});
