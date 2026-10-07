import 'bootstrap/dist/css/bootstrap.min.css';
import '@phosphor-icons/web/regular';
import '@phosphor-icons/web/bold';
import '@fontsource-variable/geist';
import '@fontsource/geist-mono/400.css';
import '@fontsource/instrument-serif/400.css';
import '../css/app.css';

import * as bootstrap from 'bootstrap';
import './transaksi';

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    // Tooltip Bootstrap
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));

    // Konfirmasi sebelum menghapus / tindakan penting
    document.querySelectorAll('form[data-konfirmasi]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.konfirmasi)) {
                event.preventDefault();
            }
        });
    });

    // Cegah klik ganda pada tombol simpan
    document.querySelectorAll('form[data-cegah-ganda]').forEach((form) => {
        form.addEventListener('submit', () => {
            form.querySelectorAll('button[type="submit"]').forEach((btn) => {
                btn.disabled = true;
                if (btn.dataset.teksProses) {
                    btn.innerHTML = btn.dataset.teksProses;
                }
            });
        });
    });

    // Tampilkan nama file yang dipilih pada area unggah
    document.querySelectorAll('[data-unggah-file]').forEach((wrap) => {
        const input = wrap.querySelector('input[type="file"]');
        const label = wrap.querySelector('[data-nama-file]');
        input?.addEventListener('change', () => {
            if (label) {
                label.textContent = input.files.length ? input.files[0].name : label.dataset.default;
            }
            wrap.classList.toggle('terisi', input.files.length > 0);
        });
    });
});
