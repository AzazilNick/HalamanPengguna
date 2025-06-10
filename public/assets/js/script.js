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
    const notificationContainer = document.getElementById('profile-notification');
    const profilePhotoImg = document.querySelector('.profile-photo');
    const currentPasswordInput = document.getElementById('current_password');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    if (profileForm && notificationContainer && profilePhotoImg) {
        profileForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            notificationContainer.innerHTML = '';

            const formData = new FormData(profileForm);
            const url = profileForm.action;

            profileForm.querySelector('.btn-update').disabled = true;
            profileForm.querySelector('.btn-update').textContent = 'Updating...';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const result = await response.json();

                if (result.success) {
                    notificationContainer.innerHTML = `<div class="notification success">${result.message}</div>`;
                    if (result.new_photo_url) {
                        profilePhotoImg.src = result.new_photo_url;
                    }
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
                itemWidth = firstSliderItem.offsetWidth + 15;
            }
            let itemsToScroll;
            if (window.innerWidth <= 425) {
                itemsToScroll = 2;
            } else {
                itemsToScroll = 4;
            }
            return itemWidth > 0 ? itemWidth * itemsToScroll : seriesSliderContainer.offsetWidth / itemsToScroll;
        };
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
        window.addEventListener('resize', () => {
            scrollAmount = calculateScrollAmount();
        });
    }

    // --- AJAX for Series Like Button (Halaman Daftar Series) ---
    const likeButtons = document.querySelectorAll('.btn-like-series-ajax');

    likeButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();

            const seriesId = button.dataset.seriesId;
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
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const result = await response.json();

                if (result.success) {
                    if (result.is_liked_by_user) {
                        icon.classList.remove('bx-heart');
                        icon.classList.add('bxs-heart');
                        button.dataset.isLiked = '1';
                    } else {
                        icon.classList.remove('bxs-heart');
                        icon.classList.add('bx-heart');
                        button.dataset.isLiked = '0';
                    }
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
        // Ambil ID series dari URL. Contoh: http://localhost/niflix_project/public/daftar_series/edit/3 -> seriesId = 3
        const pathSegments = window.location.pathname.split('/');
        const seriesId = pathSegments[pathSegments.length - 1]; // Ambil segmen terakhir

        const baseUrl = window.location.origin + '<?= $basePath ?>';

        const validateField = async (field, value) => {
            const formData = new FormData();
            formData.append('fieldName', field);
            formData.append('fieldValue', value);
            formData.append('seriesId', seriesId);

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
                // Mengembalikan objek error spesifik jika terjadi kesalahan jaringan/server
                return { valid: false, message: 'Terjadi kesalahan jaringan atau server.' };
            }
        };

        const displayValidationMessage = (fieldId, isValid, message) => {
            const validationSpan = document.getElementById(`${fieldId}Validation`);
            if (validationSpan) {
                // SANGAT PENTING: Bersihkan semua kelas dan textContent setiap kali dipanggil
                validationSpan.className = 'validation-message'; // Mengatur ulang semua kelas ke default
                validationSpan.textContent = ''; // Mengosongkan teks pesan

                if (!isValid) {
                    validationSpan.classList.add('error');
                    validationSpan.textContent = message;
                } else if (message) { // Jika valid dan ada pesan (misal: "Username tersedia!"), tambahkan kelas success
                    validationSpan.classList.add('success');
                    validationSpan.textContent = message;
                }
                // Jika valid dan tidak ada pesan, biarkan transparan
            }
        };

        // Fungsi untuk memvalidasi semua field saat submit form
        const validateAllFields = async () => {
            let overallValid = true;

            // Validasi Title
            const titleResult = await validateField('title', titleInput.value);
            displayValidationMessage('title', titleResult.valid, titleResult.message);
            if (!titleResult.valid) overallValid = false;

            // Validasi Release Year
            const releaseYearResult = await validateField('release_year', releaseYearInput.value);
            displayValidationMessage('release_year', releaseYearResult.valid, releaseYearResult.message);
            if (!releaseYearResult.valid) overallValid = false;

            // Validasi Image URL
            const imageUrlResult = await validateField('image_url', imageUrlInput.value);
            displayValidationMessage('image_url', imageUrlResult.valid, imageUrlResult.message);
            if (!imageUrlResult.valid) overallValid = false;

            return overallValid;
        };


        // Event listeners for input fields (onblur)
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

        // Mencegah submit form jika ada kesalahan validasi
        editSeriesForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // Cegah submit default dulu

            // Lakukan validasi penuh sekali lagi saat submit
            const formIsValid = await validateAllFields();

            if (formIsValid) {
                // Jika semua valid, baru submit form secara tradisional
                editSeriesForm.submit();
            } else {
                alert('Mohon perbaiki kesalahan dalam formulir sebelum menyimpan.');
            }
        });
    }
});