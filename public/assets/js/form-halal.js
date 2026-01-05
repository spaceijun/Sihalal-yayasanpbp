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
    let isNikValid = false;

    // ============================================
    // AUTO HIDE SUCCESS/ERROR ALERTS (EXCLUDE SELECTED ENUMERATOR ALERT)
    // ============================================
    const alerts = document.querySelectorAll(".alert");
    if (alerts.length > 0) {
        alerts.forEach((alert) => {
            // Jangan auto-hide alert nama pendamping yang terpilih
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

        const options = Array.from(enumeratorSelect.options).slice(1); // Skip first empty option
        const filtered = options.filter((option) =>
            option.text.toLowerCase().includes(searchTerm)
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

    // Close search results when clicking outside
    document.addEventListener("click", function (e) {
        if (
            !enumeratorSearch.contains(e.target) &&
            !searchResults.contains(e.target)
        ) {
            searchResults.style.display = "none";
        }
    });

    // Global function to select enumerator
    window.selectEnumerator = function (id, name) {
        enumeratorSelect.value = id;
        enumeratorSearch.value = "";
        searchResults.style.display = "none";
        selectedName.textContent = name;
        selectedEnumerator.style.display = "block";
        enumeratorSelect.classList.remove("is-invalid");

        // Sembunyikan form pencarian setelah memilih
        if (searchContainer) {
            searchContainer.style.display = "none";
        }
    };

    // Global function to clear selection
    window.clearEnumeratorSelection = function () {
        enumeratorSelect.value = "";
        selectedEnumerator.style.display = "none";
        enumeratorSearch.value = "";

        // Tampilkan kembali form pencarian
        if (searchContainer) {
            searchContainer.style.display = "block";
        }

        enumeratorSearch.focus();
    };

    // Show selected enumerator on page load if already selected
    if (enumeratorSelect.value) {
        const selectedOption =
            enumeratorSelect.options[enumeratorSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            selectedName.textContent = selectedOption.text;
            selectedEnumerator.style.display = "block";
            // Sembunyikan form pencarian jika sudah ada yang terpilih
            if (searchContainer) {
                searchContainer.style.display = "none";
            }
        }
    }

    // ============================================
    // NAMA PU - AUTO UPPERCASE (CLIENT-SIDE)
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
            "text"
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
            start + pastedText.length
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
                "text-warning"
            );
            nikCounter.classList.add("text-success");
            nikCounter.innerHTML = `<i class="ri-checkbox-circle-line"></i> ${length}/16 digit`;
            nikStatus.textContent = "✓ Lengkap";
            nikStatus.classList.remove(
                "text-muted",
                "text-danger",
                "text-warning"
            );
            nikStatus.classList.add("text-success");
        } else if (length > 16) {
            nikCounter.classList.remove(
                "text-muted",
                "text-success",
                "text-warning"
            );
            nikCounter.classList.add("text-danger");
            nikCounter.innerHTML = `<i class="ri-error-warning-line"></i> ${length}/16 digit`;
            nikStatus.textContent = "✗ Terlalu panjang!";
            nikStatus.classList.remove(
                "text-muted",
                "text-success",
                "text-warning"
            );
            nikStatus.classList.add("text-danger");
        } else if (length > 0) {
            nikCounter.classList.remove(
                "text-muted",
                "text-success",
                "text-danger"
            );
            nikCounter.classList.add("text-warning");
            nikCounter.innerHTML = `<i class="ri-error-warning-line"></i> ${length}/16 digit`;
            nikStatus.textContent = `Kurang ${16 - length} digit`;
            nikStatus.classList.remove(
                "text-muted",
                "text-success",
                "text-danger"
            );
            nikStatus.classList.add("text-warning");
        } else {
            nikCounter.classList.remove(
                "text-success",
                "text-danger",
                "text-warning"
            );
            nikCounter.classList.add("text-muted");
            nikCounter.innerHTML = `<i class="ri-information-line"></i> ${length}/16 digit`;
            nikStatus.textContent = "Belum lengkap";
            nikStatus.classList.remove(
                "text-success",
                "text-danger",
                "text-warning"
            );
            nikStatus.classList.add("text-muted");
        }
    }

    function checkNikExists(nik) {
        if (!nikInput) return;

        if (nik.length !== 16) {
            isNikValid = false;
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

        fetch("/check-nik", {
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
                    isNikValid = false;
                    nikInput.classList.remove("is-valid");
                    nikInput.classList.add("is-invalid");

                    if (warningDiv) {
                        warningDiv.innerHTML = `
                        <div class="alert alert-warning alert-dismissible fade show p-2 mb-0" role="alert">
                            <i class="ri-error-warning-line me-1"></i>
                            <small><strong>NIK sudah terdaftar!</strong> NIK ini sudah digunakan oleh: <strong>${
                                data.nama_pu || "Pengguna lain"
                            }</strong></small>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.7rem; padding: 0.25rem;"></button>
                        </div>
                    `;
                    }

                    showToast("warning", "NIK sudah terdaftar di database!");
                } else {
                    isNikValid = true;
                    nikInput.classList.remove("is-invalid");
                    nikInput.classList.add("is-valid");

                    if (warningDiv) {
                        warningDiv.innerHTML =
                            '<small class="text-success"><i class="ri-checkbox-circle-line me-1"></i>NIK tersedia</small>';
                    }
                }
            })
            .catch((error) => {
                console.error("Error checking NIK:", error);
                const warningDiv = document.getElementById("nikExistsWarning");
                if (warningDiv) {
                    warningDiv.remove();
                }
                isNikValid = true;
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
        } else {
            isNikValid = false;
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
            "text"
        );
        const numericData = pastedData.replace(/[^0-9]/g, "").slice(0, 16);
        this.value = numericData;

        const event = new Event("input", {
            bubbles: true,
        });
        this.dispatchEvent(event);
    });

    // ============================================
    // FORM SUBMISSION
    // ============================================
    form.addEventListener("submit", function (e) {
        // Check enumerator selection
        if (!enumeratorSelect.value) {
            e.preventDefault();
            enumeratorSearch.classList.add("is-invalid");
            enumeratorSearch.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
            enumeratorSearch.focus();
            showToast("error", "Silakan pilih nama pendamping!");
            return false;
        }

        // Check NIK
        if (!nikInput) return;

        const nikValue = nikInput.value;

        if (nikValue.length !== 16) {
            e.preventDefault();
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

        if (!isNikValid) {
            e.preventDefault();
            nikInput.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
            nikInput.focus();

            showToast("error", "NIK sudah terdaftar di database!");
            return false;
        }

        // Disable submit button to prevent double submission
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
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
                                " "
                            )} maksimal 10MB!`
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
                                " "
                            )} harus JPG, JPEG, atau PNG!`
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
