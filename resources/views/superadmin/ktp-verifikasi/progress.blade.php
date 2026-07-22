@extends('layouts.app')
@section('template_title')
    Verifikasi KTP — Memproses...
@endsection

@section('content')
<div class="adm-page" style="max-width:680px;margin:0 auto;">

    <div class="adm-header" style="margin-bottom:24px;">
        <div class="adm-header-left">
            <h1>Memproses Verifikasi</h1>
            <p>AI sedang menganalisis setiap foto — mohon tunggu hingga selesai</p>
        </div>
    </div>

    {{-- PROGRESS CARD --}}
    <div class="adm-card" style="overflow:hidden;">

        {{-- Header berwarna --}}
        <div style="background:linear-gradient(135deg,#7C3AED,#4F46E5);padding:28px 28px 60px;position:relative;">
            <div style="color:#fff;font-size:13px;font-weight:600;opacity:.8;margin-bottom:6px;">Session ID</div>
            <div style="color:#fff;font-family:monospace;font-size:12px;opacity:.6;">{{ $session->session_key }}</div>

            {{-- Animated orbs --}}
            <div style="position:absolute;right:-20px;top:-20px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.06);"></div>
            <div style="position:absolute;right:40px;bottom:-40px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.04);"></div>
        </div>

        {{-- Progress area --}}
        <div style="margin-top:-36px;padding:0 24px 28px;position:relative;z-index:1;">

            {{-- Counter card --}}
            <div style="background:#fff;border-radius:16px;padding:20px 24px;box-shadow:0 8px 32px rgba(0,0,0,0.12);margin-bottom:20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <div>
                        <div style="font-size:13px;color:var(--adm-text-muted);margin-bottom:4px;">Progres Analisis</div>
                        <div style="font-size:28px;font-weight:800;color:#7C3AED;" id="progressText">0%</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:13px;color:var(--adm-text-muted);margin-bottom:4px;">Foto Dianalisis</div>
                        <div style="font-size:20px;font-weight:700;color:var(--adm-text-dark);">
                            <span id="processedCount">0</span> / <span id="totalCount">{{ $session->total_photos }}</span>
                        </div>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div style="height:12px;background:#F1F5F9;border-radius:6px;overflow:hidden;">
                    <div id="progressBar"
                         style="height:100%;width:0%;background:linear-gradient(90deg,#7C3AED,#6366F1);border-radius:6px;transition:width .5s ease;"></div>
                </div>

                <div style="margin-top:10px;font-size:12px;color:var(--adm-text-faint);text-align:center;" id="etaText">
                    Menghitung estimasi waktu...
                </div>
            </div>

            {{-- Status Steps --}}
            <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px;">
                <div class="kv-prog-step" id="step1" style="--color:#7C3AED;">
                    <div class="kv-prog-dot done">
                        <svg viewBox="0 0 24 24" style="width:12px;height:12px;stroke:#fff;fill:none;stroke-width:2.5;">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <span>File ZIP diekstrak & foto di-encode</span>
                </div>
                <div class="kv-prog-step" id="step2" style="--color:#7C3AED;">
                    <div class="kv-prog-dot active">
                        <div class="kv-prog-pulse"></div>
                    </div>
                    <span id="step2Text">Mengirim ke antrian AI...</span>
                </div>
                <div class="kv-prog-step" id="step3" style="--color:#7C3AED;opacity:.4;">
                    <div class="kv-prog-dot pending"></div>
                    <span>Menyusun top 3 hasil terbaik</span>
                </div>
            </div>

            {{-- Info box --}}
            <div style="background:#F5F3FF;border:1px solid #DDD6FE;border-radius:10px;padding:12px 16px;margin-bottom:20px;">
                <div style="font-size:12px;color:#5B21B6;line-height:1.6;">
                    ⚡ <strong>Proses berjalan di background.</strong>
                    Anda bisa menutup tab ini dan kembali lagi — proses tetap berjalan.
                    Gunakan link berikut untuk cek ulang:
                    <br>
                    <a href="{{ url()->current() }}" style="color:#7C3AED;font-weight:600;word-break:break-all;">{{ url()->current() }}</a>
                </div>
            </div>

            {{-- Tombol —tersembunyi sampai selesai --}}
            <div id="doneArea" style="display:none;text-align:center;">
                <a id="resultLink" href="#" class="adm-btn-primary" style="justify-content:center;gap:8px;width:100%;">
                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Lihat Hasil Verifikasi
                </a>
            </div>

            <div id="processingArea" style="text-align:center;">
                <a href="{{ route($routePrefix . '.ktp-verifikasi.index') }}"
                   style="font-size:12.5px;color:var(--adm-text-muted);text-decoration:none;">
                    ← Kembali ke form (proses tetap berjalan)
                </a>
            </div>
        </div>
    </div>

</div>

<style>
.kv-prog-step {
    display: flex; align-items: center; gap: 10px;
    font-size: 13px; color: var(--adm-text-mid);
}
.kv-prog-dot {
    width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    position: relative;
}
.kv-prog-dot.done    { background: #7C3AED; }
.kv-prog-dot.active  { background: #7C3AED; }
.kv-prog-dot.pending { background: #E2E8F0; }
.kv-prog-pulse {
    position: absolute; inset: 0; border-radius: 50%;
    background: rgba(124,58,237,0.3);
    animation: kv-pulse 1.5s ease-out infinite;
}
@keyframes kv-pulse {
    0%   { transform: scale(1);   opacity: .8; }
    100% { transform: scale(1.8); opacity: 0;  }
}
</style>

<script>
const sessionKey    = '{{ $session->session_key }}';
const statusUrl     = '{{ route($routePrefix . '.ktp-verifikasi.status', $session->session_key) }}';
const totalPhotos   = {{ $session->total_photos }};
let startTime       = Date.now();
let lastProcessed   = 0;
let pollInterval;

async function poll() {
    try {
        const res  = await fetch(statusUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();

        const pct       = data.percent || 0;
        const processed = data.processed || 0;

        document.getElementById('progressBar').style.width   = pct + '%';
        document.getElementById('progressText').textContent  = pct + '%';
        document.getElementById('processedCount').textContent = processed;

        // ETA
        if (processed > 0 && processed > lastProcessed) {
            const elapsed  = (Date.now() - startTime) / 1000;
            const rate     = processed / elapsed; // foto/detik
            const remaining = totalPhotos - processed;
            const etaSec   = Math.round(remaining / rate);
            document.getElementById('etaText').textContent =
                etaSec > 60
                    ? `Estimasi sisa: ± ${Math.ceil(etaSec / 60)} menit`
                    : `Estimasi sisa: ± ${etaSec} detik`;
            lastProcessed = processed;
        }

        // Update step text
        if (processed > 0) {
            document.getElementById('step2Text').textContent =
                `Menganalisis foto (${processed}/${totalPhotos})...`;
        }

        if (data.done) {
            clearInterval(pollInterval);

            // Selesai — update UI
            document.getElementById('progressBar').style.width = '100%';
            document.getElementById('progressText').textContent = '100%';
            document.getElementById('etaText').textContent = '✅ Analisis selesai!';

            // Update step 3
            const step3 = document.getElementById('step3');
            step3.style.opacity = '1';
            step3.querySelector('.kv-prog-dot').className = 'kv-prog-dot done';
            step3.querySelector('.kv-prog-dot').innerHTML =
                '<svg viewBox="0 0 24 24" style="width:12px;height:12px;stroke:#fff;fill:none;stroke-width:2.5;"><polyline points="20 6 9 17 4 12"/></svg>';

            document.getElementById('processingArea').style.display = 'none';
            document.getElementById('doneArea').style.display = 'block';

            // Hanya redirect jika result_url valid (bukan null/undefined)
            if (data.result_url && data.result_url !== 'null') {
                document.getElementById('resultLink').href = data.result_url;
                // Auto redirect setelah 2 detik
                setTimeout(() => { window.location.href = data.result_url; }, 2000);
            } else {
                // Fallback: reload halaman status untuk trigger finalization ulang
                setTimeout(() => { window.location.reload(); }, 3000);
            }
        }
    } catch (e) {
        console.error('Poll error:', e);
    }
}

// Poll setiap 3 detik
pollInterval = setInterval(poll, 3000);
poll(); // langsung panggil pertama kali
</script>
@endsection
