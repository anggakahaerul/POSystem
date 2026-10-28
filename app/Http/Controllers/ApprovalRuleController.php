<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRule;
use App\Models\Department;
use Illuminate\Http\Request;

class ApprovalRuleController extends Controller
{
    /**
     * Display a listing of approval rules.
     */
    public function index(Request $request)
    {
        $departmentId = $request->get('department_id');

        $query = ApprovalRule::with('department')->orderByRaw('department_id IS NULL DESC')->orderBy('department_id')->orderBy('min_amount');

        if ($departmentId !== null && $departmentId !== '') {
            if ($departmentId === 'global') {
                $query->whereNull('department_id');
            } else {
                $query->where('department_id', $departmentId);
            }
        }

        $rules = $query->get();
        $departments = Department::active()->orderBy('name')->get();

        return view('approval-rules.index', compact('rules', 'departments', 'departmentId'));
    }

    /**
     * Store a newly created approval rule.
     */
    public function store(Request $request)
    {
        // Clean currency input
        $request->merge([
            'min_amount' => str_replace(['.', ','], '', $request->min_amount ?? '0'),
            'max_amount' => $request->filled('max_amount') ? str_replace(['.', ','], '', $request->max_amount) : null,
        ]);

        $validated = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:100',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|gt:min_amount',
            'approver_for_staff' => 'required|string|in:L3,L4,L5,DIRECTOR,NONE',
            'approver_for_l3' => 'required|string|in:L4,L5,DIRECTOR,NONE',
            'approver_for_l4' => 'nullable|string|in:L5,DIRECTOR,NONE',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama aturan wajib diisi.',
            'min_amount.required' => 'Nominal minimum wajib diisi.',
            'max_amount.gt' => 'Nominal maksimum harus lebih besar dari nominal minimum.',
            'approver_for_staff.required' => 'Target approver untuk Staff / SPV wajib dipilih.',
            'approver_for_l3.required' => 'Target approver untuk Manager L3 wajib dipilih.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['department_id'] = $request->filled('department_id') ? $validated['department_id'] : null;
        $validated['approver_for_l4'] = $request->input('approver_for_l4', 'L5');
        $validated['approver_role'] = ApprovalRule::formatLevelLabel($validated['approver_for_staff']);

        ApprovalRule::create($validated);

        return redirect()->route('approval-rules.index')
            ->with('success', 'Aturan approval berhasil ditambahkan.');
    }

    /**
     * Get single rule for AJAX edit modal.
     */
    public function show(ApprovalRule $approvalRule)
    {
        return response()->json([
            'success' => true,
            'data' => $approvalRule,
        ]);
    }

    /**
     * Update the specified approval rule.
     */
    public function update(Request $request, ApprovalRule $approvalRule)
    {
        // Clean currency input
        $request->merge([
            'min_amount' => str_replace(['.', ','], '', $request->min_amount ?? '0'),
            'max_amount' => $request->filled('max_amount') ? str_replace(['.', ','], '', $request->max_amount) : null,
        ]);

        $validated = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:100',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|gt:min_amount',
            'approver_for_staff' => 'required|string|in:L3,L4,L5,DIRECTOR,NONE',
            'approver_for_l3' => 'required|string|in:L4,L5,DIRECTOR,NONE',
            'approver_for_l4' => 'nullable|string|in:L5,DIRECTOR,NONE',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama aturan wajib diisi.',
            'min_amount.required' => 'Nominal minimum wajib diisi.',
            'max_amount.gt' => 'Nominal maksimum harus lebih besar dari nominal minimum.',
            'approver_for_staff.required' => 'Target approver untuk Staff / SPV wajib dipilih.',
            'approver_for_l3.required' => 'Target approver untuk Manager L3 wajib dipilih.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['department_id'] = $request->filled('department_id') ? $validated['department_id'] : null;
        $validated['approver_for_l4'] = $request->input('approver_for_l4', $approvalRule->approver_for_l4 ?: 'L5');
        $validated['approver_role'] = ApprovalRule::formatLevelLabel($validated['approver_for_staff']);

        $approvalRule->update($validated);

        return redirect()->route('approval-rules.index')
            ->with('success', 'Aturan approval berhasil diperbarui.');
    }

    /**
     * Toggle active status.
     */
    public function toggle(ApprovalRule $approvalRule)
    {
        $approvalRule->update(['is_active' => !$approvalRule->is_active]);

        return back()->with('success', 'Status aturan approval berhasil diubah.');
    }

    /**
     * Remove the specified approval rule.
     */
    public function destroy(ApprovalRule $approvalRule)
    {
        $approvalRule->delete();

        return redirect()->route('approval-rules.index')
            ->with('success', 'Aturan approval berhasil dihapus.');
    }
}
