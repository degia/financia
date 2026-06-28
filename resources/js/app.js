import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('theme', () => ({
        isDark: false,

        init() {
            const stored = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            this.isDark = stored !== null ? stored === 'dark' : prefersDark;
        },

        toggle() {
            this.isDark = !this.isDark;
            localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
        },
    }));

    Alpine.data('dashboardCards', (cardsData, defaultOrder) => ({
        cardsData: cardsData,
        defaultOrder: defaultOrder,
        order: [],
        hidden: [],
        labels: {},
        showSettings: false,
        dragIdx: null,

        init() {
            const keys = Object.keys(this.cardsData);
            this.labels = {};
            keys.forEach(k => { this.labels[k] = this.cardsData[k].label; });

            const saved = localStorage.getItem('dashboardCardOrder');
            this.order = saved ? JSON.parse(saved) : [...this.defaultOrder];

            const savedHidden = localStorage.getItem('dashboardCardHidden');
            this.hidden = savedHidden ? JSON.parse(savedHidden) : [];

            this.$watch('order', val => {
                localStorage.setItem('dashboardCardOrder', JSON.stringify(val));
            });
            this.$watch('hidden', val => {
                localStorage.setItem('dashboardCardHidden', JSON.stringify(val));
            });
        },

        toggleCard(id) {
            const idx = this.hidden.indexOf(id);
            if (idx > -1) {
                this.hidden.splice(idx, 1);
            } else {
                this.hidden.push(id);
            }
        },

        dragStart(e, idx) {
            this.dragIdx = idx;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', idx);
            this.$el.querySelectorAll('.card').forEach(el => el.style.opacity = '1');
            e.target.style.opacity = '0.5';
        },

        dragOver(e, idx) {
            if (this.dragIdx === null || this.dragIdx === idx) return;
            const item = this.order.splice(this.dragIdx, 1)[0];
            this.order.splice(idx, 0, item);
            this.dragIdx = idx;
        },

        dragEnd() {
            this.dragIdx = null;
            this.$el.querySelectorAll('.card').forEach(el => el.style.opacity = '1');
        },

        resetOrder() {
            this.order = [...this.defaultOrder];
            this.hidden = [];
        },
    }));
});

Alpine.start();
