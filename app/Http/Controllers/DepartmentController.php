<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Models\Branch;
use App\Models\Department;
use App\Models\User;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentService $departmentService
    ) {}

    /**
     * Display a listing of departments.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $departments = $this->departmentService->getPaginatedList($filters, 10);
        $users = User::with('branch')->orderBy('name')->get();
        $branches = Branch::active()->orderBy('name')->get();

        return view('departments.index', [
            'departments' => $departments,
            'users' => $users,
            'branches' => $branches,
            'moduleName' => 'Departemen',
            'moduleRoute' => 'departments',
        ]);
    }

    /**
     * Store a newly created department.
     */
    public function store(DepartmentRequest $request)
    {
        $department = $this->departmentService->createDepartment($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Departemen berhasil ditambahkan.',
                'data' => $department,
            ]);
        }

        return redirect()->route('departments.index')
            ->with('success', 'Departemen berhasil ditambahkan.');
    }

    /**
     * Get single department data for modal.
     */
    public function show(Department $department): JsonResponse
    {
        $department->load([
            'managerL3.branch',
            'managerL4.branch',
            'managerL5.branch',
            'departmentBranches.branch',
            'departmentBranches.managerL3',
            'departmentBranches.managerL4',
            'departmentBranches.managerL5',
        ]);

        return response()->json([
            'success' => true,
            'data' => $department,
        ]);
    }

    /**
     * Show form for editing (supports AJAX modal).
     */
    public function edit(Department $department, Request $request)
    {
        return $this->show($department);
    }

    /**
     * Update the department.
     */
    public function update(DepartmentRequest $request, Department $department)
    {
        $updated = $this->departmentService->updateDepartment($department->id, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Departemen berhasil diperbarui.',
                'data' => $updated,
            ]);
        }

        return redirect()->route('departments.index')
            ->with('success', 'Departemen berhasil diperbarui.');
    }

    /**
     * Remove the department.
     */
    public function destroy(Department $department, Request $request)
    {
        $result = $this->departmentService->deleteDepartment($department->id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return redirect()->route('departments.index')->with('error', $result['message']);
        }

        return redirect()->route('departments.index')->with('success', $result['message']);
    }
}
