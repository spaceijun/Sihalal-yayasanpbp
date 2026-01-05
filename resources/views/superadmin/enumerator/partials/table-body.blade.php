@forelse ($enumerators as $enumerator)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $enumerator->koordinator->nama_lengkap ?? '-' }}</td>
        <td>{{ $enumerator->nama_lengkap }}</td>
        <td>{{ $enumerator->telephone }}</td>
        <td>REG-{{ $enumerator->no_registrasi }}</td>
        <td>
            <span class="badge bg-{{ $enumerator->status == 'Aktif' ? 'success' : 'danger' }}">
                {{ $enumerator->status }}
            </span>
        </td>
        <td>
            <a class="btn btn-sm btn-primary" href="{{ route('superadmin.enumerators.show', $enumerator->id) }}">
                <i class="las la-eye"></i> {{ __('Edit') }}
            </a>
            <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $enumerator->id }}">
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
