@php
    $kopSurat = App\Models\KopSurat::first();
@endphp

<div class="kop-surat-template">
    @if ($kopSurat->logo)
        <img src="{{ asset('storage/' . $kopSurat->logo) }}" style="height: 80px; float: left; margin-right: 15px;">
    @endif

    <div style="overflow: hidden;">
        <h4 style="margin: 0; line-height: 1.2; text-align: center">{{ $kopSurat->kementerian }}</h4>
        <h3 style="margin: 0; text-align: center">{{ $kopSurat->universitas }}</h3>
        <h4 style="margin: 5px 0; text-align: center">{{ $kopSurat->fakultas }}</h4>

        <p style="margin: 5px 0; text-align: center"><strong>{{ $kopSurat->prodi }}</strong></p>
        <p style="margin: 5px 0; text-align: center">{{ $kopSurat->alamat }}</p>
        <p style="margin: 5px 0; text-align: center">{{ $kopSurat->kontak }}</p>
    </div>

    <div style="clear: both;"></div>
    <hr style="border-top: 2px solid #000; margin: 10px 0;">
</div>

<style>
    .kop-surat-template {
        font-family: 'Times New Roman', serif;
        margin-bottom: 20px;
    }

    .kop-surat-template h3 {
        font-size: 14pt;
        font-weight: bold;
    }

    .kop-surat-template h4 {
        font-size: 12pt;
    }

    .kop-surat-template p {
        font-size: 11pt;
        margin: 2px 0;
    }
</style>
