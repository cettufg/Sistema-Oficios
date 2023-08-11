<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OficioController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DestinatarioController;
use App\Http\Controllers\DiretoriaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CronController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::group([
    'middleware' => ['auth', 'isActive'],
    'prefix' => '/',
], function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Route group Ofício
    Route::group([
        'prefix' => '/oficio',
    ], function () {
        Route::get('/', [OficioController::class, 'index'])->name('oficio.index');
        Route::get('/create', [OficioController::class, 'create'])->name('oficio.create');
        Route::post('/create', [OficioController::class, 'store'])->name('oficio.store');

        Route::get('/detail/{id}', [OficioController::class, 'detail'])->name('oficio.detail');
        Route::post('/ciencia/{id}', [OficioController::class, 'ciencia'])->name('oficio.ciencia');

        Route::get('/edit/{id}', [OficioController::class, 'edit'])->name('oficio.edit');
        Route::post('/edit/{id}', [OficioController::class, 'update'])->name('oficio.update');

        Route::delete('/anexo/{id}', [OficioController::class, 'destroyAnexo'])->name('oficio.destroyAnexo');
        Route::get('/email/{id}', [OficioController::class, 'email'])->name('oficio.email');

        Route::delete('/', [OficioController::class, 'destroy'])->name('oficio.destroy');
        Route::delete('/selected', [OficioController::class, 'destroySelected'])->name('oficio.destroySelected');

        Route::get('/pdf/{id}', [OficioController::class, 'generatepdf'])->name('oficio.generatepdf');

        Route::get('/teste', [OficioController::class, 'teste'])->name('oficio.teste');
    });


    //Route group Destinatários
    Route::group([
        'prefix' => '/destinatario',
    ], function () {
        Route::get('/', [DestinatarioController::class, 'index'])->name('destinatario.index');
        Route::post('/', [DestinatarioController::class, 'store'])->name('destinatario.store');
        Route::put('/{id}', [DestinatarioController::class, 'update'])->name('destinatario.update');
        Route::delete('/{id}', [DestinatarioController::class, 'destroy'])->name('destinatario.destroy');
        Route::post('/selected', [DestinatarioController::class, 'destroySelected'])->name('destinatario.destroySelected');
    });

    //Route group Diretoria
    Route::group([
        'prefix' => '/diretoria',
    ], function () {
        Route::get('/', [DiretoriaController::class, 'index'])->name('diretoria.index');
        Route::post('/', [DiretoriaController::class, 'store'])->name('diretoria.store');
        Route::put('/{id}', [DiretoriaController::class, 'update'])->name('diretoria.update');
        Route::delete('/{id}', [DiretoriaController::class, 'destroy'])->name('diretoria.destroy');
        Route::post('/selected', [DiretoriaController::class, 'destroySelected'])->name('diretoria.destroySelected');
    });

    //Route group usuários
    Route::group([
        'prefix' => '/usuarios',
    ], function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('usuario.index');
        Route::put('/{id}', [UsuarioController::class, 'update'])->name('usuario.update');
        Route::delete('/{id}', [UsuarioController::class, 'destroy'])->name('usuario.destroy');
    });
});

//Route group Diretoria
Route::group([
    'prefix' => '/cron',
], function () {
    Route::get('/prazo/{hash}', [CronController::class, 'generateprazo'])->name('cron.generateprazo');
    Route::get('/{hash}', [CronController::class, 'index'])->name('cron.index');
});

require __DIR__.'/auth.php';
