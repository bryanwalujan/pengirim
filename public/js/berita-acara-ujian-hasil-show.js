/**
 * Berita Acara Ujian Hasil - Detail Page JavaScript
 * Handles: Penilaian modals, approval workflows, grade calculations, etc.
 */

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Prepare data for modal (injected from Blade)
const BaData = {
    penilaian: window.baPenilaianData || {},
    koreksi: window.baKoreksiData || {},
    penguji: window.baPengujiData || {},
    csrfToken: window.baCsrfToken || '',
    baId: window.baId || null
};

/**
 * Calculate grade letter based on nilai_mutu
 * @param {number|null} nilaiMutu
 * @returns {string}
 */
function getGradeLetter(nilaiMutu) {
    if (nilaiMutu === null || nilaiMutu === undefined) return '-';
    if (nilaiMutu >= 3.60) return 'A';
    if (nilaiMutu >= 3.00) return 'B';
    if (nilaiMutu >= 2.00) return 'C';
    if (nilaiMutu >= 1.00) return 'D';
    return 'E';
}

/**
 * Get badge class based on grade
 * @param {string} grade
 * @returns {string}
 */
function getGradeBadgeClass(grade) {
    const classes = {
        'A': 'bg-success',
        'B': 'bg-info',
        'C': 'bg-warning',
        'D': 'bg-danger',
        'E': 'bg-dark'
    };
    return classes[grade] || 'bg-label-warning';
}

/**
 * Show detail modal with penilaian and koreksi data
 * @param {number} dosenId
 * @param {string} dosenName
 * @param {string} posisi
 */
function showDetailModal(dosenId, dosenName, posisi) {
    document.getElementById('detail_dosen_name').value = dosenName;
    document.getElementById('detail_posisi').textContent = posisi;

    const penilaian = BaData.penilaian[dosenId];
    renderPenilaianSection(penilaian);
    renderKoreksiSection(dosenId);

    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();
}

/**
 * Render penilaian section in modal
 * @param {object|null} penilaian
 */
function renderPenilaianSection(penilaian) {
    const penilaianContent = document.getElementById('penilaianContent');
    
    if (!penilaian) {
        penilaianContent.innerHTML = createEmptyState('Belum ada data penilaian');
        return;
    }

    const komponenConfig = {
        nilai_kebaruan: { label: 'Kebaruan dan Signifikansi Penelitian', bobot: 1.5 },
        nilai_kesesuaian: { label: 'Kesesuaian Judul, Masalah, Tujuan, Pembahasan, Kesimpulan, dan Saran', bobot: 1.5 },
        nilai_metode: { label: 'Metode Penelitian dan Pemecahan Masalah', bobot: 1 },
        nilai_kajian_teori: { label: 'Kajian Teori', bobot: 1 },
        nilai_hasil_penelitian: { label: 'Hasil Penelitian', bobot: 3 },
        nilai_referensi: { label: 'Referensi', bobot: 1 },
        nilai_tata_bahasa: { label: 'Tata Bahasa', bobot: 1 }
    };

    let komponenRows = '';
    Object.entries(komponenConfig).forEach(([key, config]) => {
        const nilai = penilaian[key];
        if (nilai !== null && nilai !== undefined) {
            const kontribusi = ((nilai / 100) * config.bobot).toFixed(2);
            komponenRows += `
                <tr>
                    <td>${config.label}</td>
                    <td class="text-center fw-bold">${config.bobot}</td>
                    <td class="text-center">${nilai}</td>
                    <td class="text-center fw-bold text-primary">${kontribusi}</td>
                </tr>
            `;
        }
    });

    const grade = getGradeLetter(penilaian.nilai_mutu);
    const gradeBadge = getGradeBadgeClass(grade);

    penilaianContent.innerHTML = `
        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-3 bg-white rounded border">
                    <small class="text-muted d-block mb-1">Nilai Mutu (Skala 4.00)</small>
                    <div class="fs-3 fw-bold text-warning">${penilaian.nilai_mutu ? parseFloat(penilaian.nilai_mutu).toFixed(2) : '-'}</div>
                    <span class="badge ${gradeBadge}">${grade}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 bg-white rounded border">
                    <small class="text-muted d-block mb-1">Total Bobot</small>
                    <div class="fs-3 fw-bold text-info">${penilaian.total_nilai ? parseFloat(penilaian.total_nilai).toFixed(2) : '-'}</div>
                    <small class="text-muted">dari maksimal 10.00</small>
                </div>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Komponen</th>
                                <th class="text-center" width="12%">Bobot</th>
                                <th class="text-center" width="15%">Nilai</th>
                                <th class="text-center" width="15%">Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody>${komponenRows}</tbody>
                        <tfoot class="table-primary">
                            <tr>
                                <th colspan="3" class="text-end">Total Bobot</th>
                                <th class="text-center">${penilaian.total_nilai ? parseFloat(penilaian.total_nilai).toFixed(2) : '-'}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mt-2 p-2 bg-light rounded border">
                    <small class="text-muted">
                        <strong>Rumus:</strong> Nilai Mutu = (Total Bobot / 10) × 4 = ${penilaian.nilai_mutu ? parseFloat(penilaian.nilai_mutu).toFixed(2) : '-'}
                    </small>
                </div>
            </div>
            ${penilaian.catatan ? `
                <div class="col-12">
                    <label class="form-label fw-bold small">Catatan Dosen:</label>
                    <div class="p-3 bg-white rounded border">
                        <p class="mb-0 small text-muted">${penilaian.catatan}</p>
                    </div>
                </div>
            ` : ''}
        </div>
    `;
}

/**
 * Render koreksi section in modal
 * @param {number} dosenId
 */
function renderKoreksiSection(dosenId) {
    const koreksiContent = document.getElementById('koreksiContent');
    const koreksi = BaData.koreksi[dosenId];

    if (!koreksi || !koreksi.koreksi_data || koreksi.koreksi_data.length === 0) {
        koreksiContent.innerHTML = createEmptyState('Belum ada lembar koreksi');
        return;
    }

    const rows = koreksi.koreksi_data.map(item => `
        <tr>
            <td class="text-center fw-bold">${item.halaman || '-'}</td>
            <td>${item.catatan || '-'}</td>
        </tr>
    `).join('');

    const dateStr = koreksi.created_at ? new Date(koreksi.created_at).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }) : '-';

    koreksiContent.innerHTML = `
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="15%">Halaman</th>
                        <th>Catatan Koreksi</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        </div>
        <div class="mt-3 p-2 bg-white rounded border">
            <small class="text-muted">
                <i class="bx bx-time me-1"></i>
                Diisi pada: ${dateStr}
            </small>
        </div>
    `;
}

/**
 * Create empty state HTML
 * @param {string} message
 * @returns {string}
 */
function createEmptyState(message) {
    return `
        <div class="text-center text-muted py-3">
            <i class="bx bx-info-circle fs-4"></i>
            <p class="mb-0 mt-2">${message}</p>
        </div>
    `;
}

/**
 * Show approve on behalf modal
 * @param {number} dosenId
 * @param {string} dosenName
 * @param {string} posisi
 */
function showApproveOnBehalfModal(dosenId, dosenName, posisi) {
    const form = document.getElementById('approveOnBehalfForm');
    form.action = `/admin/berita-acara-ujian-hasil/${BaData.baId}/approve-on-behalf`;

    document.getElementById('modal_dosen_id').value = dosenId;
    document.getElementById('modal_dosen_name').value = dosenName;
    document.getElementById('alasan').value = '';
    document.getElementById('confirmation').checked = false;

    // Reset penilaian fields
    document.getElementById('nilai_mutu').value = '';
    document.getElementById('catatan_penilaian').value = '';
    updateGradePreview('');
    hideNilaiMutuWarning();

    // Handle Lembar Koreksi section visibility
    const koreksiSection = document.getElementById('lembarKoreksiSection');
    const tbody = document.getElementById('koreksiTableBody');
    const isPembimbing = posisi && (posisi.includes('PS1') || posisi.includes('PS2') || posisi.includes('Pembimbing'));

    if (isPembimbing) {
        koreksiSection.style.display = 'block';
        tbody.innerHTML = '';
        addKoreksiRow();
    } else {
        koreksiSection.style.display = 'none';
        tbody.innerHTML = '';
    }

    const modal = new bootstrap.Modal(document.getElementById('approveOnBehalfModal'));
    modal.show();
}

// ========== NILAI MUTU VALIDATION FUNCTIONS ==========

/**
 * Validate keypress for nilai mutu input
 * @param {Event} event
 * @returns {boolean}
 */
function isValidNilaiMutuKey(event) {
    const char = String.fromCharCode(event.which);
    const input = event.target;
    const currentValue = input.value;

    // Allow: backspace, delete, tab, escape, enter
    if ([8, 9, 13, 27, 46].includes(event.keyCode)) return true;

    // Allow digits 0-9
    if (/[0-9]/.test(char)) {
        const newValue = currentValue + char;
        const numValue = parseFloat(newValue);
        if (!isNaN(numValue) && numValue > 4) {
            return false;
        }
        return true;
    }

    // Allow one decimal point
    if (char === '.') {
        return !currentValue.includes('.');
    }

    return false;
}

/**
 * Validate and clamp nilai mutu in real-time
 * @param {HTMLInputElement} input
 */
function validateAndClampNilaiMutu(input) {
    let value = input.value;

    // Remove invalid characters
    value = value.replace(/[^0-9.]/g, '');

    // Ensure only one decimal point
    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
    }

    // Limit to 2 decimal places
    if (parts.length === 2 && parts[1].length > 2) {
        value = parts[0] + '.' + parts[1].substring(0, 2);
    }

    // Parse and validate range
    const numValue = parseFloat(value);

    if (!isNaN(numValue)) {
        if (numValue > 4) {
            value = '4.00';
            input.value = value;
            showNilaiMutuWarning('Nilai maksimal adalah 4.00');
        } else if (numValue < 0) {
            value = '0.00';
            input.value = value;
            showNilaiMutuWarning('Nilai minimal adalah 0.00');
        } else {
            input.value = value;
            hideNilaiMutuWarning();
        }
    } else {
        input.value = value;
    }

    updateGradePreview(input.value);
}

/**
 * Format nilai mutu on blur
 * @param {HTMLInputElement} input
 */
function formatNilaiMutu(input) {
    let value = input.value.trim();

    if (value === '' || value === '.') {
        input.value = '';
        updateGradePreview('');
        return;
    }

    let numValue = parseFloat(value);

    if (isNaN(numValue)) {
        input.value = '';
        updateGradePreview('');
        return;
    }

    // Clamp between 0 and 4
    numValue = Math.min(4, Math.max(0, numValue));

    // Format to 2 decimal places
    input.value = numValue.toFixed(2);
    updateGradePreview(input.value);
}

/**
 * Show warning message
 * @param {string} message
 */
function showNilaiMutuWarning(message) {
    let warning = document.getElementById('nilaiMutuWarning');
    if (!warning) {
        warning = document.createElement('div');
        warning.id = 'nilaiMutuWarning';
        warning.className = 'text-danger small mt-1';
        const input = document.getElementById('nilai_mutu');
        input.parentNode.appendChild(warning);
    }
    warning.innerHTML = '<i class="bx bx-error-circle me-1"></i>' + message;
    warning.style.display = 'block';

    setTimeout(() => {
        hideNilaiMutuWarning();
    }, 2000);
}

/**
 * Hide warning message
 */
function hideNilaiMutuWarning() {
    const warning = document.getElementById('nilaiMutuWarning');
    if (warning) {
        warning.style.display = 'none';
    }
}

/**
 * Update grade preview based on nilai mutu input
 * @param {string} value
 */
function updateGradePreview(value) {
    const gradePreview = document.getElementById('gradePreview');
    const gradeDescription = document.getElementById('gradeDescription');

    if (!value || value === '' || isNaN(parseFloat(value))) {
        gradePreview.textContent = '-';
        gradePreview.className = 'badge bg-secondary fs-6 px-3 py-2';
        gradeDescription.textContent = 'Masukkan nilai';
        return;
    }

    const nilaiMutu = parseFloat(value);
    const grades = [
        { min: 3.60, grade: 'A', class: 'bg-success', desc: 'Sangat Baik' },
        { min: 3.00, grade: 'B', class: 'bg-info', desc: 'Baik' },
        { min: 2.00, grade: 'C', class: 'bg-warning', desc: 'Cukup' },
        { min: 1.00, grade: 'D', class: 'bg-danger', desc: 'Kurang' },
        { min: 0, grade: 'E', class: 'bg-dark', desc: 'Sangat Kurang' }
    ];

    const result = grades.find(g => nilaiMutu >= g.min);

    gradePreview.textContent = result.grade;
    gradePreview.className = `badge ${result.class} fs-6 px-3 py-2`;
    gradeDescription.textContent = `${result.desc} (${parseFloat(nilaiMutu).toFixed(2)})`;
}

// ========== KOREKSI TABLE FUNCTIONS ==========

let koreksiRowIndex = 0;

/**
 * Add new row to koreksi table
 */
function addKoreksiRow() {
    const tbody = document.getElementById('koreksiTableBody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <input type="text" name="lembar_koreksi[${koreksiRowIndex}][halaman]" class="form-control form-control-sm" placeholder="Hal.">
        </td>
        <td>
            <textarea name="lembar_koreksi[${koreksiRowIndex}][catatan]" class="form-control form-control-sm" rows="1" placeholder="Catatan..."></textarea>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-icon btn-label-danger" onclick="this.closest('tr').remove()">
                <i class="bx bx-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    koreksiRowIndex++;
}

// ========== DELETE CONFIRMATION ==========

const STATUS_LABELS = {
    'draft': 'Draft',
    'menunggu_ttd_penguji': 'Menunggu TTD Penguji',
    'menunggu_ttd_ketua': 'Menunggu TTD Ketua',
    'selesai': 'Selesai',
    'ditolak': 'Ditolak'
};

/**
 * Delete berita acara with SweetAlert confirmation
 * @param {number} id
 * @param {string} mahasiswaName
 * @param {string} status
 */
function deleteBeritaAcara(id, mahasiswaName, status) {
    const isSelesai = status === 'selesai';
    const statusLabel = STATUS_LABELS[status] || status;

    const warningMessage = isSelesai ?
        `<div class="alert alert-danger mt-3 mb-0 text-white" style="background-color: #ff3e1d !important;">
            <i class="bx bx-error-circle me-2"></i>
            <strong>PERINGATAN!</strong> Dokumen ini sudah <strong>SELESAI</strong>. 
            Penghapusan akan menghilangkan semua data permanen!
        </div>` :
        `<div class="alert alert-warning mt-3 mb-0" style="background-color: #fff2e0 !important; color: #ffab00 !important;">
            <i class="bx bx-error-circle me-2"></i>
            Data berita acara akan dihapus permanen!
        </div>`;

    Swal.fire({
        title: isSelesai ? '⚠️ Hapus Dokumen Selesai?' : 'Hapus Berita Acara?',
        html: `
            <div class="text-center p-2">
                <p class="text-muted mb-4">Konfirmasi penghapusan rekaman untuk mahasiswa:</p>
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="fw-bold fs-5 text-dark">${mahasiswaName}</div>
                    <div class="text-warning fw-bold small mb-0">${statusLabel}</div>
                </div>
                ${warningMessage}
            </div>
        `,
        icon: isSelesai ? 'error' : 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus Permanen',
        cancelButtonText: 'Batalkan',
        confirmButtonColor: '#ff3e1d',
        cancelButtonColor: '#8592a3',
        reverseButtons: true,
        customClass: {
            container: 'premium-swal-container',
            popup: 'rounded-3 border-0 shadow-lg',
            confirmButton: 'btn btn-danger px-4 py-2 fw-bold',
            cancelButton: 'btn btn-secondary px-4 py-2'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitDeleteForm(id);
        }
    });
}

/**
 * Submit delete form programmatically
 * @param {number} id
 */
function submitDeleteForm(id) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/berita-acara-ujian-hasil/${id}`;

    form.innerHTML = `
        <input type="hidden" name="_token" value="${BaData.csrfToken}">
        <input type="hidden" name="_method" value="DELETE">
    `;

    document.body.appendChild(form);
    form.submit();
}

// Export functions for global access
window.BaUjianHasil = {
    showDetailModal,
    showApproveOnBehalfModal,
    isValidNilaiMutuKey,
    validateAndClampNilaiMutu,
    formatNilaiMutu,
    updateGradePreview,
    addKoreksiRow,
    deleteBeritaAcara,
    getGradeLetter,
    getGradeBadgeClass
};
