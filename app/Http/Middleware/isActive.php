<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class isActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->status == 0) {
            auth()->logout();
            // Redirecione para a página desejada
            return redirect()->route('login')->with('status', 'Usuário inativo. Entre em contato com o administrador do sistema.');
        }
        return $next($request);
    }
}
