<?php

namespace App\Http\Controllers\ServiceDesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Services\TicketLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('ticket.viewAny');

        DB::beginTransaction();
        try {
            $data = $request->safe()->except('attachment');
            $data['ticket_id'] = $ticket->id;
            $data['user_id'] = auth()->id();

            if ($request->hasFile('attachment')) {
                $data['attachment_path'] = $request->file('attachment')
                    ->store('tickets/' . $ticket->id . '/comments', 'public');
            }

            $comment = TicketComment::create($data);

            TicketLogService::log(
                $ticket,
                null,
                'commented',
                null, null, null,
                strlen($comment->body) > 100 ? substr($comment->body, 0, 100) . '...' : $comment->body
            );

            DB::commit();

            return redirect()
                ->route('sd.tickets.show', $ticket)
                ->with('success', 'Komentar berhasil ditambahkan.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan komentar.', ['error' => $e->getMessage()]);

            return back()->with('error', 'Gagal menambahkan komentar. Silakan coba lagi.');
        }
    }

    public function destroy(Ticket $ticket, TicketComment $comment): RedirectResponse
    {
        if ($comment->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus komentar ini.');
        }

        DB::beginTransaction();
        try {
            if ($comment->attachment_path) {
                Storage::disk('public')->delete($comment->attachment_path);
            }
            $comment->delete();
            DB::commit();

            return redirect()
                ->route('sd.tickets.show', $ticket)
                ->with('success', 'Komentar berhasil dihapus.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal hapus komentar.', ['error' => $e->getMessage()]);

            return back()->with('error', 'Gagal menghapus komentar.');
        }
    }
}
