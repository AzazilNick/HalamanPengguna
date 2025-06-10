// niflix_project/public/assets/js/script.js

document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const mainContent = document.querySelector('main');
    const header = document.querySelector('header');

    if (menuToggle && navMenu && mainContent && header) {
        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            // Sesuaikan margin-top main content berdasarkan tinggi header dan apakah menu aktif
            if (navMenu.classList.contains('active')) {
                mainContent.style.marginTop = `${header.offsetHeight + navMenu.offsetHeight}px`;
            } else {
                mainContent.style.marginTop = `${header.offsetHeight}px`;
            }
        });

        // Tambahkan event listener untuk mereset margin-top saat ukuran jendela berubah
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                // Jika lebar lebih dari 768px (breakpoint desktop)
                navMenu.classList.remove('active'); // Pastikan menu mobile tidak aktif
                mainContent.style.marginTop = `${header.offsetHeight}px`; // Reset margin ke tinggi header saja
            } else {
                // Di layar mobile, jika menu sedang aktif, hitung ulang margin
                if (navMenu.classList.contains('active')) {
                    mainContent.style.marginTop = `${header.offsetHeight + navMenu.offsetHeight}px`;
                }
            }
        });
    }

    // --- Kode AJAX untuk Profil ---
    const profileForm = document.querySelector('.profile-container form');
    const notificationContainer = document.getElementById('profile-notification'); // Tambahkan elemen ini di profile.php
    const profilePhotoImg = document.querySelector('.profile-photo');
    const currentPasswordInput = document.getElementById('current_password');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    if (profileForm && notificationContainer && profilePhotoImg) {
        profileForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // Mencegah submit form tradisional

            notificationContainer.innerHTML = ''; // Bersihkan notifikasi sebelumnya

            const formData = new FormData(profileForm); // Ambil data form, termasuk file
            const url = profileForm.action; // Ambil URL dari atribut action form

            // Tambahkan indikator loading
            profileForm.querySelector('.btn-update').disabled = true;
            profileForm.querySelector('.btn-update').textContent = 'Updating...';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData, // FormData otomatis mengatur header Content-Type
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Menandai ini adalah request AJAX
                    },
                });

                const result = await response.json(); // Menguraikan respons JSON

                if (result.success) {
                    notificationContainer.innerHTML = `<div class="notification success">${result.message}</div>`;
                    // Update foto profil jika diunggah
                    if (result.new_photo_url) {
                        profilePhotoImg.src = result.new_photo_url;
                    }
                    // Bersihkan kolom password setelah update berhasil
                    if (result.password_updated) {
                        if (currentPasswordInput) currentPasswordInput.value = '';
                        if (newPasswordInput) newPasswordInput.value = '';
                        if (confirmPasswordInput) confirmPasswordInput.value = '';
                    }
                } else {
                    notificationContainer.innerHTML = `<div class="notification error">${result.message}</div>`;
                }
            } catch (error) {
                console.error('Error:', error);
                notificationContainer.innerHTML = `<div class="notification error">Terjadi kesalahan jaringan atau server.</div>`;
            } finally {
                // Kembalikan tombol ke keadaan semula
                profileForm.querySelector('.btn-update').disabled = false;
                profileForm.querySelector('.btn-update').textContent = 'Update Profile';
            }
        });
    }

    // --- Slider Logic for Daftar Series Page ---
    const seriesSliderContainer = document.querySelector('.series-container .slider-container');
    const leftArrow = document.querySelector('.series-container .left-arrow');
    const rightArrow = document.querySelector('.series-container .right-arrow');

    if (seriesSliderContainer && leftArrow && rightArrow) {
        const calculateScrollAmount = () => {
            const firstSliderItem = seriesSliderContainer.querySelector('.slider-item');
            let itemWidth = 0;
            if (firstSliderItem) {
                // Lebar item + gap kanan (asumsi gap adalah 15px dari series.css)
                itemWidth = firstSliderItem.offsetWidth + 15;
            }

            // Tentukan berapa item yang bergeser berdasarkan lebar layar
            let itemsToScroll;
            if (window.innerWidth <= 425) { // Contoh breakpoint untuk mobile/tablet
                itemsToScroll = 2; // Geser 2 item di mobile
            } else {
                itemsToScroll = 4; // Geser 4 item di desktop
            }

            // Jika itemWidth belum terdefinisi atau 0, fallback ke perhitungan lebar kontainer
            return itemWidth > 0 ? itemWidth * itemsToScroll : seriesSliderContainer.offsetWidth / itemsToScroll;
        };

        // Inisialisasi scrollAmount saat DOMContentLoaded
        let scrollAmount = calculateScrollAmount();

        leftArrow.addEventListener('click', () => {
            seriesSliderContainer.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        });

        rightArrow.addEventListener('click', () => {
            seriesSliderContainer.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });

        // Update scrollAmount saat ukuran jendela berubah
        window.addEventListener('resize', () => {
            scrollAmount = calculateScrollAmount();
        });
    }

    // --- AJAX for Series Like Button (Halaman Daftar Series) ---
    const likeButtons = document.querySelectorAll('.btn-like-series-ajax');

    likeButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault(); // Mencegah form submit atau navigasi

            const seriesId = button.dataset.seriesId;
            const isLiked = button.dataset.isLiked === '1'; // Convert string to boolean
            const icon = button.querySelector('i');
            const totalLikesSpan = document.querySelector(`.total-likes-${seriesId}`);

            const baseUrl = window.location.origin + '<?= $basePath ?>';

            try {
                const formData = new FormData();
                formData.append('series_id', seriesId);

                const response = await fetch(`${baseUrl}/daftar_series/toggleLikeAjax`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Menandai ini adalah request AJAX
                    },
                });

                const result = await response.json();

                if (result.success) {
                    // Update icon
                    if (result.is_liked_by_user) {
                        icon.classList.remove('bx-heart');
                        icon.classList.add('bxs-heart');
                        button.dataset.isLiked = '1';
                    } else {
                        icon.classList.remove('bxs-heart');
                        icon.classList.add('bx-heart');
                        button.dataset.isLiked = '0';
                    }
                    // Update total likes count
                    if (totalLikesSpan) {
                        totalLikesSpan.textContent = result.total_likes;
                    }
                    console.log(result.message);
                } else {
                    console.error('Error toggling like:', result.message);
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    }
                }
            } catch (error) {
                console.error('Network or server error:', error);
            }
        });
    });

    // --- AJAX for Real-time Validation on Series Edit Page ---
    const editSeriesForm = document.getElementById('editSeriesForm');
    if (editSeriesForm) {
        const titleInput = document.getElementById('title');
        const releaseYearInput = document.getElementById('release_year');
        const imageUrlInput = document.getElementById('image_url');
        const seriesId = window.location.pathname.split('/').pop(); // Ambil ID series dari URL

        const baseUrl = window.location.origin + '<?= $basePath ?>'; //

        const validateField = async (field, value) => {
            const formData = new FormData();
            formData.append('fieldName', field);
            formData.append('fieldValue', value);
            formData.append('seriesId', seriesId); // Kirim ID series juga

            try {
                const response = await fetch(`${baseUrl}/daftar_series/validateFieldAjax`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const result = await response.json();
                return result;
            } catch (error) {
                console.error('Error during AJAX validation:', error);
                return { valid: false, message: 'Terjadi kesalahan validasi.' };
            }
        };

        const displayValidationMessage = (fieldId, isValid, message) => {
            const validationSpan = document.getElementById(`${fieldId}Validation`);
            if (validationSpan) {
                validationSpan.textContent = message;
                validationSpan.className = 'validation-message'; // Reset classes
                if (!isValid) {
                    validationSpan.classList.add('error');
                } else if (message) { // Only add success class if there's a message
                    validationSpan.classList.add('success');
                }
            }
        };

        // Event listeners for input fields (onblur for simplicity, can be onkeyup)
        titleInput.addEventListener('blur', async () => {
            const result = await validateField('title', titleInput.value);
            displayValidationMessage('title', result.valid, result.message);
        });

        releaseYearInput.addEventListener('blur', async () => {
            const result = await validateField('release_year', releaseYearInput.value);
            displayValidationMessage('release_year', result.valid, result.message);
        });

        imageUrlInput.addEventListener('blur', async () => {
            const result = await validateField('image_url', imageUrlInput.value);
            displayValidationMessage('image_url', result.valid, result.message);
        });

        // Optional: Prevent form submission if there are validation errors
        editSeriesForm.addEventListener('submit', (e) => {
            let hasError = false;
            // Check all validation spans for error messages
            document.querySelectorAll('.validation-message').forEach(span => {
                if (span.classList.contains('error') && span.textContent !== '') {
                    hasError = true;
                }
            });

            if (hasError) {
                e.preventDefault();
                alert('Mohon perbaiki kesalahan dalam formulir sebelum menyimpan.');
            }
        });
    }
});