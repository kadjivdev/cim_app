<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Rules\AvoirRule;
use Illuminate\Http\Request;

class AvoirController extends Controller
{
    public function create(User $user)
    {
        $roles = Role::orderBy('libelle')->get();
        return view('avoirs.create', compact('user', 'roles'));
    }

    public function store(Request $request, User $user)
    {
        $request->validate([
            'role_id' => ['required', new AvoirRule($user)],
        ]);

        $roles = $request->role_id;
        $tab = $request->all()['role_id'];
        $roles = $user->roles()->whereIn('id', $tab)->get();

        $avoir ='';
        $roles = $request->role_id;


        foreach($roles as $role) {
            $avoir = $user->roles()->attach([$role ]);
        }

        if (!$avoir) {
            Session()->flash('message', 'Rôle(s) associé(s) à l\'utilisateur avec succès!');
            return redirect()->route('avoirs.index', ['user' => $user->id]);
        }
    }



    public function index(User $user)
    {
        return view('avoirs.index', compact('user'));
    }

    public function delete(User $user, Role $role)
    {
        $role = $user->roles()->findOrFail($role->id);

        return view('avoirs.delete', compact('role', 'user'));
    }

    public function destroy(User $user, Role $role)
    {
        $role = $user->roles()->detach([$role->id]);
        if ($role) {
            Session()->flash('message', "Rôle de l'utilisateur enlever avec succès!");
            return redirect()->route('avoirs.index', ['user' => $user->id]);
        }
    }
}
