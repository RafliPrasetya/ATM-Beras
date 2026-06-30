<div class="modal fade" id="modalTambahBerita" tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <form action="{{ route('berita.store') }}" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title">

                        Tambah Berita

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">

                            Judul Berita

                        </label>

                        <input type="text" name="judul" class="form-control" required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Gambar

                        </label>

                        <input type="file" name="gambar" class="form-control">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Konten

                        </label>

                        <textarea name="konten" rows="8" class="form-control" required></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Status

                        </label>

                        <select name="status" class="form-select">

                            <option value="publish">

                                Publish

                            </option>

                            <option value="draft">

                                Draft

                            </option>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                        Batal

                    </button>

                    <button type="submit" class="btn btn-tambah">

                        Simpan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
