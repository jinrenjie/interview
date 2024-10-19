<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * User Controller
 * Date: 19/10/2024
 *
 * @author George
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Query user list
     * Date: 18/10/2024
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
        
        $query = User::query();
        
        if ($request->get('search')) {
            $query->where('email', 'like', '%' . $request->get('search') . '%');
        }
        
        $users = $query
            ->with('roles')
            ->paginate($request->get('limit', 10));
        
        $collection = $users->getCollection()->map(function ($user) {
            /**
             * @var User $user
             */
            $user->roles = $user->roles->pluck('name')->toArray();
            $user->unsetRelation('roles');
            return $user;
        });
        
        $users->setCollection($collection);
        
        return Inertia::render('User/Index', [
            'title' => 'Users list',
            'users' => $users,
            'roles' => Role::all()
        ]);
    }
    
    /**
     * Update user info
     * Date: 19/10/2024
     *
     * @param  Request  $request
     * @param  User  $user
     * @author George
     * @return RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        if (!$request->user()->hasRole(['admin'])) {
            abort(403);
        }
        
        $attributes = $request->validate([
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],
            'roles' => 'present|array'
        ]);
        
        $user->update($request->only('name', 'email'));
        $user->syncRoles($attributes['roles']);
        
        return to_route('users.index', [], HttpResponse::HTTP_SEE_OTHER);
    }
}
