# Workflow: Modul Fee Enumerator

> Modul baru — terpisah dari form Koordinator.
> Mengacu pada `.agent/pattern-sistem.md`.

---

## Konsep

Fee Enumerator adalah pengaturan besaran fee yang berlaku untuk enumerator di bawah koordinator.
Fee dapat dikonfigurasi pada dua skala:

| Skala | Keterangan | Prioritas |
|---|---|---|
| **Global** | Berlaku untuk semua koordinator tanpa pengecualian | Rendah (fallback) |
| **Provinsi** | Override khusus untuk koordinator di provinsi tertentu | Tinggi (prioritas utama) |

**Logika resolusi fee:**
> Jika koordinator punya wilayah kerja di Provinsi X, cek apakah ada fee khusus untuk Provinsi X.
> Jika ada → pakai fee Provinsi. Jika tidak ada → pakai fee Global.

Tipe fee tetap dua pilihan: **Bulan** (per bulan) atau **Target** (per N data).

---

## Struktur Tabel

### Tabel: `fee_enumerators`

```php
Schema::create('fee_enumerators', function (Blueprint $table) {
    $table->id();
    $table->enum('skala', ['Global', 'Provinsi']);
    $table->string('provinsi')->nullable();  // null jika skala Global
    $table->enum('tipe_fee', ['Bulan', 'Target']);
    $table->unsignedInteger('target_data')->nullable(); // isi jika tipe_fee = Target
    $table->unsignedBigInteger('nominal_fee');           // dalam Rupiah
    $table->boolean('is_aktif')->default(true);
    $table->text('keterangan')->nullable();
    $table->timestamps();

    // Constraint: hanya boleh 1 record Global aktif per tipe_fee
    // Constraint: hanya boleh 1 record Provinsi aktif per (provinsi + tipe_fee)
    // Enforce via unique index atau validasi di service
});
```

> **Catatan:** Tidak perlu FK ke `koordinators`. Fee di-resolve secara dinamis saat dibutuhkan.

---

## Model

**File:** `app/Models/Superadmin/FeeEnumerator.php`

```php
namespace App\Models\Superadmin;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;

class FeeEnumerator extends Model
{
    use HasHashedId;

    protected $table = 'fee_enumerators';

    protected $fillable = [
        'skala',
        'provinsi',
        'tipe_fee',
        'target_data',
        'nominal_fee',
        'is_aktif',
        'keterangan',
    ];

    protected $casts = [
        'is_aktif'    => 'boolean',
        'nominal_fee' => 'integer',
        'target_data' => 'integer',
    ];

    // Scope: hanya yang aktif
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    // Scope: filter per skala
    public function scopeGlobal($query)
    {
        return $query->where('skala', 'Global');
    }

    public function scopeProvinsi($query, string $provinsi)
    {
        return $query->where('skala', 'Provinsi')->where('provinsi', $provinsi);
    }
}
```

**Tambahan relasi di `Koordinator` model:**
```php
// Resolve fee aktif berdasarkan wilayah kerja koordinator
public function getFeeAktifAttribute(): ?FeeEnumerator
{
    // Cek fee Provinsi jika koordinator punya wilayah kerja provinsi
    if ($this->provinsi_kerja) {
        $feeProvinsi = FeeEnumerator::aktif()
            ->provinsi($this->provinsi_kerja)
            ->latest()
            ->first();
        if ($feeProvinsi) return $feeProvinsi;
    }

    // Fallback ke Global
    return FeeEnumerator::aktif()->global()->latest()->first();
}
```

---

## Service Layer

**File:** `app/Services/Superadmin/FeeEnumeratorService.php`

**Method yang dibutuhkan:**

```php
// store(array $data): FeeEnumerator
// - Validasi: jika skala Global, pastikan tidak ada Global aktif
//   dengan tipe_fee yang sama (atau nonaktifkan yang lama)
// - Jika skala Provinsi, pastikan tidak ada record Provinsi aktif
//   untuk provinsi + tipe_fee yang sama
// - Simpan record baru

// update(FeeEnumerator $fee, array $data): FeeEnumerator
// - Update data fee
// - Jika skala atau provinsi berubah, re-validasi uniqueness

// toggleAktif(FeeEnumerator $fee): FeeEnumerator
// - Flip is_aktif (aktif ↔ nonaktif)
// - Return updated record

// delete(FeeEnumerator $fee): void
// - Soft check: jika ini satu-satunya fee Global aktif, tolak hapus
//   (lempar exception dengan pesan jelas)
// - Hard delete

// resolveForKoordinator(Koordinator $koordinator): ?FeeEnumerator
// - Implementasi logika prioritas (Provinsi > Global)
```

---

## Form Request

**File:** `app/Http/Requests/FeeEnumeratorRequest.php`

```php
public function rules(): array
{
    return [
        'skala'       => ['required', 'in:Global,Provinsi'],
        'provinsi'    => [
            Rule::requiredIf(fn() => $this->input('skala') === 'Provinsi'),
            'nullable', 'string',
        ],
        'tipe_fee'    => ['required', 'in:Bulan,Target'],
        'target_data' => [
            Rule::requiredIf(fn() => $this->input('tipe_fee') === 'Target'),
            'nullable', 'integer', 'min:1',
        ],
        'nominal_fee' => ['required', 'integer', 'min:0'],
        'is_aktif'    => ['boolean'],
        'keterangan'  => ['nullable', 'string', 'max:500'],
    ];
}
```

---

## Controller

**File:** `app/Http/Controllers/Superadmin/FeeEnumeratorController.php`

```php
// index()  → view listing (DataTables)
// data()   → JSON DataTables endpoint
// create() → view form tambah
// store()  → simpan via service, redirect + flash success
// edit()   → view form edit
// update() → update via service, redirect + flash success
// destroy()→ AJAX JsonResponse, delete via service
// toggleAktif() → AJAX JsonResponse, toggle is_aktif via service
```

---

## Routes

Tambahkan di route group superadmin (dan/atau admin_umum sesuai kebutuhan):

```php
Route::prefix('fee-enumerator')->name('fee-enumerator.')->group(function () {
    Route::get('/',                   [FeeEnumeratorController::class, 'index'])->name('index');
    Route::get('/data',               [FeeEnumeratorController::class, 'data'])->name('data');
    Route::get('/create',             [FeeEnumeratorController::class, 'create'])->name('create');
    Route::post('/',                  [FeeEnumeratorController::class, 'store'])->name('store');
    Route::get('/{feeEnumerator}/edit',   [FeeEnumeratorController::class, 'edit'])->name('edit');
    Route::put('/{feeEnumerator}',        [FeeEnumeratorController::class, 'update'])->name('update');
    Route::delete('/{feeEnumerator}',     [FeeEnumeratorController::class, 'destroy'])->name('destroy');
    Route::post('/{feeEnumerator}/toggle',[FeeEnumeratorController::class, 'toggleAktif'])->name('toggle');
});
```

---

## Views

### `index.blade.php`

**Header tabel DataTables:**

| # | Skala | Tipe Fee | Nominal | Keterangan | Status | Aksi |
|---|---|---|---|---|---|---|

**Badge skala:**
- `Global` → `adm-badge-info`
- `Provinsi` → `adm-badge-indigo` + nama provinsi

**Badge tipe fee:**
- `Bulan` → `adm-badge-cyan` + "/ Bulan"
- `Target` → `adm-badge-pending` + "/ N data"

**Badge status aktif:**
- `is_aktif = true`  → `adm-badge-success` "Aktif"
- `is_aktif = false` → `adm-badge-nonaktif` "Nonaktif"

**Aksi per baris:**
- Toggle aktif/nonaktif → AJAX `toggleAktif(hashedId)` tanpa konfirmasi
- Edit → link ke halaman edit
- Hapus → `confirmDelete(hashedId, label)` dengan SweetAlert2

---

### `create.blade.php` & `edit.blade.php`

Satu `adm-form-section` dengan 2 bagian:

**Konfigurasi Fee**
```
adm-form-grid cols-2:
  [skala — select: Global / Provinsi]   [provinsi — select cascade (conditional)]
  [tipe_fee — select: Bulan / Target]   [target_data — number (conditional)]

adm-form-grid cols-1:
  [nominal_fee — number, label "Nominal Fee (Rp)"]
  [keterangan — textarea, optional]
```

**Status**
```
adm-form-grid cols-1:
  [is_aktif — toggle/checkbox, default: checked]
```

**JS Conditional:**
- `provinsi` muncul hanya jika `skala = Provinsi`
- `target_data` muncul hanya jika `tipe_fee = Target`

---

### Tampilan di `show.blade.php` Koordinator

Di sidebar kiri koordinator, tambahkan blok **Fee Aktif**:

```blade
{{-- Fee Aktif dari FeeEnumerator --}}
@php $feeAktif = $koordinator->fee_aktif; @endphp
<div class="adm-info-row">
    <span class="adm-info-key">Fee Aktif</span>
    <span class="adm-info-val">
        @if($feeAktif)
            <span class="adm-mono">Rp {{ number_format($feeAktif->nominal_fee, 0, ',', '.') }}</span>
            <small style="display:block;color:var(--adm-text-muted);">
                {{ $feeAktif->tipe_fee === 'Target'
                    ? '/ '.$feeAktif->target_data.' data'
                    : '/ Bulan' }}
                · <em>{{ $feeAktif->skala }}</em>
            </small>
        @else
            <span style="color:var(--adm-text-faint)">Belum ada fee</span>
        @endif
    </span>
</div>
```

---

## Navigasi Sidebar

Tambahkan menu **Fee Enumerator** di sidebar `navigation.blade.php` di bawah menu Koordinator:

```html
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs($routePrefix.'.fee-enumerator.*') ? 'active' : '' }}"
       href="{{ route($routePrefix.'.fee-enumerator.index') }}">
        <i class="ri-money-dollar-circle-line"></i>
        <span>Fee Enumerator</span>
    </a>
</li>
```

---

## Checklist Modul Fee Enumerator

- [ ] Migration `create_fee_enumerators_table` dibuat & dijalankan
- [ ] Model `FeeEnumerator` dibuat (HasHashedId, scopes: aktif/global/provinsi)
- [ ] Accessor `fee_aktif` ditambahkan ke model `Koordinator`
- [ ] `FeeEnumeratorService` dibuat (store, update, toggleAktif, delete, resolveForKoordinator)
- [ ] `FeeEnumeratorRequest` dibuat dengan conditional rules
- [ ] `FeeEnumeratorController` dibuat (thin, inject service)
- [ ] Routes didaftarkan di group superadmin
- [ ] `index.blade.php` — DataTables server-side, badge skala/tipe/status
- [ ] `create.blade.php` & `edit.blade.php` — form dengan field conditional
- [ ] JS: toggle provinsi (show/hide) berfungsi
- [ ] JS: toggle target_data (show/hide) berfungsi
- [ ] AJAX toggleAktif berfungsi (tanpa reload halaman)
- [ ] AJAX confirmDelete berfungsi
- [ ] SweetAlert2 Toast untuk semua notifikasi
- [ ] Fee aktif tampil di `show.blade.php` Koordinator
- [ ] Menu sidebar ditambahkan
- [ ] `hashed_id` digunakan di semua route & URL
