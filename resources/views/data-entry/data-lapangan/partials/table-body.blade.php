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
                <span class="badge bg-info">{{ $dataLapangan->status }}</span>
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
            @php
                $progress = $dataLapangan->dataEntryProgress->first();
                $status = $progress?->status;
            @endphp

            @if ($status === 'PENDING')
                <span class="badge badge-warning">
                    <i class="las la-clock"></i> {{ __('Pending') }}
                </span>
            @elseif($status === 'REVISI')
                <span class="badge badge-danger">
                    <i class="las la-edit"></i> {{ __('Revisi') }}
                </span>
            @else
                {{-- Tidak ada progress, DITERIMA, atau status lain --}}
                <a class="btn btn-sm btn-primary btn-show-data"
                    href="{{ route('data-entry.data-lapangan.show', $dataLapangan->hashed_id) }}"
                    data-id="{{ $dataLapangan->id }}">
                    <i class="las la-eye"></i> {{ __('Show') }}
                </a>
            @endif
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
