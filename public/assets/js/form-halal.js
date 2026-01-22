document.addEventListener("DOMContentLoaded", function () {
    const nikInput = document.getElementById("nik");
    const nikCounter = document.getElementById("nikCounter");
    const nikError = document.getElementById("nikError");
    const form = document.getElementById("formDataLapangan");
    const submitBtn = document.getElementById("submitBtn");
    const namaPuInput = document.getElementById("nama_pu");
    const enumeratorSearch = document.getElementById("enumerator_search");
    const enumeratorSelect = document.getElementById("enumerator_id");
    const searchResults = document.getElementById("search_results");
    const selectedEnumerator = document.getElementById("selected_enumerator");
    const selectedName = document.getElementById("selected_name");
    const searchContainer = enumeratorSearch?.parentElement;

    // Check if all elements exist before proceeding
    if (!nikInput || !nikCounter || !form || !submitBtn || !namaPuInput) {
        console.error("Required form elements not found");
        return;
    }

    let nikCheckTimeout;

    // ============================================
    // AUTO HIDE SUCCESS/ERROR ALERTS
    // ============================================
    const alerts = document.querySelectorAll(".alert");
    if (alerts.length > 0) {
        alerts.forEach((alert) => {
            if (!alert.closest("#selected_enumerator")) {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            }
        });
    }

    // ============================================
    // ENUMERATOR SEARCH FUNCTIONALITY
    // ============================================
    enumeratorSearch.addEventListener("input", function () {
        const searchTerm = this.value.toLowerCase().trim();

        if (searchTerm.length === 0) {
            searchResults.style.display = "none";
            searchResults.innerHTML = "";
            return;
        }

        const options = Array.from(enumeratorSelect.options).slice(1);
        const filtered = options.filter((option) =>
            option.text.toLowerCase().includes(searchTerm),
        );

        if (filtered.length > 0) {
            let html = '<ul class="list-group list-group-flush">';
            filtered.forEach((option) => {
                html += `
                    <li class="list-group-item list-group-item-action" 
                        style="cursor: pointer; padding: 0.5rem 1rem;"
                        onclick="selectEnumerator('${option.value}', '${option.text}')">
                        <i class="ri-user-line me-2"></i>${option.text}
                    </li>
                `;
            });
            html += "</ul>";
            searchResults.innerHTML = html;
            searchResults.style.display = "block";
        } else {
            searchResults.innerHTML =
                '<div class="p-3 text-muted text-center"><i class="ri-search-line me-2"></i>Tidak ada hasil</div>';
            searchResults.style.display = "block";
        }
    });

    document.addEventListener("click", function (e) {
        if (
            !enumeratorSearch.contains(e.target) &&
            !searchResults.contains(e.target)
        ) {
            searchResults.style.display = "none";
        }
    });

    window.selectEnumerator = function (id, name) {
        enumeratorSelect.value = id;
        enumeratorSearch.value = "";
        searchResults.style.display = "none";
        selectedName.textContent = name;
        selectedEnumerator.style.display = "block";
        enumeratorSelect.classList.remove("is-invalid");

        if (searchContainer) {
            searchContainer.style.display = "none";
        }
    };

    window.clearEnumeratorSelection = function () {
        enumeratorSelect.value = "";
        selectedEnumerator.style.display = "none";
        enumeratorSearch.value = "";

        if (searchContainer) {
            searchContainer.style.display = "block";
        }

        enumeratorSearch.focus();
    };

    if (enumeratorSelect.value) {
        const selectedOption =
            enumeratorSelect.options[enumeratorSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            selectedName.textContent = selectedOption.text;
            selectedEnumerator.style.display = "block";
            if (searchContainer) {
                searchContainer.style.display = "none";
            }
        }
    }

    // ============================================
    // NAMA PU - AUTO UPPERCASE
    // ============================================
    namaPuInput.addEventListener("input", function (e) {
        const start = this.selectionStart;
        const end = this.selectionEnd;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(start, end);
    });

    namaPuInput.addEventListener("paste", function (e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData(
            "text",
        );
        const start = this.selectionStart;
        const end = this.selectionEnd;
        const currentValue = this.value;
        const newValue =
            currentValue.substring(0, start) +
            pastedText.toUpperCase() +
            currentValue.substring(end);
        this.value = newValue;
        this.setSelectionRange(
            start + pastedText.length,
            start + pastedText.length,
        );
    });

    // ============================================
    // NIK VALIDATION & COUNTER
    // ============================================
    function updateNikCounter(length) {
        if (!nikCounter) return;

        const nikStatus = document.getElementById("nikStatus");
        if (!nikStatus) return;

        nikCounter.innerHTML = `<i class="ri-information-line"></i> ${length}/16 digit`;

        if (length === 16) {
            nikCounter.classList.remove(
                "text-muted",
                "text-danger",
                "text-warning",
            );
            nikCounter.classList.add("text-success");
            nikCounter.innerHTML = `<i class="ri-checkbox-circle-line"></i> ${length}/16 digit`;
            nikStatus.textContent = "✓ Lengkap";
            nikStatus.classList.remove(
                "text-muted",
                "text-danger",
                "text-warning",
            );
            nikStatus.classList.add("text-success");
        } else if (length > 16) {
            nikCounter.classList.remove(
                "text-muted",
                "text-success",
                "text-warning",
            );
            nikCounter.classList.add("text-danger");
            nikCounter.innerHTML = `<i class="ri-error-warning-line"></i> ${length}/16 digit`;
            nikStatus.textContent = "✗ Terlalu panjang!";
            nikStatus.classList.remove(
                "text-muted",
                "text-success",
                "text-warning",
            );
            nikStatus.classList.add("text-danger");
        } else if (length > 0) {
            nikCounter.classList.remove(
                "text-muted",
                "text-success",
                "text-danger",
            );
            nikCounter.classList.add("text-warning");
            nikCounter.innerHTML = `<i class="ri-error-warning-line"></i> ${length}/16 digit`;
            nikStatus.textContent = `Kurang ${16 - length} digit`;
            nikStatus.classList.remove(
                "text-muted",
                "text-success",
                "text-danger",
            );
            nikStatus.classList.add("text-warning");
        } else {
            nikCounter.classList.remove(
                "text-success",
                "text-danger",
                "text-warning",
            );
            nikCounter.classList.add("text-muted");
            nikCounter.innerHTML = `<i class="ri-information-line"></i> ${length}/16 digit`;
            nikStatus.textContent = "Belum lengkap";
            nikStatus.classList.remove(
                "text-success",
                "text-danger",
                "text-warning",
            );
            nikStatus.classList.add("text-muted");
        }
    }

    function checkNikExists(nik) {
        if (!nikInput) return;

        if (nik.length !== 16) {
            return;
        }

        const existingWarning = document.getElementById("nikExistsWarning");
        if (existingWarning) {
            existingWarning.remove();
        }

        const loadingDiv = document.createElement("div");
        loadingDiv.id = "nikExistsWarning";
        loadingDiv.className = "mt-2";
        loadingDiv.innerHTML =
            '<small class="text-info"><i class="ri-loader-4-line"></i> Memeriksa NIK...</small>';
        nikInput.parentElement.appendChild(loadingDiv);

        fetch("/api/check-nik", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content ||
                    document.querySelector('input[name="_token"]')?.value,
            },
            body: JSON.stringify({
                nik: nik,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                const warningDiv = document.getElementById("nikExistsWarning");

                if (data.exists) {
                    // PERUBAHAN: Hanya tampilkan info, tidak blokir
                    nikInput.classList.remove("is-invalid");
                    nikInput.classList.add("is-valid");

                    if (warningDiv) {
                        warningDiv.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show p-2 mb-0" role="alert">
                        <small><strong>NIK ini sudah pernah terdaftar. Anda Mendaftarkan kembali dan pastikan produknya berbeda.</strong>"
                        </small>
                    </div>
                `;
                    }

                    showToast(
                        "info",
                        "NIK sudah terdaftar, namun Anda tetap bisa melanjutkan.",
                    );
                } else {
                    nikInput.classList.remove("is-invalid");
                    nikInput.classList.add("is-valid");

                    if (warningDiv) {
                        warningDiv.innerHTML =
                            '<small class="text-success"><i class="ri-checkbox-circle-line me-1"></i>NIK belum terdaftar</small>';
                    }
                }
            })
            .catch((error) => {
                console.error("Error checking NIK:", error);
                const warningDiv = document.getElementById("nikExistsWarning");
                if (warningDiv) {
                    warningDiv.remove();
                }
            });
    }

    nikInput.addEventListener("input", function (e) {
        let value = this.value.replace(/[^0-9]/g, "");

        if (value.length > 16) {
            value = value.slice(0, 16);
        }

        this.value = value;
        const length = value.length;
        updateNikCounter(length);

        nikInput.classList.remove("is-valid", "is-invalid");
        const existingWarning = document.getElementById("nikExistsWarning");
        if (existingWarning) {
            existingWarning.remove();
        }

        clearTimeout(nikCheckTimeout);

        if (length === 16) {
            nikCheckTimeout = setTimeout(() => {
                checkNikExists(this.value);
            }, 500);
        }
    });

    nikInput.addEventListener("keypress", function (e) {
        if (e.key < "0" || e.key > "9") {
            e.preventDefault();
        }
    });

    nikInput.addEventListener("paste", function (e) {
        e.preventDefault();
        const pastedData = (e.clipboardData || window.clipboardData).getData(
            "text",
        );
        const numericData = pastedData.replace(/[^0-9]/g, "").slice(0, 16);
        this.value = numericData;

        const event = new Event("input", {
            bubbles: true,
        });
        this.dispatchEvent(event);
    });

    // ============================================
    // SEQUENTIAL UPLOAD WITH PROGRESS BAR
    // ============================================

    // Create upload modal HTML
    const uploadModalHTML = `
        <div class="modal fade" id="uploadProgressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">
                            <i class="ri-upload-cloud-line me-2"></i>Mengupload Data
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div id="uploadSteps"></div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted" id="currentUploadText">Mempersiapkan...</small>
                                <small class="text-muted" id="uploadPercentage">0%</small>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                     id="uploadProgressBar" 
                                     role="progressbar" 
                                     style="width: 0%">
                                    <span id="progressText">0%</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="ri-information-line"></i> Jangan tutup halaman ini
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Append modal to body if not exists
    if (!document.getElementById("uploadProgressModal")) {
        document.body.insertAdjacentHTML("beforeend", uploadModalHTML);
    }

    // Upload steps configuration
    const uploadSteps = [
        { name: "foto_ktp", label: "Foto KTP", icon: "ri-bank-card-line" },
        { name: "foto_rumah", label: "Foto Rumah", icon: "ri-home-4-line" },
        {
            name: "foto_pendamping",
            label: "Foto Pendamping",
            icon: "ri-user-3-line",
        },
        { name: "foto_proses", label: "Foto Proses", icon: "ri-image-line" },
        {
            name: "foto_produk",
            label: "Foto Produk",
            icon: "ri-product-hunt-line",
        },
    ];

    function createUploadStepsHTML() {
        return uploadSteps
            .map(
                (step, index) => `
            <div class="d-flex align-items-center mb-2" id="step-${step.name}">
                <div class="me-2" style="width: 30px;">
                    <i class="${step.icon} fs-5 text-muted" id="icon-${step.name}"></i>
                </div>
                <div class="flex-grow-1">
                    <small class="text-muted">${step.label}</small>
                </div>
                <div class="ms-2" id="status-${step.name}">
                    <i class="ri-checkbox-blank-circle-line text-muted"></i>
                </div>
            </div>
        `,
            )
            .join("");
    }

    function updateStepStatus(stepName, status) {
        const iconEl = document.getElementById(`icon-${stepName}`);
        const statusEl = document.getElementById(`status-${stepName}`);

        if (!iconEl || !statusEl) return;

        iconEl.classList.remove("text-muted", "text-primary", "text-success");

        switch (status) {
            case "uploading":
                iconEl.classList.add("text-primary");
                statusEl.innerHTML =
                    '<div class="spinner-border spinner-border-sm text-primary"></div>';
                break;
            case "success":
                iconEl.classList.add("text-success");
                statusEl.innerHTML =
                    '<i class="ri-checkbox-circle-fill text-success"></i>';
                break;
            case "pending":
                iconEl.classList.add("text-muted");
                statusEl.innerHTML =
                    '<i class="ri-checkbox-blank-circle-line text-muted"></i>';
                break;
        }
    }

    function updateProgressBar(percentage, text) {
        const progressBar = document.getElementById("uploadProgressBar");
        const progressText = document.getElementById("progressText");
        const uploadPercentage = document.getElementById("uploadPercentage");

        if (progressBar) {
            progressBar.style.width = percentage + "%";
            progressBar.setAttribute("aria-valuenow", percentage);
        }
        if (progressText) {
            progressText.textContent = Math.round(percentage) + "%";
        }
        if (uploadPercentage) {
            uploadPercentage.textContent = Math.round(percentage) + "%";
        }
        if (text) {
            const currentUploadText =
                document.getElementById("currentUploadText");
            if (currentUploadText) {
                currentUploadText.textContent = text;
            }
        }
    }

    async function uploadFileSequentially(formData, stepIndex = 0) {
        if (stepIndex >= uploadSteps.length) {
            // All uploads complete - submit remaining form data
            return await submitFormData(formData);
        }

        const step = uploadSteps[stepIndex];
        const fileInput = document.getElementById(step.name);

        if (!fileInput || !fileInput.files[0]) {
            // Skip if no file for this step
            return uploadFileSequentially(formData, stepIndex + 1);
        }

        updateStepStatus(step.name, "uploading");
        const baseProgress = (stepIndex / uploadSteps.length) * 100;
        const stepProgress = 100 / uploadSteps.length;
        updateProgressBar(baseProgress, `Mengupload ${step.label}...`);

        const fileFormData = new FormData();
        fileFormData.append(step.name, fileInput.files[0]);
        fileFormData.append(
            "_token",
            document.querySelector('input[name="_token"]').value,
        );

        try {
            const response = await fetch(`/upload/${step.name}`, {
                method: "POST",
                body: fileFormData,
            });

            const result = await response.json();

            if (response.ok && result.success) {
                updateStepStatus(step.name, "success");
                formData.append(`${step.name}_path`, result.path);

                // Update progress to end of this step
                updateProgressBar(
                    baseProgress + stepProgress,
                    `${step.label} berhasil diupload`,
                );

                // Continue to next file
                return uploadFileSequentially(formData, stepIndex + 1);
            } else {
                throw new Error(result.message || "Upload gagal");
            }
        } catch (error) {
            console.error(`Error uploading ${step.name}:`, error);
            throw error;
        }
    }

    async function submitFormData(formData) {
        updateProgressBar(95, "Menyimpan data...");

        try {
            // Submit form normally (not AJAX) to get redirect response
            updateProgressBar(100, "Selesai!");

            // Create a temporary form and submit it
            const tempForm = document.createElement("form");
            tempForm.method = "POST";
            tempForm.action = form.action;
            tempForm.style.display = "none";

            // Append all form data
            for (let [key, value] of formData.entries()) {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = key;
                input.value = value;
                tempForm.appendChild(input);
            }

            document.body.appendChild(tempForm);

            setTimeout(() => {
                tempForm.submit();
            }, 500);
        } catch (error) {
            console.error("Error submitting form:", error);
            throw error;
        }
    }

    // ============================================
    // FORM SUBMISSION
    // ============================================
    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        // Validation checks
        if (!enumeratorSelect.value) {
            enumeratorSearch.classList.add("is-invalid");
            enumeratorSearch.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
            enumeratorSearch.focus();
            showToast("error", "Silakan pilih nama pendamping!");
            return false;
        }

        const nikValue = nikInput.value;
        if (nikValue.length !== 16) {
            nikInput.classList.add("is-invalid");
            if (nikError) nikError.style.display = "block";
            nikInput.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
            nikInput.focus();
            showToast("error", "NIK harus tepat 16 digit!");
            return false;
        }

        // Disable submit button
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        }

        // Show upload modal
        const uploadModal = new bootstrap.Modal(
            document.getElementById("uploadProgressModal"),
        );
        const uploadStepsDiv = document.getElementById("uploadSteps");
        if (uploadStepsDiv) {
            uploadStepsDiv.innerHTML = createUploadStepsHTML();
        }
        uploadModal.show();

        // Prepare form data (without files)
        const formData = new FormData();
        formData.append(
            "_token",
            document.querySelector('input[name="_token"]').value,
        );
        formData.append("enumerator_id", enumeratorSelect.value);
        formData.append("nama_pu", namaPuInput.value);
        formData.append(
            "nama_produk",
            document.getElementById("nama_produk").value,
        );
        formData.append(
            "telephone",
            document.getElementById("telephone").value,
        );
        formData.append("nik", nikValue);
        formData.append("alamat", document.getElementById("alamat").value);
        formData.append(
            "titik_koordinat",
            document.getElementById("titik_koordinat").value,
        );

        try {
            await uploadFileSequentially(formData);
        } catch (error) {
            uploadModal.hide();
            showToast(
                "error",
                error.message || "Terjadi kesalahan saat mengupload",
            );

            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML =
                    '<i class="ri-save-line me-1"></i> Simpan Data';
            }
        }
    });

    // ============================================
    // IMAGE FILE VALIDATION
    // ============================================
    const imageInputs = [
        "foto_ktp",
        "foto_rumah",
        "foto_pendamping",
        "foto_proses",
        "foto_produk",
    ];

    imageInputs.forEach((inputId) => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener("change", function (e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 10485760) {
                        showToast(
                            "error",
                            `Ukuran file ${inputId.replace(
                                /_/g,
                                " ",
                            )} maksimal 10MB!`,
                        );
                        this.value = "";
                        return;
                    }

                    const allowedTypes = [
                        "image/jpeg",
                        "image/jpg",
                        "image/png",
                    ];
                    if (!allowedTypes.includes(file.type)) {
                        showToast(
                            "error",
                            `Format file ${inputId.replace(
                                /_/g,
                                " ",
                            )} harus JPG, JPEG, atau PNG!`,
                        );
                        this.value = "";
                        return;
                    }
                }
            });
        }
    });

    // ============================================
    // TOAST NOTIFICATION FUNCTION
    // ============================================
    function showToast(type, message) {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                icon: type,
                title:
                    type === "success"
                        ? "Berhasil!"
                        : type === "warning"
                          ? "Peringatan!"
                          : type === "info"
                            ? "Informasi"
                            : "Gagal!",
                text: message,
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        } else {
            alert(message);
        }
    }

    // Trigger counter on page load if NIK already has value
    if (nikInput.value) {
        updateNikCounter(nikInput.value.length);
    }
});
