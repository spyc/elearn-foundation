<?php
/**
 * HHVM
 *
 * Copyright (C) Tony Yip 2015.
 *
 * @author   Tony Yip <tony@opensource.hk>
 * @license  http://opensource.org/licenses/GPL-3.0 GNU General Public License
 */

namespace Elearn\Foundation\Middleware;

use Closure;
use Illuminate\Http\Request;

class FrameworkHeader
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Framework', 'Laravel 5.1');

        $response->headers->set('X-Power-By', 'HHVM');

        $response->headers->set('X-Github-Source', 'github.com/spyc/spyc-elearn');

        return $response;
    }
}