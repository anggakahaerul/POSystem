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

class POTaxReviewCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public PurchaseOrder $po;
    public string $actionType;
    public string $poUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(PurchaseOrder $po, string $actionType = 'verify')
    {
        $this->po = $po;
        $this->actionType = $actionType;
        $this->poUrl = route('purchase-orders.show', $po->id);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusText = $this->actionType === 'verify' ? 'Telah Diverifikasi' : 'Telah Dikoreksi & Disimpan';
        return new Envelope(
            subject: '[REVIEW PAJAK SELESAI] PO ' . $this->po->po_number . ' ' . $statusText,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.po-tax-review-completed',
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
