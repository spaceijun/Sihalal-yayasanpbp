@extends('layouts.guest')
@section('title', 'Konfirmasi Lamaran')
@section('content')
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-8">
                        <div class="card text-center p-4 p-lg-5">

                            <div class="mb-3">
                                <div class="avatar-lg mx-auto">
                                    <div class="avatar-title bg-success-subtle text-success rounded-circle fs-1">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </div>
                                </div>
                            </div>

                            <h4 class="text-success mb-1">Lamaran Berhasil Dikirim!</h4>
                            <p class="text-muted mb-4">
                                Terima kasih <strong>{{ $recruitment->nama_lengkap }}</strong>,<br>
                                data Anda telah kami terima. Silakan konfirmasi via WhatsApp.
                            </p>

                            {{-- Ringkasan Data --}}
                            <div class="table-responsive text-start mb-4">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted" width="45%">Posisi Dilamar</td>
                                        <td class="fw-semibold">{{ $recruitment->recruit_type }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">No. Telepon</td>
                                        <td class="fw-semibold">{{ $recruitment->telephone }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Pendidikan</td>
                                        <td class="fw-semibold">{{ $recruitment->pendidikan_terakhir }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Status</td>
                                        <td><span class="badge bg-success-subtle text-success">Melamar</span></td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Tombol WhatsApp --}}
                            @php
                                $waNumber = '628976774482';
                                $pesan = urlencode(
                                    "Halo, saya *{$recruitment->nama_lengkap}* ingin mengkonfirmasi pengisian Form Recruitment.\n\n" .
                                        "*Posisi Dilamar:* {$recruitment->recruit_type}\n" .
                                        "*No. Telepon:* {$recruitment->telephone}\n" .
                                        "*Pendidikan:* {$recruitment->pendidikan_terakhir}\n" .
                                        "*Jenis Kelamin:* {$recruitment->jenis_kelamin}\n" .
                                        '*Rekomendasi:* ' .
                                        ($recruitment->rekomendasi ?? '-') .
                                        "\n" .
                                        "*Status:* Melamar\n\n" .
                                        'Mohon informasi proses selanjutnya. Terima kasih.',
                                );
                            @endphp

                            <a href="https://wa.me/{{ $waNumber }}?text={{ $pesan }}" target="_blank"
                                class="btn btn-success btn-lg w-100 mb-2">
                                <i class="ri-whatsapp-line me-2"></i> Konfirmasi ke WhatsApp
                            </a>

                            {{-- <a href="{{ route('home') }}" class="btn btn-light w-100">
                                <i class="ri-arrow-left-line me-1"></i> Kembali ke Beranda
                            </a> --}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
