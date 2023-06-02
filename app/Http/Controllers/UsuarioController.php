<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::all();

        return Inertia::render('Usuario/Index', [
            'usuarios' => $usuarios,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'is_admin.value' => 'nullable',
            'status' => 'required',
            'password_reset' => 'required',
        ]);

        $usuario = User::find($id);

        $usuario->name = $request->input('name');
        $usuario->email = $request->input('email');
        $usuario->is_admin = $request->input('is_admin.value');
        $usuario->status = $request->input('status.value');
        $usuario->password_reset = $request->input('password_reset.value');
        if($request->password_reset['value'] == 1) {
            $usuario->password = Hash::make('12345678');
        }
        $usuario->save();


        return redirect()->back()->with('response', $usuario);
    }

    public function destroy($id)
    {
        $usuario = User::find($id);

        $usuario->delete();

        return redirect()->back()->with('response', $usuario);
    }
}
