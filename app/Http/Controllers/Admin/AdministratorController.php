<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdministratorController extends Controller
{
    public function index() { return view('admin.users.index', ['users' => User::where('is_admin', true)->orderBy('name')->get()]); }
    public function create() { return view('admin.users.form', ['item' => new User]); }
    public function edit(User $user) { abort_unless($user->is_admin, 404); return view('admin.users.form', ['item' => $user]); }
    public function store(Request $request) { return $this->save($request, new User); }
    public function update(Request $request, User $user) { abort_unless($user->is_admin, 404); return $this->save($request, $user); }

    private function save(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => [$user->exists ? 'nullable' : 'required', 'confirmed', Password::min(12)],
        ]);
        if (empty($data['password'])) unset($data['password']);
        else $data['remember_token'] = Str::random(60);
        $user->forceFill([...$data, 'is_admin' => true])->save();
        return redirect()->route('users.index')->with('status', 'Administrator saved.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($user->is_admin, 404);
        abort_if($user->is($request->user()), 422, 'Use another administrator to remove this account.');
        DB::transaction(function () use ($user) {
            $admins = User::where('is_admin', true)->lockForUpdate()->get();
            abort_if($admins->count() <= 1, 422, 'The last administrator cannot be removed.');
            $user->delete();
        });
        return redirect()->route('users.index')->with('status', 'Administrator removed.');
    }
}
