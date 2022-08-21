<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPassword
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->getPathInfo() != '/livewire/message/manage-password') {
            if ($request->route()->getName() != 'manage_password' && $request->route()->getName() != 'logout') {
                if (auth()->check() && auth()->user()->password == null) {
                    return redirect()->route('manage_password');
                }
            }
        }
        return $next($request);
    }
}
