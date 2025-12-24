<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentTerm;

class PaymentTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terms = [
            // Full payment terms (single payment)
            [
                'name' => 'COD',
                'days' => 0,
                'payment_type' => 'full',
                'installments' => null,
                'description' => 'Cash On Delivery - Bayar saat barang diterima',
            ],
            [
                'name' => 'Net 7',
                'days' => 7,
                'payment_type' => 'full',
                'installments' => null,
                'description' => 'Pembayaran 7 hari setelah PO disetujui',
            ],
            [
                'name' => 'Net 14',
                'days' => 14,
                'payment_type' => 'full',
                'installments' => null,
                'description' => 'Pembayaran 14 hari setelah PO disetujui',
            ],
            [
                'name' => 'Net 30',
                'days' => 30,
                'payment_type' => 'full',
                'installments' => null,
                'description' => 'Pembayaran 30 hari setelah PO disetujui',
            ],
            [
                'name' => 'Net 45',
                'days' => 45,
                'payment_type' => 'full',
                'installments' => null,
                'description' => 'Pembayaran 45 hari setelah PO disetujui',
            ],
            [
                'name' => 'Net 60',
                'days' => 60,
                'payment_type' => 'full',
                'installments' => null,
                'description' => 'Pembayaran 60 hari setelah PO disetujui',
            ],
            // Partial payment terms (multiple installments)
            [
                'name' => 'DP 50% + Pelunasan',
                'days' => 0,
                'payment_type' => 'partial',
                'installments' => json_encode([
                    ['percentage' => 50, 'days' => 0, 'description' => 'Down Payment 50%'],
                    ['percentage' => 50, 'days' => 30, 'description' => 'Pelunasan 50%'],
                ]),
                'description' => 'DP 50% saat approve, pelunasan 30 hari kemudian',
            ],
            [
                'name' => 'DP 30% + 2x Cicilan',
                'days' => 0,
                'payment_type' => 'partial',
                'installments' => json_encode([
                    ['percentage' => 30, 'days' => 0, 'description' => 'Down Payment 30%'],
                    ['percentage' => 35, 'days' => 30, 'description' => 'Cicilan 1 (35%)'],
                    ['percentage' => 35, 'days' => 60, 'description' => 'Cicilan 2 (35%)'],
                ]),
                'description' => 'DP 30%, lalu 2x cicilan masing-masing 35%',
            ],
            [
                'name' => '3x Cicilan',
                'days' => 0,
                'payment_type' => 'partial',
                'installments' => json_encode([
                    ['percentage' => 34, 'days' => 0, 'description' => 'Cicilan 1 (34%)'],
                    ['percentage' => 33, 'days' => 30, 'description' => 'Cicilan 2 (33%)'],
                    ['percentage' => 33, 'days' => 60, 'description' => 'Cicilan 3 (33%)'],
                ]),
                'description' => '3x cicilan bulanan',
            ],
        ];

        foreach ($terms as $term) {
            PaymentTerm::firstOrCreate(
                ['name' => $term['name']],
                $term
            );
        }

        $this->command->info('Payment Terms seeded successfully!');
    }
}
