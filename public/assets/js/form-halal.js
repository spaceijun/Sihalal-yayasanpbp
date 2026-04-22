/**
 * form-halal.js
 * Unified script — menggabungkan form-halal.js lama + inline blade script
 * Fix: upload produk dinamis (nama_produk_2–5 & foto_produk_2–5_path)
 */

document.addEventListener("DOMContentLoaded", function () {
    // ============================================================
    // ELEMENT REFERENCES
    // ============================================================
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

    if (!nikInput || !form || !submitBtn || !namaPuInput) {
        console.error("Required form elements not found");
        return;
    }

    // enumeratorStatusMap di-inject dari blade (tetap di blade sebagai
    // <script>const enumeratorStatusMap = { ... };</script> sebelum tag ini)
    // Fallback jika tidak ada
    const statusMap =
        typeof enumeratorStatusMap !== "undefined" ? enumeratorStatusMap : {};

    // ============================================================
    // CSRF TOKEN helper
    // ============================================================
    function getCsrfToken() {
        return (
            document.querySelector('meta[name="csrf-token"]')?.content ||
            document.querySelector('input[name="_token"]')?.value ||
            ""
        );
    }

    // ============================================================
    // AUTO-HIDE ALERTS (5 detik)
    // ============================================================
    setTimeout(() => {
        ["alertSuccess", "alertError"].forEach((id) => {
            const el = document.getElementById(id);
            if (!el) return;
            el.style.transition = "opacity 0.4s";
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 400);
        });
    }, 5000);

    // ============================================================
    // NAMA PU — AUTO UPPERCASE
    // ============================================================
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

    // ============================================================
    // TELEPHONE — digits only
    // ============================================================
    const telInput = document.getElementById("telephone");
    if (telInput) {
        telInput.addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, "").slice(0, 15);
        });
    }

    // ============================================================
    // NIK — counter + validation + duplicate check
    // ============================================================
    let nikCheckTimeout;

    function updateNikCounter(length) {
        if (!nikCounter || !nikStatus) return;
        if (length === 16) {
            nikCounter.textContent = length + "/16 digit";
            nikStatus.textContent = "Lengkap";
            nikStatus.className = "fh-nik-status ok";
            nikInput.classList.remove("is-invalid");
        } else if (length > 0) {
            nikCounter.textContent = length + "/16 digit";
            nikStatus.textContent = "Belum lengkap";
            nikStatus.className = "fh-nik-status err";
        } else {
            nikCounter.textContent = "0/16 digit";
            nikStatus.textContent = "Belum lengkap";
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
            },
            body: JSON.stringify({ nik }),
        })
            .then((r) => r.json())
            .then((data) => {
                const warningDiv = document.getElementById("nikExistsWarning");
                if (data.exists) {
                    if (warningDiv) {
                        warningDiv.innerHTML = `
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
                    }
                } else {
                    if (warningDiv) {
                        warningDiv.innerHTML =
                            '<small class="fh-hint" style="color:#059669;">✓ NIK belum terdaftar</small>';
                    }
                }
            })
            .catch(() => {
                const warningDiv = document.getElementById("nikExistsWarning");
                if (warningDiv) warningDiv.remove();
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

    // ============================================================
    // ENUMERATOR SEARCH
    // ============================================================
    function lockForm() {
        if (!formFields) return;
        formFields
            .querySelectorAll("input, textarea, select, button[type='submit']")
            .forEach((el) => (el.disabled = true));
        formFields.classList.add("fh-form-locked");
    }

    function unlockForm() {
        if (!formFields) return;
        formFields
            .querySelectorAll("input, textarea, select, button[type='submit']")
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

            if (opts.length) {
                searchResults.innerHTML = opts
                    .map(
                        (o) => `
                    <div class="fh-search-item" onclick="selectEnumerator('${o.value}', '${o.text}', '${o.dataset.status || ""}')">
                        <span class="item-name">${o.text}</span>
                        <span class="item-badge ${o.dataset.status === "Aktif" ? "aktif" : "tidak"}">
                            ${o.dataset.status || ""}
                        </span>
                    </div>`,
                    )
                    .join("");
            } else {
                searchResults.innerHTML =
                    '<div style="padding:12px 14px;font-size:13px;color:#8A99B3;text-align:center;">Tidak ada hasil</div>';
            }
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

    window.selectEnumerator = function (id, nama, status) {
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

    // Init enumerator status on load
    if (enumeratorSelect?.value) {
        const opt = enumeratorSelect.options[enumeratorSelect.selectedIndex];
        checkEnumeratorStatus(enumeratorSelect.value, opt?.text || "");
        if (searchContainer) searchContainer.style.display = "none";
    }

    // ============================================================
    // PRODUK TAMBAHAN (dinamis slot 2–5)
    // ============================================================
    const MAX_PRODUK = 5;
    const PRODUK_SLOTS = [2, 3, 4, 5];
    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
    const produkList = document.getElementById("produkTambahanList");
    const btnAddProduk = document.getElementById("btnAddProduk");
    const maxProdukNotice = document.getElementById("maxProdukNotice");

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
                    <svg viewBox="0 0 24 24">
                        <line x1="18" y1="6"  x2="6"  y2="18"/>
                        <line x1="6"  y1="6"  x2="18" y2="18"/>
                    </svg>
                    Hapus
                </button>
            </div>
            <div class="fh-produk-item-grid">
                <div class="fh-field" style="margin-bottom:0;">
                    <label class="fh-label" for="nama_produk_${slot}">
                        Nama Produk ${slot} <span class="req">*</span>
                    </label>
                    <input type="text"
                           id="nama_produk_${slot}"
                           name="nama_produk_${slot}"
                           class="fh-input"
                           placeholder="Nama produk ${slot}"
                           required>
                </div>
                <div class="fh-field" style="margin-bottom:0;">
                    <label class="fh-label" for="foto_produk_${slot}_file">
                        Foto Produk ${slot} <span class="req">*</span>
                    </label>

                    <input type="hidden"
                           id="foto_produk_${slot}_path"
                           name="foto_produk_${slot}_path"
                           value="">

                    <input type="file"
                           id="foto_produk_${slot}_file"
                           class="fh-file-input"
                           accept="image/jpeg,image/jpg,image/png"
                           data-slot="${slot}">

                    <span class="fh-hint" id="upload_status_${slot}">Belum diupload</span>
                </div>
            </div>`;

        // ── Remove button ──
        item.querySelector(".fh-btn-remove").addEventListener(
            "click",
            function () {
                const s = parseInt(this.dataset.slot);
                activeSlots = activeSlots.filter((x) => x !== s);
                item.style.transition = "opacity 0.2s, transform 0.2s";
                item.style.opacity = "0";
                item.style.transform = "translateY(-8px)";
                setTimeout(() => {
                    item.remove();
                    refreshAddButton();
                }, 220);
            },
        );

        // ── File → AJAX upload ──
        item.querySelector(`#foto_produk_${slot}_file`).addEventListener(
            "change",
            function () {
                handleDynamicFotoUpload(this, slot);
            },
        );

        produkList.appendChild(item);
        refreshAddButton();
    }

    /**
     * Upload foto produk dinamis via AJAX ke uploadFileSequintal endpoint.
     * Setelah berhasil, path disimpan ke hidden input foto_produk_{slot}_path.
     */
    async function handleDynamicFotoUpload(fileInput, slot) {
        const file = fileInput.files[0];
        if (!file) return;

        const statusSpan = document.getElementById(`upload_status_${slot}`);
        const pathInput = document.getElementById(`foto_produk_${slot}_path`);

        // Validasi ukuran & tipe
        if (file.size > MAX_FILE_SIZE) {
            fileInput.value = "";
            fileInput.classList.add("is-invalid");
            pathInput.value = "";
            statusSpan.textContent = "✗ File terlalu besar (maks 10MB)";
            statusSpan.style.color = "#EF4444";
            showToast("error", `Ukuran foto produk ${slot} maksimal 10MB!`);
            return;
        }

        const allowed = ["image/jpeg", "image/jpg", "image/png"];
        if (!allowed.includes(file.type)) {
            fileInput.value = "";
            fileInput.classList.add("is-invalid");
            pathInput.value = "";
            statusSpan.textContent = "✗ Format tidak didukung (JPG/PNG)";
            statusSpan.style.color = "#EF4444";
            showToast(
                "error",
                `Format foto produk ${slot} harus JPG atau PNG!`,
            );
            return;
        }

        // Mulai upload
        fileInput.classList.remove("is-invalid");
        statusSpan.textContent = "Mengupload...";
        statusSpan.style.color = "#1A5FC8";

        const fd = new FormData();
        fd.append(`foto_produk_${slot}`, file);
        fd.append("_token", getCsrfToken());

        try {
            const res = await fetch(`/upload/foto_produk_${slot}`, {
                method: "POST",
                body: fd,
            });
            const data = await res.json();

            if (res.ok && data.success) {
                pathInput.value = data.path;
                fileInput.classList.add("is-valid");
                statusSpan.textContent = "✓ Berhasil diupload";
                statusSpan.style.color = "#059669";
            } else {
                throw new Error(data.message || "Upload gagal");
            }
        } catch (err) {
            pathInput.value = "";
            fileInput.classList.add("is-invalid");
            statusSpan.textContent = "✗ " + (err.message || "Gagal upload");
            statusSpan.style.color = "#EF4444";
            showToast(
                "error",
                `Gagal upload foto produk ${slot}: ${err.message}`,
            );
        }
    }

    if (btnAddProduk) btnAddProduk.addEventListener("click", addProdukSlot);

    // ============================================================
    // UPLOAD MODAL (foto utama: ktp, rumah, pendamping, produk)
    // ============================================================
    const uploadModalHTML = `
        <div class="modal fade" id="uploadProgressModal"
             data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:16px;border:none;">
                    <div class="modal-header border-0" style="padding:1.5rem 1.5rem 0.5rem;">
                        <h5 class="modal-title" style="font-family:'Sora',sans-serif;font-size:16px;color:#0F1F40;">
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
                                            border-radius:99px;transition:width 0.3s ease;"></div>
                            </div>
                        </div>
                        <div style="text-align:center;margin-top:12px;">
                            <small style="color:#B0BCCE;">Jangan tutup halaman ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

    if (!document.getElementById("uploadProgressModal")) {
        document.body.insertAdjacentHTML("beforeend", uploadModalHTML);
    }

    // Foto utama yang selalu ada (urutan upload)
    const STATIC_STEPS = [
        { name: "foto_ktp", label: "Foto KTP" },
        { name: "foto_rumah", label: "Foto Rumah" },
        { name: "foto_pendamping", label: "Foto Pendamping" },
        { name: "foto_produk", label: "Foto Produk 1" },
    ];

    function buildUploadStepsHTML(allSteps) {
        return allSteps
            .map(
                (s) => `
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;" id="step-${s.name}">
                <div style="flex:1;font-size:13px;color:#6B7A99;">${s.label}</div>
                <div id="status-${s.name}" style="font-size:13px;color:#B0BCCE;">○</div>
            </div>`,
            )
            .join("");
    }

    function setStepStatus(name, status) {
        const el = document.getElementById(`status-${name}`);
        if (!el) return;
        if (status === "uploading")
            el.innerHTML = '<span style="color:#1A5FC8;">↑</span>';
        else if (status === "success")
            el.innerHTML = '<span style="color:#059669;">✓</span>';
        else el.innerHTML = '<span style="color:#B0BCCE;">○</span>';
    }

    function setProgress(pct, text) {
        const bar = document.getElementById("uploadProgressBar");
        const pEl = document.getElementById("uploadPercentage");
        const tEl = document.getElementById("currentUploadText");
        if (bar) bar.style.width = pct + "%";
        if (pEl) pEl.textContent = Math.round(pct) + "%";
        if (tEl && text) tEl.textContent = text;
    }

    async function uploadStaticFiles(formData, steps) {
        for (let i = 0; i < steps.length; i++) {
            const step = steps[i];
            const fileInput = document.getElementById(step.name);

            if (!fileInput?.files[0]) continue;

            setStepStatus(step.name, "uploading");
            setProgress((i / steps.length) * 90, `Mengupload ${step.label}...`);

            const fd = new FormData();
            fd.append(step.name, fileInput.files[0]);
            fd.append("_token", getCsrfToken());

            const res = await fetch(`/upload/${step.name}`, {
                method: "POST",
                body: fd,
            });
            const data = await res.json();

            if (!res.ok || !data.success)
                throw new Error(data.message || `Gagal upload ${step.label}`);

            formData.set(`${step.name}_path`, data.path);
            setStepStatus(step.name, "success");
        }
    }

    // ============================================================
    // FORM SUBMIT
    // ============================================================
    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        // ── Validasi enumerator ──
        if (!enumeratorSelect.value) {
            enumeratorSearch?.classList.add("is-invalid");
            enumeratorSearch?.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
            showToast("error", "Silakan pilih nama pendamping!");
            return;
        }

        // ── Validasi NIK ──
        if (nikInput.value.length !== 16) {
            nikInput.classList.add("is-invalid");
            nikInput.scrollIntoView({ behavior: "smooth", block: "center" });
            showToast("error", "NIK harus tepat 16 digit!");
            return;
        }

        // ── Validasi foto produk dinamis: semua slot harus sudah terupload ──
        let dynamicUploadOk = true;
        for (const slot of activeSlots) {
            const pathInput = document.getElementById(
                `foto_produk_${slot}_path`,
            );
            const namaInput = document.getElementById(`nama_produk_${slot}`);
            const statusSpan = document.getElementById(`upload_status_${slot}`);

            // Jika nama produk diisi tapi foto belum terupload → tolak submit
            if (namaInput?.value?.trim() && !pathInput?.value) {
                dynamicUploadOk = false;
                const fileInput = document.getElementById(
                    `foto_produk_${slot}_file`,
                );
                if (fileInput) fileInput.classList.add("is-invalid");
                if (statusSpan) {
                    statusSpan.textContent = "✗ Foto wajib diupload";
                    statusSpan.style.color = "#EF4444";
                }
            }
        }

        if (!dynamicUploadOk) {
            showToast(
                "error",
                "Semua foto produk tambahan harus selesai diupload!",
            );
            return;
        }

        // ── Disable submit ──
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="fh-spinner"></div> Menyimpan...';

        // ── Siapkan allSteps untuk modal: statis + dinamis yang aktif ──
        const dynamicSteps = activeSlots.map((s) => ({
            name: `foto_produk_${s}`, // hanya untuk label modal
            label: `Foto Produk ${s}`,
        }));
        const allSteps = [...STATIC_STEPS, ...dynamicSteps];

        // ── Tampilkan modal ──
        let uploadModal;
        try {
            uploadModal = new bootstrap.Modal(
                document.getElementById("uploadProgressModal"),
            );
            document.getElementById("uploadSteps").innerHTML =
                buildUploadStepsHTML(allSteps);
            uploadModal.show();
        } catch (_) {
            /* bootstrap mungkin tidak tersedia */
        }

        // ── Kumpulkan data teks ──
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

        // Nama & path produk tambahan (slot 2–5)
        for (const slot of activeSlots) {
            const namaVal = document
                .getElementById(`nama_produk_${slot}`)
                ?.value?.trim();
            const pathVal = document.getElementById(
                `foto_produk_${slot}_path`,
            )?.value;
            if (namaVal) formData.append(`nama_produk_${slot}`, namaVal);
            if (pathVal) formData.append(`foto_produk_${slot}_path`, pathVal);
        }

        try {
            // Upload foto utama (statis) secara sekuensial
            await uploadStaticFiles(formData, STATIC_STEPS);

            setProgress(95, "Menyimpan data...");

            // Buat temp form dan submit
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
            setProgress(100, "Selesai!");
            setTimeout(() => tempForm.submit(), 400);
        } catch (err) {
            if (uploadModal) uploadModal.hide();
            showToast(
                "error",
                err.message || "Terjadi kesalahan saat mengupload",
            );
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none"
                     stroke="rgba(255,255,255,0.85)" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg> Simpan Data`;
        }
    });

    // ============================================================
    // PAGE CACHE RESET (back button)
    // ============================================================
    window.addEventListener("pageshow", function (e) {
        if (!e.persisted) return;
        submitBtn.disabled = false;
        submitBtn.innerHTML = `
            <svg viewBox="0 0 24 24" width="17" height="17" fill="none"
                 stroke="rgba(255,255,255,0.85)" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg> Simpan Data`;
    });

    // ============================================================
    // VALIDASI FILE FOTO UTAMA (statis)
    // ============================================================
    ["foto_ktp", "foto_rumah", "foto_pendamping", "foto_produk"].forEach(
        (id) => {
            const inp = document.getElementById(id);
            if (!inp) return;
            inp.addEventListener("change", function () {
                const file = this.files[0];
                if (!file) return;
                if (file.size > MAX_FILE_SIZE) {
                    showToast(
                        "error",
                        `Ukuran file ${id.replace(/_/g, " ")} maksimal 10MB!`,
                    );
                    this.value = "";
                    this.classList.add("is-invalid");
                    return;
                }
                const allowed = ["image/jpeg", "image/jpg", "image/png"];
                if (!allowed.includes(file.type)) {
                    showToast(
                        "error",
                        `Format file ${id.replace(/_/g, " ")} harus JPG atau PNG!`,
                    );
                    this.value = "";
                    this.classList.add("is-invalid");
                    return;
                }
                this.classList.remove("is-invalid");
            });
        },
    );

    // ============================================================
    // TOAST
    // ============================================================
    function showToast(type, message) {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                icon: type,
                text: message,
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
            });
        } else {
            alert(message);
        }
    }
});
