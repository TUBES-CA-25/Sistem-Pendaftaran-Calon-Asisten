function showModal(message, gifUrl = null) {
        // Reuse global sweet alert or Bootstrap Modal if exists
        // For consistency in this refactor, let's use a dynamic Bootstrap modal notification
        let feedbackModalEscaped = new bootstrap.Modal(document.getElementById('editProfileModal')); // Close the edit modal first if open
        
        // Simple alert for now as per previous logic reusing custom modal code logic but adapted
        // Ideally we should use SweetAlert2 or the Toast component, but sticking to logic:
        const modalEl = document.getElementById("customModal");
        if (modalEl) {
             const modalMessage = document.getElementById("modalMessage");
             const modalGif = document.getElementById("modalGif");
             if (modalMessage) modalMessage.textContent = message;
             if (modalGif) {
                 modalGif.style.display = gifUrl ? "block" : "none";
                 if (gifUrl) modalGif.src = gifUrl;
             }
             const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
             modal.show();
        } else {
             alert(message);
        }
    }

    function validatePhoneNumber(phoneNumber) {
        const phoneRegex = /^(?:\+62|62|0)(8[1-9][0-9]{6,9})$/;
        if (!phoneRegex.test(phoneNumber)) {
            return { success: false, message: "Nomor telepon tidak valid." };
        }
        return { success: true, message: "Nomor telepon valid." };
    }

    function validateNoNumber(input) {
        const noNumberRegex = /^[A-Za-z\s]*$/;
        if (!noNumberRegex.test(input)) {
            return { success: false, message: "Input tidak valid: tidak boleh mengandung angka." };
        }
        return { success: true, message: "Input valid: Tidak ada angka." };
    }

    $(document).ready(function () {
        const phoneInput = document.getElementById("noHp");
        const namaInput = document.getElementById("nama");
        const tempatLahirInput = document.getElementById("tempatLahir");
        
        const safeListeners = (el) => {
            if (el) {
                el.addEventListener("input", function () {
                    el.setCustomValidity("");
                    el.reportValidity();
                });
            }
        };

        safeListeners(phoneInput);
        safeListeners(namaInput);
        safeListeners(tempatLahirInput);

        // Update Kelas on Load and Change
        updateKelasOptions();
        $('#jenisKelamin').on('change', function () {
            updateKelasOptions();
        });

        function updateKelasOptions() {
            const genderSelect = document.getElementById('jenisKelamin');
            const kelasSelect = document.getElementById('kelas');
            if(!genderSelect || !kelasSelect) return;

            const gender = genderSelect.value;
            const currentKelas = "<?= $kelas ?>";

            // Clear existing options except default if we want, but here we rebuild
            kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';

            const kelasOptions = gender === 'Wanita'
                ? ['B1', 'B2', 'B3', 'B4', 'B5', 'B6']
                : ['A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9'];
            
            kelasOptions.forEach(kelas => {
                const option = document.createElement('option');
                option.value = kelas;
                option.textContent = kelas;
                if(kelas === currentKelas) option.selected = true;
                kelasSelect.appendChild(option);
            });
        }

        $('#logoutButton').click(function (e) {
            e.preventDefault();
            $.ajax({
                url: '/Sistem-Pendaftaran-Calon-Asisten/public/logout',
                type: 'POST',
                success: function (response) {
                    // Assuming response is JSON
                    const res = (typeof response === 'string') ? JSON.parse(response) : response;
                    if (res.status === 'success') {
                         // We can use the simple modal or just redirect
                         window.location.href = '/Sistem-Pendaftaran-Calon-Asisten/public/';
                    } else {
                        alert('Logout gagal');
                    }
                }
            });
        });

        $('#editProfileForm').submit(function (e) {
            e.preventDefault();
            
            const formData = new FormData(this); // Easier way to capture form data

            let isValid = true;
            if (namaInput && !validateNoNumber(namaInput.value).success) {
                namaInput.setCustomValidity(validateNoNumber(namaInput.value).message);
                namaInput.reportValidity();
                isValid = false;
            }
            if (tempatLahirInput && !validateNoNumber(tempatLahirInput.value).success) {
                tempatLahirInput.setCustomValidity(validateNoNumber(tempatLahirInput.value).message);
                tempatLahirInput.reportValidity();
                isValid = false;
            }
            if (phoneInput && !validatePhoneNumber(phoneInput.value).success) {
                phoneInput.setCustomValidity(validatePhoneNumber(phoneInput.value).message);
                phoneInput.reportValidity();
                isValid = false;
            }

            if (!isValid) return;

            $.ajax({
                url: '/Sistem-Pendaftaran-Calon-Asisten/public/updateprofile',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    try {
                        const res = typeof response === 'string' ? JSON.parse(response) : response;
                        // Hide the modal first
                        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
                        if (modalInstance) modalInstance.hide();
                        
                        if (res.status === 'success') {
                            showModal(res.message || 'Profil berhasil diperbarui', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif');
                            
                            // Update all profile images on the page immediately
                            if (res.newPhoto) {
                                const newPhotoUrl = res.newPhoto + '?v=' + new Date().getTime();
                                // Select common profile image indicators: navbar img, sidebar icons if any, and page images
                                // FIXED: Removed .sidebar img.icon because it targets the App Logo!
                                $('.navbar-profile-img, .rounded-circle.border-primary img, img.rounded-4, img[alt="Profile Picture"]').attr('src', newPhotoUrl);
                            }

                            setTimeout(() => {
                                if(window.loadPage) loadPage('profile');
                                else window.location.reload();
                            }, 1500);
                        } else {
                            showModal(res.message || 'Gagal memperbarui profil', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failed.gif');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                }
            });
        });
    });