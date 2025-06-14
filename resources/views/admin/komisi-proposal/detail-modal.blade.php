<div class="row">
    <div class="col-12">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <th width="200px">Nama Mahasiswa</th>
                    <td>{{ $komisi->user->name }}</td>
                </tr>
                <tr>
                    <th>NIM</th>
                    <td>{{ $komisi->user->nim }}</td>
                </tr>
                <tr>
                    <th>Tanggal Pengajuan</th>
                    <td> {{ \Carbon\Carbon::parse($komisi->created_at)->translatedFormat('l, d M Y') }}</td>
                </tr>
                <tr>
                    <th style="vertical-align: top">Judul Skripsi</th>
                    <td style="vertical-align: top">{!! $komisi->judul_skripsi !!}</td>
                </tr>
                <tr>
                    <th>Pembimbing 1</th>
                    <td>{{ $komisi->pembimbing->name }}</td>
                </tr>
                <tr>
                    <th>Status Saat Ini</th>
                    <td>
                        @if ($komisi->status == 'pending')
                            <span class="badge bg-label-warning">Pending</span>
                        @elseif($komisi->status == 'approved')
                            <span class="badge bg-label-success">Approved</span>
                        @else
                            <span class="badge bg-label-danger">Rejected</span>
                        @endif
                    </td>
                </tr>
                @if ($komisi->keterangan)
                    <tr>
                        <th style="vertical-align: top">Keterangan</th>
                        <td style="vertical-align: top">{{ $komisi->keterangan }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <form action="{{ route('admin.komisi-proposal.update-status', $komisi->id) }}" method="POST" class="w-100">
            @csrf
            <div class="mb-3">
                <label for="status" class="form-label">Ubah Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="approved" {{ $komisi->status == 'approved' ? 'selected' : '' }}>Approve</option>
                    <option value="rejected" {{ $komisi->status == 'rejected' ? 'selected' : '' }}>Reject</option>
                    <option value="pending" {{ $komisi->status == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ $komisi->keterangan }}</textarea>
            </div>
            <button type="submit" class="btn btn-warning w-100">Update Status</button>
        </form>

        @if ($komisi->status == 'approved' && $komisi->file_komisi)
            <a href="{{ route('admin.komisi-proposal.download', $komisi->id) }}" class="btn btn-success w-100 mt-2"
                target="_blank">
                <i class="bx bxs-file-pdf me-1"></i> Download PDF
            </a>
        @endif
    </div>
</div>
