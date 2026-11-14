// resources/js/app.js
// alpine entrypoint plus small interaction helpers
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('countdown', (expiresAt) => ({
    remaining: Math.max(0, Math.floor((expiresAt - Date.now()) / 1000)),
    reloaded: false,
    init() {
        this.timer = setInterval(() => {
            this.remaining = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000));
            if (this.remaining === 0 && !this.reloaded) {
                this.reloaded = true;
                clearInterval(this.timer);
                window.location.reload();
            }
        }, 1000);
    },
    label() {
        const minutes = String(Math.floor(this.remaining / 60)).padStart(2, '0');
        const seconds = String(this.remaining % 60).padStart(2, '0');
        return `${minutes}:${seconds}`;
    },
}));

Alpine.data('orderForm', (categories, maxPerOrder) => ({
    quantities: Object.fromEntries(categories.map((category) => [category.id, 0])),
    prices: Object.fromEntries(categories.map((category) => [category.id, category.price])),
    remaining: Object.fromEntries(categories.map((category) => [category.id, category.remaining])),
    maxPerOrder,
    serverErrors: [],
    submitting: false,
    total() {
        return Object.entries(this.quantities).reduce((sum, [id, quantity]) => sum + (this.prices[id] ?? 0) * Number(quantity || 0), 0);
    },
    count() {
        return Object.values(this.quantities).reduce((sum, quantity) => sum + Number(quantity || 0), 0);
    },
    clamp(id) {
        const quantity = Math.max(0, Math.min(Number(this.quantities[id] || 0), this.remaining[id], this.maxPerOrder));
        this.quantities[id] = Number.isFinite(quantity) ? quantity : 0;
    },
    format(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    },
}));

document.addEventListener('submit', (event) => {
    const button = event.target.querySelector('[data-submit]');
    if (button) {
        button.disabled = true;
        button.dataset.originalText = button.textContent;
        button.textContent = 'Memproses...';
    }
});

Alpine.start();
