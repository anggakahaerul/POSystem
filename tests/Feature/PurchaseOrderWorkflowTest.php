<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Tests\TestCase;

class PurchaseOrderWorkflowTest extends TestCase
{
    public function test_create_po_page_renders_with_enhanced_modal(): void
    {
        $admin = User::role('Admin')->first();

        $response = $this->actingAs($admin)->get('/purchase-orders/create');

        $response->assertStatus(200);
        $response->assertSee('id="productModal"', false);
        $response->assertSee('catalog-column', false);
        $response->assertSee('selected-column', false);
        $response->assertSee('id="modal-search"', false);
        $response->assertSee('id="btn-clear-search"', false);
        $response->assertSee('id="selected-items-list"', false);
        $response->assertSee('id="footer-selected-count"', false);
        $response->assertSee('id="btn-add-selected"', false);
    }

    public function test_po_creation_succeeds_with_selected_products(): void
    {
        $admin = User::role('Admin')->first();
        $vendor = Vendor::first();
        $branch = Branch::first();
        $products = Product::limit(2)->get();

        $response = $this->actingAs($admin)->post('/purchase-orders', [
            'vendor_id' => $vendor->id,
            'branch_id' => $branch->id,
            'required_date' => date('Y-m-d', strtotime('+3 days')),
            'note' => 'Test PO from redesigned modal',
            'discount_type' => 'percentage',
            'discount_value' => '5',
            'ppn_percentage' => '11',
            'products' => [
                [
                    'product_id' => $products[0]->id,
                    'quantity' => 3,
                    'unit_price' => number_format($products[0]->price, 0, ',', '.'),
                    'discount_type' => '',
                    'discount_value' => '0',
                ],
                [
                    'product_id' => $products[1]->id,
                    'quantity' => 2,
                    'unit_price' => number_format($products[1]->price, 0, ',', '.'),
                    'discount_type' => 'fixed',
                    'discount_value' => '10.000',
                ]
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'vendor_id' => $vendor->id,
            'branch_id' => $branch->id,
            'status' => 'draft',
        ]);
    }

    public function test_po_creation_fails_without_products(): void
    {
        $admin = User::role('Admin')->first();
        $vendor = Vendor::first();
        $branch = Branch::first();

        $response = $this->actingAs($admin)->post('/purchase-orders', [
            'vendor_id' => $vendor->id,
            'branch_id' => $branch->id,
            'products' => [],
        ]);

        $response->assertSessionHasErrors('products');
    }

    public function test_po_index_renders_with_aligned_status_badges(): void
    {
        $admin = User::role('Admin')->first();

        $response = $this->actingAs($admin)->get('/purchase-orders');

        $response->assertStatus(200);
        $response->assertSee('table-valign-middle', false);
        $response->assertSee('status-badge', false);
        $response->assertSee('fa-fw', false);
        $response->assertSee('receipt-popover-trigger', false);
        $response->assertSee('receipt-popover-content', false);
        $response->assertSee('table-roadmap-container', false);
        $response->assertSee('table-roadmap-steps', false);
        $response->assertSee('table-roadmap-node', false);
        $response->assertSee('Roadmap PO', false);
        $response->assertDontSee('btn-roadmap-modal-trigger', false);
    }

    public function test_po_show_renders_with_top_roadmap_stepper_and_receipt_hover(): void
    {
        $admin = User::role('Admin')->first();
        $po = \App\Models\PurchaseOrder::first();

        $response = $this->actingAs($admin)->get('/purchase-orders/' . $po->id);

        $response->assertStatus(200);
        $response->assertSee('roadmap-card', false);
        $response->assertSee('roadmap-stepper', false);
        $response->assertSee('roadmap-progress-track', false);
        $response->assertSee('receipt-hover-trigger', false);
        $response->assertSee('receipt-popover-card', false);
        $response->assertSee('Langkah 1', false);
        $response->assertSee('Langkah 2', false);
        $response->assertSee('Langkah 3', false);
        $response->assertSee('Langkah 4', false);
        $response->assertSee('Langkah 5', false);
        $response->assertSee('Langkah 6', false);
        $response->assertSee('Subtotal', false);
        $response->assertSee('Total PO', false);
    }
}
