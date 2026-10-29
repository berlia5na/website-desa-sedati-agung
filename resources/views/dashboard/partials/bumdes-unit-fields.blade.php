<article class="bumdes-unit-editor" data-bumdes-unit data-index="{{ $index }}">
    <div class="bumdes-unit-editor__heading">
        <span class="bumdes-unit-editor__number">{{ is_numeric($index) ? str_pad($index + 1, 2, '0', STR_PAD_LEFT) : '+' }}</span>
        <strong>{{ !empty($unit['id']) ? 'Unit usaha' : 'Unit usaha baru' }}</strong>
        @if (!empty($unit['id']))
            <label class="bumdes-unit-editor__remove">
                <input type="checkbox" name="unit[{{ $index }}][hapus]" value="1" @checked(old("unit.$index.hapus", false))>
                <span>Hapus unit</span>
            </label>
        @endif
    </div>
    @if (!empty($unit['id']))
        <input type="hidden" name="unit[{{ $index }}][id]" value="{{ $unit['id'] }}">
    @endif
    <div class="bumdes-editor__fields bumdes-editor__fields--two">
        <div class="bumdes-editor__field">
            <label for="unit-{{ $index }}-nama">Nama unit usaha <span>*</span></label>
            <input id="unit-{{ $index }}-nama" name="unit[{{ $index }}][nama_unit]" type="text" maxlength="255" value="{{ old("unit.$index.nama_unit", $unit['nama_unit'] ?? '') }}">
            @error("unit.$index.nama_unit") <small class="form-error">{{ $message }}</small> @enderror
        </div>
        <div class="bumdes-editor__field">
            <label for="unit-{{ $index }}-jenis">Kategori <span>*</span></label>
            <input id="unit-{{ $index }}-jenis" name="unit[{{ $index }}][jenis]" type="text" maxlength="100" value="{{ old("unit.$index.jenis", $unit['jenis'] ?? '') }}" placeholder="Contoh: Pertanian">
            @error("unit.$index.jenis") <small class="form-error">{{ $message }}</small> @enderror
        </div>
        <div class="bumdes-editor__field bumdes-editor__field--full">
            <label for="unit-{{ $index }}-deskripsi">Deskripsi unit</label>
            <textarea id="unit-{{ $index }}-deskripsi" name="unit[{{ $index }}][deskripsi]" rows="3" maxlength="2000">{{ old("unit.$index.deskripsi", $unit['deskripsi'] ?? '') }}</textarea>
            @error("unit.$index.deskripsi") <small class="form-error">{{ $message }}</small> @enderror
        </div>
        <div class="bumdes-editor__field">
            <label for="unit-{{ $index }}-harga">Harga (opsional)</label>
            <input id="unit-{{ $index }}-harga" name="unit[{{ $index }}][harga]" type="number" min="0" step="0.01" value="{{ old("unit.$index.harga", $unit['harga'] ?? '') }}">
            @error("unit.$index.harga") <small class="form-error">{{ $message }}</small> @enderror
        </div>
        <div class="bumdes-editor__field">
            <label for="unit-{{ $index }}-telepon">Nomor yang bisa dihubungi</label>
            <input id="unit-{{ $index }}-telepon" name="unit[{{ $index }}][telepon]" type="tel" inputmode="tel" maxlength="40" value="{{ old("unit.$index.telepon", $unit['telepon'] ?? '') }}" placeholder="Contoh: 0812-3456-7890">
            <small class="bumdes-editor__hint">Nomor ini ditampilkan sebagai tombol telepon di halaman publik.</small>
            @error("unit.$index.telepon") <small class="form-error">{{ $message }}</small> @enderror
        </div>
        <div class="bumdes-editor__field">
            <label for="unit-{{ $index }}-status">Status</label>
            <select id="unit-{{ $index }}-status" name="unit[{{ $index }}][status]">
                <option value="aktif" @selected(old("unit.$index.status", $unit['status'] ?? 'aktif') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(old("unit.$index.status", $unit['status'] ?? 'aktif') === 'nonaktif')>Nonaktif</option>
            </select>
            @error("unit.$index.status") <small class="form-error">{{ $message }}</small> @enderror
        </div>
        <div class="bumdes-editor__field bumdes-editor__field--full">
            <label for="unit-{{ $index }}-gambar">Foto unit usaha</label>
            <input id="unit-{{ $index }}-gambar" name="unit[{{ $index }}][gambar]" type="file" accept="image/*">
            @if (!empty($unit['gambar_tersimpan']))
                <small class="bumdes-editor__hint">Foto saat ini: {{ basename($unit['gambar_tersimpan']) }}. Unggah file baru jika ingin menggantinya.</small>
            @endif
            @error("unit.$index.gambar") <small class="form-error">{{ $message }}</small> @enderror
        </div>
    </div>
</article>
