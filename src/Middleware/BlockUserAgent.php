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
use Illuminate\Support\Str;

class BlockUserAgent
{

    /**
     * User Agent Being Block
     * @var array
     */
    protected $badUserAgents = [];

    public function handle(Request $request, Closure $next)
    {
        $agent = $request->header('User-Agent');
        if ($agent && Str::contains($agent, $this->badUserAgents)) {
            return abort(400);
        }

        return $next($request);
    }
}