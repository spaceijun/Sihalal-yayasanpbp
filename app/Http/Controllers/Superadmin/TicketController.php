<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = Ticket::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->paginate();

        return view('superadmin.ticket.index', compact('tickets'))
            ->with('i', ($request->input('page', 1) - 1) * $tickets->perPage());
    }

    public function show($hashedId): View
    {
        $ticket = Ticket::with('user')->findByHashedId($hashedId);

        return view('superadmin.ticket.show', compact('ticket'));
    }

    public function destroy($hashedId): RedirectResponse
    {
        Ticket::findByHashedId($hashedId)->delete();

        return redirect()->route('superadmin.tickets.index')
            ->with('success', 'Tiket berhasil dihapus.');
    }

    public function close($hashedId): RedirectResponse
    {
        $ticket = Ticket::findByHashedId($hashedId);

        if ($ticket->status === 'closed') {
            return redirect()->back()->with('error', 'Tiket sudah ditutup.');
        }

        $ticket->update(['status' => 'closed']);

        return redirect()->back()->with('success', 'Tiket berhasil ditutup.');
    }
}
