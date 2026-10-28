<?php

namespace App\Mail;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PORejected extends Mailable
{
    use Queueable, SerializesModels;

    public PurchaseOrder $po;
    public string $poUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(PurchaseOrder $po)
    {
        $this->po = $po;
        $this->poUrl = route('purchase-orders.show', $po->id);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $vendorName = $this->po->vendor->name ?? 'Vendor';
        return new Envelope(
            subject: '[PO DITOLAK] Purchase Order ' . $this->po->po_number . ' - ' . $vendorName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.po-rejected',
        );
    }
}
