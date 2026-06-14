@extends('layouts.app')
@section('title', 'Memproses Face Match')

@section('content')
    <div class="container py-5">

        {{-- Header --}}
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1">
                <i class="bi bi-cpu me-2 text-primary"></i>Memproses Pencocokan Wajah
            </h4>
            <p class="text-muted small mb-0">
                Dimulai: {{ $meta['started_at'] }} &mdash; Total data: <strong>{{ $meta['total'] }}</strong>
            </p>
        </div>

        {{-- Foto query preview --}}
        <div class="text-center mb-4">
            <p class="text-muted small mb-2">Foto yang Anda cari:</p>
            <img src="{{ $meta['query_url'] }}" alt="Foto Query" class="rounded-circle border border-3 border-primary shadow"
                style="width:100px; height:100px; object-fit:cover;">
        </div>

        {{-- Progress card --}}
        <div class="card shadow-sm border-0 rounded-4 mb-3">
            <div class="card-body p-4">

                {{-- Angka proses --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Progress</span>
                    <span class="fw-semibold small">
                        <span id="processed">0</span> / <span id="total">{{ $meta['total'] }}</span> foto
                    </span>
                </div>

                {{-- Progress bar --}}
                <div class="progress rounded-pill mb-3" style="height: 14px;">
                    <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                        role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>

                {{-- Persentase --}}
                <div class="text-center">
                    <span id="percentage" class="display-6 fw-bold text-primary">0%</span>
                </div>

            </div>
        </div>

        {{-- Status teks --}}
        <div class="text-center mb-3">
            <span id="status-text" class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6">
                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                Sedang memproses...
            </span>
        </div>

        {{-- Info gagal (tersembunyi dulu) --}}
        <div id="failed-info" class="alert alert-warning rounded-3 small text-center d-none">
            <i class="bi bi-exclamation-triangle me-1"></i>
            <span id="failed-count">0</span> foto gagal diproses (dilewati secara otomatis).
        </div>

        {{-- Tombol batal (opsional — hanya UI, tidak cancel batch secara backend) --}}
        <div class="text-center mt-3">
            <a href="{{ route($routePrefix . '.face-match.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4"
                onclick="return confirm('Batalkan dan kembali ke halaman utama?')">
                <i class="bi bi-x-circle me-1"></i>Batalkan
            </a>
        </div>

    </div>

    <script>
        (function() {
            const KEY = @json($sessionKey);
            const POLL_URL = @json(route($routePrefix . '.face-match.poll', ['key' => $sessionKey]));
            const RESULT_URL = @json(route($routePrefix . '.face-match.result', ['key' => $sessionKey]));
            const INTERVAL = 2000; // ms

            const elBar = document.getElementById('progress-bar');
            const elPct = document.getElementById('percentage');
            const elProcessed = document.getElementById('processed');
            const elTotal = document.getElementById('total');
            const elStatus = document.getElementById('status-text');
            const elFailed = document.getElementById('failed-info');
            const elFailedCnt = document.getElementById('failed-count');

            let timer = null;

            function updateUI(data) {
                const pct = data.percentage ?? 0;

                elBar.style.width = pct + '%';
                elBar.setAttribute('aria-valuenow', pct);
                elPct.textContent = pct + '%';
                elProcessed.textContent = data.processed ?? 0;
                elTotal.textContent = data.total ?? 0;

                // Tampilkan info gagal jika ada
                if ((data.failed ?? 0) > 0) {
                    elFailedCnt.textContent = data.failed;
                    elFailed.classList.remove('d-none');
                }

                // Selesai → redirect ke hasil
                if (data.finished) {
                    clearInterval(timer);

                    elBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
                    elBar.classList.add('bg-success');
                    elStatus.innerHTML = '<i class="bi bi-check-circle me-1"></i>Selesai! Mengalihkan ke hasil...';
                    elStatus.className = 'badge bg-success px-3 py-2 rounded-pill fs-6';

                    setTimeout(() => {
                        window.location.href = RESULT_URL;
                    }, 1200);
                }
            }

            async function poll() {
                try {
                    const res = await fetch(POLL_URL, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const data = await res.json();
                    updateUI(data);
                } catch (err) {
                    console.warn('Poll error:', err);
                    // Tidak hentikan polling, coba lagi di interval berikutnya
                }
            }

            // Langsung poll sekali, lalu mulai interval
            poll();
            timer = setInterval(poll, INTERVAL);
        })();
    </script>
@endsection
