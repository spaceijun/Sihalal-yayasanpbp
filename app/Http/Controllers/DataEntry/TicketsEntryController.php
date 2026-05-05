<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TicketsEntryController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::paginate();
        return view('data-entry.ticket.index', compact('tickets'))
            ->with('i', ($request->input('page', 1) - 1) * $tickets->perPage());
    }

    public function create(): View
    {
        $ticket  = new Ticket();
        $noTicket = 'KH-' . now()->format('YmdHis');
        return view('data-entry.ticket.create', compact('ticket', 'noTicket'));
    }

    public function store(TicketRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id']   = Auth::id();
        $validated['no_ticket'] = 'KH-' . now()->format('YmdHis');
        $validated['status']    = 'open';

        if ($request->hasFile('file')) {
            $validated['file'] = $request->file('file')
                ->store('file-tiket', 'public');
        }

        Ticket::create($validated);

        return Redirect::route('data-entry.tickets.index')
            ->with('success', 'Ticket created successfully.');
    }

    public function show(string $id): View
    {
        $ticket = Ticket::findByHashedIdOrFail($id);
        return view('data-entry.ticket.show', compact('ticket'));
    }

    public function edit(string $id): View
    {
        $ticket = Ticket::findByHashedIdOrFail($id);
        return view('data-entry.ticket.edit', compact('ticket'));
    }

    public function update(TicketRequest $request, string $id): RedirectResponse
    {
        $ticket    = Ticket::findByHashedIdOrFail($id);
        $validated = $request->validated();

        // Upload file baru jika ada, hapus file lama
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($ticket->file && Storage::disk('public')->exists($ticket->file)) {
                Storage::disk('public')->delete($ticket->file);
            }
            $validated['file'] = $request->file('file')
                ->store('file-tiket', 'public');
        } else {
            // Tidak ada file baru — pertahankan file lama
            unset($validated['file']);
        }

        $ticket->update($validated);

        return Redirect::route('data-entry.tickets.index')
            ->with('success', 'Ticket updated successfully');
    }

    public function destroy(string $id): RedirectResponse
    {
        $ticket = Ticket::findByHashedIdOrFail($id);

        // Hapus file dari storage saat ticket dihapus
        if ($ticket->file && Storage::disk('public')->exists($ticket->file)) {
            Storage::disk('public')->delete($ticket->file);
        }

        $ticket->delete();

        return Redirect::route('data-entry.tickets.index')
            ->with('success', 'Ticket deleted successfully');
    }
}
