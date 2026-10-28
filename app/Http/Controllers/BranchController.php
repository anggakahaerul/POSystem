<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct(
        protected BranchService $branchService
    ) {}

    /**
     * Display a listing of branches.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $branches = $this->branchService->getPaginatedList($filters, 10);

        return view('branches.index', [
            'branches' => $branches,
            'moduleName' => 'Cabang',
            'moduleRoute' => 'branches',
        ]);
    }

    /**
     * Store a newly created branch.
     */
    public function store(BranchRequest $request)
    {
        $branch = $this->branchService->createBranch($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cabang berhasil ditambahkan.',
                'data' => $branch,
            ]);
        }

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil ditambahkan.');
    }

    /**
     * Display branch data for modal.
     */
    public function show(Branch $branch): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $branch,
        ]);
    }

    /**
     * Show form for editing.
     */
    public function edit(Branch $branch, Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $branch,
            ]);
        }

        return view('branches.form', compact('branch'));
    }

    /**
     * Update the branch.
     */
    public function update(BranchRequest $request, Branch $branch)
    {
        $updated = $this->branchService->updateBranch($branch->id, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cabang berhasil diperbarui.',
                'data' => $updated,
            ]);
        }

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    /**
     * Remove the branch.
     */
    public function destroy(Branch $branch, Request $request)
    {
        $result = $this->branchService->deleteBranch($branch->id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return redirect()->route('branches.index')->with('error', $result['message']);
        }

        return redirect()->route('branches.index')->with('success', $result['message']);
    }
}
