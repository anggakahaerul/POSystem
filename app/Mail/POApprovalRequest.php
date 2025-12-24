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

class POApprovalRequest extends Mailable
{
    use Queueable, SerializesModels;

    public PurchaseOrder $po;
    public string $approvalUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(PurchaseOrder $po)
    {
        $this->po = $po;
        $this->approvalUrl = route('purchase-orders.show', $po->id);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[APPROVAL REQUIRED] PO ' . $this->po->po_number . ' - ' . $this->po->vendor->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.po-approval-request',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Load PO with relationships for PDF
        $this->po->load(['vendor', 'details.product', 'creator', 'paymentTerm', 'branch']);

        // Generate PDF
        $pdf = Pdf::loadView('purchase-orders.print', ['po' => $this->po, 'isPdf' => true]);
        $pdfContent = $pdf->output();

        // Sanitize filename
        $filename = 'PO-' . str_replace('/', '-', $this->po->po_number) . '.pdf';

        return [
            Attachment::fromData(fn () => $pdfContent, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
