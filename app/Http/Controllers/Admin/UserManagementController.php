<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DataProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $users = User::with('dataProvider')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,statistician,provider',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->role === 'provider') {
            DataProvider::create([
                'user_id' => $user->id,
                'organization_name' => $request->organization_name ?? $request->name,
                'is_verified' => $request->has('verify_provider'),
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,statistician,provider',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Veri sağlayıcı kaydını güncelle
        if ($request->role === 'provider') {
            $provider = $user->dataProvider ?? new DataProvider(['user_id' => $user->id]);
            $provider->organization_name = $request->organization_name ?? $user->name;
            $provider->is_verified = $request->has('verify_provider');
            $provider->save();
        } elseif ($user->dataProvider) {
            $user->dataProvider->delete();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return redirect()->back()
                ->with('error', 'Son admin kullanıcısını silemezsiniz.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
    }

    public function verifyProvider(DataProvider $provider)
    {
        $provider->update(['is_verified' => true]);
        return redirect()->back()
            ->with('success', 'Veri sağlayıcı başarıyla doğrulandı.');
    }
}
