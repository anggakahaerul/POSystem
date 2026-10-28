<?php

namespace App\Mail;

use App\Models\PurchaseOrder;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class POTaxReviewRequest extends Mailable
{
    use Queueable, SerializesModels;

    public PurchaseOrder $po;
    public string $reviewUrl;
    public ?string $notes;
    public ?User $sender;

    /**
     * Create a new message instance.
     */
    public function __construct(PurchaseOrder $po, ?string $notes = null, ?User $sender = null)
    {
        $this->po = $po;
        $this->notes = $notes;
        $this->sender = $sender;
        $this->reviewUrl = route('tax-reviews.index', ['search' => $po->po_number]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $vendorName = $this->po->vendor->name ?? 'Vendor';
        return new Envelope(
            subject: '[PERMOHONAN REVIEW PAJAK] PO ' . $this->po->po_number . ' - ' . $vendorName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.po-tax-review-request',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $this->po->load(['vendor', 'details.product', 'creator', 'paymentTerm', 'branch']);

        $pdf = Pdf::loadView('purchase-orders.print', ['po' => $this->po, 'isPdf' => true]);
        $pdfContent = $pdf->output();

        $filename = 'PO-' . str_replace('/', '-', $this->po->po_number) . '.pdf';

        return [
            Attachment::fromData(fn () => $pdfContent, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
