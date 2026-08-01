/**
 * dom.js — Helper DOM vanilla ringkas.
 *
 * Menggantikan subset jQuery yang benar-benar dipakai project ini
 * (seleksi, event, val/text/html, show/hide, class, ajax) tanpa memuat
 * pustaka 90 KB. Bukan tiruan jQuery yang lengkap — hanya yang dibutuhkan.
 *
 * Catatan penting soal show/hide:
 *   jQuery .show()/.hide() menulis `style.display`. Di sini kita memakai
 *   class Tailwind `hidden` agar konsisten dengan sisa aplikasi.
 */
(function (window) {
    'use strict';

    /* ---------------------------------------------------------------- *
     * Seleksi
     * ---------------------------------------------------------------- */
    function qs(sel, root) { return (root || document).querySelector(sel); }
    function qsa(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

    /* ---------------------------------------------------------------- *
     * Event — selalu delegasi di document supaya idempoten terhadap
     * SPA re-inject (#content diganti app.js). Ini yang dulu dipaksakan
     * dengan pola $(document).off(...).on(...).
     * ---------------------------------------------------------------- */
    function on(eventName, selector, handler) {
        document.addEventListener(eventName, function (e) {
            const target = e.target.closest(selector);
            if (target && document.contains(target)) {
                handler.call(target, e);
            }
        });
    }

    /* ---------------------------------------------------------------- *
     * Tampil / sembunyi (berbasis class `hidden`)
     * ---------------------------------------------------------------- */
    function show(el) {
        if (!el) return;
        el.classList.remove('hidden');
        // Bersihkan sisa display:none inline dari kode lama
        if (el.style.display === 'none') el.style.display = '';
    }
    function hide(el) {
        if (!el) return;
        el.classList.add('hidden');
    }
    function toggle(el, force) {
        if (!el) return;
        const shouldShow = typeof force === 'boolean' ? force : el.classList.contains('hidden');
        shouldShow ? show(el) : hide(el);
    }

    /* ---------------------------------------------------------------- *
     * Nilai & isi
     * ---------------------------------------------------------------- */
    function val(el, value) {
        if (!el) return '';
        if (value === undefined) return el.value;
        el.value = value;
        return el;
    }
    function text(el, value) {
        if (!el) return '';
        if (value === undefined) return el.textContent;
        el.textContent = value;
        return el;
    }
    function html(el, value) {
        if (!el) return '';
        if (value === undefined) return el.innerHTML;
        el.innerHTML = value;
        return el;
    }

    /* ---------------------------------------------------------------- *
     * Class
     * ---------------------------------------------------------------- */
    function addClass(el, classes) {
        if (!el) return;
        String(classes).split(/\s+/).filter(Boolean).forEach(function (c) { el.classList.add(c); });
    }
    function removeClass(el, classes) {
        if (!el) return;
        String(classes).split(/\s+/).filter(Boolean).forEach(function (c) { el.classList.remove(c); });
    }

    /* ---------------------------------------------------------------- *
     * AJAX — pengganti $.ajax untuk pola yang dipakai di project ini
     * ---------------------------------------------------------------- */

    /** POST form-urlencoded, balas JSON. Pengganti $.ajax({data:{...}}). */
    function postJSON(url, data) {
        let body;
        if (data instanceof FormData) {
            body = new URLSearchParams(data).toString();
        } else if (typeof data === 'string') {
            body = data;
        } else {
            body = new URLSearchParams(data || {}).toString();
        }
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body,
        }).then(function (r) { return r.json(); });
    }

    /** POST multipart (upload file). Pengganti $.ajax({processData:false}). */
    function postForm(url, formData) {
        return fetch(url, { method: 'POST', body: formData })
            .then(function (r) { return r.json(); });
    }

    /** POST body JSON (Content-Type: application/json). Balas JSON. */
    function postBodyJSON(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        }).then(function (r) { return r.json(); });
    }

    // getJSON() dan serialize() dihapus: keduanya tidak pernah dipanggil.
    // Untuk GET cukup fetch(url).then(r => r.json()); untuk serialisasi form
    // pakai new URLSearchParams(new FormData(form)).toString().

    window.dom = {
        qs: qs, qsa: qsa, on: on,
        show: show, hide: hide, toggle: toggle,
        val: val, text: text, html: html,
        addClass: addClass, removeClass: removeClass,
        postJSON: postJSON, postForm: postForm, postBodyJSON: postBodyJSON,
    };
})(window);
