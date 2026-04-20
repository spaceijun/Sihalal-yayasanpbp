@forelse ($enumerators as $enumerator)
    <tr>
        <td>KH-{{ $enumerator->no_registrasi }}</td>
        <td>{{ $enumerator->koordinator->nama_lengkap ?? '-' }}</td>
        <td>{{ $enumerator->nama_lengkap }}</td>
        <td>{{ $enumerator->telephone }}</td>
        <td>
            @if ($enumerator->bank && $enumerator->no_rekening && $enumerator->nama_rekening)
                {{ $enumerator->bank->name }}, {{ $enumerator->no_rekening }} an.
                {{ $enumerator->nama_rekening }}
            @else
                No data
            @endif
        </td>
        <td>
            @if (!$enumerator->user_id)
                <button type="button" class="btn btn-warning btn-sm btn-generate-user" data-id="{{ $enumerator->id }}"
                    data-nama="{{ $enumerator->nama_lengkap }}" data-hp="{{ $enumerator->telephone }}"
                    title="Generate akun user untuk enumerator ini">
                    <i class="las la-user-plus"></i> Generate User
                </button>
            @endif
            <a class="btn btn-sm btn-primary" href="{{ route('superadmin.enumerators.show', $enumerator->id) }}">
                <i class="las la-eye"></i> {{ __('Show') }}
            </a>
            <a class="btn btn-sm btn-success" href="{{ route('superadmin.enumerators.edit', $enumerator->id) }}">
                <i class="las la-edit"></i> {{ __('Edit') }}
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
