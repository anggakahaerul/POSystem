<?php

namespace App\Mail;

use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class POApproved extends Mailable
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
            subject: '[PO DISETUJUI] Purchase Order ' . $this->po->po_number . ' - ' . $vendorName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.po-approved',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $this->po->load(['vendor', 'details.product', 'creator', 'approver', 'paymentTerm', 'branch']);

        $pdf = Pdf::loadView('purchase-orders.print', ['po' => $this->po, 'isPdf' => true]);
        $pdfContent = $pdf->output();

        $filename = 'PO-' . str_replace('/', '-', $this->po->po_number) . '-APPROVED.pdf';

        return [
            Attachment::fromData(fn () => $pdfContent, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
