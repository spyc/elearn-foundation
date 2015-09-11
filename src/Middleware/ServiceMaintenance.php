<?php
/**
 * elearn-foundation
 *
 * PHP version 5
 *
 * Copyright (C) Tony Yip 2015.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Guardian
 * @author   Tony Yip <tony@opensource.hk>
 * @license  http://opensource.org/licenses/GPL-3.0 GNU General Public License
 */

namespace Elearn\Foundation\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ServiceMaintenance
{

    /**
     * Page in maintenance
     *
     * @var array
     */
    protected $services = [];

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return \Illuminate\Http\Response
     * @throws HttpException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->isInMaintain($request)) {
            throw new HttpException(503);
        }
        return $next($request);
    }

    /**
     * Determine the request has a URI that is in maintenance.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function isInMaintain(Request $request)
    {
        foreach ($this->services as $service) {
            if ($request->is($service)) {
                return true;
            }
        }

        return false;
    }
}