<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $request->validate(['sql' => 'required']);
        $sql = $request->get('sql');
        
        $errors = [];
        $result = [];
        $columns = [];
        
        if (stripos($sql, 'select') !== 0) {
            $errors[] = 'Only SELECT queries are allowed';
        }
        
        try {
            if (empty($errors)) {
                $result = DB::select($sql);
            }
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
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
        
        Journal::create([
            'sql' => $sql,
            'error' => empty($errors) ? null : reset($errors),
            'user_id' => $request->user()->id,
            'created_at' => Carbon::now()
        ]);
        
        return Inertia::render('Dev/Index', [
            'errors' => $errors,
            'result' => $result,
            'columns' => $columns
        ]);
    }
}
