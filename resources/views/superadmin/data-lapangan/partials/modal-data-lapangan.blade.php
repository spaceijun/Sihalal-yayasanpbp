<div id="showModal{{ $dataLapangan->id }}" class="modal flip" tabindex="-1"
    aria-labelledby="showModalLabel{{ $dataLapangan->id }}" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showModalLabel{{ $dataLapangan->id }}">
                    Detail Data Lapangan - {{ $dataLapangan->nama_pu }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Nama PU</strong>
                    </div>
                    <div class="col-sm-8">
                        {{ $dataLapangan->nama_pu }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>NIK</strong>
                    </div>
                    <div class="col-sm-8">
                        {{ $dataLapangan->nik }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>RT / RW</strong>
                    </div>
                    <div class="col-sm-8">
                        {{ $dataLapangan->rt }} / {{ $dataLapangan->rw }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Alamat</strong>
                    </div>
                    <div class="col-sm-8">
                        {{ $dataLapangan->alamat }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Titik Koordinat</strong>
                    </div>
                    <div class="col-sm-8">
                        {{ $dataLapangan->titik_koordinat }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Status</strong>
                    </div>
                    <div class="col-sm-8">
                        @if ($dataLapangan->status == 'PENDING')
                            <span class="badge bg-secondary">{{ $dataLapangan->status }}</span>
                        @elseif($dataLapangan->status == 'PROGRESS OSS')
                            <span class="badge bg-warning">{{ $dataLapangan->status }}</span>
                        @elseif($dataLapangan->status == 'PROGRESS SIHALAL')
                            <span class="badge bg-info">{{ $dataLapangan->status }}</span>
                        @elseif($dataLapangan->status == 'TERBIT SH')
                            <span class="badge bg-success">{{ $dataLapangan->status }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Foto KTP</strong>
                    </div>
                    <div class="col-sm-8">
                        <a href="{{ asset('storage/' . $dataLapangan->foto_ktp) }}" target="_blank"
                            class="btn btn-sm btn-primary">
                            <i class="ri-eye-line"></i> Lihat Foto
                        </a>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Foto Rumah</strong>
                    </div>
                    <div class="col-sm-8">
                        <a href="{{ asset('storage/' . $dataLapangan->foto_rumah) }}" target="_blank"
                            class="btn btn-sm btn-primary">
                            <i class="ri-eye-line"></i> Lihat Foto
                        </a>
                        <a href="{{ route('superadmin.datalapangan.download-foto-rumah-pdf', $dataLapangan->id) }}"
                            class="btn btn-sm btn-success">
                            <i class="ri-download-line"></i> Download PDF
                        </a>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Foto Pendamping</strong>
                    </div>
                    <div class="col-sm-8">
                        <a href="{{ asset('storage/' . $dataLapangan->foto_pendamping) }}" target="_blank"
                            class="btn btn-sm btn-primary">
                            <i class="ri-eye-line"></i> Lihat Foto
                        </a>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Foto Proses</strong>
                    </div>
                    <div class="col-sm-8">
                        <a href="{{ asset('storage/' . $dataLapangan->foto_proses) }}" target="_blank"
                            class="btn btn-sm btn-primary">
                            <i class="ri-eye-line"></i> Lihat Foto
                        </a>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Foto Produk</strong>
                    </div>
                    <div class="col-sm-8">
                        <a href="{{ asset('storage/' . $dataLapangan->foto_produk) }}" target="_blank"
                            class="btn btn-sm btn-primary">
                            <i class="ri-eye-line"></i> Lihat Foto
                        </a>
                    </div>
                </div>

                @if ($dataLapangan->file_oss)
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>File OSS</strong>
                        </div>
                        <div class="col-sm-8">
                            <a href="{{ asset('storage/' . $dataLapangan->file_oss) }}" target="_blank"
                                class="btn btn-sm btn-success">
                                <i class="ri-download-line"></i> Download File
                            </a>
                        </div>
                    </div>
                @endif

                @if ($dataLapangan->file_sihalal)
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>File SIHALAL</strong>
                        </div>
                        <div class="col-sm-8">
                            <a href="{{ asset('storage/' . $dataLapangan->file_sihalal) }}" target="_blank"
                                class="btn btn-sm btn-success">
                                <i class="ri-download-line"></i> Download File
                            </a>
                        </div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Tanggal Dibuat</strong>
                    </div>
                    <div class="col-sm-8">
                        {{ $dataLapangan->created_at->format('d-m-Y H:i') }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Terakhir Update</strong>
                    </div>
                    <div class="col-sm-8">
                        {{ $dataLapangan->updated_at->format('d-m-Y H:i') }}
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
