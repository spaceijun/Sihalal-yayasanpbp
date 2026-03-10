@forelse ($progresses as $progress)
    <tr>
        <td>{{ $i + $loop->iteration }}</td>
        <td>{{ \Carbon\Carbon::parse($progress->actioned_at)->format('d/m/Y H:i') }}</td>
        <td>{{ $progress->dataLapangan?->nama_pu ?? '-' }}</td>
        <td>{{ $progress->dataLapangan?->nik ?? '-' }}</td>
        <td>
            @php
                $actionBadge = match (strtolower($progress->action)) {
                    'create' => 'success',
                    'update' => 'warning',
                    'delete' => 'danger',
                    default => 'secondary',
                };
            @endphp
            <span class="badge bg-{{ $actionBadge }}">
                {{ strtoupper($progress->action) }}
            </span>
        </td>
        <td>
            <span class="badge bg-info">
                {{ $progress->status ?? '-' }}
            </span>
        </td>
        <td>
            <a class="btn btn-sm btn-primary"
                href="{{ route('data-entry.progress.show', $progress->dataLapangan->hashed_id) }}">
                <i class="las la-eye"></i> {{ __('Show') }}
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted py-4">Tidak ada data progress.</td>
    </tr>
@endforelse
