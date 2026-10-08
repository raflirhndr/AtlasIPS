document.querySelectorAll('.toggle').forEach((button) => {
    button.addEventListener('click', () => {
        const lessons = button.nextElementSibling;
        const open = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', String(!open));
        lessons.classList.toggle('open', !open);
    });
});
function confirmDelete(message) { return window.confirm(message || 'Yakin ingin menghapus data ini?'); }

// Admin V5: tabs
const adminTabs = document.querySelectorAll('[data-admin-tab]');
const adminPanels = document.querySelectorAll('[data-admin-panel]');
adminTabs.forEach((tab) => {
    tab.addEventListener('click', () => {
        const target = tab.dataset.adminTab;
        adminTabs.forEach((item) => item.classList.toggle('active', item === tab));
        adminPanels.forEach((panel) => panel.classList.toggle('active', panel.dataset.adminPanel === target));
        if (history.replaceState) history.replaceState(null, '', `#${target}`);
    });
});

if (adminTabs.length && location.hash) {
    const requested = document.querySelector(`[data-admin-tab="${location.hash.slice(1)}"]`);
    if (requested) requested.click();
}

// Admin V5: client-side filtering
const adminFilters = document.querySelectorAll('[data-filter-target]');
adminFilters.forEach((input) => {
    input.addEventListener('input', () => {
        const target = document.getElementById(input.dataset.filterTarget);
        if (!target) return;
        const query = input.value.trim().toLowerCase();
        target.querySelectorAll('.filter-item').forEach((item) => {
            item.classList.toggle('is-hidden', query !== '' && !item.dataset.filterText.includes(query));
        });
    });
});

// Admin V5: video preview before upload
const videoInput = document.getElementById('video-file-input');
const videoPreviewBox = document.getElementById('video-preview-box');
const videoPreview = document.getElementById('video-preview');
const videoFileInfo = document.getElementById('video-file-info');
let previewUrl = null;
if (videoInput && videoPreviewBox && videoPreview) {
    videoInput.addEventListener('change', () => {
        const file = videoInput.files?.[0];
        if (!file) {
            videoPreviewBox.hidden = true;
            videoPreview.removeAttribute('src');
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = null;
            return;
        }
        if (!file.type.startsWith('video/')) {
            videoInput.value = '';
            videoPreviewBox.hidden = true;
            alert('Pilih file video yang valid.');
            return;
        }
        if (file.size > 200 * 1024 * 1024) {
            videoInput.value = '';
            videoPreviewBox.hidden = true;
            alert('Ukuran video melebihi batas 200 MB.');
            return;
        }
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = URL.createObjectURL(file);
        videoPreview.src = previewUrl;
        videoPreviewBox.hidden = false;
        videoFileInfo.textContent = `${file.name} · ${(file.size / (1024 * 1024)).toFixed(1)} MB`;
    });
}

// V9: sumber video (upload sendiri / YouTube)
const videoSourceType = document.getElementById('video-source-type');
const videoUploadField = document.getElementById('video-upload-field');
const videoUrlField = document.getElementById('video-url-field');
const videoUrlInput = document.getElementById('video-url-input');
function syncVideoSourceFields() {
    if (!videoSourceType || !videoUploadField || !videoUrlField || !videoUrlInput) return;
    const upload = videoSourceType.value === 'upload';
    videoUploadField.hidden = !upload;
    videoUrlField.hidden = upload;
    const file = document.getElementById('video-file-input');
    if (file) file.required = upload;
    videoUrlInput.disabled = upload;
    videoUrlInput.required = !upload;
    if (!upload && videoSourceType.value === 'youtube') videoUrlInput.placeholder = 'https://www.youtube.com/watch?v=...';
}
if (videoSourceType) {
    videoSourceType.addEventListener('change', syncVideoSourceFields);
    syncVideoSourceFields();
}

document.querySelectorAll('.admin-video-edit-form').forEach((form) => {
    const type = form.querySelector('.edit-video-source');
    const fileWrap = form.querySelector('.edit-video-file');
    const urlWrap = form.querySelector('.edit-video-url');
    const urlInput = urlWrap?.querySelector('input[name="video_url"]');
    const fileInput = fileWrap?.querySelector('input[name="file_video"]');
    if (!type || !fileWrap || !urlWrap || !urlInput || !fileInput) return;
    const sync = () => {
        const upload = type.value === 'upload';
        fileWrap.hidden = !upload;
        urlWrap.hidden = upload;
        urlInput.disabled = upload;
        urlInput.required = !upload;
        if (!upload && type.value === 'youtube') urlInput.placeholder = 'https://www.youtube.com/watch?v=...';
        if (upload && form.dataset.hasFile !== '1') fileInput.required = true;
        else fileInput.required = false;
    };
    type.addEventListener('change', sync);
    sync();
});

document.addEventListener('DOMContentLoaded', () => {
    const menuButton = document.querySelector('.nav-menu-toggle');
    const nav = document.querySelector('#main-nav');
    if (menuButton && nav) {
        menuButton.addEventListener('click', () => {
            const expanded = menuButton.getAttribute('aria-expanded') === 'true';
            menuButton.setAttribute('aria-expanded', String(!expanded));
            nav.classList.toggle('open', !expanded);
        });
    }
    const chapterSearch = document.querySelector('#chapter-search');
    const chapters = [...document.querySelectorAll('#chapter-list .chapter')];
    const chapterEmpty = document.querySelector('#chapter-empty');
    if (chapterSearch && chapters.length) {
        const filter = () => {
            const q = chapterSearch.value.trim().toLowerCase();
            let visible = 0;
            chapters.forEach(chapter => {
                const match = !q || (chapter.dataset.chapterTitle || '').includes(q);
                chapter.hidden = !match;
                if (match) visible++;
            });
            if (chapterEmpty) chapterEmpty.classList.toggle('hidden', visible !== 0);
        };
        chapterSearch.addEventListener('input', filter);
    }
});


// Referensi Hybrid: link website / PDF
const referensiSourceType = document.querySelector('.referensi-source-type');
const referensiUrlField = document.querySelector('.referensi-url-field');
const referensiPdfField = document.querySelector('.referensi-pdf-field');
function syncReferensiSourceFields() {
    if (!referensiSourceType || !referensiUrlField || !referensiPdfField) return;
    const pdf = referensiSourceType.value === 'pdf';
    referensiUrlField.hidden = pdf;
    referensiPdfField.hidden = !pdf;
    const urlInput = referensiUrlField.querySelector('input[name="url"]');
    const pdfInput = referensiPdfField.querySelector('input[name="file_pdf"]');
    if (urlInput) {
        urlInput.disabled = pdf;
        urlInput.required = !pdf;
    }
    if (pdfInput) {
        pdfInput.disabled = !pdf;
        pdfInput.required = pdf;
    }
}
if (referensiSourceType) {
    referensiSourceType.addEventListener('change', syncReferensiSourceFields);
    syncReferensiSourceFields();
}

document.querySelectorAll('.admin-referensi-edit-form').forEach((form) => {
    const type = form.querySelector('.edit-referensi-source');
    const urlWrap = form.querySelector('.edit-referensi-url');
    const pdfWrap = form.querySelector('.edit-referensi-pdf');
    const urlInput = urlWrap?.querySelector('input[name="url"]');
    const pdfInput = pdfWrap?.querySelector('input[name="file_pdf"]');
    if (!type || !urlWrap || !pdfWrap || !urlInput || !pdfInput) return;

    const sync = () => {
        const pdf = type.value === 'pdf';
        urlWrap.hidden = pdf;
        pdfWrap.hidden = !pdf;
        urlInput.disabled = pdf;
        urlInput.required = !pdf;
        pdfInput.disabled = !pdf;
        pdfInput.required = pdf && form.dataset.hasFile !== '1';
    };

    type.addEventListener('change', sync);
    sync();
});
