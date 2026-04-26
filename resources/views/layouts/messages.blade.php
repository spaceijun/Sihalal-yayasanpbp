{{-- ══════════════════════════════════════════════════════════
     ADM MESSAGES — Centralized flash notification component
     Supports: error, success, warning, info, validation errors
══════════════════════════════════════════════════════════ --}}

@php
    $messages = [
        'success' => [
            'session' => session('success'),
            'icon'    => '<polyline points="20 6 9 17 4 12"/>',
            'class'   => 'adm-msg-success',
        ],
        'error' => [
            'session' => session('error'),
            'icon'    => '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
            'class'   => 'adm-msg-danger',
        ],
        'warning' => [
            'session' => session('warning'),
            'icon'    => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
            'class'   => 'adm-msg-warning',
        ],
        'info' => [
            'session' => session('info'),
            'icon'    => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
            'class'   => 'adm-msg-info',
        ],
    ];
@endphp

@foreach ($messages as $type => $msg)
    @if ($msg['session'])
        <div class="adm-msg {{ $msg['class'] }}" role="alert" data-adm-msg>
            <div class="adm-msg-icon">
                <svg viewBox="0 0 24 24">{!! $msg['icon'] !!}</svg>
            </div>
            <div class="adm-msg-body">
                <span class="adm-msg-label">{{ ucfirst($type) }}</span>
                <span class="adm-msg-text">{{ $msg['session'] }}</span>
            </div>
            <button type="button" class="adm-msg-close" aria-label="Close" onclick="this.closest('[data-adm-msg]').remove()">
                <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    @endif
@endforeach

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="adm-msg adm-msg-danger adm-msg-list" role="alert" data-adm-msg>
        <div class="adm-msg-icon">
            <svg viewBox="0 0 24 24">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div class="adm-msg-body">
            <span class="adm-msg-label">Validasi Gagal</span>
            <ul class="adm-msg-list-items">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="adm-msg-close" aria-label="Close" onclick="this.closest('[data-adm-msg]').remove()">
            <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
@endif

<style>
/* ── ADM Message Component ── */
.adm-msg {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 14px 13px 16px;
    border-radius: 10px;
    border: 1px solid transparent;
    margin-bottom: 12px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    position: relative;
    animation: admMsgIn .25s ease;
}
@keyframes admMsgIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Variants */
.adm-msg-success {
    background: #f0fdf4;
    border-color: #bbf7d0;
}
.adm-msg-success .adm-msg-icon { color: #16a34a; background: #dcfce7; }
.adm-msg-success .adm-msg-label { color: #15803d; }

.adm-msg-danger {
    background: #fef2f2;
    border-color: #fecaca;
}
.adm-msg-danger .adm-msg-icon { color: #dc2626; background: #fee2e2; }
.adm-msg-danger .adm-msg-label { color: #b91c1c; }

.adm-msg-warning {
    background: #fffbeb;
    border-color: #fde68a;
}
.adm-msg-warning .adm-msg-icon { color: #b45309; background: #fef3c7; }
.adm-msg-warning .adm-msg-label { color: #92400e; }

.adm-msg-info {
    background: #eff6ff;
    border-color: #bfdbfe;
}
.adm-msg-info .adm-msg-icon { color: #1d4ed8; background: #dbeafe; }
.adm-msg-info .adm-msg-label { color: #1e40af; }

/* Icon */
.adm-msg-icon {
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.adm-msg-icon svg {
    width: 16px;
    height: 16px;
    fill: none;
    stroke: currentColor;
    stroke-width: 2.2;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* Body */
.adm-msg-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}
.adm-msg-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.adm-msg-text {
    font-size: 13.5px;
    color: #374151;
    line-height: 1.5;
}

/* Validation list */
.adm-msg-list-items {
    margin: 4px 0 0 0;
    padding-left: 18px;
    font-size: 13px;
    color: #374151;
    line-height: 1.7;
}

/* Close button */
.adm-msg-close {
    flex-shrink: 0;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    border-radius: 6px;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s, color .15s;
}
.adm-msg-close:hover { background: rgba(0,0,0,.06); color: #111; }
.adm-msg-close svg {
    width: 14px;
    height: 14px;
    fill: none;
    stroke: currentColor;
    stroke-width: 2.2;
    stroke-linecap: round;
}
</style>

<script>
    // Auto-dismiss setelah 6 detik
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-adm-msg]').forEach(function (el) {
            setTimeout(function () {
                el.style.transition = 'opacity .4s, transform .4s';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-6px)';
                setTimeout(function () { el.remove(); }, 400);
            }, 6000);
        });
    });
</script>
