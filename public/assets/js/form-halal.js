/**
 * form-halal.js  — Robust Edition + Image Compression
 *
 * Perbaikan utama:
 * 1. Toast system mandiri (tidak bergantung SweetAlert2) — tampil di pojok kanan atas
 *    dengan animasi slide-in, progress bar, icon, dan tombol close.
 * 2. Diagnosa upload yang jauh lebih detail: cek MIME, ukuran, HTTP status, body.
 * 3. Upload foto_ktp sekarang memakai endpoint /upload/foto_ktp secara eksplisit
 *    dan fieldName "foto_ktp" — bukan "foto_produk". Endpoint per-jenis foto
 *    didefinisikan di UPLOAD_ENDPOINTS sehingga mudah diubah.
 * 4. Setiap fetch upload dilengkapi timeout 60 detik agar tidak hang.
 * 5. Retry otomatis 1× jika server mengembalikan 5xx.
 * 6. Modal progress menampilkan spinner animasi per step.
 * 7. Validasi form lebih ketat sebelum upload dimulai.
 * 8. [NEU] Kompresi gambar otomatis via Canvas API sebelum upload.
 *    - Target ukuran: 800KB (foto_ktp: 1200KB)
 *    - Lebar maksimal: 1280px (foto_ktp: 1920px)
 *    - Iterasi kualitas JPEG dari 0.85 turun ke 0.30
 *    - Fallback ke file asli jika kompresi gagal
 */

document.addEventListener("DOMContentLoaded", function () {
    /* ================================================================
       UPLOAD ENDPOINT MAP
       Kunci = name/id elemen <input type="file">
       Nilai = { endpoint, fieldName }
       Ubah di sini jika route Laravel berubah.
    ================================================================ */
    const UPLOAD_ENDPOINTS = {
        foto_ktp: { endpoint: "/upload/foto_ktp", fieldName: "foto_ktp" },
        foto_rumah: { endpoint: "/upload/foto_rumah", fieldName: "foto_rumah" },
        foto_pendamping: {
            endpoint: "/upload/foto_pendamping",
            fieldName: "foto_pendamping",
        },
        foto_produk: {
            endpoint: "/upload/foto_produk",
            fieldName: "foto_produk",
        },
    };

    // Konfigurasi kompresi per jenis foto
    // targetSizeKB: target ukuran hasil kompresi
    // maxWidthPx  : lebar maksimal gambar
    const COMPRESS_CONFIG = {
        foto_ktp: { targetSizeKB: 1200, maxWidthPx: 1920 }, // KTP butuh resolusi lebih tinggi
        foto_rumah: { targetSizeKB: 800, maxWidthPx: 1280 },
        foto_pendamping: { targetSizeKB: 800, maxWidthPx: 1280 },
        foto_produk: { targetSizeKB: 800, maxWidthPx: 1280 },
        dynamic: { targetSizeKB: 800, maxWidthPx: 1280 }, // slot produk tambahan
    };

    // Foto produk tambahan (slot 2–5) semua pakai endpoint foto_produk
    const DYNAMIC_FOTO_ENDPOINT = "/upload/foto_produk";
    const DYNAMIC_FOTO_FIELDNAME = "foto_produk";

    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB
    const MAX_PRODUK = 5;
    const PRODUK_SLOTS = [2, 3, 4, 5];

    /* ================================================================
       ELEMENT REFERENCES
    ================================================================ */
    const form = document.getElementById("formDataLapangan");
    const submitBtn = document.getElementById("submitBtn");
    const nikInput = document.getElementById("nik");
    const nikCounter = document.getElementById("nikCounter");
    const nikStatus = document.getElementById("nikStatus");
    const namaPuInput = document.getElementById("nama_pu");
    const enumeratorSearch = document.getElementById("enumerator_search");
    const enumeratorSelect = document.getElementById("enumerator_id");
    const searchResults = document.getElementById("search_results");
    const selectedEnumerator = document.getElementById("selected_enumerator");
    const selectedName = document.getElementById("selected_name");
    const alertTidakAktif = document.getElementById("alert_tidak_aktif");
    const namaTidakAktif = document.getElementById("nama_tidak_aktif");
    const formFields = document.getElementById("formFields");
    const searchContainer = enumeratorSearch?.parentElement;
    const produkList = document.getElementById("produkTambahanList");
    const btnAddProduk = document.getElementById("btnAddProduk");
    const maxProdukNotice = document.getElementById("maxProdukNotice");

    if (!nikInput || !form || !submitBtn || !namaPuInput) {
        console.error("[form-halal] Elemen form utama tidak ditemukan.");
        return;
    }

    const statusMap =
        typeof enumeratorStatusMap !== "undefined" ? enumeratorStatusMap : {};

    /* ================================================================
       TOAST NOTIFICATION SYSTEM
       Mandiri — tidak bergantung SweetAlert2 maupun Bootstrap.
    ================================================================ */
    (function injectToastStyles() {
        if (document.getElementById("fh-toast-styles")) return;
        const s = document.createElement("style");
        s.id = "fh-toast-styles";
        s.textContent = `
        #fh-toast-container{
            position:fixed;top:20px;right:20px;z-index:99999;
            display:flex;flex-direction:column;gap:10px;pointer-events:none;
        }
        .fh-toast{
            pointer-events:all;
            display:flex;align-items:flex-start;gap:12px;
            min-width:300px;max-width:420px;
            background:#fff;border-radius:12px;
            box-shadow:0 8px 32px rgba(0,0,0,.14);
            padding:14px 16px;position:relative;overflow:hidden;
            animation:fhSlideIn .32s cubic-bezier(.16,1,.3,1) both;
            border-left:4px solid #ccc;
        }
        .fh-toast.leaving{animation:fhSlideOut .28s cubic-bezier(.4,0,1,1) forwards;}
        .fh-toast.success{border-left-color:#059669;}
        .fh-toast.error  {border-left-color:#EF4444;}
        .fh-toast.warning{border-left-color:#F59E0B;}
        .fh-toast.info   {border-left-color:#1A5FC8;}
        .fh-toast-icon{flex-shrink:0;width:22px;height:22px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;margin-top:1px;}
        .fh-toast.success .fh-toast-icon{background:#D1FAE5;color:#059669;}
        .fh-toast.error   .fh-toast-icon{background:#FEE2E2;color:#EF4444;}
        .fh-toast.warning .fh-toast-icon{background:#FEF3C7;color:#F59E0B;}
        .fh-toast.info    .fh-toast-icon{background:#DBEAFE;color:#1A5FC8;}
        .fh-toast-body{flex:1;min-width:0;}
        .fh-toast-title{font-size:13.5px;font-weight:700;color:#0F1F40;margin-bottom:2px;font-family:'Sora',sans-serif;}
        .fh-toast-msg{font-size:12.5px;color:#6B7A99;line-height:1.5;word-break:break-word;}
        .fh-toast-close{
            background:none;border:none;cursor:pointer;
            color:#B0BCCE;font-size:16px;line-height:1;padding:0;
            transition:color .15s;flex-shrink:0;
        }
        .fh-toast-close:hover{color:#0F1F40;}
        .fh-toast-bar{
            position:absolute;bottom:0;left:0;height:3px;
            border-radius:0 0 0 0;
        }
        .fh-toast.success .fh-toast-bar{background:#059669;}
        .fh-toast.error   .fh-toast-bar{background:#EF4444;}
        .fh-toast.warning .fh-toast-bar{background:#F59E0B;}
        .fh-toast.info    .fh-toast-bar{background:#1A5FC8;}
        @keyframes fhSlideIn{
            from{opacity:0;transform:translateX(60px);}
            to  {opacity:1;transform:translateX(0);}
        }
        @keyframes fhSlideOut{
            from{opacity:1;transform:translateX(0);}
            to  {opacity:0;transform:translateX(60px);}
        }
        @keyframes fhBarShrink{from{width:100%;}to{width:0%;}}
        `;
        document.head.appendChild(s);
    })();

    function getToastContainer() {
        let c = document.getElementById("fh-toast-container");
        if (!c) {
            c = document.createElement("div");
            c.id = "fh-toast-container";
            document.body.appendChild(c);
        }
        return c;
    }

    /**
     * showToast(type, title, message, duration)
     * type: "success" | "error" | "warning" | "info"
     */
    function showToast(type, title, message, duration) {
        // Backward-compat: showToast("error", "pesan") — title=type, message=title
        if (message === undefined) {
            message = title;
            title =
                {
                    success: "Berhasil",
                    error: "Gagal",
                    warning: "Perhatian",
                    info: "Info",
                }[type] || "Notifikasi";
        }
        duration = duration || (type === "error" ? 7000 : 4000);

        const icons = {
            success: `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`,
            error: `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
            warning: `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
            info: `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`,
        };

        const toast = document.createElement("div");
        toast.className = `fh-toast ${type}`;
        toast.innerHTML = `
            <div class="fh-toast-icon">${icons[type] || icons.info}</div>
            <div class="fh-toast-body">
                <div class="fh-toast-title">${title}</div>
                <div class="fh-toast-msg">${message}</div>
            </div>
            <button class="fh-toast-close" aria-label="Tutup">&#x2715;</button>
            <div class="fh-toast-bar"
                 style="animation:fhBarShrink ${duration}ms linear forwards;"></div>
        `;

        const container = getToastContainer();
        container.appendChild(toast);

        function dismiss() {
            toast.classList.add("leaving");
            toast.addEventListener("animationend", () => toast.remove(), {
                once: true,
            });
        }

        toast
            .querySelector(".fh-toast-close")
            .addEventListener("click", dismiss);
        setTimeout(dismiss, duration);
        return toast;
    }

    /* ================================================================
       CSRF TOKEN
    ================================================================ */
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]')?.content;
        if (meta) return meta;
        const inp = document.querySelector('input[name="_token"]')?.value;
        if (inp) return inp;
        const row = document.cookie
            .split("; ")
            .find((r) => r.startsWith("XSRF-TOKEN="));
        if (row) return decodeURIComponent(row.split("=")[1]);
        console.warn("[form-halal] CSRF token tidak ditemukan.");
        return "";
    }

    /* ================================================================
       AUTO-HIDE SESSION ALERTS
    ================================================================ */
    setTimeout(() => {
        ["alertSuccess", "alertError"].forEach((id) => {
            const el = document.getElementById(id);
            if (!el) return;
            el.style.transition = "opacity 0.4s";
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 400);
        });
    }, 5000);

    /* ================================================================
       NAMA PU — AUTO UPPERCASE
    ================================================================ */
    namaPuInput.addEventListener("input", function () {
        const p = this.selectionStart;
        this.value = this.value.toUpperCase();
        try {
            this.setSelectionRange(p, p);
        } catch (_) {}
    });
    namaPuInput.addEventListener("paste", function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData(
            "text",
        );
        const s = this.selectionStart,
            end = this.selectionEnd;
        this.value =
            this.value.substring(0, s) +
            pasted.toUpperCase() +
            this.value.substring(end);
        this.setSelectionRange(s + pasted.length, s + pasted.length);
    });

    /* ================================================================
       TELEPHONE — digits only
    ================================================================ */
    const telInput = document.getElementById("telephone");
    if (telInput) {
        telInput.addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, "").slice(0, 15);
        });
    }

    /* ================================================================
       NIK
    ================================================================ */
    let nikCheckTimeout;

    function updateNikCounter(length) {
        if (!nikCounter || !nikStatus) return;
        nikCounter.textContent = `${length}/16 digit`;
        if (length === 16) {
            nikStatus.textContent = "Lengkap ✓";
            nikStatus.className = "fh-nik-status ok";
            nikInput.classList.remove("is-invalid");
        } else {
            nikStatus.textContent =
                length > 0 ? "Belum lengkap" : "Belum diisi";
            nikStatus.className = "fh-nik-status err";
        }
    }

    function checkNikExists(nik) {
        if (nik.length !== 16) return;
        const existing = document.getElementById("nikExistsWarning");
        if (existing) existing.remove();

        const loadingDiv = document.createElement("div");
        loadingDiv.id = "nikExistsWarning";
        loadingDiv.className = "mt-2";
        loadingDiv.innerHTML =
            '<small class="fh-hint">Memeriksa NIK...</small>';
        nikInput.parentElement.appendChild(loadingDiv);

        fetch("/api/check-nik", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({ nik }),
        })
            .then((r) => r.json())
            .then((data) => {
                const div = document.getElementById("nikExistsWarning");
                if (data.exists) {
                    if (div)
                        div.innerHTML = `
                    <div class="alert-danger-modern" role="alert" style="margin-top:6px;">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#EF4444" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8"  x2="12"   y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <div class="alt-text">
                            <strong>NIK sudah terdaftar.</strong>
                            Pastikan produk yang didaftarkan berbeda.
                        </div>
                    </div>`;
                    showToast(
                        "warning",
                        "NIK Sudah Terdaftar",
                        "NIK ini sudah ada di sistem. Pastikan produk yang didaftarkan berbeda.",
                    );
                } else {
                    if (div)
                        div.innerHTML =
                            '<small class="fh-hint" style="color:#059669;">✓ NIK belum terdaftar</small>';
                }
            })
            .catch(() => {
                const div = document.getElementById("nikExistsWarning");
                if (div) div.remove();
            });
    }

    nikInput.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, "").slice(0, 16);
        updateNikCounter(this.value.length);
        nikInput.classList.remove("is-valid", "is-invalid");
        const w = document.getElementById("nikExistsWarning");
        if (w) w.remove();
        clearTimeout(nikCheckTimeout);
        if (this.value.length === 16) {
            nikCheckTimeout = setTimeout(() => checkNikExists(this.value), 500);
        }
    });
    nikInput.addEventListener("keypress", function (e) {
        if (e.key < "0" || e.key > "9") e.preventDefault();
    });
    nikInput.addEventListener("paste", function (e) {
        e.preventDefault();
        const num = (e.clipboardData || window.clipboardData)
            .getData("text")
            .replace(/\D/g, "")
            .slice(0, 16);
        this.value = num;
        this.dispatchEvent(new Event("input", { bubbles: true }));
    });
    if (nikInput.value) updateNikCounter(nikInput.value.length);

    /* ================================================================
       ENUMERATOR SEARCH
    ================================================================ */
    function lockForm() {
        if (!formFields) return;
        formFields
            .querySelectorAll("input,textarea,select,button[type='submit']")
            .forEach((el) => (el.disabled = true));
        formFields.classList.add("fh-form-locked");
    }
    function unlockForm() {
        if (!formFields) return;
        formFields
            .querySelectorAll("input,textarea,select,button[type='submit']")
            .forEach((el) => (el.disabled = false));
        formFields.classList.remove("fh-form-locked");
    }

    function checkEnumeratorStatus(id, nama) {
        if (!id) {
            if (selectedEnumerator) selectedEnumerator.style.display = "none";
            if (alertTidakAktif) alertTidakAktif.style.display = "none";
            unlockForm();
            return;
        }
        if (selectedName) selectedName.textContent = nama;
        if (selectedEnumerator) selectedEnumerator.style.display = "block";

        if (statusMap[id] === "Tidak Aktif") {
            if (namaTidakAktif) namaTidakAktif.textContent = nama;
            if (alertTidakAktif) alertTidakAktif.style.display = "block";
            lockForm();
            showToast(
                "warning",
                "Pendamping Tidak Aktif",
                `${nama} tidak dapat digunakan saat ini. Pilih pendamping lain.`,
            );
        } else {
            if (alertTidakAktif) alertTidakAktif.style.display = "none";
            unlockForm();
        }
    }

    if (enumeratorSearch) {
        enumeratorSearch.addEventListener("input", function () {
            const term = this.value.toLowerCase().trim();
            if (!term) {
                searchResults.style.display = "none";
                return;
            }

            const opts = Array.from(enumeratorSelect.options)
                .slice(1)
                .filter((o) => o.text.toLowerCase().includes(term));

            searchResults.innerHTML = opts.length
                ? opts
                      .map(
                          (o) => `
                    <div class="fh-search-item"
                         onclick="selectEnumerator('${o.value}','${o.text}','${o.dataset.status || ""}')">
                        <span class="item-name">${o.text}</span>
                        <span class="item-badge ${o.dataset.status === "Aktif" ? "aktif" : "tidak"}">
                            ${o.dataset.status || ""}
                        </span>
                    </div>`,
                      )
                      .join("")
                : '<div style="padding:12px 14px;font-size:13px;color:#8A99B3;text-align:center;">Tidak ada hasil</div>';

            searchResults.style.display = "block";
        });
    }

    document.addEventListener("click", function (e) {
        if (
            !enumeratorSearch?.contains(e.target) &&
            !searchResults?.contains(e.target)
        ) {
            if (searchResults) searchResults.style.display = "none";
        }
    });

    window.selectEnumerator = function (id, nama) {
        enumeratorSelect.value = id;
        if (enumeratorSearch) enumeratorSearch.value = "";
        if (searchResults) searchResults.style.display = "none";
        if (searchContainer) searchContainer.style.display = "none";
        checkEnumeratorStatus(id, nama);
    };
    window.clearEnumeratorSelection = function () {
        enumeratorSelect.value = "";
        if (selectedEnumerator) selectedEnumerator.style.display = "none";
        if (alertTidakAktif) alertTidakAktif.style.display = "none";
        if (enumeratorSearch) enumeratorSearch.value = "";
        if (searchContainer) searchContainer.style.display = "block";
        if (enumeratorSearch) enumeratorSearch.focus();
        unlockForm();
    };

    if (enumeratorSelect?.value) {
        const opt = enumeratorSelect.options[enumeratorSelect.selectedIndex];
        checkEnumeratorStatus(enumeratorSelect.value, opt?.text || "");
        if (searchContainer) searchContainer.style.display = "none";
    }

    /* ================================================================
       PRODUK TAMBAHAN (slot 2–5)
    ================================================================ */
    let activeSlots = [];

    function getNextSlot() {
        for (const s of PRODUK_SLOTS) {
            if (!activeSlots.includes(s)) return s;
        }
        return null;
    }
    function refreshAddButton() {
        const isFull = activeSlots.length >= MAX_PRODUK - 1;
        if (btnAddProduk) btnAddProduk.disabled = isFull;
        if (maxProdukNotice)
            maxProdukNotice.style.display = isFull ? "block" : "none";
    }

    function addProdukSlot() {
        const slot = getNextSlot();
        if (!slot) return;
        activeSlots.push(slot);

        const item = document.createElement("div");
        item.className = "fh-produk-item";
        item.dataset.slot = slot;
        item.innerHTML = `
            <div class="fh-produk-item-header">
                <div class="fh-produk-item-title">
                    <div class="produk-num">${slot}</div>
                    Produk Tambahan
                </div>
                <button type="button" class="fh-btn-remove" data-slot="${slot}">
                    <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Hapus
                </button>
            </div>
            <div class="fh-produk-item-grid">
                <div class="fh-field" style="margin-bottom:0;">
                    <label class="fh-label" for="nama_produk_${slot}">
                        Nama Produk ${slot} <span class="req">*</span>
                    </label>
                    <input type="text" id="nama_produk_${slot}" name="nama_produk_${slot}"
                           class="fh-input" placeholder="Nama produk ${slot}" required>
                </div>
                <div class="fh-field" style="margin-bottom:0;">
                    <label class="fh-label" for="foto_produk_${slot}_file">
                        Foto Produk ${slot} <span class="req">*</span>
                    </label>
                    <input type="hidden" id="foto_produk_${slot}_path"
                           name="foto_produk_${slot}_path" value="">
                    <input type="file" id="foto_produk_${slot}_file"
                           class="fh-file-input"
                           accept="image/jpeg,image/jpg,image/png" data-slot="${slot}">
                    <span class="fh-hint" id="upload_status_${slot}">Belum dipilih</span>
                </div>
            </div>`;

        item.querySelector(".fh-btn-remove").addEventListener(
            "click",
            function () {
                const s = parseInt(this.dataset.slot);
                activeSlots = activeSlots.filter((x) => x !== s);
                item.style.transition = "opacity .2s, transform .2s";
                item.style.opacity = "0";
                item.style.transform = "translateY(-8px)";
                setTimeout(() => {
                    item.remove();
                    refreshAddButton();
                }, 220);
            },
        );
        item.querySelector(`#foto_produk_${slot}_file`).addEventListener(
            "change",
            function () {
                validateFilePicker(this, slot);
            },
        );

        produkList.appendChild(item);
        refreshAddButton();
    }

    function validateFilePicker(fileInput, slot) {
        const file = fileInput.files[0];
        const statusSpan = document.getElementById(`upload_status_${slot}`);
        const pathInput = document.getElementById(`foto_produk_${slot}_path`);
        if (pathInput) pathInput.value = "";

        if (!file) {
            statusSpan.textContent = "Belum dipilih";
            statusSpan.style.color = "";
            return;
        }
        if (file.size > MAX_FILE_SIZE) {
            fileInput.value = "";
            fileInput.classList.add("is-invalid");
            statusSpan.textContent = "✗ File terlalu besar (maks 10MB)";
            statusSpan.style.color = "#EF4444";
            showToast(
                "error",
                "File Terlalu Besar",
                `Foto produk ${slot} melebihi 10MB.`,
            );
            return;
        }
        if (!["image/jpeg", "image/jpg", "image/png"].includes(file.type)) {
            fileInput.value = "";
            fileInput.classList.add("is-invalid");
            statusSpan.textContent = "✗ Format tidak didukung";
            statusSpan.style.color = "#EF4444";
            showToast(
                "error",
                "Format Salah",
                `Foto produk ${slot} harus JPG atau PNG.`,
            );
            return;
        }
        fileInput.classList.remove("is-invalid");
        statusSpan.textContent = `✓ ${file.name} (${(file.size / 1024 / 1024).toFixed(1)} MB)`;
        statusSpan.style.color = "#059669";
    }

    if (btnAddProduk) btnAddProduk.addEventListener("click", addProdukSlot);

    /* ================================================================
       VALIDASI FILE FOTO UTAMA (statis)
    ================================================================ */
    ["foto_ktp", "foto_rumah", "foto_pendamping", "foto_produk"].forEach(
        (id) => {
            const inp = document.getElementById(id);
            if (!inp) return;
            inp.addEventListener("change", function () {
                const file = this.files[0];
                if (!file) return;
                const label = id
                    .replace(/_/g, " ")
                    .replace(/\b\w/g, (c) => c.toUpperCase());
                if (file.size > MAX_FILE_SIZE) {
                    showToast(
                        "error",
                        "File Terlalu Besar",
                        `${label} melebihi batas 10MB. Kompres dahulu.`,
                    );
                    this.value = "";
                    this.classList.add("is-invalid");
                    return;
                }
                if (
                    !["image/jpeg", "image/jpg", "image/png"].includes(
                        file.type,
                    )
                ) {
                    showToast(
                        "error",
                        "Format Tidak Didukung",
                        `${label} harus berformat JPG atau PNG.`,
                    );
                    this.value = "";
                    this.classList.add("is-invalid");
                    return;
                }
                this.classList.remove("is-invalid");

                // Tampilkan nama file kecil di bawah input
                const hint = this.nextElementSibling;
                if (hint && hint.classList.contains("fh-hint")) {
                    hint.textContent = `✓ ${file.name} (${(file.size / 1024 / 1024).toFixed(1)} MB)`;
                    hint.style.color = "#059669";
                }
            });
        },
    );

    /* ================================================================
       UPLOAD MODAL
    ================================================================ */
    const uploadModalHTML = `
    <div class="modal fade" id="uploadProgressModal"
         data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;">
                <div class="modal-header border-0" style="padding:1.5rem 1.5rem 0.5rem;">
                    <h5 class="modal-title"
                        style="font-family:'Sora',sans-serif;font-size:16px;color:#0F1F40;display:flex;align-items:center;gap:8px;">
                        <svg id="modalSpinner" width="18" height="18" viewBox="0 0 24 24" fill="none"
                             stroke="#1A5FC8" stroke-width="2.5"
                             style="animation:fhSpin 1s linear infinite;">
                            <circle cx="12" cy="12" r="10" stroke-opacity=".25"/>
                            <path d="M12 2 a10 10 0 0 1 10 10" stroke-linecap="round"/>
                        </svg>
                        Mengupload Data
                    </h5>
                </div>
                <div class="modal-body" style="padding:1rem 1.5rem 1.5rem;">
                    <div id="uploadSteps"></div>
                    <div class="mt-3">
                        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                            <small style="color:#8A99B3;" id="currentUploadText">Mempersiapkan...</small>
                            <small style="color:#8A99B3;" id="uploadPercentage">0%</small>
                        </div>
                        <div style="height:8px;background:#EDF0F7;border-radius:99px;overflow:hidden;">
                            <div id="uploadProgressBar"
                                 style="height:100%;width:0%;background:linear-gradient(90deg,#1A5FC8,#1040A0);
                                        border-radius:99px;transition:width 0.35s ease;"></div>
                        </div>
                    </div>
                    <div style="text-align:center;margin-top:12px;">
                        <small style="color:#B0BCCE;font-size:11.5px;">
                            ⚠ Jangan tutup atau refresh halaman ini
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>`;

    if (!document.getElementById("uploadProgressModal")) {
        document.body.insertAdjacentHTML("beforeend", uploadModalHTML);
    }

    // Inject spinner keyframes
    if (!document.getElementById("fh-spinner-style")) {
        const ss = document.createElement("style");
        ss.id = "fh-spinner-style";
        ss.textContent = `@keyframes fhSpin{to{transform:rotate(360deg);}}`;
        document.head.appendChild(ss);
    }

    const STATIC_STEPS = [
        { name: "foto_ktp", label: "Foto KTP" },
        { name: "foto_rumah", label: "Foto Rumah" },
        { name: "foto_pendamping", label: "Foto Pendamping" },
        { name: "foto_produk", label: "Foto Produk 1" },
    ];

    function buildUploadStepsHTML(steps) {
        return steps
            .map(
                (s) => `
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;"
                 id="step-${s.name}">
                <div id="step-icon-${s.name}"
                     style="width:22px;height:22px;display:flex;align-items:center;
                            justify-content:center;flex-shrink:0;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="#CBD5E1" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                    </svg>
                </div>
                <div style="flex:1;font-size:13px;color:#6B7A99;" id="step-label-${s.name}">
                    ${s.label}
                </div>
                <div id="status-${s.name}" style="font-size:11px;color:#B0BCCE;">—</div>
            </div>`,
            )
            .join("");
    }

    function setStepStatus(name, status, detail) {
        const iconEl = document.getElementById(`step-icon-${name}`);
        const statusEl = document.getElementById(`status-${name}`);
        const labelEl = document.getElementById(`step-label-${name}`);
        if (!iconEl || !statusEl) return;

        if (status === "compressing") {
            iconEl.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="#F59E0B" stroke-width="2.5"
                style="animation:fhSpin .8s linear infinite;">
                <circle cx="12" cy="12" r="10" stroke-opacity=".25"/>
                <path d="M12 2 a10 10 0 0 1 10 10" stroke-linecap="round"/>
            </svg>`;
            statusEl.textContent = "Mengompresi...";
            statusEl.style.color = "#F59E0B";
            if (labelEl) labelEl.style.color = "#0F1F40";
        } else if (status === "uploading") {
            iconEl.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="#1A5FC8" stroke-width="2.5"
                style="animation:fhSpin .8s linear infinite;">
                <circle cx="12" cy="12" r="10" stroke-opacity=".25"/>
                <path d="M12 2 a10 10 0 0 1 10 10" stroke-linecap="round"/>
            </svg>`;
            statusEl.textContent = "Mengupload...";
            statusEl.style.color = "#1A5FC8";
            if (labelEl) labelEl.style.color = "#0F1F40";
        } else if (status === "success") {
            iconEl.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="#059669" stroke-width="2.5"><circle cx="12" cy="12" r="10"/>
                <polyline points="9 12 11 14 15 10"/></svg>`;
            statusEl.textContent = detail || "Selesai";
            statusEl.style.color = "#059669";
            if (labelEl) labelEl.style.color = "#059669";
        } else if (status === "skip") {
            iconEl.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="#B0BCCE" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>`;
            statusEl.textContent = "Dilewati";
            statusEl.style.color = "#B0BCCE";
        } else if (status === "error") {
            iconEl.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="#EF4444" stroke-width="2.5"><circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`;
            statusEl.textContent = "Gagal";
            statusEl.style.color = "#EF4444";
            if (labelEl) labelEl.style.color = "#EF4444";
        }
    }

    function setProgress(pct, text) {
        const bar = document.getElementById("uploadProgressBar");
        const pEl = document.getElementById("uploadPercentage");
        const tEl = document.getElementById("currentUploadText");
        if (bar) bar.style.width = pct + "%";
        if (pEl) pEl.textContent = Math.round(pct) + "%";
        if (tEl && text) tEl.textContent = text;
    }

    /* ================================================================
       IMAGE COMPRESSION
       Kompres gambar menggunakan Canvas API sebelum upload.
       targetSizeKB : target ukuran maksimal output dalam KB
       maxWidthPx   : lebar maksimal gambar hasil kompresi
       Mengembalikan File baru (JPEG) yang sudah dikompres.
    ================================================================ */
    async function compressImage(file, targetSizeKB, maxWidthPx) {
        targetSizeKB = targetSizeKB || 800;
        maxWidthPx = maxWidthPx || 1280;

        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onerror = () =>
                reject(new Error("Gagal membaca file untuk kompresi."));
            reader.onload = function (ev) {
                const img = new Image();
                img.onerror = () =>
                    reject(new Error("Gagal memuat gambar untuk kompresi."));
                img.onload = function () {
                    // Hitung dimensi baru proporsional
                    let { width, height } = img;
                    if (width > maxWidthPx) {
                        height = Math.round((height * maxWidthPx) / width);
                        width = maxWidthPx;
                    }

                    const canvas = document.createElement("canvas");
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext("2d");

                    // Latar putih agar PNG transparan tidak jadi hitam saat ke JPEG
                    ctx.fillStyle = "#FFFFFF";
                    ctx.fillRect(0, 0, width, height);
                    ctx.drawImage(img, 0, 0, width, height);

                    // Iterasi kualitas menurun sampai target KB terpenuhi
                    let quality = 0.85;
                    const minQuality = 0.3;
                    const step = 0.07;
                    const targetBytes = targetSizeKB * 1024;

                    function tryCompress() {
                        canvas.toBlob(
                            function (blob) {
                                if (!blob) {
                                    reject(
                                        new Error(
                                            "Canvas gagal menghasilkan blob.",
                                        ),
                                    );
                                    return;
                                }
                                if (
                                    blob.size <= targetBytes ||
                                    quality <= minQuality
                                ) {
                                    // Bungkus blob menjadi File agar nama & type terbawa
                                    const baseName = file.name.replace(
                                        /\.[^.]+$/,
                                        "",
                                    );
                                    const compressed = new File(
                                        [blob],
                                        `${baseName}.jpg`,
                                        {
                                            type: "image/jpeg",
                                            lastModified: Date.now(),
                                        },
                                    );
                                    resolve(compressed);
                                } else {
                                    quality = Math.max(
                                        quality - step,
                                        minQuality,
                                    );
                                    tryCompress();
                                }
                            },
                            "image/jpeg",
                            quality,
                        );
                    }

                    tryCompress();
                };
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    /* ================================================================
       FETCH WITH TIMEOUT + RETRY
    ================================================================ */
    async function fetchWithTimeout(url, options, timeoutMs) {
        timeoutMs = timeoutMs || 60000;
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), timeoutMs);
        try {
            const res = await fetch(url, {
                ...options,
                signal: controller.signal,
            });
            return res;
        } finally {
            clearTimeout(timer);
        }
    }

    /**
     * uploadSingleFile — kompres lalu upload satu file, retry sekali jika 5xx.
     * Mengembalikan path (string) jika sukses, throw Error jika gagal.
     */
    async function uploadSingleFile(step, token) {
        const file = step.fileInput?.files[0];
        if (!file) return null; // tidak ada file → skip

        // --- Pre-flight validasi lokal ---
        if (file.size > MAX_FILE_SIZE) {
            throw new Error(
                `File Terlalu Besar — ${step.label}\n` +
                    `Ukuran file: ${(file.size / 1024 / 1024).toFixed(1)} MB, batas 10 MB.\n` +
                    `Kompres gambar terlebih dahulu, lalu coba lagi.`,
            );
        }
        if (!["image/jpeg", "image/jpg", "image/png"].includes(file.type)) {
            throw new Error(
                `Format Tidak Didukung — ${step.label}\n` +
                    `Tipe file terdeteksi: "${file.type}". Hanya JPG/JPEG/PNG yang diizinkan.`,
            );
        }

        // --- Kompresi gambar sebelum upload ---
        let fileToUpload = file;
        setStepStatus(step.name, "compressing");

        try {
            const cfg = step.compressConfig || COMPRESS_CONFIG.dynamic;
            const originalSize = file.size;
            fileToUpload = await compressImage(
                file,
                cfg.targetSizeKB,
                cfg.maxWidthPx,
            );
            const compressedSize = fileToUpload.size;
            const savedPct = Math.round(
                (1 - compressedSize / originalSize) * 100,
            );

            console.info(
                `[compress] ${step.label}: ` +
                    `${(originalSize / 1024).toFixed(0)}KB → ${(compressedSize / 1024).toFixed(0)}KB ` +
                    `(hemat ${savedPct >= 0 ? savedPct : 0}%)`,
            );
        } catch (compressErr) {
            // Fallback ke file asli — upload tetap dilanjutkan
            console.warn(
                `[compress] Gagal kompres ${step.label}, pakai file asli:`,
                compressErr,
            );
            fileToUpload = file;
        }

        // --- Upload ---
        setStepStatus(step.name, "uploading");

        const fd = new FormData();
        fd.append(step.fieldName, fileToUpload);
        fd.append("_token", token);

        const opts = {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": token,
                "X-Requested-With": "XMLHttpRequest",
            },
            body: fd,
        };

        let res;
        for (let attempt = 1; attempt <= 2; attempt++) {
            try {
                res = await fetchWithTimeout(step.endpoint, opts, 60000);
            } catch (networkErr) {
                if (networkErr.name === "AbortError") {
                    throw new Error(
                        `Timeout — ${step.label}\n` +
                            `Server tidak merespons dalam 60 detik.\n` +
                            `Periksa koneksi internet atau coba lagi nanti.`,
                    );
                }
                throw new Error(
                    `Kesalahan Jaringan — ${step.label}\n` +
                        `${networkErr.message}\n` +
                        `Pastikan koneksi internet stabil.`,
                );
            }

            // Retry jika 5xx pada percobaan pertama
            if (res.status >= 500 && attempt === 1) {
                await new Promise((r) => setTimeout(r, 1500));
                continue;
            }
            break;
        }

        // --- Analisis response ---
        const ct = res.headers.get("content-type") || "";
        if (!ct.includes("application/json")) {
            let snippet = "";
            try {
                snippet = (await res.text()).substring(0, 300);
            } catch (_) {}

            let reason;
            if (res.status === 419) {
                reason =
                    "CSRF token tidak valid atau kedaluwarsa (419).\n" +
                    "Solusi: Muat ulang halaman (F5) lalu coba lagi.\n" +
                    "Pastikan layout blade memiliki:\n" +
                    '<meta name="csrf-token" content="{{ csrf_token() }}">';
            } else if (res.status === 302 || res.status === 301) {
                const loc = res.headers.get("location") || "tidak diketahui";
                reason =
                    `Server melakukan redirect (${res.status}) → ${loc}\n` +
                    "Kemungkinan route upload membutuhkan login.\n" +
                    "Pastikan middleware route adalah 'web' saja (bukan 'auth').";
            } else if (res.status === 401 || res.status === 403) {
                reason =
                    `Akses ditolak (${res.status}).\n` +
                    "Pastikan Anda masih memiliki sesi login yang valid.";
            } else if (res.status === 404) {
                reason =
                    `Route tidak ditemukan (404): ${step.endpoint}\n` +
                    "Pastikan route sudah didaftarkan di routes/web.php.";
            } else if (res.status >= 500) {
                reason =
                    `Server error (${res.status}).\n` +
                    "Periksa log Laravel: storage/logs/laravel.log\n" +
                    "Cek juga storage permission: chmod -R 775 storage/";
            } else {
                reason =
                    `HTTP ${res.status} — server mengembalikan HTML bukan JSON.\n` +
                    `Snippet response:\n${snippet}`;
            }

            throw new Error(`Upload Gagal — ${step.label}\n${reason}`);
        }

        let data;
        try {
            data = await res.json();
        } catch (parseErr) {
            throw new Error(
                `Response Tidak Valid — ${step.label}\n` +
                    `Server mengembalikan JSON yang tidak dapat diparsing.\n` +
                    `Detail: ${parseErr.message}`,
            );
        }

        if (!res.ok || !data.success) {
            throw new Error(
                `Upload Ditolak — ${step.label}\n` +
                    (data.message || `HTTP ${res.status}`),
            );
        }

        return data.path;
    }

    /* ================================================================
       UPLOAD ALL FILES SEQUENTIAL
    ================================================================ */
    async function uploadFilesSequential(formData, allSteps) {
        const token = getCsrfToken();

        for (let i = 0; i < allSteps.length; i++) {
            const step = allSteps[i];
            const file = step.fileInput?.files[0];

            if (!file) {
                setStepStatus(step.name, "skip");
                continue;
            }

            // Progress dihitung dari 0–85% selama upload berlangsung
            // Setiap step mendapat porsi: compress (setengah) + upload (setengah)
            const baseProgress = (i / allSteps.length) * 85;
            setProgress(baseProgress, `Mengompresi ${step.label}...`);

            try {
                const path = await uploadSingleFile(step, token);
                if (path) formData.set(step.pathKey, path);

                const doneProgress = ((i + 1) / allSteps.length) * 85;
                setProgress(doneProgress, `${step.label} selesai`);

                const originalKB = (file.size / 1024).toFixed(0);
                setStepStatus(
                    step.name,
                    "success",
                    `${originalKB}KB → terkompresi`,
                );
            } catch (err) {
                setStepStatus(step.name, "error");
                throw err;
            }
        }
    }

    /* ================================================================
       FORM SUBMIT
    ================================================================ */
    let isSubmitting = false; // Guard untuk mencegah double submit

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        // -- Guard: cegah double submit --
        if (isSubmitting) return;
        isSubmitting = true;

        // -- Validasi enumerator --
        if (!enumeratorSelect.value) {
            isSubmitting = false;
            enumeratorSearch?.classList.add("is-invalid");
            enumeratorSearch?.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
            showToast(
                "error",
                "Pilih Pendamping",
                "Nama pendamping wajib dipilih.",
            );
            return;
        }

        // -- Validasi NIK --
        if (nikInput.value.length !== 16) {
            isSubmitting = false;
            nikInput.classList.add("is-invalid");
            nikInput.scrollIntoView({ behavior: "smooth", block: "center" });
            showToast(
                "error",
                "NIK Tidak Valid",
                "NIK harus tepat 16 digit angka.",
            );
            return;
        }

        // -- Validasi foto statis wajib --
        const staticRequired = [
            "foto_ktp",
            "foto_rumah",
            "foto_pendamping",
            "foto_produk",
        ];
        let staticOk = true;
        for (const id of staticRequired) {
            const inp = document.getElementById(id);
            if (!inp?.files[0]) {
                inp?.classList.add("is-invalid");
                const label = id
                    .replace(/_/g, " ")
                    .replace(/\b\w/g, (c) => c.toUpperCase());
                showToast(
                    "error",
                    "Foto Belum Dipilih",
                    `${label} wajib diisi.`,
                );
                inp?.scrollIntoView({ behavior: "smooth", block: "center" });
                staticOk = false;
                break;
            }
        }
        if (!staticOk) {
            isSubmitting = false;
            return;
        }

        // -- Validasi foto produk tambahan --
        let dynamicOk = true;
        for (const slot of activeSlots) {
            const namaInp = document.getElementById(`nama_produk_${slot}`);
            const fileInp = document.getElementById(`foto_produk_${slot}_file`);
            if (namaInp?.value?.trim() && !fileInp?.files[0]) {
                fileInp?.classList.add("is-invalid");
                const sp = document.getElementById(`upload_status_${slot}`);
                if (sp) {
                    sp.textContent = "✗ Foto wajib dipilih";
                    sp.style.color = "#EF4444";
                }
                dynamicOk = false;
            }
        }
        if (!dynamicOk) {
            isSubmitting = false;
            showToast(
                "error",
                "Foto Produk Tambahan",
                "Setiap produk tambahan harus menyertakan foto.",
            );
            return;
        }

        // -- Disable submit --
        submitBtn.disabled = true;
        submitBtn.setAttribute('aria-disabled', 'true');
        submitBtn.innerHTML = `
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                 stroke="rgba(255,255,255,.85)" stroke-width="2.5"
                 style="animation:fhSpin .9s linear infinite;">
                <circle cx="12" cy="12" r="10" stroke-opacity=".25"/>
                <path d="M12 2 a10 10 0 0 1 10 10" stroke-linecap="round"/>
            </svg> Menyimpan...`;

        // -- Build steps (dengan compressConfig per jenis foto) --
        const staticUploadSteps = STATIC_STEPS.map((s) => ({
            name: s.name,
            label: s.label,
            fileInput: document.getElementById(s.name),
            endpoint: UPLOAD_ENDPOINTS[s.name]?.endpoint || `/upload/${s.name}`,
            fieldName: UPLOAD_ENDPOINTS[s.name]?.fieldName || s.name,
            pathKey: `${s.name}_path`,
            compressConfig: COMPRESS_CONFIG[s.name] || COMPRESS_CONFIG.dynamic,
        }));

        const dynamicUploadSteps = activeSlots.map((slot) => ({
            name: `foto_produk_${slot}`,
            label: `Foto Produk ${slot}`,
            fileInput: document.getElementById(`foto_produk_${slot}_file`),
            endpoint: DYNAMIC_FOTO_ENDPOINT,
            fieldName: DYNAMIC_FOTO_FIELDNAME,
            pathKey: `foto_produk_${slot}_path`,
            compressConfig: COMPRESS_CONFIG.dynamic,
        }));

        const allSteps = [...staticUploadSteps, ...dynamicUploadSteps];

        // -- Tampilkan modal --
        let uploadModal;
        try {
            uploadModal = new bootstrap.Modal(
                document.getElementById("uploadProgressModal"),
            );
            document.getElementById("uploadSteps").innerHTML =
                buildUploadStepsHTML(allSteps);
            uploadModal.show();
        } catch (_) {
            /* bootstrap tidak tersedia */
        }

        // -- Kumpulkan data teks --
        const formData = new FormData();
        formData.append("_token", getCsrfToken());
        formData.append("enumerator_id", enumeratorSelect.value);
        formData.append("nama_pu", namaPuInput.value);
        formData.append("nik", nikInput.value);
        formData.append(
            "telephone",
            document.getElementById("telephone")?.value || "",
        );
        formData.append(
            "nama_produk",
            document.getElementById("nama_produk")?.value || "",
        );
        formData.append(
            "alamat",
            document.getElementById("alamat")?.value || "",
        );

        for (const slot of activeSlots) {
            const v = document
                .getElementById(`nama_produk_${slot}`)
                ?.value?.trim();
            if (v) formData.append(`nama_produk_${slot}`, v);
        }

        try {
            await uploadFilesSequential(formData, allSteps);

            // Selesaikan spinner modal
            const spinner = document.getElementById("modalSpinner");
            if (spinner) {
                spinner.innerHTML = `<circle cx="12" cy="12" r="10" fill="#059669"/>
                    <polyline points="9 12 11 14 15 10" stroke="white" stroke-width="2.5" fill="none"/>`;
                spinner.style.animation = "none";
                spinner.setAttribute("stroke", "#059669");
            }
            document
                .querySelector("#uploadProgressModal .modal-title")
                ?.lastChild?.replaceWith?.(" Data tersimpan!");

            setProgress(100, "Selesai! Mengalihkan...");

            // Build & submit temp form
            const tempForm = document.createElement("form");
            tempForm.method = "POST";
            tempForm.action = form.action;
            tempForm.style.display = "none";

            for (const [key, val] of formData.entries()) {
                const inp = document.createElement("input");
                inp.type = "hidden";
                inp.name = key;
                inp.value = val;
                tempForm.appendChild(inp);
            }
            document.body.appendChild(tempForm);

            showToast(
                "success",
                "Upload Berhasil",
                "Semua file berhasil dikompresi dan diupload. Mengalihkan...",
                3000,
            );

            // isSubmitting tetap true — form asli tidak bisa disubmit lagi
            setTimeout(() => tempForm.submit(), 800);
        } catch (err) {
            const resetBtn = () => {
                isSubmitting = false; // Reset flag agar bisa submit ulang setelah error
                submitBtn.disabled = false;
                submitBtn.removeAttribute('aria-disabled');
                submitBtn.innerHTML = `
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none"
                         stroke="rgba(255,255,255,.85)" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg> Simpan Data`;
            };

            // Parse error message
            const lines = (err.message || "Terjadi kesalahan").split("\n");
            const title = lines[0] || "Upload Gagal";
            const detail = lines.slice(1).join("\n").trim();

            if (uploadModal) {
                const modalEl = document.getElementById("uploadProgressModal");
                modalEl?.addEventListener(
                    "hidden.bs.modal",
                    function onHidden() {
                        modalEl.removeEventListener(
                            "hidden.bs.modal",
                            onHidden,
                        );
                        showUploadError(title, detail);
                        resetBtn();
                    },
                    { once: true },
                );
                uploadModal.hide();
            } else {
                showUploadError(title, detail);
                resetBtn();
            }
        }
    });

    /* ================================================================
       UPLOAD ERROR DIALOG
    ================================================================ */
    function showUploadError(title, detail) {
        // Toast singkat
        showToast(
            "error",
            title,
            detail ? detail.split("\n")[0] : "Cek detail di bawah.",
            8000,
        );

        // SweetAlert2 jika tersedia
        if (typeof Swal !== "undefined") {
            Swal.fire({
                icon: "error",
                title: title,
                html: detail
                    ? `<div style="text-align:left;font-size:13px;line-height:1.6;color:#6B7A99;
                                   background:#F8FAFF;border-radius:8px;padding:12px 14px;
                                   margin-top:8px;white-space:pre-wrap;">${detail}</div>`
                    : undefined,
                confirmButtonText: "Tutup & Coba Lagi",
                confirmButtonColor: "#1A5FC8",
            });
        }
    }

    /* ================================================================
       PAGE CACHE RESET (back button)
    ================================================================ */
    window.addEventListener("pageshow", function (e) {
        if (!e.persisted) return;
        submitBtn.disabled = false;
        submitBtn.innerHTML = `
            <svg viewBox="0 0 24 24" width="17" height="17" fill="none"
                 stroke="rgba(255,255,255,.85)" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg> Simpan Data`;
    });
});
