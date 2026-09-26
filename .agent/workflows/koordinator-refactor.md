# Workflow: Refaktor Modul Koordinator

> Mengacu pada `.agent/pattern-sistem.md` — semua implementasi wajib mengikuti pattern tersebut.

---

## Ringkasan Perubahan

| Area | Before | After |
|---|---|---|
| Form fields | 6 field (nama, email, telp, alamat, fee, status) | 17+ field dalam 5 section terpisah |
| Foto | Tidak ada | Foto KTP + Foto Formal |
| Alamat | 1 field teks bebas | Cascading wilayah (Provinsi→Kab→Kec→Desa) + RT/RW |
| Wilayah Kerja | Tidak ada | Tipe wilayah + Provinsi + Kabupaten (conditional) |
| Fee | 1 field nominal di Koordinator | **Dihapus dari Koordinator** → pindah ke modul baru **Fee Enumerator** (skala Global/Provinsi) |
| Status | Aktif / Tidak Aktif | Aktif / Tidak Aktif / **Blacklist** (baru) |
| Service Layer | Tidak ada (logic di controller) | `KoordinatorService` |
| Hapus | Form POST redirect | AJAX + SweetAlert2 + DataTable reload |

---

## Langkah 1 — Migration Baru

Buat migration untuk menambahkan kolom baru ke tabel `koordinators`.

**File:** `database/migrations/YYYY_MM_DD_add_fields_to_koordinators_table.php`

```php
Schema::table('koordinators', function (Blueprint $table) {
    // Foto
    $table->string('foto_ktp')->nullable()->after('alamat');
    $table->string('foto_formal')->nullable()->after('foto_ktp');

    // Alamat detail (gantikan kolom 'alamat' lama)
    $table->string('provinsi_ktp')->nullable()->after('foto_formal');
    $table->string('kabupaten_ktp')->nullable()->after('provinsi_ktp');
    $table->string('kecamatan_ktp')->nullable()->after('kabupaten_ktp');
    $table->string('desa_ktp')->nullable()->after('kecamatan_ktp');
    $table->string('rt_ktp', 10)->nullable()->after('desa_ktp');
    $table->string('rw_ktp', 10)->nullable()->after('rt_ktp');
    // kolom 'alamat' sudah ada → rename menjadi alamat_lengkap_ktp
    // atau rename via migration:
    $table->renameColumn('alamat', 'alamat_lengkap_ktp');

    // Wilayah Kerja
    $table->enum('tipe_wilayah_kerja', ['Provinsi', 'Kabupaten'])->nullable()->after('alamat_lengkap_ktp');
    $table->string('provinsi_kerja')->nullable()->after('tipe_wilayah_kerja');
    $table->string('kabupaten_kerja')->nullable()->after('provinsi_kerja');

    // Informasi Lainnya
    $table->date('tanggal_mulai')->nullable()->after('kabupaten_kerja');
    // status: tambah 'Blacklist'
    // MySQL enum tidak bisa diubah langsung, gunakan DB::statement

    // HAPUS fee_enum — fee dikelola via modul Fee Enumerator tersendiri
    $table->dropColumn('fee_enum');
});

// Ubah enum status untuk tambah 'Blacklist'
DB::statement("ALTER TABLE koordinators MODIFY COLUMN status ENUM('Aktif', 'Tidak Aktif', 'Blacklist') NOT NULL DEFAULT 'Aktif'");
```

> **Catatan:** Jalankan `php artisan migrate` setelah membuat migration.

---

## Langkah 2 — Update Model

**File:** `app/Models/Superadmin/Koordinator.php`

```php
protected $fillable = [
    'user_id',
    'nama_lengkap',
    'email',
    'telephone',
    // Foto
    'foto_ktp',
    'foto_formal',
    // Alamat KTP
    'provinsi_ktp',
    'kabupaten_ktp',
    'kecamatan_ktp',
    'desa_ktp',
    'rt_ktp',
    'rw_ktp',
    'alamat_lengkap_ktp',
    // Wilayah Kerja
    'tipe_wilayah_kerja',
    'provinsi_kerja',
    'kabupaten_kerja',
    // Informasi Lainnya
    'tanggal_mulai',
    'status',
];

protected $casts = [
    'tanggal_mulai' => 'date',
];

// Relasi ke fee aktif (dari modul Fee Enumerator)
// public function feeAktif() — resolve via FeeEnumerator model (lihat workflow fee-enumerator.md)
```

> `HasHashedId` trait sudah ada — pertahankan.

---

## Langkah 3 — Service Layer

Buat `app/Services/Superadmin/KoordinatorService.php`.

**Tanggung jawab:**
- `store(array $data): Koordinator` — buat User + Koordinator dalam satu DB transaction, handle upload foto KTP & foto formal
- `update(Koordinator $koordinator, array $data): Koordinator` — update data + replace foto jika ada upload baru, hapus file lama dari storage
- `delete(Koordinator $koordinator): void` — hapus foto dari storage, hapus koordinator (cascade ke user via FK)

**Pola upload foto:**
```php
// Simpan ke storage/app/public/koordinator/foto-ktp/
$path = $request->file('foto_ktp')->store('koordinator/foto-ktp', 'public');

// Hapus file lama saat update
if ($koordinator->foto_ktp) {
    Storage::disk('public')->delete($koordinator->foto_ktp);
}
```

---

## Langkah 4 — Form Request

**File:** `app/Http/Requests/KoordinatorRequest.php`

Update rules untuk semua field baru:

```php
public function rules(): array
{
    $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
    $koordinatorId = $this->route('koordinator')?->id;

    return [
        // Akun Login
        'email'    => ['required', 'email', Rule::unique('koordinators')->ignore($koordinatorId)],
        'password' => $isUpdate ? ['nullable', 'min:8'] : ['required', 'min:8'],

        // Data Pribadi
        'foto_ktp'    => $isUpdate ? ['nullable', 'image', 'max:2048'] : ['required', 'image', 'max:2048'],
        'nama_lengkap' => ['required', 'string', 'max:255'],
        'telephone'    => ['required', 'string', 'max:20'],
        'foto_formal'  => $isUpdate ? ['nullable', 'image', 'max:2048'] : ['required', 'image', 'max:2048'],

        // Alamat KTP
        'provinsi_ktp'      => ['required', 'string'],
        'kabupaten_ktp'     => ['required', 'string'],
        'kecamatan_ktp'     => ['required', 'string'],
        'desa_ktp'          => ['required', 'string'],
        'rt_ktp'            => ['required', 'string', 'max:5'],
        'rw_ktp'            => ['required', 'string', 'max:5'],
        'alamat_lengkap_ktp'=> ['required', 'string'],

        // Wilayah Kerja
        'tipe_wilayah_kerja' => ['required', 'in:Provinsi,Kabupaten'],
        'provinsi_kerja'     => ['required', 'string'],
        'kabupaten_kerja'    => [
            Rule::requiredIf(fn() => $this->input('tipe_wilayah_kerja') === 'Kabupaten'),
            'nullable', 'string',
        ],

        // Informasi Lainnya
        'tanggal_mulai' => ['required', 'date'],
        'status'        => ['required', 'in:Aktif,Tidak Aktif,Blacklist'],
        // tipe_fee, target_data, nominal_fee → dikelola di modul Fee Enumerator
    ];
}
```

---

## Langkah 5 — Controller

**File:** `app/Http/Controllers/Superadmin/KoordinatorController.php`

Refaktor menjadi thin controller — semua logic ke service:

```php
public function __construct(private KoordinatorService $service) {}

// store(): panggil $this->service->store($request->validated() + files)
// update(): panggil $this->service->update($koordinator, ...)
// destroy(): AJAX, return JsonResponse (bukan redirect)
//            $this->service->delete($koordinator)
//            return response()->json(['message' => 'Koordinator berhasil dihapus'])

// data() untuk DataTables: update kolom → tambah foto_formal cell,
//   update status_badge untuk handle 'Blacklist' (badge danger)
//   HAPUS fee_fmt — tidak ada lagi kolom fee di tabel koordinator
```

**Kolom di DataTables `data()`:**
- `nama_cell` → foto_formal sebagai avatar jika ada, fallback inisial
- `wilayah_badge` → tipe_wilayah_kerja + nama wilayah
- `status_badge` → `Aktif`=success, `Tidak Aktif`=nonaktif, `Blacklist`=danger
- **Hapus** kolom `fee_fmt`, `fee_enum`

---

## Langkah 6 — Views

### 6a. `index.blade.php`
Tambah kolom baru di header tabel:

| # | Nama | Email | Telepon | Wilayah Kerja | Status | Aksi |
|---|---|---|---|---|---|---|

Hapus kolom `Total Data`, `Terbit SH`, `Fee Enum` dari tabel index (pindah ke `show`).
Update inisialisasi DataTables sesuai kolom baru.
**Delete button** diubah dari form POST menjadi AJAX `confirmDelete()` sesuai pattern.

### 6b. `form.blade.php` — Susun ulang menjadi 5 section

**Section 1: Akun Login**
```
adm-form-grid cols-2:
  [email]  [password]
```
- Password: hint "Kosongkan jika tidak ingin mengubah" (hanya pada edit)

**Section 2: Data Pribadi**
```
adm-form-grid cols-2:
  [foto_ktp — file upload + preview]  [foto_formal — file upload + preview]
  [nama_lengkap]                       [telephone]
```
- Preview foto saat edit jika sudah ada (`<img src="{{ asset('storage/' . $koordinator->foto_ktp) }}">`)

**Section 3: Alamat Sesuai KTP**
```
adm-form-grid cols-3:
  [provinsi_ktp — select cascade]  [kabupaten_ktp — select cascade]  [kecamatan_ktp — select cascade]
  [desa_ktp — select cascade]      [rt_ktp]                           [rw_ktp]

adm-form-grid cols-1:
  [alamat_lengkap_ktp — textarea]
```
- Cascade select menggunakan `WilayahService` (sudah ada) via AJAX

**Section 4: Wilayah Kerja**
```
adm-form-grid cols-3:
  [tipe_wilayah_kerja — select]  [provinsi_kerja — select]  [kabupaten_kerja — select, conditional]
```
- `kabupaten_kerja` hanya muncul jika `tipe_wilayah_kerja === 'Kabupaten'` (JS toggle)

**Section 5: Informasi Lainnya**
```
adm-form-grid cols-2:
  [tanggal_mulai — date]   [status — select: Aktif / Tidak Aktif / Blacklist]
```

> Fee (tipe, target, nominal) **tidak ada** di form ini — dikelola di modul **Fee Enumerator**.

### 6c. `create.blade.php` & `edit.blade.php`
Susun dengan multiple `adm-form-section` (satu per section form), bukan satu section tunggal.
Tambah loading state pada submit button (sesuai pattern).

### 6d. `show.blade.php`
Redesign menjadi layout 2 kolom (sidebar + main) seperti pola `recruitment/show.blade.php`:

**Sidebar kiri:**
- Foto formal (avatar besar)
- Nama, badge status
- Info ringkas: Email, Telepon, Tanggal Mulai
- **Fee aktif** (ambil dari tabel `fee_enumerators` — lihat relasi `feeAktif()`)

**Main content kanan** — 4 card section:
1. **Alamat KTP** — tampilkan semua field alamat + wilayah
2. **Wilayah Kerja** — tipe + nama wilayah
3. **Foto Dokumen** — foto KTP (preview image + link download)
4. **Statistik** — Total Enumerator, Total Data Lapangan, Terbit SH (data dari relasi)

---

## Langkah 7 — JavaScript (di `@push('scripts')`)

### Cascade Wilayah (Alamat KTP)
Gunakan `WilayahService` yang sudah ada via endpoint AJAX:
- Provinsi → trigger load Kabupaten
- Kabupaten → trigger load Kecamatan
- Kecamatan → trigger load Desa/Kelurahan
- Saat edit: pre-populate cascade berdasarkan value yang tersimpan

### Toggle Kabupaten Kerja
```javascript
document.getElementById('tipe_wilayah_kerja').addEventListener('change', function () {
    const kabWrap = document.getElementById('kabupaten_kerja_wrap');
    kabWrap.style.display = this.value === 'Kabupaten' ? '' : 'none';
    if (this.value !== 'Kabupaten') {
        document.getElementById('kabupaten_kerja').value = '';
    }
});
```

> Toggle `target_data` **tidak diperlukan** di form Koordinator — fee dikelola di modul terpisah.

### Preview Foto Upload
```javascript
function previewFoto(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById(previewId).src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
```

---

## Langkah 8 — Cek Wilayah API

Pastikan endpoint wilayah dari `WilayahService` sudah tersedia:
- `GET /wilayah/provinsi` → list provinsi
- `GET /wilayah/kabupaten?provinsi_id=X`
- `GET /wilayah/kecamatan?kabupaten_id=X`
- `GET /wilayah/desa?kecamatan_id=X`

Jika belum ada endpoint yang melayani filter untuk **Provinsi Kerja** dan **Kabupaten Kerja**, gunakan endpoint yang sama (reuse).

---

## Checklist Refaktor Koordinator

- [ ] Migration dibuat & dijalankan — kolom fee_enum di-drop, tanggal_mulai ditambah
- [ ] Model `Koordinator` — fillable & casts diupdate (hapus semua field fee)
- [ ] `KoordinatorService` dibuat (store, update, delete + handle foto)
- [ ] `KoordinatorRequest` — rules diupdate, tanpa field fee
- [ ] Controller refaktor ke thin controller (inject service)
- [ ] `destroy()` controller → JsonResponse (AJAX, bukan redirect)
- [ ] `data()` DataTables → hapus kolom fee, tambah wilayah_badge, status Blacklist
- [ ] `form.blade.php` → 5 section, **tanpa** field fee
- [ ] `create.blade.php` & `edit.blade.php` → multi-section layout + loading state
- [ ] `show.blade.php` → layout 2 kolom, tampilkan fee aktif dari relasi `feeAktif()`
- [ ] `index.blade.php` → hapus kolom fee, delete pakai AJAX confirmDelete
- [ ] JS cascade wilayah berfungsi (create & edit)
- [ ] JS toggle kabupaten_kerja berfungsi
- [ ] Preview foto upload berfungsi
- [ ] SweetAlert2 Toast untuk semua notifikasi
- [ ] Semua JS ada di `@push('scripts')`
- [ ] `hashed_id` digunakan di semua route & URL
- [ ] Modul Fee Enumerator dibuat (lihat `fee-enumerator.md`)
