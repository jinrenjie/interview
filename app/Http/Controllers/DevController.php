<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

/**
 * Date: 19/10/2024
 *
 * @author George
 * @package App\Http\Controllers
 */
class DevController extends Controller
{
    /**
     * Date: 19/10/2024
     *
     * @param  Request  $request
     * @author George
     * @return Response
     */
    public function index(Request $request): Response
    {
        if (!$request->user()->hasRole(['admin'])) {
            abort(403);
        }
        
        return Inertia::render('Dev/Index', [
            'filters' => $request->all('search', 'trashed'),
        ]);
    }
}
