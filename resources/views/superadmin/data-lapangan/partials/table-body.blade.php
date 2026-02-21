@forelse ($dataLapangans as $dataLapangan)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ \Carbon\Carbon::parse($dataLapangan->created_at)->translatedFormat('d M Y H:i') }}</td>
        <td>{{ $dataLapangan->enumerator->nama_lengkap ?? 'N/A' }}</td>
        <td>{{ $dataLapangan->nama_pu }}</td>
        <td>{{ $dataLapangan->nik }}</td>
        <td>
            @if ($dataLapangan->status == 'PENDING')
                <span class="badge bg-warning text-dark">{{ $dataLapangan->status }}</span>
            @elseif($dataLapangan->status == 'TERVERIFIKASI')
                <span class="badge bg-secondary">{{ $dataLapangan->status }}</span>
            @elseif($dataLapangan->status == 'PROGRESS OSS')
                <span class="badge bg-info">{{ $dataLapangan->status }}</span>
            @elseif($dataLapangan->status == 'PROGRESS SIHALAL')
                <span class="badge bg-primary">{{ $dataLapangan->status }}</span>
            @elseif($dataLapangan->status == 'TERBIT SH')
                <span class="badge bg-success">{{ $dataLapangan->status }}</span>
            @elseif($dataLapangan->status == 'DITOLAK')
                <span class="badge bg-dark">{{ $dataLapangan->status }}</span>
            @elseif($dataLapangan->status == 'REVISI')
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
            @if ($dataLapangan->spotchecks && $dataLapangan->spotchecks->count() > 0)
                <span class="badge bg-success">
                    <i class="las la-check-circle"></i> Sudah Spotcheck
                </span>
            @else
                <span class="badge bg-secondary">
                    <i class="las la-times-circle"></i> Belum Spotcheck
                </span>
            @endif
        </td>
        <td>
            <a class="btn btn-sm btn-primary"
                href="{{ route('superadmin.data-lapangans.show', $dataLapangan->hashed_id) }}" title="Lihat Detail">
                <i class="las la-eye"></i>
            </a>
            @if ($dataLapangan->status == 'TERBIT SH')
                <form action="{{ route('superadmin.data-lapangans.update-status-payment', $dataLapangan->hashed_id) }}"
                    method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-success" title="Status Dibayar">
                        <i class="las la-file-invoice-dollar"></i>
                    </button>
                </form>
            @endif
            {{-- <a class="btn btn-sm btn-success"
                    href="{{ route('superadmin.data-lapangans.edit', $dataLapangan->id) }}">
                    <i class="las la-edit"></i> {{ __('Edit') }}
                </a> --}}
            <form action="{{ route('superadmin.data-lapangans.destroy', $dataLapangan->hashed_id) }}" method="POST"
                class="delete-form d-inline" data-id="{{ $dataLapangan->id }}">
                @csrf
                @method('DELETE')
                @if ($dataLapangan->status == 'PENDING' || $dataLapangan->status == 'DITOLAK')
                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus Data">
                        <i class="las la-trash"></i>
                    </button>
                @endif
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center py-4">
            <div class="text-muted">
                <i class="las la-inbox la-3x mb-2"></i>
                <p class="mb-0">{{ __('No data available') }}</p>
            </div>
        </td>
    </tr>
@endforelse
