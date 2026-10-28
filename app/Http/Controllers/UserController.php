<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Display a listing of users with pagination.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'role', 'department_id', 'branch_id']);
        $users = $this->userService->getPaginatedList($filters, 10);
        $roles = Role::orderBy('name')->get();
        $departments = \App\Models\Department::active()->orderBy('name')->get();
        $branches = \App\Models\Branch::active()->orderBy('name')->get();

        return view('users.index', [
            'users' => $users,
            'roles' => $roles,
            'departments' => $departments,
            'branches' => $branches,
            'moduleName' => 'User',
            'moduleRoute' => 'users',
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(UserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan.',
                'data' => $user,
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display user data for edit modal.
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['roles', 'department', 'branch']);
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'department_id' => $user->department_id,
                'branch_id' => $user->branch_id,
                'role' => $user->roles->first()?->name ?? '',
            ],
        ]);
    }

    /**
     * Show form for editing (supports AJAX modal).
     */
    public function edit(User $user, Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->show($user);
        }

        $roles = Role::all();

        return view('users.form', [
            'user' => $user,
            'roles' => $roles,
            'moduleName' => 'User',
            'moduleRoute' => 'users',
        ]);
    }

    /**
     * Update the user.
     */
    public function update(UserRequest $request, User $user)
    {
        $updated = $this->userService->updateUser($user->id, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui.',
                'data' => $updated,
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the user.
     */
    public function destroy(User $user, Request $request)
    {
        $result = $this->userService->deleteUser($user->id, auth()->id());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return redirect()->route('users.index')->with('error', $result['message']);
        }

        return redirect()->route('users.index')->with('success', $result['message']);
    }
}
