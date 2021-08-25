<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Auth;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Request;

class UserController extends Controller
{
    public function index()
    {
        // check AuthServiceProvider
        Gate::authorize('view', 'users');

        $users = User::all();
        return UserResource::collection($users);
        //return User::all();
        //return User::with('role')->paginate();
    }

    public function show($id)
    {
        Gate::authorize('view', 'users');

        $user = User::find($id);
        return new UserResource($user);
        //return User::find($id);
        //return User::with('role')->find($id);
    }

    //public function store(Request $request)
    public function store(UserCreateRequest $request)
    {
        Gate::authorize('edit', 'users');

        /* $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            //'password' => Hash::make($request->input('password'))
            'password' => Hash::make(1234)
        ]);*/
        $user = User::create(
            $request->only('first_name', 'last_name', 'email', 'role_id')
                + ['password' => Hash::make(1234)]
        );
        return response(new UserResource($user), Response::HTTP_CREATED);
    }

    public function update(UserUpdateRequest $request, $id)
    {
        Gate::authorize('edit', 'users');

        $user = User::find($id);
        /* $user->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            //'password' => Hash::make($request->input('password'))            
        ]);*/

        $user->update($request->only(['first_name', 'last_name', 'email', 'role_id']));

        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }

    public function destroy($id)
    {
        Gate::authorize('edit', 'users');

        User::destroy($id);
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function user()
    {
        $user =  Auth::user();
        return (new UserResource($user))->additional([
            'data' => [
                'permissions' => $user->permissions(),
            ],
        ]);
    }

    public function updateInfo(Request $request)
    {
        $user = Auth::user();
        $user->update($request->only(['first_name', 'last_name', 'email']));
        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $user->update(['password' => Hash::make($request->input('password'))]);
        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }
}
