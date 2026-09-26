# Pattern Sistem — SiHalal (Yayasan PBP)
> File ini adalah **standar wajib** untuk seluruh pengembangan & refaktor kode pada role `superadmin` dan `admin_umum`.
> Setiap fitur baru atau refaktor HARUS mengikuti semua poin di bawah ini.

---

## 1. Layout & Design System (`admin-ui.css`)

Semua halaman admin (`superadmin` dan `admin_umum`) **wajib** menggunakan kelas dari `public/assets/css/admin-ui.css`.
Tidak boleh ada inline style ad-hoc atau kelas Bootstrap mentah pada elemen utama.

### Struktur Halaman Standar

```blade
@extends('layouts.app')
@section('template_title') Judul Halaman @endsection

@section('content')
<div class="adm-page">
    @include('layouts.messages')

    {{-- Page Header --}}
    <div class="adm-header">
        <div class="adm-header-left">
            <h1>Judul Halaman</h1>
            <p>Deskripsi singkat halaman</p>
        </div>
        <a href="{{ route(...) }}" class="adm-btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Data
        </a>
    </div>

    {{-- Stats (opsional) --}}
    <div class="adm-stats">
        <div class="adm-stat is-accent">
            <div class="adm-stat-label">Total</div>
            <div class="adm-stat-value">{{ $total }}</div>
            <div class="adm-stat-sub">Keseluruhan data</div>
        </div>
        {{-- repeat untuk stat lain --}}
    </div>

    {{-- Main Card --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">
                <svg viewBox="0 0 24 24">...</svg>
                Daftar Data
            </div>
        </div>
        {{-- filter bar, table, footer di sini --}}
    </div>
</div>
@endsection
```

### Token Warna (CSS Variables)
| Variable | Kegunaan |
|---|---|
| `--adm-blue` | Aksi utama / primary |
| `--adm-green` | Sukses / aktif |
| `--adm-red` | Bahaya / hapus |
| `--adm-amber` | Peringatan / pending |
| `--adm-cyan` | Info tambahan |
| `--adm-indigo` | Label khusus |

### Komponen Utama
- **Button header:** `.adm-btn-primary`, `.adm-btn-secondary`
- **Button aksi tabel:** `.adm-btn.primary`, `.adm-btn.success`, `.adm-btn.danger`, `.adm-btn.warning`, `.adm-btn.info`, `.adm-btn.icon-only`
- **Badge status:** `.adm-badge` + `.adm-badge-success | -danger | -pending | -info | -cyan | -indigo | -aktif | -nonaktif | -terbit | -revisi | -ditolak`
- **Form:** `.adm-form-section > .adm-form-section-header + .adm-form-body > .adm-form-grid > .adm-field`
- **Input:** `.adm-input`, `.adm-textarea`, `.adm-field-select`
- **Info detail:** `.adm-info-list > .adm-info-row > .adm-info-key + .adm-info-val`
- **Modal:** `.adm-modal` (header biru gradient) atau `.adm-modal-plain` (header putih)
- **Alert inline:** `.adm-alert.adm-alert-success | -warning | -danger | -info`

---

## 2. Loading Button State (Wajib di Semua Submit / Aksi Async)

Setiap button submit **WAJIB** menampilkan loading state saat proses berlangsung.

### Pattern: Form Submit Biasa

```html
<button type="submit" class="adm-btn-primary" id="btnSimpan">
    <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
    Simpan
</button>
```

```javascript
document.getElementById('namaForm').addEventListener('submit', function () {
    const btn = document.getElementById('btnSimpan');
    btn.disabled = true;
    btn.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Menyimpan...
    `;
});
```

### Pattern: AJAX Button Loading

```javascript
function setLoading(btn, state, originalHTML) {
    if (state) {
        btn.disabled = true;
        btn.dataset.original = btn.innerHTML;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Memproses...`;
    } else {
        btn.disabled = false;
        btn.innerHTML = originalHTML || btn.dataset.original;
    }
}
```

---

## 3. JavaScript Loading State untuk Form

Gunakan event `submit` pada form — bukan `click` pada button — agar mencakup validasi HTML5.

```javascript
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('namaForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const submitBtn = form.querySelector('[type="submit"]');
        if (!submitBtn) return;

        // Simpan label asli
        const originalLabel = submitBtn.innerHTML;

        // Set loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status"></span>
            Memproses...
        `;

        // Safety timeout (jika server error / redirect tidak terjadi)
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalLabel;
        }, 15000);
    });
});
```

---

## 4. AJAX Button (Async Actions)

Untuk aksi yang tidak perlu reload halaman (toggle status, update field, dll).

```javascript
document.getElementById('btnToggle').addEventListener('click', async function () {
    const btn = this;
    const originalHTML = btn.innerHTML;

    // Loading state
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;

    try {
        const response = await fetch('{{ route("...") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: '{{ $model->hashed_id }}' }),
        });

        const data = await response.json();

        if (!response.ok) throw new Error(data.message || 'Terjadi kesalahan');

        // Toast sukses
        Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            title: data.message || 'Berhasil!',
            showConfirmButton: false, timer: 2500, timerProgressBar: true,
        });

        // Update UI jika perlu (misal reload tabel)
        if (window.dataTableInstance) window.dataTableInstance.ajax.reload(null, false);

    } catch (err) {
        Swal.fire({
            toast: true, position: 'top-end', icon: 'error',
            title: err.message || 'Gagal memproses permintaan',
            showConfirmButton: false, timer: 3000,
        });
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
});
```

---

## 5. Delete Button (SweetAlert + Loading)

**Wajib** konfirmasi SweetAlert2 sebelum hapus. Tidak boleh pakai `confirm()` browser biasa.

```javascript
function confirmDelete(hashedId, label) {
    Swal.fire({
        title: 'Hapus Data?',
        html: `Data <strong>${label}</strong> akan dihapus permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then(async (result) => {
        if (!result.isConfirmed) return;

        // Tampilkan loading global
        Swal.fire({
            title: 'Menghapus...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
        });

        try {
            const response = await fetch(`/route/${hashedId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Gagal menghapus');

            Swal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: data.message || 'Data berhasil dihapus!',
                showConfirmButton: false, timer: 2500, timerProgressBar: true,
            });

            // Reload datatable tanpa reset halaman
            if (window.dataTableInstance) window.dataTableInstance.ajax.reload(null, false);

        } catch (err) {
            Swal.fire('Gagal!', err.message, 'error');
        }
    });
}
```

**Contoh button di view:**
```html
<button
    class="adm-btn danger icon-only"
    onclick="confirmDelete('{{ $row->hashed_id }}', '{{ addslashes($row->nama) }}')"
    title="Hapus"
>
    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
</button>
```

---

## 6. DataTables (Wajib di Semua Tabel Listing)

Semua tabel listing **wajib** menggunakan DataTables server-side. Tidak boleh render data via Blade loop manual.

### View (HTML)

```html
<div class="table-responsive">
    <table id="tabelNama" class="adm-table w-100">
        <thead>
            <tr>
                <th style="width:44px">#</th>
                <th>Nama</th>
                <th class="tc">Status</th>
                <th class="tc" style="width:120px">Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
```

### Inisialisasi JavaScript

```javascript
document.addEventListener('DOMContentLoaded', function () {
    window.dataTableInstance = $('#tabelNama').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route($routePrefix . ".resource.data") }}',
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'tc' },
            { data: 'nama',        name: 'nama' },
            { data: 'status_badge',name: 'status', className: 'tc', orderable: false },
            { data: 'aksi',        name: 'aksi',  orderable: false, searchable: false, className: 'tc' },
        ],
        language: {
            search:      'Cari:',
            lengthMenu:  'Tampilkan _MENU_ data',
            info:        'Menampilkan _START_ – _END_ dari _TOTAL_ data',
            infoEmpty:   'Tidak ada data',
            paginate:    { previous: '‹', next: '›' },
            zeroRecords: 'Data tidak ditemukan',
            emptyTable:  'Belum ada data',
            processing:  '<div class="spinner-border text-primary" role="status"></div>',
        },
        pageLength: 15,
        order: [[1, 'asc']],
        responsive: true,
    });
});
```

### Controller (data endpoint)

```php
public function data(Request $request): JsonResponse
{
    $query = ModelNama::query();

    return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('status_badge', fn($row) => view('components.badges.status', ['status' => $row->status])->render())
        ->addColumn('aksi', fn($row) => view('superadmin.resource.partials.aksi', compact('row'))->render())
        ->rawColumns(['status_badge', 'aksi'])
        ->make(true);
}
```

---

## 7. Notifikasi — SweetAlert2 Toast (Wajib)

**Dilarang keras** menggunakan `alert()` JavaScript atau Bootstrap modal sebagai notifikasi.
Semua notifikasi **wajib** pakai SweetAlert2 Toast.

### Toast Sukses
```javascript
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: 'Data berhasil disimpan!',
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true,
});
```

### Toast Error
```javascript
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'error',
    title: 'Gagal! Coba lagi.',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});
```

### Flash dari Server (Blade)

```blade
{{-- Di dalam @push('scripts') --}}
@if (session('success'))
<script>
    Swal.fire({
        toast: true, position: 'top-end', icon: 'success',
        title: '{{ session("success") }}',
        showConfirmButton: false, timer: 2500, timerProgressBar: true,
    });
</script>
@endif

@if (session('error'))
<script>
    Swal.fire({
        toast: true, position: 'top-end', icon: 'error',
        title: '{{ session("error") }}',
        showConfirmButton: false, timer: 3500,
    });
</script>
@endif
```

---

## 8. HashedId — Route Model Binding (Wajib)

Semua Model **wajib** menggunakan trait `HasHashedId`. ID asli (integer) tidak boleh terekspos di URL.

### Setup Model

```php
use App\Traits\HasHashedId;

class ModelNama extends Model
{
    use HasHashedId;
    // ...
}
```

### Route (gunakan hashed_id, bukan id)

```php
// routes/web.php
Route::get('/resource/{model}', [Controller::class, 'show'])->name('resource.show');
Route::put('/resource/{model}', [Controller::class, 'update'])->name('resource.update');
Route::delete('/resource/{model}', [Controller::class, 'destroy'])->name('resource.destroy');
```

### Controller

```php
// Laravel otomatis resolve via resolveRouteBinding() di HasHashedId trait
public function show(ModelNama $model): View
{
    return view('superadmin.resource.show', compact('model'));
}

public function destroy(ModelNama $model): JsonResponse
{
    // Gunakan Service layer untuk logic
    $this->resourceService->delete($model);

    return response()->json(['message' => 'Data berhasil dihapus']);
}
```

### Di View / Link

```blade
{{-- Gunakan hashed_id accessor, bukan id --}}
<a href="{{ route($routePrefix . '.resource.show', $model->hashed_id) }}">Detail</a>

{{-- Di AJAX --}}
fetch(`/path/{{ $model->hashed_id }}`, { method: 'DELETE', ... })
```

### Trait Referensi (`app/Traits/HasHashedId.php`)

```php
// Format hash: XXXXXXXXXX-XXXXXXXXXX-XXXXXXXXXX-XXXXXXXXXX-XXXXXXXXXX (50 chars, 5 blok)
// getHashedIdAttribute()   → encode id → padded → formatted
// findByHashedId($hash)    → decode → find
// findByHashedIdOrFail()   → decode → findOrFail / abort(404)
// resolveRouteBinding()    → otomatis dipanggil Laravel saat Route Model Binding
```

---

## 9. `@push` / `@stack` Scripts (Wajib)

Semua JavaScript spesifik halaman **wajib** diletakkan di `@push('scripts')`, **bukan** di dalam `@section('content')`.

### Di Layout (`layouts/app.blade.php`)
```blade
{{-- Sudah ada di app.blade.php: --}}
@stack('scripts')
```

### Di View
```blade
@section('content')
    {{-- HTML saja, tidak ada <script> di sini --}}
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // semua JS halaman di sini
    });
</script>
@endpush
```

**Aturan:**
- Satu blok `@push('scripts')` per view (gabungkan semua JS dalam satu blok).
- Gunakan `DOMContentLoaded` atau jQuery `$(function(){})` sebagai wrapper.
- Tidak boleh ada `<script>` tag di tengah-tengah `@section('content')`.

---

## 10. Service Layer (Wajib untuk Logic Bisnis)

Controller **tidak boleh** mengandung query Eloquent atau logika bisnis langsung.
Semua logic **wajib** didelegasikan ke Service class.

### Struktur Direktori
```
app/Services/
├── Superadmin/
│   ├── ResourceService.php
│   ├── KtpVerifikasiService.php
│   └── ...
├── Koordinator/
│   └── ...
└── WilayahService.php
```

### Contoh Service

```php
// app/Services/Superadmin/ResourceService.php
namespace App\Services\Superadmin;

use App\Models\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ResourceService
{
    public function store(array $data): Resource
    {
        return DB::transaction(function () use ($data) {
            // logic bisnis di sini
            return Resource::create($data);
        });
    }

    public function update(Resource $resource, array $data): Resource
    {
        return DB::transaction(function () use ($resource, $data) {
            $resource->update($data);
            return $resource->fresh();
        });
    }

    public function delete(Resource $resource): void
    {
        DB::transaction(function () use ($resource) {
            // hapus file terkait jika ada
            if ($resource->foto) {
                Storage::disk('public')->delete($resource->foto);
            }
            $resource->delete();
        });
    }
}
```

### Controller (tipis, hanya delegasi)

```php
// app/Http/Controllers/Superadmin/ResourceController.php
namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Services\Superadmin\ResourceService;

class ResourceController extends Controller
{
    public function __construct(private ResourceService $service) {}

    public function store(StoreResourceRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return redirect()->route($routePrefix . '.resource.index')
                ->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Resource $resource)
    {
        try {
            $this->service->delete($resource);
            return response()->json(['message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
```

---

## 11. Modal (Standard Sistem)

Gunakan modal Bootstrap dengan wrapper class `.adm-modal` (header biru gradient) atau `.adm-modal-plain` (header putih).
Modal **hanya** digunakan untuk konfirmasi/aksi inline yang perlu input tambahan, bukan sebagai halaman form penuh.

### Struktur Modal Standar (`.adm-modal`)

```html
<div class="modal fade adm-modal" id="modalNama" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route($routePrefix . '.resource.aksi', $model->hashed_id) }}" method="POST">
                @csrf
                {{-- tambahkan @method('PUT') jika perlu --}}

                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg viewBox="0 0 24 24">...</svg>
                        Judul Modal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Field form --}}
                    <div class="adm-field">
                        <label class="adm-label">Label <span class="req">*</span></label>
                        <input type="text" name="field" class="adm-input" required>
                        <span class="adm-hint">Keterangan field</span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="adm-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="adm-btn-primary" id="btnSubmitModal">
                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

### Trigger Modal

```html
{{-- Via data attribute --}}
<button class="adm-btn primary" data-bs-toggle="modal" data-bs-target="#modalNama">
    Buka Modal
</button>

{{-- Via JavaScript (untuk pass data dinamis) --}}
<button class="adm-btn primary" onclick="bukaModal('{{ $row->hashed_id }}', '{{ $row->nama }}')">
    Aksi
</button>
```

```javascript
function bukaModal(hashedId, nama) {
    // Isi data ke form modal
    document.getElementById('modalHashedId').value = hashedId;
    document.getElementById('modalNamaLabel').textContent = nama;

    // Update action URL
    const form = document.getElementById('formModal');
    form.action = `/superadmin/resource/${hashedId}/aksi`;

    // Tampilkan
    const modal = new bootstrap.Modal(document.getElementById('modalNama'));
    modal.show();
}
```

### Loading State pada Submit Modal

```javascript
document.getElementById('formModal').addEventListener('submit', function () {
    const btn = document.getElementById('btnSubmitModal');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Memproses...`;
});
```

---

## Checklist Refaktor

Sebelum PR/commit, verifikasi:

- [ ] View menggunakan `.adm-page`, `.adm-header`, `.adm-card`, kelas `admin-ui.css`
- [ ] Semua button submit punya loading state
- [ ] DataTables server-side digunakan (bukan Blade loop)
- [ ] Notifikasi pakai SweetAlert2 Toast (tidak ada `alert()` atau Bootstrap alert modal)
- [ ] URL menggunakan `hashed_id`, bukan integer `id`
- [ ] Logic bisnis ada di Service class, bukan di Controller
- [ ] Semua JS ada di `@push('scripts')`
- [ ] Modal mengikuti struktur `.adm-modal` atau `.adm-modal-plain`
- [ ] Delete pakai `confirmDelete()` dengan SweetAlert2
