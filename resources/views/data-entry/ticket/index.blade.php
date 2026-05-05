@extends('layouts.app')

@section('template_title')
    Tickets
@endsection

@section('content')
    <div class="container-fluhashed_id">
        <div class="row">
            <div class="col">
                @include('layouts.messages')
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span hashed_id="card_title">
                                {{ __('Ticketing System') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('data-entry.tickets.create') }}" class="btn btn-primary btn-sm float-right"
                                    data-placement="left">
                                    {{ __('Create New') }}
                                </a>
                            </div>

                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No Ticket</th>

                                        <th>Nama Lengkap</th>
                                        <th>Subject</th>
                                        <th>Status</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tickets as $ticket)
                                        <tr>
                                            <td><span class="badge badge-gradient-info">{{ $ticket->no_ticket }}</span></td>
                                            <td>{{ $ticket->user->name }}</td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>
                                                @php $status = $ticket->status ?? '-'; @endphp
                                                @if ($status === 'open')
                                                    <span class="badge bg-primary">OPEN</span>
                                                @elseif ($status === 'in_progress')
                                                    <span class="badge bg-danger">PROGRESS</span>
                                                @elseif ($status === 'closed')
                                                    <span class="badge bg-success">SOLVED</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $status }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                <form action="{{ route('data-entry.tickets.destroy', $ticket->hashed_id) }}"
                                                    method="POST">
                                                    <a class="btn btn-sm btn-primary"
                                                        href="{{ route('data-entry.tickets.show', $ticket->hashed_id) }}"><i
                                                            class="las la-eye"></i></a>
                                                    @if ($ticket->status === 'open')
                                                        <a class="btn btn-sm btn-success"
                                                            href="{{ route('data-entry.tickets.edit', $ticket->hashed_id) }}"><i
                                                                class="las la-edit"></i></a>
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i
                                                                class="las la-trash"></i></button>
                                                    @endif
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="las la-inbox la-3x mb-2"></i>
                                                    <p class="mb-0">{{ __('No data available') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @include('layouts.pagination', ['paginator' => $tickets])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
