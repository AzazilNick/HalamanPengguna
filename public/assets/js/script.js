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

            const baseUrl = window.location.origin + BASE_URL; // Use BASE_URL

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

    // --- AJAX Validation for Series Create/Edit Form ---
    const seriesForm = document.querySelector('.form-container form');
    if (seriesForm) {
        const inputFields = seriesForm.querySelectorAll('input[data-field], textarea[data-field], select[data-field]');
        const currentYear = new Date().getFullYear();
        let validationTimers = {}; // Object to hold timers for debouncing

        const validateField = async (inputElement) => {
            const fieldName = inputElement.dataset.field;
            const fieldValue = inputElement.value;
            const errorSpan = document.getElementById(`${fieldName}-error`);

            // Clear previous error message immediately
            if (errorSpan) {
                errorSpan.textContent = '';
                errorSpan.classList.remove('error'); // Ensure error class is removed
            }

            // Client-side validation first (less aggressive, mainly for empty or obviously malformed inputs)
            let clientSideError = '';
            if (fieldName === 'release_year') {
                const year = parseInt(fieldValue);
                if (fieldValue.trim() === '') {
                    clientSideError = 'Tahun rilis tidak boleh kosong.';
                } else if (isNaN(year) || year <= 0) { // Check for non-numeric or zero/negative
                    clientSideError = 'Tahun rilis harus berupa angka valid.';
                } else if (year > currentYear) {
                    clientSideError = `Tahun rilis tidak boleh lebih dari tahun sekarang (${currentYear}).`;
                } else if (year < 1888) {
                    clientSideError = 'Tahun rilis terlalu lama (minimal 1888).';
                }
            } else if (fieldName === 'title' && fieldValue.trim() === '') {
                clientSideError = 'Judul series tidak boleh kosong.';
            } else if (fieldName === 'description' && fieldValue.trim() === '') {
                clientSideError = 'Deskripsi series tidak boleh kosong.';
            } else if (fieldName === 'image_url' && fieldValue.trim() !== '' && !/^(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})$/i.test(fieldValue)) {
                clientSideError = 'Format URL gambar tidak valid.';
            }

            if (clientSideError) {
                if (errorSpan) {
                    errorSpan.textContent = clientSideError;
                    errorSpan.classList.add('error');
                }
                return; // Stop here if client-side validation fails
            }

            // If client-side checks pass, proceed to server-side validation
            const formData = new FormData();
            formData.append('field', fieldName);
            formData.append('value', fieldValue);

            try {
                const baseUrl = window.location.origin + BASE_URL; // Use BASE_URL
                const response = await fetch(`${baseUrl}/daftar_series/validateFieldAjax`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const result = await response.json();

                if (!result.isValid) {
                    if (errorSpan) {
                        errorSpan.textContent = result.message;
                        errorSpan.classList.add('error');
                    }
                } else {
                    // If server-side validation passes, ensure no error message is displayed
                    if (errorSpan) {
                        errorSpan.textContent = '';
                        errorSpan.classList.remove('error');
                    }
                }
            } catch (error) {
                console.error('Error during AJAX validation:', error);
                // Only show a generic error if it's a network/server issue, not a validation error from server
                if (errorSpan && !errorSpan.textContent) { // Only set if no client-side error already
                    errorSpan.textContent = 'Terjadi kesalahan validasi.'; // More specific could be "Terjadi kesalahan koneksi."
                    errorSpan.classList.add('error');
                }
            }
        };

        inputFields.forEach(input => {
            input.addEventListener('input', (e) => {
                const fieldName = e.target.dataset.field;
                // Clear any existing timer for this field
                if (validationTimers[fieldName]) {
                    clearTimeout(validationTimers[fieldName]);
                }
                // Set a new timer to call validateField after a delay
                // This prevents validation on every keystroke but still provides near real-time feedback
                validationTimers[fieldName] = setTimeout(() => {
                    validateField(e.target);
                }, 500); // 500ms delay
            });

            input.addEventListener('blur', (e) => {
                const fieldName = e.target.dataset.field;
                // Immediately validate on blur, cancelling any pending input timer
                if (validationTimers[fieldName]) {
                    clearTimeout(validationTimers[fieldName]);
                }
                validateField(e.target);
            });
        });
    }

    // --- AJAX Validation for Film Create/Edit Form ---
    const filmForm = document.querySelector('.form-container form');
    if (filmForm) {
        const inputFields = filmForm.querySelectorAll('input[data-field], textarea[data-field], select[data-field]');
        const currentYear = new Date().getFullYear();
        let validationTimers = {}; // Object to hold timers for debouncing

        const validateField = async (inputElement) => {
            const fieldName = inputElement.dataset.field;
            const fieldValue = inputElement.value;
            const errorSpan = document.getElementById(`${fieldName}-error`);

            // Clear previous error message immediately
            if (errorSpan) {
                errorSpan.textContent = '';
                errorSpan.classList.remove('error'); // Ensure error class is removed
            }

            // Client-side validation first (less aggressive, mainly for empty or obviously malformed inputs)
            let clientSideError = '';
            if (fieldName === 'release_year') {
                const year = parseInt(fieldValue);
                if (fieldValue.trim() === '') {
                    clientSideError = 'Tahun rilis tidak boleh kosong.';
                } else if (isNaN(year) || year <= 0) { // Check for non-numeric or zero/negative
                    clientSideError = 'Tahun rilis harus berupa angka valid.';
                } else if (year > currentYear) {
                    clientSideError = `Tahun rilis tidak boleh lebih dari tahun sekarang (${currentYear}).`;
                } else if (year < 1888) {
                    clientSideError = 'Tahun rilis terlalu lama (minimal 1888).';
                }
            } else if (fieldName === 'title' && fieldValue.trim() === '') {
                clientSideError = 'Judul film tidak boleh kosong.';
            } else if (fieldName === 'description' && fieldValue.trim() === '') {
                clientSideError = 'Deskripsi film tidak boleh kosong.';
            } else if (fieldName === 'image_url' && fieldValue.trim() !== '' && !/^(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})$/i.test(fieldValue)) {
                clientSideError = 'Format URL gambar tidak valid.';
            }

            if (clientSideError) {
                if (errorSpan) {
                    errorSpan.textContent = clientSideError;
                    errorSpan.classList.add('error');
                }
                return; // Stop here if client-side validation fails
            }

            // If client-side checks pass, proceed to server-side validation
            const formData = new FormData();
            formData.append('field', fieldName);
            formData.append('value', fieldValue);

            try {
                const baseUrl = window.location.origin + BASE_URL; // Use BASE_URL
                const response = await fetch(`${baseUrl}/daftar_film/validateFieldAjax`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const result = await response.json();

                if (!result.isValid) {
                    if (errorSpan) {
                        errorSpan.textContent = result.message;
                        errorSpan.classList.add('error');
                    }
                } else {
                    // If server-side validation passes, ensure no error message is displayed
                    if (errorSpan) {
                        errorSpan.textContent = '';
                        errorSpan.classList.remove('error');
                    }
                }
            } catch (error) {
                console.error('Error during AJAX validation:', error);
                // Only show a generic error if it's a network/server issue, not a validation error from server
                if (errorSpan && !errorSpan.textContent) { // Only set if no client-side error already
                    errorSpan.textContent = 'Terjadi kesalahan validasi.'; // More specific could be "Terjadi kesalahan koneksi."
                    errorSpan.classList.add('error');
                }
            }
        };

        inputFields.forEach(input => {
            input.addEventListener('input', (e) => {
                const fieldName = e.target.dataset.field;
                // Clear any existing timer for this field
                if (validationTimers[fieldName]) {
                    clearTimeout(validationTimers[fieldName]);
                }
                // Set a new timer to call validateField after a delay
                // This prevents validation on every keystroke but still provides near real-time feedback
                validationTimers[fieldName] = setTimeout(() => {
                    validateField(e.target);
                }, 500); // 500ms delay
            });

            input.addEventListener('blur', (e) => {
                const fieldName = e.target.dataset.field;
                // Immediately validate on blur, cancelling any pending input timer
                if (validationTimers[fieldName]) {
                    clearTimeout(validationTimers[fieldName]);
                }
                validateField(e.target);
            });
        });
    }

    // --- Common helper functions (moved from Komentar Rating Detail) ---
    function nl2br(str) {
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
    }

    function escapeHTML(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    // --- Komentar & Rating Detail Page (film/series) logic ---
    const commentReviewForm = document.getElementById('comment-review-form');
    const parentCommentIdInput = document.getElementById('parent-comment-id');
    const commentTextInput = document.getElementById('comment_text');
    const commentLabel = document.getElementById('comment-label');
    const cancelReplyButton = document.getElementById('cancel-reply');
    const ratingValueInput = document.getElementById('rating_value');
    const totalCommentsRatingsDisplay = document.getElementById('total-comments-ratings-display');
    const averageRatingDisplay = document.getElementById('average-rating-display');
    const noCommentsReviewsMessage = document.getElementById('no-comments-reviews-message');

    // These values are needed in JS, so we'll fetch them from data attributes or hidden inputs
    // Assuming you set data attributes on the comments container or form in the PHP view
    const itemType = commentReviewForm ? commentReviewForm.querySelector('input[name="item_type"]').value : null;
    const itemId = commentReviewForm ? commentReviewForm.querySelector('input[name="item_id"]').value : null;
    // Assuming currentUser data is available globally via BASE_URL or similar, or passed from view
    const currentUserId = document.body.dataset.currentUserId; // Get from body data attribute
    const currentUserIsAdmin = document.body.dataset.currentUserIsAdmin; // Get from body data attribute


    // Function to create a new comment/review HTML element for Komentar & Rating page
    const createCommentReviewElement = (comment, item_type, item_id) => {
        const commentItem = document.createElement('div');
        commentItem.classList.add('comment-item');
        commentItem.id = `comment-${comment.id}`;

        const commenterPhotoUrl = `${BASE_URL}/uploads/profile_photos/${comment.commenter_photo || 'default.png'}`;
        const isCommentLiked = false; // Default for new element, like status will be updated by toggleLike form

        commentItem.innerHTML = `
            <div class="comment-header">
                <img src="${commenterPhotoUrl}" alt="Commenter Photo" class="commenter-photo-thumb">
                <p class="comment-author"><strong>${escapeHTML(comment.commenter_username)}</strong></p>
                ${comment.rating_value !== null ? `<p class="comment-rating">Rating: <strong>${escapeHTML(parseFloat(comment.rating_value).toFixed(1))}/10</strong></p>` : ''}
                <p class="comment-likes"><i class="bx bxs-heart"></i> <span id="likes-count-${comment.id}">${comment.total_likes !== undefined ? escapeHTML(comment.total_likes) : 0}</span></p>
                <p class="comment-date">${new Date(comment.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
            </div>
            <p class="comment-text">${nl2br(escapeHTML(comment.comment_text))}</p>
            <div class="comment-actions">
                <form class="toggle-comment-like-form" data-comment-id="${escapeHTML(comment.id)}" action="${BASE_URL}/komentar_rating/detail/${item_type}/${item_id}" method="POST">
                    <input type="hidden" name="action" value="toggle_comment_like">
                    <input type="hidden" name="comment_id" value="${escapeHTML(comment.id)}">
                    <button type="submit" class="btn-like-comment">
                        <i class="bx ${isCommentLiked ? 'bxs-heart' : 'bx-heart'}"></i>
                    </button>
                </form>
                ${comment.rating_value === null ? `<button class="btn-reply" data-comment-id="${escapeHTML(comment.id)}" data-comment-user="${escapeHTML(comment.commenter_username)}">Balas</button>` : ''}
                ${(currentUserId == comment.user_id || currentUserIsAdmin == 1) ? // This part might need adjustment if article author is needed.
                    `<a href="${BASE_URL}/komentar_rating/deleteEntry/${escapeHTML(comment.id)}" onclick="return confirm('Yakin ingin menghapus entri ini?')" class="btn-delete-comment">Hapus</a>` : ''
                }
            </div>
        `;

        // Create nested replies container if it's a top-level comment (for future replies)
        if (!comment.parent_comment_id) {
            const repliesDiv = document.createElement('div');
            repliesDiv.classList.add('comment-replies');
            commentItem.appendChild(repliesDiv);
        }

        return commentItem;
    };


    if (commentReviewForm) {
        commentReviewForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(commentReviewForm);
            const url = commentReviewForm.action;
            const submitButton = commentReviewForm.querySelector('button[type="submit"]');

            submitButton.disabled = true;
            submitButton.textContent = 'Mengirim...';

            const existingNotification = document.querySelector('.notification');
            if (existingNotification) {
                existingNotification.remove();
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Important for AJAX detection in PHP
                    },
                });

                const contentType = response.headers.get('content-type');
                if (response.ok && contentType && contentType.includes('application/json')) {
                    const result = await response.json();

                    if (result.success) {
                        const notificationDiv = document.createElement('div');
                        notificationDiv.classList.add('notification', 'success');
                        notificationDiv.textContent = result.message;
                        commentReviewForm.before(notificationDiv);

                        if (result.comment) {
                            const newCommentElement = createCommentReviewElement(result.comment, itemType, itemId);
                            if (result.comment.parent_comment_id) {
                                // Append to replies of parent comment
                                const parentCommentItem = document.getElementById(`comment-${result.comment.parent_comment_id}`);
                                if (parentCommentItem) {
                                    let repliesContainer = parentCommentItem.querySelector('.comment-replies');
                                    if (!repliesContainer) { // Create if it doesn't exist
                                        repliesContainer = document.createElement('div');
                                        repliesContainer.classList.add('comment-replies');
                                        parentCommentItem.appendChild(repliesContainer);
                                    }
                                    repliesContainer.appendChild(newCommentElement);
                                }
                            } else {
                                // If it's a new review or update, replace/add at top level
                                if (result.action_type === 'update') {
                                    const oldCommentElement = document.getElementById(`comment-${result.comment.id}`);
                                    if (oldCommentElement) {
                                        oldCommentElement.replaceWith(newCommentElement);
                                    }
                                } else {
                                    const commentsList = document.querySelector('.comments-list');
                                    if (commentsList) {
                                        commentsList.prepend(newCommentElement);
                                    }
                                    if (noCommentsReviewsMessage) {
                                        noCommentsReviewsMessage.style.display = 'none';
                                    }
                                }
                            }
                        }

                        // Update total comments/ratings and average rating if provided
                        if (result.new_total_comments_ratings !== undefined && totalCommentsRatingsDisplay) {
                            totalCommentsRatingsDisplay.textContent = result.new_total_comments_ratings;
                        }
                        if (result.new_average_rating !== undefined && averageRatingDisplay) {
                            averageRatingDisplay.textContent = parseFloat(result.new_average_rating).toFixed(1);
                        }

                        // Reset form fields
                        commentTextInput.value = '';
                        parentCommentIdInput.value = '';
                        commentLabel.textContent = 'Komentar/Ulasan Anda:';
                        commentTextInput.placeholder = 'Tulis komentar atau ulasan Anda di sini...';
                        cancelReplyButton.style.display = 'none';
                        ratingValueInput.value = '';
                        ratingValueInput.removeAttribute('required');
                        ratingValueInput.style.display = 'block';
                        commentReviewForm.querySelector('label[for="rating_value"]').style.display = 'block';

                    } else {
                        const notificationDiv = document.createElement('div');
                        notificationDiv.classList.add('notification', 'error');
                        notificationDiv.textContent = result.message;
                        commentReviewForm.before(notificationDiv);
                        if (result.redirect) {
                            setTimeout(() => window.location.href = result.redirect, 1500);
                        }
                    }
                } else {
                    console.warn("Unexpected response from server. Might be a redirect or non-JSON error.");
                    if (!response.ok) {
                         const notificationDiv = document.createElement('div');
                        notificationDiv.classList.add('notification', 'error');
                        notificationDiv.textContent = `Error: ${response.status} ${response.statusText}. Please try again.`;
                        commentReviewForm.before(notificationDiv);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                const notificationDiv = document.createElement('div');
                notificationDiv.classList.add('notification', 'error');
                notificationDiv.textContent = 'Terjadi kesalahan jaringan atau server.';
                commentReviewForm.before(notificationDiv);
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Kirim';
            }
        });

        // Add event listeners for reply buttons (delegation for dynamically added buttons)
        document.querySelector('.comments-list-container').addEventListener('click', (event) => {
            if (event.target.classList.contains('btn-reply')) {
                const button = event.target;
                const commentId = button.dataset.commentId;
                const commentUser = button.dataset.commentUser;

                parentCommentIdInput.value = commentId;
                commentLabel.textContent = `Balas Komentar @${commentUser}:`;
                commentTextInput.placeholder = `Tulis balasan untuk @${commentUser} di sini...`;
                commentTextInput.focus();
                cancelReplyButton.style.display = 'inline-block';
                ratingValueInput.value = '';
                ratingValueInput.removeAttribute('required');
                ratingValueInput.style.display = 'none';
                commentReviewForm.querySelector('label[for="rating_value"]').style.display = 'none';
            }
        });

        // Add event listener for cancel reply button
        cancelReplyButton.addEventListener('click', () => {
            parentCommentIdInput.value = '';
            commentLabel.textContent = 'Komentar/Ulasan Anda:';
            commentTextInput.placeholder = 'Tulis komentar atau ulasan Anda di sini...';
            commentTextInput.value = '';
            cancelReplyButton.style.display = 'none';
            ratingValueInput.setAttribute('required', 'required');
            ratingValueInput.style.display = 'block';
            commentReviewForm.querySelector('label[for="rating_value"]').style.display = 'block';

            const initialRating = commentReviewForm.dataset.initialRating || ''; // Get initial rating from data attribute
            if (initialRating !== '0' && initialRating !== '') {
                 ratingValueInput.value = initialRating;
            } else {
                 ratingValueInput.value = '';
            }
        });

        // Event listener for comment like forms (delegation for dynamically added buttons)
        document.querySelector('.comments-list-container').addEventListener('submit', async (event) => {
            if (event.target.classList.contains('toggle-comment-like-form')) {
                event.preventDefault();
                console.log('Like form submitted via AJAX!');
                const form = event.target;
                const formData = new FormData(form);
                const commentId = form.dataset.commentId;
                const url = `${BASE_URL}/comment/toggleCommentLikeAjax`;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const result = await response.json(); // This assumes PHP side sends JSON for like toggles now

                    if (result.success) {
                        const likeIcon = form.querySelector('i');
                        const likesCountSpan = document.getElementById(`likes-count-${commentId}`);

                        if (result.is_liked) {
                            likeIcon.classList.remove('bx-heart');
                            likeIcon.classList.add('bxs-heart');
                        } else {
                            likeIcon.classList.remove('bxs-heart');
                            likeIcon.classList.add('bx-heart');
                        }
                        if (likesCountSpan) {
                            likesCountSpan.textContent = result.total_likes;
                        }
                    } else {
                        console.error('Error toggling comment like:', result.message);
                        alert('Gagal mengubah status like: ' + result.message);
                    }
                } catch (error) {
                    console.error('Network or server error toggling like:', error);
                    alert('Terjadi kesalahan saat mengubah status like. Silakan coba lagi.');
                }
            }
        });
    }


    // --- Articles Detail Page (articles/show.php) logic ---
    const articleCommentFormAjax = document.getElementById('comment-form-ajax');
    if (articleCommentFormAjax) {
        const articleCommentTextInput = document.getElementById('comment_text_article');
        const articleParentCommentIdInput = document.getElementById('parent-comment-id-article');
        const articleCommentLabel = document.getElementById('comment-label-article');
        const articleCancelReplyButton = document.getElementById('cancel-reply-article');
        const articleCommentsListContainer = document.querySelector('.comments-list-container'); // This might be duplicated, ensure correct selector
        const articleNoCommentsMessage = document.getElementById('no-comments-message');
        const articleCommentCountSpan = document.getElementById('comment-count');
        const articleIdFromForm = articleCommentFormAjax.querySelector('input[name="item_id"]').value; // This is the article ID
        // The articleAuthorId, currentUserId, currentUserIsAdmin would need to be passed as data attributes
        // on the body or a containing div if they are not globally available.
        // For example, add data-article-author-id to a body or main tag.
        const articleAuthorId = document.body.dataset.articleAuthorId; // Example: <body data-article-author-id="<?= escape_html($article['user_id']) ?>">
        // currentUserId and currentUserIsAdmin are assumed global or also from data attributes.


        // Function to create a new comment HTML element for Article page
        const createCommentElement = (comment) => {
            const commentItem = document.createElement('div');
            commentItem.classList.add('comment-item');
            commentItem.id = `comment-${comment.id}`;

            const commenterPhotoUrl = `${BASE_URL}/uploads/profile_photos/${comment.commenter_photo || 'default.png'}`;

            commentItem.innerHTML = `
                <div class="comment-header">
                    <img src="${commenterPhotoUrl}" alt="Commenter Photo" class="commenter-photo-thumb">
                    <p class="comment-author"><strong>${escapeHTML(comment.commenter_username)}</strong></p>
                    <p class="comment-date">${new Date(comment.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                </div>
                <p class="comment-text">${nl2br(escapeHTML(comment.comment_text))}</p>
                <div class="comment-actions">
                    <button class="btn-reply" data-comment-id="${escapeHTML(comment.id)}" data-comment-user="${escapeHTML(comment.commenter_username)}">Balas</button>
                    ${(currentUserId == comment.user_id || currentUserId == articleAuthorId || currentUserIsAdmin == 1) ?
                        `<a href="${BASE_URL}/comment/delete/${escapeHTML(comment.id)}" onclick="return confirm('Yakin ingin menghapus komentar ini?')" class="btn-delete-comment">Hapus</a>` : ''
                    }
                </div>
            `;

            // Add event listener for reply button on the newly created comment
            commentItem.querySelector('.btn-reply').addEventListener('click', (e) => {
                const replyButton = e.target;
                const commentId = replyButton.dataset.commentId;
                const commentUser = replyButton.dataset.commentUser;

                articleParentCommentIdInput.value = commentId;
                articleCommentLabel.textContent = `Balas Komentar @${commentUser}:`;
                articleCommentTextInput.placeholder = `Tulis balasan untuk @${commentUser} di sini...`;
                articleCommentTextInput.focus();
                articleCancelReplyButton.style.display = 'inline-block';
            });

            return commentItem;
        };


        articleCommentFormAjax.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(articleCommentFormAjax);
            const url = articleCommentFormAjax.action;
            const submitButton = articleCommentFormAjax.querySelector('button[type="submit"]');

            submitButton.disabled = true;
            submitButton.textContent = 'Mengirim...';

            const existingNotification = document.querySelector('.notification');
            if (existingNotification) {
                existingNotification.remove();
            }

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
                    const notificationDiv = document.createElement('div');
                    notificationDiv.classList.add('notification', 'success');
                    notificationDiv.textContent = result.message;
                    articleCommentFormAjax.before(notificationDiv);

                    if (result.comment) {
                        const newCommentElement = createCommentElement(result.comment);
                        if (result.comment.parent_comment_id) {
                            const parentCommentItem = document.getElementById(`comment-${result.comment.parent_comment_id}`);
                            if (parentCommentItem) {
                                let repliesContainer = parentCommentItem.querySelector('.comment-replies');
                                if (!repliesContainer) {
                                    repliesContainer = document.createElement('div');
                                    repliesContainer.classList.add('comment-replies');
                                    parentCommentItem.appendChild(repliesContainer);
                                }
                                repliesContainer.appendChild(newCommentElement);
                            }
                        } else {
                            articleCommentsListContainer.prepend(newCommentElement);
                            if (articleNoCommentsMessage) {
                                articleNoCommentsMessage.style.display = 'none';
                            }
                        }
                         if (articleCommentCountSpan) {
                            let currentCount = parseInt(articleCommentCountSpan.textContent);
                            articleCommentCountSpan.textContent = currentCount + 1;
                        }
                    }

                    articleCommentTextInput.value = '';
                    articleParentCommentIdInput.value = '';
                    articleCommentLabel.textContent = 'Tulis komentar Anda di sini:';
                    articleCommentTextInput.placeholder = 'Tulis komentar Anda di sini...';
                    articleCancelReplyButton.style.display = 'none';

                } else {
                    const notificationDiv = document.createElement('div');
                    notificationDiv.classList.add('notification', 'error');
                    notificationDiv.textContent = result.message;
                    articleCommentFormAjax.before(notificationDiv);
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                const notificationDiv = document.createElement('div');
                notificationDiv.classList.add('notification', 'error');
                notificationDiv.textContent = 'Terjadi kesalahan jaringan atau server.';
                articleCommentFormAjax.before(notificationDiv);
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Kirim Komentar';
            }
        });

        document.querySelectorAll('.comments-list-container').forEach(container => {
            container.addEventListener('click', (event) => {
                if (event.target.classList.contains('btn-reply')) {
                    const replyButton = event.target;
                    const commentId = replyButton.dataset.commentId;
                    const commentUser = replyButton.dataset.commentUser;

                    articleParentCommentIdInput.value = commentId;
                    articleCommentLabel.textContent = `Balas Komentar @${commentUser}:`;
                    articleCommentTextInput.placeholder = `Tulis balasan untuk @${commentUser} di sini...`;
                    articleCommentTextInput.focus();
                    articleCancelReplyButton.style.display = 'inline-block';
                }
            });
        });

        articleCancelReplyButton.addEventListener('click', () => {
            articleParentCommentIdInput.value = '';
            articleCommentLabel.textContent = 'Tulis komentar Anda di sini:';
            articleCommentTextInput.placeholder = 'Tulis komentar Anda di sini...';
            articleCommentTextInput.value = '';
            articleCancelReplyButton.style.display = 'none';
        });
    }
});