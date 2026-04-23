{{-- resources/views/admin/pendaftaran-ujian-hasil/debug-sync.blade.php --}}
@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">
    <h4>Debug Sync File ke Repodosen</h4>
    
    <div class="card">
        <div class="card-body">
            <h5>Pendaftaran: {{ $pendaftaran->user->name }} ({{ $pendaftaran->user->nim }})</h5>
            
            <table class="table">
                <tr>
                    <th>Field</th>
                    <th>Path di Database</th>
                    <th>Exists (local)</th>
                    <th>Exists (public)</th>
                    <th>Size</th>
                </tr>
                <tr>
                    <td>file_skripsi</td>
                    <td>{{ $pendaftaran->file_skripsi }}</td>
                    <td>{{ Storage::disk('local')->exists($pendaftaran->file_skripsi) ? 'Yes' : 'No' }}</td>
                    <td>{{ Storage::disk('public')->exists($pendaftaran->file_skripsi) ? 'Yes' : 'No' }}</td>
                    <td>
                        @if($pendaftaran->file_skripsi && Storage::disk('local')->exists($pendaftaran->file_skripsi))
                            {{ round(Storage::disk('local')->size($pendaftaran->file_skripsi) / 1024, 2) }} KB
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>file_sk_pembimbing</td>
                    <td>{{ $pendaftaran->file_sk_pembimbing }}</td>
                    <td>{{ Storage::disk('local')->exists($pendaftaran->file_sk_pembimbing) ? 'Yes' : 'No' }}</td>
                    <td>{{ Storage::disk('public')->exists($pendaftaran->file_sk_pembimbing) ? 'Yes' : 'No' }}</td>
                    <td>
                        @if($pendaftaran->file_sk_pembimbing && Storage::disk('local')->exists($pendaftaran->file_sk_pembimbing))
                            {{ round(Storage::disk('local')->size($pendaftaran->file_sk_pembimbing) / 1024, 2) }} KB
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>file_proposal</td>
                    <td>{{ $pendaftaran->file_proposal }}</td>
                    <td>{{ Storage::disk('local')->exists($pendaftaran->file_proposal) ? 'Yes' : 'No' }}</td>
                    <td>{{ Storage::disk('public')->exists($pendaftaran->file_proposal) ? 'Yes' : 'No' }}</td>
                    <td>
                        @if($pendaftaran->file_proposal && Storage::disk('local')->exists($pendaftaran->file_proposal))
                            {{ round(Storage::disk('local')->size($pendaftaran->file_proposal) / 1024, 2) }} KB
                        @endif
                    </td>
                </tr>
            </table>
            
            <form action="{{ route('admin.pendaftaran-ujian-hasil.sync-to-repodosen', $pendaftaran) }}" method="POST">
                @csrf
                <input type="hidden" name="mode" value="skripsi">
                <button type="submit" class="btn btn-primary">Sync Ulang ke Repodosen</button>
            </form>
        </div>
    </div>
</div>
@endsection