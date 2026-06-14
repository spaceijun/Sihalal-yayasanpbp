<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContactMessageController extends Controller
{
    use HasRoutePrefix;
    /**
     * Display a listing of contact messages
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $messages = ContactMessage::query()->orderBy('created_at', 'desc');

            return DataTables::of($messages)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($message) {
                    $colors = [
                        'pending' => 'warning',
                        'read' => 'info',
                        'replied' => 'success',
                        'archived' => 'secondary',
                    ];
                    $color = $colors[$message->status] ?? 'secondary';
                    $label = ucfirst($message->status);
                    return '<span class="badge bg-' . $color . '">' . $label . '</span>';
                })
                ->addColumn('date', function ($message) {
                    return $message->created_at->format('d/m/Y H:i');
                })
                ->addColumn('preview', function ($message) {
                    return '<span class="text-truncate d-inline-block" style="max-width: 200px;">'
                        . e($message->message) . '</span>';
                })
                ->addColumn('aksi', function ($message) {
                    $showUrl = route($this->routePrefix() . '.contact-messages.show', $message->id);
                    $deleteUrl = route($this->routePrefix() . '.contact-messages.destroy', $message->id);

                    return '<div class="adm-actions justify-content-center">
                        <a class="adm-btn primary icon-only" href="' . $showUrl . '" title="Detail">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                        <form action="' . $deleteUrl . '" method="POST" class="d-inline form-delete">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="adm-btn danger icon-only" title="Hapus">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['status_badge', 'preview', 'aksi'])
                ->make(true);
        }

        $pendingCount = ContactMessage::pending()->count();
        $routePrefix = $this->routePrefix();

        return view('superadmin.company-profile.contact-messages.index', compact('pendingCount', 'routePrefix'));
    }

    /**
     * Show a contact message
     */
    public function show(int $id)
    {
        $message = ContactMessage::findOrFail($id);

        // Mark as read if not already
        if (!$message->read_at) {
            $message->markAsRead();
        }

        $routePrefix = $this->routePrefix();
        return view('superadmin.company-profile.contact-messages.show', compact('message', 'routePrefix'));
    }

    /**
     * Update message status
     */
    public function updateStatus(Request $request, int $id)
    {
        $message = ContactMessage::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,read,replied,archived',
        ]);

        $message->update($validated);

        return redirect()->back()->with('success', 'Status pesan berhasil diperbarui');
    }

    /**
     * Remove a contact message
     */
    public function destroy(int $id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return redirect()->route($this->routePrefix() . '.contact-messages.index')
            ->with('success', 'Pesan berhasil dihapus');
    }

    /**
     * Mark all messages as read
     */
    public function markAllRead()
    {
        ContactMessage::pending()->update([
            'status' => 'read',
            'read_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Semua pesan ditandai sudah dibaca');
    }
}
