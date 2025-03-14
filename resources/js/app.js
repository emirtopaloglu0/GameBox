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

// Alpine.js başlatma
Alpine.start();

// Global olarak kullanılabilir hale getirme
window.showToast = showToast;