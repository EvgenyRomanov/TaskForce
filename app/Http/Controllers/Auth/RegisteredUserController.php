<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $cities = City::all();
        return view('components.user.registration', ['cities' => $cities, 'role' => Role::EXECUTOR]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'town' => ['required', 'exists:cities,name'],
            'role' => [Rule::in([Role::EXECUTOR])],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $role = Role::query()
            ->where('name', '=', $request->get('role') ?? Role::CUSTOMER)->first();
        $city = City::query()->where('name', '=', $request->get('town'))->first();

        $user = User::query()->create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'role_id' => $role->id,
            'city_id' => $city->id,
            'password' => Hash::make($request->get('password')),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('tasks.index'));
    }
}
