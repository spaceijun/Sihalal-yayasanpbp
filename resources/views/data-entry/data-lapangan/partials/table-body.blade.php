@forelse ($dataLapangans as $dataLapangan)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $dataLapangan->enumerator->nama_lengkap ?? 'N/A' }}</td>
        <td>{{ $dataLapangan->nama_pu }}</td>
        <td>{{ $dataLapangan->nama_produk }}</td>
        <td>
            @php $s = $dataLapangan->status; @endphp
            @if ($s === 'PENDING')
                <span class="adm-badge adm-badge-pending"><span class="dot"></span>Pending</span>
            @elseif ($s === 'TERVERIFIKASI')
                <span class="adm-badge adm-badge-info"><span class="dot"></span>Terverifikasi</span>
            @elseif ($s === 'PROGRESS OSS')
                <span class="adm-badge adm-badge-oss"><span class="dot"></span>Progress OSS</span>
            @elseif ($s === 'PROGRESS SIHALAL')
                <span class="adm-badge adm-badge-sihalal"><span class="dot"></span>Progress SiHalal</span>
            @elseif ($s === 'TERBIT SH')
                <span class="adm-badge adm-badge-terbit"><span class="dot"></span>Terbit SH</span>
            @elseif ($s === 'DITOLAK')
                <span class="adm-badge adm-badge-ditolak"><span class="dot"></span>Ditolak</span>
            @elseif ($s === 'REVISI')
                <span class="adm-badge adm-badge-revisi"><span class="dot"></span>Revisi</span>
            @endif
        </td>
        <td>
            @php
                $progresses = $dataLapangan->dataEntryProgress;
                $diterima = $progresses->firstWhere('status', 'DITERIMA');
                // Jika ada DITERIMA, prioritaskan. Jika tidak, ambil yang terbaru.
                $progress = $diterima ?? $progresses->sortByDesc('created_at')->first();
                $status = $progress?->status;
            @endphp

            @if ($dataLapangan->email_sihalal)
                <span class="badge bg-danger">
                    <i class="las la-clock"></i> {{ __('DIVERIFIKASI ADMIN') }}
                </span>
            @else
                {{-- DITERIMA, DITOLAK, atau tidak ada data → tampilkan Show --}}
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
