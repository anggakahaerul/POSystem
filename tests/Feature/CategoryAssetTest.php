<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Tests\TestCase;

class CategoryAssetTest extends TestCase
{
    public function test_can_create_category_with_asset_hrga_and_mis(): void
    {
        $admin = User::role('Admin')->first();

        $response = $this->actingAs($admin)->post('/categories', [
            'name' => 'Kategori HRGA Test',
            'asset' => 'HRGA',
            'description' => 'Test HRGA category',
            'is_active' => '1',
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'name' => 'Kategori HRGA Test',
            'asset' => 'HRGA',
        ]);

        $response2 = $this->actingAs($admin)->post('/categories', [
            'name' => 'Kategori MIS Test',
            'asset' => 'MIS',
            'description' => 'Test MIS category',
            'is_active' => '1',
        ]);

        $response2->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'name' => 'Kategori MIS Test',
            'asset' => 'MIS',
        ]);
    }

    public function test_cannot_create_category_with_invalid_asset(): void
    {
        $admin = User::role('Admin')->first();

        $response = $this->actingAs($admin)->post('/categories', [
            'name' => 'Kategori Invalid Test',
            'asset' => 'OTHER',
            'description' => 'Test invalid category',
            'is_active' => '1',
        ]);

        $response->assertSessionHasErrors('asset');
    }

    public function test_can_update_category_asset(): void
    {
        $admin = User::role('Admin')->first();
        $category = Category::where('name', 'Kategori HRGA Test')->first();

        $response = $this->actingAs($admin)->put("/categories/{$category->id}", [
            'name' => 'Kategori HRGA Test',
            'asset' => 'MIS',
            'description' => 'Updated to MIS',
            'is_active' => '1',
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'asset' => 'MIS',
        ]);

        // Cleanup test data
        $category->delete();
        Category::where('name', 'Kategori MIS Test')->delete();
    }

    public function test_category_index_renders_with_asset_filter(): void
    {
        $admin = User::role('Admin')->first();

        $response = $this->actingAs($admin)->get('/categories?asset=MIS');
        $response->assertStatus(200);
        $response->assertSee('MIS');
    }
}
