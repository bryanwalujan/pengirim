@extends('layouts.user.app')

@section('title', 'Surat Aktif Kuliah')

@push('style')
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="mt-4">Surat Aktif Kuliah</h1>
                <p>Silahkan isi form berikut untuk membuat surat aktif kuliah.</p>
                <form action="{{ route('user.surat-aktif-kuliah.store') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </form>
            </div>
        </div>
    @endsection
