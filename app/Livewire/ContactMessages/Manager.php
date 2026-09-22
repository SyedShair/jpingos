<?php

namespace App\Livewire\ContactMessages;

use App\Mail\ContactMessageReplyMail;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Manager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public ?int $selectedMessageId = null;
    public string $replyText = '';
    public string $address = '';
    public string $mapUrl = '';
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function viewMessage(int $id): void
    {
        $this->selectedMessageId = $id;
        $this->replyText = '';

        // Opening it is the "read" action — matches how most inboxes
        // work, and means an admin doesn't have to do anything extra to
        // clear the "new" badge.
        $message = ContactMessage::find($id);
        if ($message && $message->status === 'new') {
            $message->update(['status' => 'read']);
        }
    }

    public function closeDetail(): void
    {
        $this->selectedMessageId = null;
        $this->replyText = '';
    }

    public function sendReply(): void
    {
        $this->validate([
            'replyText' => ['required', 'string', 'max:5000'],
        ]);

        $message = ContactMessage::findOrFail($this->selectedMessageId);

        $message->update([
            'admin_reply' => $this->replyText,
            'status'      => 'replied',
            'replied_at'  => now(),
        ]);

        Mail::to($message->email)->send(new ContactMessageReplyMail($message));

        $this->replyText = '';
        session()->flash('status-message', 'Reply sent to ' . $message->email . '.');
    }

    public function render()
    {
        $messages = ContactMessage::query()
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('subject', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        $selectedMessage = $this->selectedMessageId
            ? ContactMessage::find($this->selectedMessageId)
            : null;

        return view('livewire.contact-messages.manager', [
            'messages'        => $messages,
            'selectedMessage' => $selectedMessage,
        ]);
    }
}