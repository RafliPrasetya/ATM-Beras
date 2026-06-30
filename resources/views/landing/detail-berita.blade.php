@extends('landing.layouts.app')

@section('title', $news->judul . ' - ATM Beras Rogojampi')

@section('content')
    <section class="detail-news-section">

        <div class="container">

            <div class="detail-news-card">

                <div class="row align-items-start">

                    {{-- KONTEN --}}
                    <div class="col-lg-7">

                        <h1 class="detail-title">
                            {{ $news->judul }}
                        </h1>

                        <div class="detail-meta">

                            <span class="badge bg-success">
                                {{ ucfirst($news->status) }}
                            </span>

                            <span>
                                {{ $news->created_at->format('d F Y') }}
                            </span>

                        </div>

                        <div class="detail-content">

                            {!! nl2br(e($news->konten)) !!}

                        </div>

                        <a href="{{ url('/#berita') }}" class="mt-4 btn btn-secondary">

                            <i class="bi bi-arrow-left"></i>
                            Kembali

                        </a>

                    </div>

                    {{-- GAMBAR --}}
                    <div class="col-lg-5">

                        @if ($news->gambar)
                            <div class="news-image-wrapper">

                                <img src="{{ asset('storage/' . $news->gambar) }}" class="img-fluid detail-image"
                                    data-bs-toggle="modal" data-bs-target="#imageModal">

                            </div>
                        @endif

                    </div>

                </div>

            </div>

        </div>

    </section>

    {{-- MODAL PREVIEW GAMBAR --}}
    <div class="modal fade" id="imageModal" tabindex="-1">

        <div class="modal-dialog modal-xl modal-dialog-centered">

            <div class="bg-transparent border-0 modal-content">

                <button class="mb-2 btn-close btn-close-white ms-auto" data-bs-dismiss="modal">
                </button>

                <img src="{{ asset('storage/' . $news->gambar) }}" class="rounded shadow img-fluid">

            </div>

        </div>

    </div>
@endsection
