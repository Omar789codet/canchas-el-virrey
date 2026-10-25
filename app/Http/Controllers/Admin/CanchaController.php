<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cancha;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CanchaController extends Controller
{
    public function index()
    {
        $canchas = Cancha::orderBy('nombre')->paginate(10);
        return view('admin.canchas.index', compact('canchas'));
    }

    public function create()
    {
        return view('admin.canchas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'tipo' => ['required', 'in:futbol,basquet,voley,tenis,otro'],
            'capacidad' => ['nullable', 'integer', 'min:1'],
            'precio_hora' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:disponible,mantenimiento,inactiva'],
        ]);

        Cancha::create($data);

        return redirect()->route('admin.canchas.index')->with('ok', 'Cancha registrada correctamente.');
    }

    public function edit(Cancha $cancha)
    {
        return view('admin.canchas.edit', compact('cancha'));
    }

    public function update(Request $request, Cancha $cancha)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'tipo' => ['required', 'in:futbol,basquet,voley,tenis,otro'],
            'capacidad' => ['nullable', 'integer', 'min:1'],
            'precio_hora' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:disponible,mantenimiento,inactiva'],
        ]);

        $cancha->update($data);

        return redirect()->route('admin.canchas.index')->with('ok', 'Cancha actualizada correctamente.');
    }

    public function destroy(Cancha $cancha)
    {
        // Si la cancha tiene reservas asociadas, la base de datos rechaza el
        // borrado (restrictOnDelete) para no perder el historial. En vez de
        // mostrar un error de SQL, lo explicamos y sugerimos desactivarla.
        if ($cancha->reservas()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar esta cancha porque tiene reservas registradas. '
                . 'Si ya no está en uso, cámbiale el estado a "Inactiva" en vez de eliminarla.'
            );
        }

        try {
            $cancha->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'No se pudo eliminar la cancha: tiene registros asociados.');
        }

        return back()->with('ok', 'Cancha eliminada.');
    }
}
