// app.js
import "./bootstrap";
import Alpine from "alpinejs";
import { Toast } from "bootstrap"; // Bootstrap Toast bileşenini import edin

window.Alpine = Alpine;

// Bildirimleri yöneten fonksiyon
function showToast(message, type = "success") {
    const toastContainer = document.createElement("div");
    toastContainer.innerHTML = `
        <div class="toast align-items-center text-white bg-${type} border-0 position-fixed top-0 end-0 m-3" 
             role="alert" 
             aria-live="assertive" 
             aria-atomic="true"
             data-bs-autohide="true"
             data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" 
                        class="btn-close btn-close-white me-2 m-auto" 
                        data-bs-dismiss="toast" 
                        aria-label="Close"></button>
            </div>
        </div>
    `;
    document.body.appendChild(toastContainer);

    const toastEl = toastContainer.querySelector('.toast');
    const toast = new Toast(toastEl);
    toast.show();

    // Toast otomatik kapatıldıktan sonra DOM'dan kaldır
    toastEl.addEventListener('hidden.bs.toast', () => {
        toastContainer.remove();
    });
}


// resources/js/app.js
document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('ratingInput');

    stars.forEach(star => {
        star.addEventListener('mouseover', (e) => updateStars(e.target.dataset.value));
        star.addEventListener('mouseout', () => resetStars());
        star.addEventListener('click', (e) => setRating(e.target.dataset.value));
    });

    // Başlangıçta mevcut rating'i yükle
    const initialRating = parseFloat(document.querySelector('.stars').dataset.rating);
    if (initialRating) {
        updateStars(initialRating, true);
    }

    function updateStars(value, isInitial = false) {
        stars.forEach(star => {
            const starValue = parseFloat(star.dataset.value);
            
            if (starValue <= value) {
                star.classList.add('active');
                star.classList.remove('half');
            } else if (starValue - 0.5 <= value) {
                star.classList.add('half');
                star.classList.remove('active');
            } else {
                star.classList.remove('active', 'half');
            }
        });

        if (!isInitial) {
            ratingInput.value = value;
        }
    }

    function resetStars() {
        const currentRating = parseFloat(ratingInput.value);
        updateStars(currentRating, true);
    }

    function setRating(value) {
        ratingInput.value = value;
    }
});

// Alpine.js başlatma
Alpine.start();

// Global olarak kullanılabilir hale getirme
window.showToast = showToast;

