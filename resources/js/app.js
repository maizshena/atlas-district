import Alpine from 'alpinejs';

window.Alpine = Alpine;

const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// stagger tile reveal and delay offscreen tiles until they scroll into view
const revealTiles = (root = document) => {
    const tiles = Array.from(root.querySelectorAll('[data-reveal]'));
    if (tiles.length === 0) {
        return;
    }

    if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
        tiles.forEach((tile) => tile.classList.add('is-revealed'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries
            .filter((entry) => entry.isIntersecting)
            .forEach((entry) => {
                const tile = entry.target;
                const index = Number(tile.dataset.reveal ?? 0);
                tile.style.animationDelay = `${Math.min(index, 7) * 50}ms`;
                tile.classList.add('is-revealed');
                observer.unobserve(tile);
            });
    }, { rootMargin: '0px 0px -40px 0px', threshold: 0.05 });

    tiles.forEach((tile, index) => {
        tile.dataset.reveal = index;
        observer.observe(tile);
    });
};

const lockSubmitButton = (button) => {
    button.dataset.originalHtml = button.innerHTML;
    button.disabled = true;
    button.textContent = 'Memproses...';
};

const unlockSubmitButton = (button) => {
    button.innerHTML = button.dataset.originalHtml ?? button.innerHTML;
    button.disabled = false;
};

Alpine.data('countdown', (expiresAt) => ({
    remaining: Math.max(0, Math.floor((expiresAt - Date.now()) / 1000)),
    reloaded: false,
    ticking: false,
    init() {
        this.timer = setInterval(() => {
            this.remaining = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000));
            this.flash();
            if (this.remaining === 0 && !this.reloaded) {
                this.reloaded = true;
                clearInterval(this.timer);
                window.location.reload();
            }
        }, 1000);
    },
    flash() {
        if (prefersReducedMotion()) {
            return;
        }
        this.ticking = true;
        window.setTimeout(() => {
            this.ticking = false;
        }, 150);
    },
    label() {
        const minutes = String(Math.floor(this.remaining / 60)).padStart(2, '0');
        const seconds = String(this.remaining % 60).padStart(2, '0');
        return `${minutes}:${seconds}`;
    },
}));

Alpine.data('countUp', (target, duration = 600) => ({
    shown: 0,
    done: false,
    init() {
        this.value = Number(target);
        this.fullLabel = String(target ?? '');
        this.shown = this.fullLabel;
        if (!Number.isFinite(this.value) || this.fullLabel.trim() === '') {
            this.done = true;
            return;
        }
        const run = this.start.bind(this);
        if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
            run();
            return;
        }
        this.guard = new IntersectionObserver((entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                this.guard.disconnect();
                run();
            }
        }, { threshold: 0.4 });
        this.guard.observe(this.$el);
    },
    start() {
        if (this.done) {
            return;
        }
        this.done = true;
        this.shown = '0';
        if (prefersReducedMotion() || this.value === 0) {
            this.shown = this.fullLabel;
            return;
        }
        const startAt = performance.now();
        const step = (now) => {
            const progress = Math.min(1, (now - startAt) / duration);
            const eased = 1 - Math.pow(1 - progress, 3);
            this.shown = new Intl.NumberFormat('id-ID').format(Math.round(this.value * eased));
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                this.shown = this.fullLabel;
            }
        };
        requestAnimationFrame(step);
    },
}));

Alpine.data('orderForm', (categories, maxPerOrder) => ({
    quantities: Object.fromEntries(categories.map((category) => [category.id, 0])),
    prices: Object.fromEntries(categories.map((category) => [category.id, category.price])),
    remaining: Object.fromEntries(categories.map((category) => [category.id, category.remaining])),
    maxPerOrder,
    serverErrors: [],
    submitting: false,
    init() {
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                this.submitting = false;
            }
        });
    },
    decrease(id) {
        this.quantities[id] = Math.max(0, Number(this.quantities[id] || 0) - 1);
    },
    increase(id, limit) {
        this.quantities[id] = Math.min(Number(this.quantities[id] || 0) + 1, limit);
    },
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
    if (button && !button.disabled) {
        window.setTimeout(() => lockSubmitButton(button), 0);
    }
});

window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        document.querySelectorAll('[data-submit][disabled]').forEach(unlockSubmitButton);
    }
});

document.addEventListener('alpine:init', () => {
    revealTiles(document);
});



Alpine.start();
