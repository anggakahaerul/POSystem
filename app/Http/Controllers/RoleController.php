<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Get all system permissions structured by module with natural action ordering.
     */
    public static function getStructuredPermissions(): array
    {
        $moduleDefs = PermissionController::getModuleDefinitions();
        $rawPermissions = Permission::all();

        // Natural action ordering weight
        $actionOrder = [
            'view' => 10,
            'create' => 20,
            'edit' => 30,
            'delete' => 40,
            'submit' => 50,
            'approve' => 60,
            'reject' => 70,
            'review' => 80,
            'receive' => 90,
            'print' => 100,
        ];

        $structured = [];

        // Initialize all 15 system modules in defined order
        foreach ($moduleDefs as $modKey => $def) {
            $structured[$modKey] = [
                'key' => $modKey,
                'name' => $def['name'],
                'color' => $def['color'] ?? 'secondary',
                'permissions' => [],
            ];
        }

        $structured['other'] = [
            'key' => 'other',
            'name' => 'Lainnya / Sistem Khusus',
            'color' => 'secondary',
            'permissions' => [],
        ];

        foreach ($rawPermissions as $perm) {
            $meta = PermissionController::parsePermission($perm->name);
            $modKey = $meta['module_key'];
            if (!isset($structured[$modKey])) {
                $modKey = 'other';
            }

            $orderWeight = $actionOrder[$meta['action_key']] ?? 150;

            $structured[$modKey]['permissions'][] = [
                'id' => $perm->id,
                'name' => $perm->name,
                'action_key' => $meta['action_key'],
                'action_label' => $meta['action_label'],
                'action_desc' => $meta['action_desc'],
                'action_type' => $meta['action_type'],
                'order' => $orderWeight,
            ];
        }

        // Sort permissions within each module by natural order
        foreach ($structured as $modKey => &$group) {
            usort($group['permissions'], function ($a, $b) {
                return $a['order'] <=> $b['order'];
            });
        }
        unset($group);

        // Remove empty 'other' if no custom permissions
        if (empty($structured['other']['permissions'])) {
            unset($structured['other']);
        }

        return $structured;
    }

    /**
     * Display a listing of roles with pagination.
     */
    public function index(Request $request)
    {
        $query = Role::withCount('permissions', 'users')->with('permissions');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $roles = $query->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $structuredModules = self::getStructuredPermissions();
        $totalPermissionsCount = Permission::count();

        return view('roles.index', [
            'roles' => $roles,
            'structuredModules' => $structuredModules,
            'totalPermissionsCount' => $totalPermissionsCount,
            'moduleName' => 'Role',
            'moduleRoute' => 'roles',
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah terdaftar.',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $permissionModels = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissionModels);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role berhasil ditambahkan.',
                'data' => $role,
            ]);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Get role data for edit modal.
     */
    public function show(Role $role): JsonResponse
    {
        $role->load('permissions');
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'permission_ids' => $role->permissions->pluck('id')->toArray(),
            ],
        ]);
    }

    /**
     * Show form for editing (supports AJAX modal).
     */
    public function edit(Role $role, Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->show($role);
        }

        $structuredModules = self::getStructuredPermissions();
        $totalPermissionsCount = Permission::count();

        return view('roles.form', [
            'role' => $role,
            'structuredModules' => $structuredModules,
            'totalPermissionsCount' => $totalPermissionsCount,
        ]);
    }

    /**
     * Update the role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah terdaftar.',
        ]);

        $role->update(['name' => $validated['name']]);
        $permissionModels = !empty($validated['permissions'])
            ? Permission::whereIn('id', $validated['permissions'])->get()
            : [];
        $role->syncPermissions($permissionModels);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role berhasil diperbarui.',
                'data' => $role,
            ]);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Remove the role.
     */
    public function destroy(Role $role, Request $request)
    {
        if ($role->name === 'Admin') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role Admin adalah peran sistem utama dan tidak dapat dihapus.'
                ], 422);
            }
            return redirect()->route('roles.index')->with('error', 'Role Admin tidak dapat dihapus.');
        }

        if ($role->users()->count() > 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role ini masih digunakan oleh sejumlah pengguna.'
                ], 422);
            }
            return redirect()->route('roles.index')->with('error', 'Role masih digunakan oleh user.');
        }

        $role->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role berhasil dihapus.'
            ]);
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
