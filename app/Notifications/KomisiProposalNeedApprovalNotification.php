<?php

namespace App\Notifications;

use App\Models\KomisiProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class KomisiProposalNeedApprovalNotification extends Notification
{
    use Queueable;

    protected $komisiProposal;
    protected $approvalType;

    public function __construct(KomisiProposal $komisiProposal, string $approvalType)
    {
        $this->komisiProposal = $komisiProposal;
        $this->approvalType = $approvalType;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $approvalTypeText = $this->approvalType === 'pa'
            ? 'Pembimbing Akademik'
            : 'Koordinator Prodi';

        return [
            'komisi_proposal_id' => $this->komisiProposal->id,
            'mahasiswa_name' => $this->komisiProposal->user->name,
            'mahasiswa_nim' => $this->komisiProposal->user->nim,
            'judul_skripsi' => $this->komisiProposal->judul_skripsi,
            'type' => $this->approvalType,
            'approval_type_text' => $approvalTypeText,
            'status' => $this->komisiProposal->status,
            'created_at' => $this->komisiProposal->created_at->format('d M Y H:i'),

            // PERBAIKAN: Gunakan route show yang sudah terdefinisi
            'url' => route('admin.komisi-proposal.show', $this->komisiProposal->id),

            'icon' => $this->approvalType === 'pa' ? 'bx-user-check' : 'bx-crown',
            'badge_class' => $this->approvalType === 'pa' ? 'bg-warning' : 'bg-info',
            'message' => "Komisi Proposal membutuhkan persetujuan Anda sebagai {$approvalTypeText}",
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->approvalType === 'pa'
            ? 'Persetujuan Komisi Proposal sebagai Pembimbing Akademik'
            : 'Persetujuan Komisi Proposal sebagai Koordinator Prodi';

        return (new MailMessage)
            ->subject($subject)
            ->line('Anda mendapat pengajuan komisi proposal yang perlu disetujui.')
            ->line('Mahasiswa: ' . $this->komisiProposal->user->name . ' (' . $this->komisiProposal->user->nim . ')')
            ->line('Judul Skripsi: ' . $this->komisiProposal->judul_skripsi)
            ->action('Lihat Detail', route('admin.komisi-proposal.show', $this->komisiProposal->id))
            ->line('Terima kasih!');
    }
}