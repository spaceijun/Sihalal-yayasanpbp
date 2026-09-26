<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\Superadmin\ContactMessageService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ContactMessageController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private ContactMessageService $service) {}

    /**
     * Display a listing of contact messages
     */
    public function index(): View
    {
        $pendingCount = ContactMessage::pending()->count();
        $routePrefix = $this->routePrefix();

        return view('superadmin.company-profile.contact-messages.index', compact('pendingCount', 'routePrefix'));
    }

    /**
     * Return DataTables JSON for contact message listing.
     */
    public function data(Request $request)
    {
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

                return '<span class="badge bg-'.$color.'">'.$label.'</span>';
            })
            ->addColumn('date', function ($message) {
                return $message->created_at->format('d/m/Y H:i');
            })
            ->addColumn('preview', function ($message) {
                return '<span class="text-truncate d-inline-block" style="max-width: 200px;">'
                    .e($message->message).'</span>';
            })
            ->addColumn('aksi', function ($message) {
                $showUrl = route($this->routePrefix().'.contact-messages.show', $message->id);

                return '<div class="adm-actions justify-content-center">
                    <a class="adm-btn primary icon-only" href="'.$showUrl.'" title="Detail">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <button type="button" class="adm-btn danger icon-only" title="Hapus"
                        onclick="confirmDeleteContactMessage(\''.$message->id.'\', \''.e(addslashes($message->name)).'\')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['status_badge', 'preview', 'aksi'])
            ->make(true);
    }

    /**
     * Show a contact message
     */
    public function show(int $id): View
    {
        $message = ContactMessage::findOrFail($id);

        $this->service->markAsRead($message);

        $routePrefix = $this->routePrefix();

        return view('superadmin.company-profile.contact-messages.show', compact('message', 'routePrefix'));
    }

    /**
     * Update message status
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $message = ContactMessage::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,read,replied,archived',
        ]);

        try {
            $this->service->updateStatus($message, $validated);

            return redirect()->back()->with('success', 'Status pesan berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui status pesan: '.$e->getMessage());
        }
    }

    /**
     * Remove a contact message
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete(ContactMessage::findOrFail($id));

            return response()->json(['message' => 'Pesan berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark all messages as read
     */
    public function markAllRead(): RedirectResponse
    {
        try {
            $this->service->markAllRead();

            return redirect()->back()->with('success', 'Semua pesan ditandai sudah dibaca');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menandai pesan: '.$e->getMessage());
        }
    }
}
