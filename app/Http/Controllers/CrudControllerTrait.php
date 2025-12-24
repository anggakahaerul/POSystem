<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

/**
 * Base CRUD Controller Trait
 * 
 * Provides common CRUD operations that can be reused across controllers.
 * Extend this trait in your resource controllers for quick CRUD setup.
 */
trait CrudControllerTrait
{
    // Override these properties in your controller
    protected string $model;           // e.g., \App\Models\Category::class
    protected string $moduleName;      // e.g., 'Kategori'
    protected string $moduleRoute;     // e.g., 'categories'
    protected string $viewPath;        // e.g., 'categories'
    protected array $validationRules = [];
    protected array $validationMessages = [];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->datatable($request);
        }

        return view($this->viewPath . '.index', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'title' => 'Daftar ' . $this->moduleName,
            'tableId' => str_replace('.', '-', $this->moduleRoute) . '-table',
            'ajaxUrl' => route($this->moduleRoute . '.index'),
            'createUrl' => route($this->moduleRoute . '.create'),
            'createText' => 'Tambah ' . $this->moduleName,
            'columns' => $this->getColumns(),
            'filters' => $this->getFilters(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->viewPath . '.form', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'action' => route($this->moduleRoute . '.store'),
            'method' => 'POST',
            'data' => null,
            'fields' => $this->getFormFields(),
            'formWidth' => $this->formWidth ?? 8,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $data = $this->beforeStore($validated);
        $record = $this->model::create($data);
        $this->afterStore($record, $request);

        return redirect()->route($this->moduleRoute . '.index')
            ->with('success', $this->moduleName . ' berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = $this->model::findOrFail($id);

        return view($this->viewPath . '.show', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'data' => $data,
            'details' => $this->getShowDetails($data),
            'editUrl' => route($this->moduleRoute . '.edit', $id),
            'deleteUrl' => route($this->moduleRoute . '.destroy', $id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = $this->model::findOrFail($id);

        return view($this->viewPath . '.form', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'action' => route($this->moduleRoute . '.update', $id),
            'method' => 'PUT',
            'data' => $data,
            'fields' => $this->getFormFields($data),
            'formWidth' => $this->formWidth ?? 8,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $record = $this->model::findOrFail($id);

        $validated = $request->validate(
            $this->getValidationRules($id),
            $this->getValidationMessages()
        );

        $data = $this->beforeUpdate($validated, $record);
        $record->update($data);
        $this->afterUpdate($record, $request);

        return redirect()->route($this->moduleRoute . '.index')
            ->with('success', $this->moduleName . ' berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $record = $this->model::findOrFail($id);

        if (!$this->canDelete($record)) {
            return redirect()->route($this->moduleRoute . '.index')
                ->with('error', $this->moduleName . ' tidak dapat dihapus.');
        }

        $this->beforeDelete($record);
        $record->delete();
        $this->afterDelete($record);

        return redirect()->route($this->moduleRoute . '.index')
            ->with('success', $this->moduleName . ' berhasil dihapus.');
    }

    // ===== Override these methods in your controller =====

    /**
     * Get DataTables columns configuration
     */
    protected function getColumns(): array
    {
        return [
            ['data' => 'id', 'title' => 'ID', 'width' => '50px'],
            ['data' => 'name', 'title' => 'Nama'],
            ['data' => 'created_at', 'title' => 'Dibuat', 'width' => '150px'],
            ['data' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false, 'width' => '120px'],
        ];
    }

    /**
     * Get filter fields for DataTable
     */
    protected function getFilters(): array
    {
        return [];
    }

    /**
     * Get form fields configuration
     */
    protected function getFormFields($data = null): array
    {
        return [
            ['name' => 'name', 'label' => 'Nama', 'type' => 'text', 'required' => true],
        ];
    }

    /**
     * Get show details configuration
     */
    protected function getShowDetails($data): array
    {
        return [
            ['label' => 'ID', 'value' => $data->id],
            ['label' => 'Nama', 'value' => $data->name ?? '-'],
            ['label' => 'Dibuat', 'value' => $data->created_at, 'date' => 'd/m/Y H:i'],
        ];
    }

    /**
     * Get validation rules
     */
    protected function getValidationRules($id = null): array
    {
        return $this->validationRules;
    }

    /**
     * Get validation messages
     */
    protected function getValidationMessages(): array
    {
        return $this->validationMessages;
    }

    /**
     * DataTable query and formatting
     */
    protected function datatable(Request $request)
    {
        $query = $this->datatableQuery($request);

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                return view('components.action-buttons', [
                    'id' => $row->id,
                    'showUrl' => route($this->moduleRoute . '.show', $row->id),
                    'editUrl' => route($this->moduleRoute . '.edit', $row->id),
                    'deleteUrl' => route($this->moduleRoute . '.destroy', $row->id),
                ])->render();
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d/m/Y H:i') : '-';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Get the query for DataTable
     */
    protected function datatableQuery(Request $request)
    {
        return $this->model::query();
    }

    // ===== Hooks for customization =====

    protected function beforeStore(array $data): array
    {
        return $data;
    }

    protected function afterStore($record, Request $request): void
    {
        //
    }

    protected function beforeUpdate(array $data, $record): array
    {
        return $data;
    }

    protected function afterUpdate($record, Request $request): void
    {
        //
    }

    protected function canDelete($record): bool
    {
        return true;
    }

    protected function beforeDelete($record): void
    {
        //
    }

    protected function afterDelete($record): void
    {
        //
    }
}
