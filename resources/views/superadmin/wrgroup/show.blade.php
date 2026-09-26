@extends('layouts.app')
@section('template_title')
    Detail Event WRGROUP
@endsection

@section('content')
    <div class="adm-page">
        @include('layouts.messages')

        <div class="adm-header">
            <div class="adm-header-left">
                <h1>{{ $event->endpoint_label }} &middot; {{ $event->transaction_id }}</h1>
                <p>Versi {{ $event->version }} &middot; event_id {{ $event->event_id }}</p>
            </div>
            <a href="{{ route('superadmin.wrgroup.index') }}" class="adm-btn-secondary">
                <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali
            </a>
        </div>

        <div class="adm-card" style="margin-bottom:16px;">
            <div class="adm-card-header">
                <div class="adm-card-title">Ringkasan</div>
            </div>
            <div style="padding:16px;">
                <div class="adm-info-list">
                    <div class="adm-info-row"><span class="adm-info-key">Status</span>
                        <span class="adm-info-val"><span class="adm-badge {{ $event->status_badge_class }}">{{ $event->status_label }}</span></span>
                    </div>
                    <div class="adm-info-row"><span class="adm-info-key">Tipe</span><span class="adm-info-val">{{ $event->type }}</span></div>
                    <div class="adm-info-row"><span class="adm-info-key">Periode</span><span class="adm-info-val">{{ $event->period ?: '—' }}</span></div>
                    <div class="adm-info-row"><span class="adm-info-key">Sumber</span><span class="adm-info-val">{{ $event->source_type ?? '—' }} #{{ $event->source_id ?? '—' }}</span></div>
                    <div class="adm-info-row"><span class="adm-info-key">Waktu Event</span><span class="adm-info-val">{{ optional($event->event_time)->format('d/m/Y H:i:s') ?? '—' }}</span></div>
                    <div class="adm-info-row"><span class="adm-info-key">Percobaan</span><span class="adm-info-val">{{ $event->attempts }}</span></div>
                    <div class="adm-info-row"><span class="adm-info-key">Coba Lagi Berikutnya</span><span class="adm-info-val">{{ optional($event->next_attempt_at)->format('d/m/Y H:i:s') ?? '—' }}</span></div>
                    <div class="adm-info-row"><span class="adm-info-key">HTTP Terakhir</span><span class="adm-info-val">{{ $event->last_http_status ?? '—' }}</span></div>
                    <div class="adm-info-row"><span class="adm-info-key">Lingkungan</span><span class="adm-info-val">{{ $event->environment ?? '—' }} {{ $event->persisted !== null ? ($event->persisted ? '(dibukukan)' : '(tidak dibukukan — kredensial testing)') : '' }}</span></div>
                    <div class="adm-info-row"><span class="adm-info-key">Terkirim Pada</span><span class="adm-info-val">{{ optional($event->sent_at)->format('d/m/Y H:i:s') ?? '—' }}</span></div>
                    @if ($event->last_error)
                        <div class="adm-info-row"><span class="adm-info-key">Pesan Terakhir</span><span class="adm-info-val">{{ $event->last_error }}</span></div>
                    @endif
                </div>

                @if ($event->isRetryable())
                    <form action="{{ route('superadmin.wrgroup.retry', $event->hashed_id) }}" method="POST" style="margin-top:12px;">
                        @csrf
                        <button type="submit" class="adm-btn-primary">Kirim Ulang</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="adm-card" style="margin-bottom:16px;">
            <div class="adm-card-header"><div class="adm-card-title">Payload Terkirim</div></div>
            <div style="padding:16px;">
                <pre style="background:#0f172a;color:#e2e8f0;padding:12px;border-radius:8px;overflow:auto;max-height:400px;">{{ json_encode($event->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card-header"><div class="adm-card-title">Respons Terakhir WRGROUP</div></div>
            <div style="padding:16px;">
                <pre style="background:#0f172a;color:#e2e8f0;padding:12px;border-radius:8px;overflow:auto;max-height:400px;">{{ $event->last_response ? json_encode($event->last_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : 'Belum ada respons.' }}</pre>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelector('form[action="{{ route('superadmin.wrgroup.retry', $event->hashed_id) }}"]')?.addEventListener('submit', function () {
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Mengirim...`;
    });
</script>
@endpush
