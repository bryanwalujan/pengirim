{{-- Empty State for Keputusan BA (when not yet filled) --}}
<div class="card mb-4 border border-dashed bg-light text-center py-5">
    <div class="card-body">
        <div class="avatar avatar-lg mx-auto mb-3">
            <span class="avatar-initial rounded-circle bg-label-secondary">
                <i class="bx bx-edit-alt"></i>
            </span>
        </div>
        <h5 class="fw-bold">Isi Berita Acara Belum Tersedia</h5>
        <p class="text-muted mb-0 small mx-auto" style="max-width: 350px;">
            @if ($beritaAcara->isMenungguTtdPenguji())
                Sistem menunggu semua dosen penguji menyetujui dokumen sebelum Ketua Penguji dapat mengisikan hasil.
            @else
                Ketua Penguji dapat melakukan pengisian data hasil ujian melalui panel kontrol di atas.
            @endif
        </p>
    </div>
</div>
