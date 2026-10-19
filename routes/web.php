<?php 
use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\Admin\CanchaController; 
use App\Http\Controllers\Admin\PagoController; 
use App\Http\Controllers\Admin\ReservaController; 
use Illuminate\Support\Facades\Route; 
Route::get('/', function () { return view('welcome'); }); 
Route::get('/dashboard', function () { return view('dashboard'); })->middleware(['auth', 'verified'])->name('dashboard'); 
Route::middleware('auth')->group(function () 
{ Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); 
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); 
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); }); require __DIR__.'/auth.php'; 
Route::middleware(['auth', 'es.staff'])->prefix('admin')->name('admin.')->group(function () 
{ Route::resource('canchas', CanchaController::class)->except(['show']); 
Route::get('reservas', [ReservaController::class, 'index'])->name('reservas.index'); 
Route::get('reservas/crear', [ReservaController::class, 'create'])->name('reservas.create'); 
Route::post('reservas', [ReservaController::class, 'store'])->name('reservas.store'); 
Route::delete('reservas/{reserva}', [ReservaController::class, 'destroy'])->name('reservas.destroy'); 
Route::get('pagos', [PagoController::class, 'index'])->name('pagos.index');
 Route::post('pagos/{pago}/marcar-pagado', [PagoController::class, 'marcarPagado'])->name('pagos.marcarPagado'); });