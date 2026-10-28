<?php

namespace App\Http\Controllers;

use App\Services\MasterExportImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MasterExportImportController extends Controller
{
    public function __construct(
        protected MasterExportImportService $service
    ) {}

    /**
     * Download Excel template for master data import
     */
    public function template(string $type): StreamedResponse
    {
        return $this->service->downloadTemplate($type);
    }

    /**
     * Export all master data to Excel
     */
    public function export(string $type): StreamedResponse
    {
        return $this->service->exportData($type);
    }

    /**
     * Import master data from uploaded Excel/CSV file
     */
    public function import(Request $request, string $type): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ], [
            'file.required' => 'Pilih file Excel atau CSV yang akan diunggah.',
            'file.mimes' => 'Format file harus berupa Excel (.xlsx, .xls) atau CSV (.csv).',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        $updateExisting = $request->boolean('update_existing', true);
        $result = $this->service->importData($type, $request->file('file'), $updateExisting);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
