/**
 * UI — Komponen Modal / Toast / Dropdown vanilla JS.
 *
 * Menggantikan shim "Bootstrap emulation" yang dulu ada di layouts/main.php &
 * layouts/main_admin.php. Tidak ada dependensi (tanpa Bootstrap, tanpa jQuery).
 *
 * Perbedaan penting dari shim lama:
 *  - Semua interaksi memakai SATU listener terdelegasi di `document`, sehingga
 *    idempoten: aman terhadap SPA re-inject (app.js menyuntik ulang #content)
 *    dan tidak membocorkan listener setiap modal dibuka.
 *  - Backdrop dibuat PER-MODAL (anak dari elemen modal itu sendiri), bukan satu
 *    backdrop global. Shim lama memakai backdrop bersama, sehingga menutup satu
 *    modal ikut menghapus backdrop modal lain yang masih terbuka.
 *  - Tidak ada Map instance global -> tidak ada state basi, tidak butuh dispose().
 *
 * Markup yang dikenali:
 *   <div id="xModal" data-modal>            elemen modal (tersembunyi via .hidden)
 *   <button data-modal-open="#xModal">       membuka
 *   <button data-modal-close>                menutup (mencari modal terdekat)
 *   <button data-dropdown-toggle>            toggle .dropdown-menu di dalam .dropdown
 *
 * API:
 *   UI.modal.open(target)    target = selector string ATAU element
 *   UI.modal.close(target)
 *   UI.modal.ref(target)     handle ber-.show()/.hide() untuk kode lama
 *   UI.toast.show(message, { type: 'success'|'error', delay: 2000 })
 *   UI.dropdown.closeAll()
 */
(function () {
    'use strict';

    /* ------------------------------------------------------------------ *
     * Util
     * ------------------------------------------------------------------ */

    /** Terima selector string atau element, kembalikan element (atau null). */
    function resolve(target) {
        if (!target) return null;
        if (typeof target === 'string') return document.querySelector(target);
        if (target instanceof Element) return target;
        return null;
    }

    function isOpen(el) {
        return !!el && el.hasAttribute('data-open');
    }

    /* ------------------------------------------------------------------ *
     * Modal
     * ------------------------------------------------------------------ */

    const OPEN_CLASS = 'flex';

    // Durasi fade modal. Nilai ini HARUS sama dengan duration-200 pada class
    // Tailwind di markup modal (transition-opacity / transition-transform).
    const MODAL_TRANSITION_MS = 200;

    let scrollLockCount = 0;
    let scrollBarWidth = 0;

    function lockScroll() {
        scrollLockCount += 1;
        if (scrollLockCount === 1) {
            scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
            if (scrollBarWidth > 0) {
                document.body.style.paddingRight = scrollBarWidth + 'px';
            }
            document.body.style.overflow = 'hidden';
        }
    }

    function unlockScroll() {
        scrollLockCount = Math.max(0, scrollLockCount - 1);
        if (scrollLockCount === 0) {
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
    }

    function openModal(target) {
        const el = resolve(target);
        if (!el || isOpen(el)) return null;

        el.classList.remove('hidden');
        el.classList.add(OPEN_CLASS);
        el.removeAttribute('aria-hidden');
        el.setAttribute('aria-modal', 'true');
        el.setAttribute('role', el.getAttribute('role') || 'dialog');

        lockScroll();

        // Paksa reflow supaya transisi opacity/scale benar-benar berjalan
        // (tanpa ini, penambahan atribut di frame yang sama tidak menganimasi).
        void el.offsetHeight;
        el.setAttribute('data-open', '');

        el.dispatchEvent(new CustomEvent('modal:shown', { bubbles: true }));
        return el;
    }

    function closeModal(target) {
        const el = resolve(target);
        if (!el || !isOpen(el)) return null;

        // Lepas fokus dari elemen di dalam modal sebelum disembunyikan.
        // Menyembunyikan container yang memegang focus memicu peringatan
        // aria-hidden di browser dan menjebak fokus keyboard.
        if (el.contains(document.activeElement) && document.activeElement.blur) {
            document.activeElement.blur();
        }

        el.removeAttribute('data-open');
        el.setAttribute('aria-hidden', 'true');
        el.removeAttribute('aria-modal');

        const finish = function () {
            // Jaga-jaga bila dibuka lagi sebelum transisi selesai
            if (isOpen(el)) return;
            el.classList.add('hidden');
            el.classList.remove(OPEN_CLASS);
            el.dispatchEvent(new CustomEvent('modal:hidden', { bubbles: true }));
        };

        // HARUS selaras dengan duration-200 di markup modal. Kalau nilai ini
        // lebih kecil, modal disembunyikan sebelum fade selesai (terlihat
        // "putus"); kalau lebih besar, backdrop tertinggal di layar.
        setTimeout(finish, MODAL_TRANSITION_MS);
        unlockScroll();
        return el;
    }

    // closeAllModals() dihapus: tidak pernah dipanggil. Menutup modal selalu
    // lewat UI.modal.close(target), tombol [data-modal-close], klik backdrop,
    // atau tombol Esc.

    /* ------------------------------------------------------------------ *
     * Toast
     * ------------------------------------------------------------------ */

    let toastTimer = null;

    /**
     * Menampilkan toast global (#liveToast di layout).
     * Warna & ikon diatur lewat class Tailwind, bukan style inline.
     */
    function showToast(message, options) {
        const opts = options || {};
        const type = opts.type === 'error' ? 'error' : 'success';
        const delay = typeof opts.delay === 'number' ? opts.delay : 2000;

        const el = document.getElementById('liveToast');
        const msgEl = document.getElementById('toastMessage');
        const iconEl = document.getElementById('toastIcon');
        const titleEl = document.getElementById('toastTitle');
        const btnEl = document.getElementById('toastBtn');

        // Fallback bila markup toast tidak ada di halaman ini
        if (!el || !msgEl) {
            window.alert((type === 'success' ? '✔ ' : '✘ ') + message);
            return null;
        }

        const skin = type === 'success'
            ? {
                shell: 'border-emerald-500 bg-emerald-50',
                icon: 'bi bi-check-circle-fill text-xl text-emerald-500',
                title: 'Success!',
                titleCls: 'font-bold text-[13px] mb-0.5 tracking-wide text-emerald-600',
                msgCls: 'text-[11px] font-medium m-0 leading-tight text-emerald-600',
                btnCls: 'shrink-0 px-3 py-1 border rounded-md text-[11px] font-bold transition-colors text-emerald-600 border-emerald-200 hover:bg-emerald-100'
            }
            : {
                shell: 'border-red-500 bg-red-50',
                icon: 'bi bi-x-circle-fill text-xl text-red-500',
                title: 'Error!',
                titleCls: 'font-bold text-[13px] mb-0.5 tracking-wide text-red-600',
                msgCls: 'text-[11px] font-medium m-0 leading-tight text-red-600',
                btnCls: 'shrink-0 px-3 py-1 border rounded-md text-[11px] font-bold transition-colors text-red-600 border-red-200 hover:bg-red-100'
            };

        msgEl.innerHTML = message;
        msgEl.className = skin.msgCls;
        if (iconEl) iconEl.className = skin.icon;
        if (titleEl) {
            titleEl.textContent = skin.title;
            titleEl.className = skin.titleCls;
        }
        if (btnEl) {
            btnEl.textContent = 'Close';
            btnEl.className = skin.btnCls;
        }

        el.className =
            'border-0 border-l-[4px] rounded-r-lg shadow-lg transition-all duration-200 ' +
            'pointer-events-auto min-w-[280px] max-w-[400px] opacity-0 translate-x-2 ' +
            skin.shell;

        void el.offsetHeight;
        el.classList.remove('opacity-0', 'translate-x-2');

        if (toastTimer) clearTimeout(toastTimer);
        if (delay > 0) {
            toastTimer = setTimeout(function () { hideToast(); }, delay);
        }
        return el;
    }

    function hideToast() {
        const el = document.getElementById('liveToast');
        if (!el) return;
        if (toastTimer) {
            clearTimeout(toastTimer);
            toastTimer = null;
        }
        el.classList.add('opacity-0', 'translate-x-2');
    }

    /* ------------------------------------------------------------------ *
     * Dropdown
     * ------------------------------------------------------------------ */

    function closeAllDropdowns(except) {
        document.querySelectorAll('[data-dropdown-menu].block').forEach(function (menu) {
            if (menu !== except) {
                menu.classList.remove('block');
                menu.classList.add('hidden');
            }
        });
    }

    function toggleDropdown(toggleEl) {
        const wrapper = toggleEl.closest('[data-dropdown]');
        if (!wrapper) return;
        const menu = wrapper.querySelector('[data-dropdown-menu]');
        if (!menu) return;

        const willOpen = menu.classList.contains('hidden');
        closeAllDropdowns(menu);
        menu.classList.toggle('hidden', !willOpen);
        menu.classList.toggle('block', willOpen);
        toggleEl.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    }

    /* ------------------------------------------------------------------ *
     * Delegasi event tunggal (idempoten terhadap SPA re-inject)
     * ------------------------------------------------------------------ */

    document.addEventListener('click', function (e) {
        const openTrigger = e.target.closest('[data-modal-open]');
        if (openTrigger) {
            e.preventDefault();
            openModal(openTrigger.getAttribute('data-modal-open'));
            return;
        }

        const closeTrigger = e.target.closest('[data-modal-close]');
        if (closeTrigger) {
            e.preventDefault();
            // Tutup modal terdekat; bila atribut diberi nilai, pakai itu sebagai target
            const explicit = closeTrigger.getAttribute('data-modal-close');
            closeModal(explicit ? explicit : closeTrigger.closest('[data-modal]'));
            return;
        }

        const toastClose = e.target.closest('[data-toast-close]');
        if (toastClose) {
            e.preventDefault();
            hideToast();
            return;
        }

        const ddToggle = e.target.closest('[data-dropdown-toggle]');
        if (ddToggle) {
            e.preventDefault();
            toggleDropdown(ddToggle);
            return;
        }

        // Menu di DALAM dropdown yang menavigasi (mis. daftar notifikasi)
        // harus ikut menutup dropdown-nya. Tanpa ini halaman berpindah
        // tetapi panel dropdown tetap menempel terbuka dan menutupi
        // konten - paling terasa di layar HP karena panelnya selebar
        // hampir seluruh layar.
        if (e.target.closest('[data-dropdown-menu] [data-page]')) {
            closeAllDropdowns();
            return;
        }

        // Klik di luar dropdown mana pun -> tutup semua
        if (!e.target.closest('[data-dropdown-menu]')) closeAllDropdowns();
    });

    // Esc menutup modal teratas + semua dropdown
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        closeAllDropdowns();
        const open = Array.from(document.querySelectorAll('[data-modal][data-open]'));
        if (open.length) closeModal(open[open.length - 1]);
    });

    /* ------------------------------------------------------------------ *
     * Export
     * ------------------------------------------------------------------ */

    /**
     * Handle ringan bergaya lama: mengembalikan objek ber-.show()/.hide()
     * untuk kode yang menyimpan instance ke variabel, mis.
     *   const modal = UI.modal.ref('#x');  modal.show();  ... modal.hide();
     * Ini hanya pembungkus tipis di atas openModal/closeModal — TIDAK menyimpan
     * state apa pun, jadi tidak ada instance basi seperti pada shim lama.
     */
    function modalRef(target) {
        const el = resolve(target);
        return {
            element: el,
            show: function () { return openModal(el); },
            hide: function () { return closeModal(el); },
            toggle: function () { return isOpen(el) ? closeModal(el) : openModal(el); }
        };
    }

    window.UI = {
        modal: {
            open: openModal,
            close: closeModal,
            ref: modalRef,
            isOpen: function (t) { return isOpen(resolve(t)); }
        },
        toast: {
            show: showToast,
            hide: hideToast
        },
        dropdown: {
            closeAll: closeAllDropdowns
        }
    };
})();
