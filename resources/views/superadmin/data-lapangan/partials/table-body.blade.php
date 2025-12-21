@forelse ($dataLapangans as $dataLapangan)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ \Carbon\Carbon::parse($dataLapangan->created_at)->translatedFormat('d M Y') }}</td>
        <td>{{ $dataLapangan->enumerator->nama_lengkap ?? 'N/A' }}</td>
        <td>{{ $dataLapangan->nama_pu }}</td>
        <td>{{ $dataLapangan->nik }}</td>
        <td>
            @if ($dataLapangan->status == 'PENDING')
                <span class="badge bg-warning text-dark">{{ $dataLapangan->status }}</span>
            @elseif($dataLapangan->status == 'PROGRESS OSS')
                <span class="badge bg-info">{{ $dataLapangan->status }}</span>
            @elseif($dataLapangan->status == 'PROGRESS SIHALAL')
                <span class="badge bg-primary">{{ $dataLapangan->status }}</span>
            @elseif($dataLapangan->status == 'TERBIT SH')
                <span class="badge bg-success">{{ $dataLapangan->status }}</span>
            @elseif($dataLapangan->status == 'DITOLAK')
                <span class="badge bg-danger">{{ $dataLapangan->status }}</span>
            @endif
        </td>
        <td>
            @if ($dataLapangan->status_pembayaran == 'PENDING')
                <span class="badge bg-warning text-dark">{{ $dataLapangan->status_pembayaran }}</span>
            @elseif($dataLapangan->status_pembayaran == 'PENGAJUAN')
                <span class="badge bg-info">{{ $dataLapangan->status_pembayaran }}</span>
            @elseif($dataLapangan->status_pembayaran == 'DIBAYAR')
                <span class="badge bg-success">{{ $dataLapangan->status_pembayaran }}</span>
            @endif
        </td>
        <td>
            <form action="{{ route('superadmin.data-lapangans.destroy', $dataLapangan->id) }}" method="POST"
                class="delete-form d-inline" data-id="{{ $dataLapangan->id }}">
                <a class="btn btn-sm btn-primary"
                    href="{{ route('superadmin.data-lapangans.show', $dataLapangan->id) }}">
                    <i class="las la-eye"></i> {{ __('Show') }}
                </a>
                {{-- <a class="btn btn-sm btn-success"
                    href="{{ route('superadmin.data-lapangans.edit', $dataLapangan->id) }}">
                    <i class="las la-edit"></i> {{ __('Edit') }}
                </a> --}}
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="las la-trash"></i> {{ __('Delete') }}
                </button>
            </form>
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
