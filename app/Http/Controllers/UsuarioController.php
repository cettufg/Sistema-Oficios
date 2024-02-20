<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::active()->get();

        return Inertia::render('Usuario/Index', [
            'usuarios' => $usuarios,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'is_admin.value' => 'nullable',
            'status' => 'required',
            'password_reset' => 'required',
        ]);

        $usuario = User::findOrFail($id);

        $usuario->name = $request->input('name');
        $usuario->email = $request->input('email');
        $usuario->is_admin = $request->input('is_admin.value');
        $usuario->status = $request->input('status.value');
        $usuario->password_reset = $request->input('password_reset.value');
        if ($request->password_reset['value'] == 1) {
            $usuario->password = Hash::make('12345678');
        }
        $usuario->save();


        return redirect()->back();
    }

    public function destroy(int $id)
    {
        $usuario = User::findOrFail($id);

        $usuario->status = Status::TRASH;
        $usuario->save();

        return redirect()->back();
    }
}
