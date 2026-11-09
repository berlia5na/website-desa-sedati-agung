@props(['for', 'label' => 'foto', 'deleteName' => null, 'checked' => false])

<div class="admin-photo-delete-control">
    @if ($deleteName)
        <input class="admin-photo-delete__input" type="checkbox" name="{{ $deleteName }}" value="1" data-saved-media-delete @checked($checked)>
    @endif
    <button class="admin-photo-delete" type="button" data-media-delete-control data-clear-upload-for="{{ $for }}" data-media-label="{{ $label }}" title="Hapus {{ $label }} yang dipilih" aria-label="Hapus {{ $label }} yang dipilih" aria-pressed="{{ $checked ? 'true' : 'false' }}" @if (! $deleteName) hidden @endif>
        <x-admin.trash-icon />
        <span class="admin-photo-delete__label" data-media-delete-label>Hapus {{ $label }}{{ $deleteName && $checked ? ' (batalkan)' : '' }}</span>
    </button>
</div>
