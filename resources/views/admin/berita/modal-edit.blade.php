@foreach ($news as $item)
    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <form action="{{ route('berita.update', $item->id) }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <div class="modal-header">

                        <h5 class="modal-title">

                            Edit Berita

                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label class="form-label">

                                Judul Berita

                            </label>

                            <input type="text" name="judul" class="form-control" value="{{ $item->judul }}"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Gambar Saat Ini

                            </label>

                            <br>

                            @if ($item->gambar)
                                <img src="{{ asset('storage/' . $item->gambar) }}" width="150" class="rounded">
                            @else
                                <span class="text-muted">

                                    Tidak ada gambar

                                </span>
                            @endif

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Ganti Gambar

                            </label>

                            <input type="file" name="gambar" class="form-control">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Konten

                            </label>

                            <textarea name="konten" rows="8" class="form-control" required>{{ $item->konten }}</textarea>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Status

                            </label>

                            <select name="status" class="form-select">

                                <option value="publish" {{ $item->status == 'publish' ? 'selected' : '' }}>

                                    Publish

                                </option>

                                <option value="draft" {{ $item->status == 'draft' ? 'selected' : '' }}>

                                    Draft

                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                            Batal

                        </button>

                        <button type="submit" class="btn btn-primary">

                            Update

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endforeach
