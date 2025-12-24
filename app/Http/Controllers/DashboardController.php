<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Gather statistics
        $stats = [
            'total_po' => PurchaseOrder::count(),
            'pending_po' => PurchaseOrder::where('status', 'pending')->count(),
            'approved_po' => PurchaseOrder::where('status', 'approved')->count(),
            'rejected_po' => PurchaseOrder::where('status', 'rejected')->count(),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_vendors' => Vendor::count(),
        ];

        // Get recent POs
        $recentPOs = PurchaseOrder::with('vendor')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get pending POs for quick approval widget
        $pendingPOs = PurchaseOrder::with('vendor')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recentPOs', 'pendingPOs'));
    }
}
