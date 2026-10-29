<article class="bumdes-manager-card" data-bumdes-manager>
    <div class="bumdes-manager-card__heading">
        <span class="bumdes-manager-card__number">{{ is_numeric($index) ? str_pad($index + 1, 2, '0', STR_PAD_LEFT) : '+' }}</span>
        <h3>Data pengurus</h3>
        <button class="bumdes-manager-card__remove" type="button" data-remove-bumdes-manager aria-label="Hapus kartu pengurus">Hapus kartu</button>
    </div>
    <div class="bumdes-manager-card__fields">
        <div class="bumdes-manager-card__field">
            <label for="pengurus-{{ $index }}-nama">Nama pengurus <span class="bumdes-manager-card__required">diisi bersama jabatan</span></label>
            <input id="pengurus-{{ $index }}-nama" name="pengurus[{{ $index }}][nama]" type="text" maxlength="255" value="{{ $anggota['nama'] ?? '' }}" placeholder="Contoh: Bapak Ahmad" autocomplete="name">
        </div>
        <div class="bumdes-manager-card__field">
            <label for="pengurus-{{ $index }}-jabatan">Jabatan <span class="bumdes-manager-card__required">diisi bersama nama</span></label>
            <input id="pengurus-{{ $index }}-jabatan" name="pengurus[{{ $index }}][jabatan]" type="text" maxlength="255" value="{{ $anggota['jabatan'] ?? '' }}" placeholder="Contoh: Direktur">
        </div>
        <div class="bumdes-manager-card__field bumdes-manager-card__field--full">
            <label for="pengurus-{{ $index }}-deskripsi">Keterangan tugas <span class="bumdes-manager-card__optional">(boleh dikosongkan)</span></label>
            <textarea id="pengurus-{{ $index }}-deskripsi" name="pengurus[{{ $index }}][deskripsi]" rows="2" maxlength="500" placeholder="Tuliskan tugas pengurus dengan singkat.">{{ $anggota['deskripsi'] ?? '' }}</textarea>
        </div>
        <div class="bumdes-manager-card__field bumdes-manager-card__field--full">
            <label for="pengurus-{{ $index }}-foto">Foto pengurus <span class="bumdes-manager-card__optional">(boleh dikosongkan)</span></label>
            <div class="bumdes-manager-card__photo-preview" data-manager-photo-preview>
                @if (!empty($anggota['foto_tersimpan']))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($anggota['foto_tersimpan']) }}" alt="Foto {{ $anggota['nama'] ?? 'pengurus' }}">
                @else
                    <span>Belum ada foto</span>
                @endif
            </div>
            <input id="pengurus-{{ $index }}-foto" class="bumdes-manager-card__photo-input" name="pengurus[{{ $index }}][foto]" type="file" accept="image/*">
            <input name="pengurus[{{ $index }}][foto_tersimpan]" type="hidden" value="{{ $anggota['foto_tersimpan'] ?? '' }}">
            <small class="bumdes-editor__hint">Foto JPG, PNG, atau WebP, maksimal 5 MB. Unggah foto baru untuk mengganti foto saat ini.</small>
            @error("pengurus.$index.foto") <small class="form-error">{{ $message }}</small> @enderror
        </div>
    </div>
</article>
