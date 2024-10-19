<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
    
    /**
     * Date: 19/10/2024
     *
     * @param  Request  $request
     * @author George
     * @return Response
     * @throws Exception
     */
    public function executeSQL(Request $request): Response
    {
        $sql = $request->input('sql');
        
        $result = [];
        $columns = [];
        
        if (stripos($sql, 'select') !== 0) {
            return Inertia::render('Dev/Index', [
                'errors' => ['Only SELECT queries are allowed'],
                'result' => $result,
                'columns' => $columns
            ]);
        }
        
        try {
            $result = DB::select($sql);
        } catch (Exception $e) {
            return Inertia::render('Dev/Index', [
                'errors' => [$e->getMessage()],
                'result' => $result,
                'columns' => $columns
            ]);
        }
        
        if (!empty($result)) {
            $item = reset($result);
            $keys = array_keys((array)$item);
            foreach ($keys as $key) {
                $columns[] = [
                    'prop' => $key,
                    'label' => $key,
                ];
            }
        }
        
        return Inertia::render('Dev/Index', [
            'errors' => [],
            'result' => $result,
            'columns' => $columns
        ]);
    }
}
