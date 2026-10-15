/**
 * SkillHive — app.js
 * Handles: explore filtering/sorting, favorites, auth modal, toasts, search, UI utilities
 */

'use strict';

/* ================================================================
   UTILITIES
   ================================================================ */

/**
 * Show the auth modal (Bootstrap modal).
 */
function showAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
}

/**
 * Show a toast notification.
 * @param {string} message
 * @param {string} type - 'info' | 'success' | 'warning' | 'error'
 */
function showToast(message, type = 'info') {
    const toast = document.getElementById('shToast');
    const msg   = document.getElementById('shToastMessage');
    if (!toast || !msg) return;

    // Icon by type
    const iconMap = { info: 'bi-info-circle', success: 'bi-check-circle', warning: 'bi-exclamation-triangle', error: 'bi-x-circle' };
    const icon = toast.querySelector('i');
    if (icon) icon.className = `bi ${iconMap[type] || 'bi-info-circle'}`;

    msg.textContent = message;
    toast.classList.add('show');

    setTimeout(() => hideToast(), 4000);
}

/**
 * Hide the toast notification.
 */
function hideToast() {
    const toast = document.getElementById('shToast');
    if (toast) toast.classList.remove('show');
}

/**
 * Toggle password field visibility.
 * @param {string} fieldId
 * @param {HTMLElement} btn
 */
function togglePasswordVisibility(fieldId, btn) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    if (field.type === 'password') {
        field.type = 'text';
        btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
    } else {
        field.type = 'password';
        btn.innerHTML = '<i class="bi bi-eye"></i>';
    }
}

/**
 * Toggle favourite button state.
 * For guests, show auth modal instead.
 * For logged-in users, send AJAX POST to /favorites/toggle.
 * @param {HTMLElement} btn
 * @param {number|string} [serviceId]
 */
function toggleFavorite(btn, serviceId) {
    if (btn.dataset.requiresAuth === 'true') {
        showAuthModal();
        return;
    }

    const sId = serviceId || btn.dataset.serviceId;
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.content : '';

    const wasActive = btn.classList.contains('active');
    btn.classList.toggle('active');
    const icon = btn.querySelector('i');
    if (icon) {
        icon.className = !wasActive ? 'bi bi-heart-fill' : 'bi bi-heart';
    }

    fetch('/favorites/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ service_id: sId })
    })
    .then(response => {
        if (response.status === 401) {
            btn.classList.toggle('active');
            if (icon) icon.className = wasActive ? 'bi bi-heart-fill' : 'bi bi-heart';
            showAuthModal();
            return null;
        }
        return response.json();
    })
    .then(data => {
        if (data && data.status === 'success') {
            showToast(data.message, 'success');
        }
    })
    .catch(() => {
        showToast(btn.classList.contains('active') ? 'Saved to favourites' : 'Removed from favourites', 'success');
    });
}

/* ================================================================
   EXPLORE PAGE — CLIENT-SIDE FILTERING & SORTING
   ================================================================ */

(function initExploreFilters() {
    const grid    = document.getElementById('serviceGrid');
    const empty   = document.getElementById('emptyState');
    const count   = document.getElementById('resultCount');
    const sortSel = document.getElementById('sortSelect');

    if (!grid) return; // Not on the explore/category page

    const items = Array.from(grid.querySelectorAll('.service-grid-item'));

    function getActiveCategories() {
        return Array.from(document.querySelectorAll('.filter-category:checked')).map(el => el.value);
    }

    function getActivePriceRanges() {
        return Array.from(document.querySelectorAll('.filter-price:checked')).map(el => {
            const [min, max] = el.value.split('-').map(Number);
            return { min, max };
        });
    }

    function getMinRating() {
        const checked = document.querySelector('.filter-rating:checked');
        return checked ? parseFloat(checked.value) : 0;
    }

    function getMaxDelivery() {
        const checked = document.querySelector('.filter-delivery:checked');
        return checked ? parseInt(checked.value) : 999;
    }

    function getSearchQuery() {
        // Read from URL param on page load OR from the search input if present on explore
        const params = new URLSearchParams(window.location.search);
        return (params.get('q') || '').toLowerCase().trim();
    }

    function priceInRanges(price, ranges) {
        if (ranges.length === 0) return true;
        return ranges.some(r => price >= r.min && price <= r.max);
    }

    function applyFilters() {
        const cats      = getActiveCategories();
        const prices    = getActivePriceRanges();
        const minRating = getMinRating();
        const maxDel    = getMaxDelivery();
        const query     = getSearchQuery();

        let visibleCount = 0;

        items.forEach(item => {
            const card = item.querySelector('.service-card');
            if (!card) return;

            const category = card.dataset.category || '';
            const price    = parseFloat(card.dataset.price) || 0;
            const rating   = parseFloat(card.dataset.rating) || 0;
            const delivery = parseInt(card.dataset.delivery) || 0;
            const title    = (card.dataset.title || '').toLowerCase();

            const matchCat    = cats.length === 0 || cats.includes(category);
            const matchPrice  = priceInRanges(price, prices);
            const matchRating = rating >= minRating;
            const matchDel    = delivery <= maxDel;
            const matchQuery  = !query || title.includes(query);

            const visible = matchCat && matchPrice && matchRating && matchDel && matchQuery;
            item.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        if (count) count.textContent = visibleCount;
        if (empty) empty.style.display = (visibleCount === 0) ? 'block' : 'none';
    }

    function applySort() {
        if (!sortSel) return;
        const sortValue = sortSel.value;

        const sortedItems = [...items].sort((a, b) => {
            const cardA = a.querySelector('.service-card');
            const cardB = b.querySelector('.service-card');
            if (!cardA || !cardB) return 0;

            const priceA  = parseFloat(cardA.dataset.price) || 0;
            const priceB  = parseFloat(cardB.dataset.price) || 0;
            const ratingA = parseFloat(cardA.dataset.rating) || 0;
            const ratingB = parseFloat(cardB.dataset.rating) || 0;

            switch (sortValue) {
                case 'price-low':  return priceA - priceB;
                case 'price-high': return priceB - priceA;
                case 'rating':     return ratingB - ratingA;
                default:           return 0; // recommended / original order
            }
        });

        sortedItems.forEach(item => grid.appendChild(item));
    }

    // Bind filter inputs
    document.querySelectorAll('.filter-category, .filter-price, .filter-rating, .filter-delivery').forEach(el => {
        el.addEventListener('change', () => {
            applySort();
            applyFilters();
        });
    });

    // Sort dropdown
    if (sortSel) {
        sortSel.addEventListener('change', () => {
            applySort();
            applyFilters();
        });
    }

    // Clear all filters button
    const clearBtn = document.getElementById('clearFilters');
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            document.querySelectorAll('.filter-category, .filter-price').forEach(el => el.checked = false);
            const anyRating = document.getElementById('rating-any');
            if (anyRating) anyRating.checked = true;
            const anyDelivery = document.getElementById('delivery-any');
            if (anyDelivery) anyDelivery.checked = true;
            applyFilters();
            showToast('Filters cleared', 'info');
        });
    }

    // Initial run (apply URL query if present)
    applyFilters();
})();

/* ================================================================
   SEARCH — highlight popular searches
   ================================================================ */

(function initSearch() {
    // Pre-fill hero search if URL has ?q= param
    const params = new URLSearchParams(window.location.search);
    const q = params.get('q');
    if (q) {
        const inputs = document.querySelectorAll('.search-bar-sh input[name="q"]');
        inputs.forEach(input => { input.value = q; });
    }
})();

/* ================================================================
   RESTRICTED GUEST ACTIONS
   Any element with data-requires-auth="true" triggers auth modal
   ================================================================ */

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-requires-auth="true"]').forEach(el => {
        if (el.tagName !== 'BUTTON') {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                showAuthModal();
            });
        }
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
