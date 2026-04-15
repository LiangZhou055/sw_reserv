<?php

namespace App\Http\Controllers;

use App\Models\StoreUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = StoreUser::orderByDesc('id')->paginate(25);

        return view('admin.users.index', [
            'users' => $users,
            'user' => auth('store')->user(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:super,manager,staff'],
            'store_code' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($data['role'] === 'super') {
            $data['store_code'] = null;
        } elseif (empty($data['store_code'])) {
            return back()->withErrors(['store_code' => 'store_code is required for non-super user'])->withInput();
        }

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = (int)($data['is_active'] ?? 1);

        StoreUser::create($data);

        return back()->with('status', 'User created');
    }

    public function resetPassword(Request $request, int $id)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);

        $target = StoreUser::findOrFail($id);
        $target->password = Hash::make($data['password']);
        $target->save();

        return back()->with('status', 'Password updated');
    }

    public function toggleActive(int $id)
    {
        $target = StoreUser::findOrFail($id);
        $target->is_active = $target->is_active ? 0 : 1;
        $target->save();

        return back()->with('status', 'User status updated');
    }
}

