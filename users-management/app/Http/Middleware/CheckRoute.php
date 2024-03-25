<?php

namespace App\Http\Middleware;

use Closure;

class CheckRoute
{
    public function handle($request, Closure $next)
    {
        $route = $request->header('referer');
        $data = [[]];
        if (strpos($route, 'admin/users') !== false) {
            $data = [['ID','name', 'phone', 'role' , 'email','email_verified_at']];
        } elseif (strpos($route, 'admin/requests') !== false) {
            $data = [['ID', 'Submit_username', 'Identity', 'First_name', 'Last_name', 'Phone', 'Email', 'Unit', 'Sub', 'Authentication_type', 'Service_type', 'Validity', 'Status', 'Description', 'Created_date', 'Approval_date']];
        }
        
        $request->merge(['custom_data' => $data]);
        
        return $next($request);
    }
}
