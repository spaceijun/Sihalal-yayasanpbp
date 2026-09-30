@extends('layouts.app')
@section('template_title')
    Surat — {{ $surat->perihal }}
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>{{ $surat->perihal }}</h1>
                <p>{{ $surat->jenis_nama }} @if ($surat->nomor) &middot; {{ $surat->nomor }} @endif</p>
            </div>
            <a href="{{ route('superadmin.wrgroup-surat.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Arsip
            </a>
        </div>

        <div class="adm-card">
            <div class="adm-card-header">
                <div class="adm-card-title">Status: <span class="adm-badge {{ $surat->status_badge_class }}">{{ $surat->status_label }}</span></div>
            </div>
            <div style="padding:16px;">
                <div class="adm-info-list">
                    <div class="adm-info-row"><span class="adm-info-key">Diajukan Oleh</span><span class="adm-info-val">{{ $surat->pengaju->name }}</span></div>
                    <div class="adm-info-row"><span class="adm-info-key">Tanggal Pengajuan</span><span class="adm-info-val">{{ $surat->created_at->format('d/m/Y H:i') }}</span></div>
                    @if ($surat->diterbitkan_at)
                        <div class="adm-info-row"><span class="adm-info-key">Tanggal Terbit</span><span class="adm-info-val">{{ $surat->diterbitkan_at->format('d/m/Y H:i') }}</span></div>
                    @endif
                    @if ($surat->catatan_terakhir)
                        <div class="adm-info-row"><span class="adm-info-key">Catatan</span><span class="adm-info-val">{{ $surat->catatan_terakhir }}</span></div>
                    @endif
                </div>

                @if (! $surat->berhasil_kirim)
                    <div class="adm-alert adm-alert-danger" style="margin-top:16px;">
                        Gagal dikirim ke WRGROUP: {{ $surat->response_message ?: 'tidak ada respons.' }}
                        <form action="{{ route('superadmin.wrgroup-surat.ulangi', $surat->hashed_id) }}" method="POST" style="display:inline-block;margin-left:8px;">
                            @csrf
                            <button type="submit" class="adm-btn danger">Ajukan Ulang</button>
                        </form>
                    </div>
                @endif

                @if ($surat->status === 'terbit' && $surat->pdf_path)
                    <a href="{{ route('superadmin.wrgroup-surat.pdf', $surat->hashed_id) }}" target="_blank" class="adm-btn-primary" style="margin-top:16px;display:inline-block;">
                        Unduh PDF
                    </a>
                @elseif ($surat->status === 'terbit')
                    <div class="adm-alert adm-alert-warning" style="margin-top:16px;">Surat sudah terbit di WRGROUP, PDF sedang disinkronkan — tunggu jadwal sinkronisasi berikutnya.</div>
                @endif
            </div>
        </div>

        @if ($surat->data_variabel)
            <div class="adm-card" style="margin-top:16px;">
                <div class="adm-card-header"><div class="adm-card-title">Isian Formulir</div></div>
                <div class="table-responsive">
                    <table class="adm-table w-100">
                        <tbody>
                            @foreach ($surat->data_variabel as $k => $v)
                                <tr><th style="width:220px;">{{ $k }}</th><td>{{ is_array($v) ? implode(', ', $v) : $v }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
