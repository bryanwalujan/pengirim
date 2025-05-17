@php
    $kopSurat = App\Models\KopSurat::first();
@endphp

<div class="kop-surat-template">
    @if ($kopSurat->logo)
        <img src="{{ storage_path('app/public/' . $kopSurat->logo) }}"
            style="height: 100px; float: left; margin-right: 15px;">
    @endif

    <div style="overflow: hidden;">
        <p style="font-size: 15px">{{ $kopSurat->kementerian }}</p>
        <p style="font-size: 14px">{{ $kopSurat->universitas }}</p>
        <p style="font-size: 14px">{{ $kopSurat->fakultas }}</p>
        <p>{{ $kopSurat->prodi }} </p>
        <p>{{ $kopSurat->alamat }}</p>
        <p>{{ $kopSurat->kontak }}</p>
    </div>

    <div style="clear: both;"></div>
    <hr style="border-top: 0.1px solid #000; margin: 2px 0;">
    <hr style="border-top: 1.5px solid #000; margin: 2px 0;">
</div>

<style>
    .kop-surat-template {
        font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
        margin-bottom: 20px;
        text-align: center
    }

    .kop-surat-template p {
        line-height: 1.1;
        margin: 2px 0;
    }

    .kop-surat-template p:nth-child(4) {
        font-weight: 700;
        font-size: 16px;
    }

    .kop-surat-template p:nth-child(5),
    .kop-surat-template p:nth-child(6) {
        font-size: 11px;
        margin: 4px 0
    }

    .kop-surat-template p:nth-child(6) {
        color: rgb(129, 129, 226);
    }
</style>
