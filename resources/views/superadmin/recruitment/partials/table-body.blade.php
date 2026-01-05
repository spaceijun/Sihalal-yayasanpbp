@forelse ($recruitments as $recruitment)
    <tr>
        <td>{{ ($recruitments->currentPage() - 1) * $recruitments->perPage() + $loop->iteration }}</td>
        <td>{{ $recruitment->koordinator->nama_lengkap ?? 'N/A' }}</td>
        <td>{{ $recruitment->nama_lengkap }}</td>
        <td>{{ $recruitment->telephone }}</td>
        <td>{{ $recruitment->rekomendasi }}</td>
        <td>
            @if ($recruitment->status == 'Diterima')
                <span class="badge bg-success">{{ $recruitment->status }}</span>
            @elseif($recruitment->status == 'Ditolak')
                <span class="badge bg-danger">{{ $recruitment->status }}</span>
            @else
                <span class="badge bg-warning">{{ $recruitment->status }}</span>
            @endif
        </td>
        <td>
            <a class="btn btn-sm btn-primary" href="{{ route('superadmin.recruitments.show', $recruitment->id) }}">
                <i class="las la-eye"></i> {{ __('Show') }}
            </a>
            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $recruitment->id }}">
                <i class="las la-trash"></i> {{ __('Delete') }}
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-4">
            <div class="text-muted">
                <i class="las la-inbox la-3x mb-2"></i>
                <p class="mb-0">{{ __('No data available') }}</p>
            </div>
        </td>
    </tr>
@endforelse
