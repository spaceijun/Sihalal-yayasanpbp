<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TicketRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $tickets = Ticket::paginate();

        return view('superadmin.ticket.index', compact('tickets'))
            ->with('i', ($request->input('page', 1) - 1) * $tickets->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $ticket = new Ticket();

        return view('superadmin.ticket.create', compact('ticket'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketRequest $request): RedirectResponse
    {
        Ticket::create($request->validated());

        return Redirect::route('superadmin.tickets.index')
            ->with('success', 'Ticket created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId): View
    {
        $ticket = Ticket::findByHashedId($hashedId);

        return view('superadmin.ticket.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $ticket = Ticket::findByHashedId($hashedId);

        return view('superadmin.ticket.edit', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->update($request->validated());

        return Redirect::route('superadmin.tickets.index')
            ->with('success', 'Ticket updated successfully');
    }

    public function destroy($hashedId): RedirectResponse
    {
        Ticket::findByHashedId($hashedId)->delete();

        return Redirect::route('superadmin.tickets.index')
            ->with('success', 'Ticket deleted successfully');
    }

    /**
     * Close the specified ticket.
     */
    public function close($hashedId): RedirectResponse
    {
        $ticket = Ticket::findByHashedIdOrFail($hashedId);

        if ($ticket->status === 'closed') {
            return Redirect::route('superadmin.tickets.show', $hashedId)
                ->with('error', 'Ticket is already closed.');
        }

        $ticket->update(['status' => 'closed']);

        return Redirect::route('superadmin.tickets.show', $hashedId)
            ->with('success', 'Ticket closed successfully.');
    }
}
