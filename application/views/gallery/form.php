<?php
$is_edit = $is_edit ?? false;
$action  = $is_edit ? base_url('gallery/edit') : base_url('gallery/add');
?>

<form id="<?= $is_edit ? 'form-edit-gallery' : 'form-add-gallery' ?>"
      action="<?= $action ?>"
      method="post"
      enctype="multipart/form-data">

    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <?php if ($is_edit): ?>
        <input type="hidden" name="id_gallery" id="edit-gallery-id">
    <?php endif; ?>

    <div class="space-y-4">

        <!-- Upload Preview -->
        <div>
            <label class="form-label">
                Foto <?= $is_edit ? '(biarkan kosong jika tidak diganti)' : '' ?>
                <?php if (!$is_edit): ?><span class="text-danger-500">*</span><?php endif; ?>
            </label>

            <div id="<?= $is_edit ? 'edit-preview-wrapper' : 'add-preview-wrapper' ?>"
                 class="relative aspect-video rounded-xl border-2 border-dashed border-neutral-300 overflow-hidden bg-neutral-50 flex items-center justify-center cursor-pointer hover:border-primary-400 transition-colors"
                 onclick="document.getElementById('<?= $is_edit ? 'edit-gallery-image' : 'add-gallery-image' ?>').click()">

                <input type="file"
                       name="image"
                       id="<?= $is_edit ? 'edit-gallery-image' : 'add-gallery-image' ?>"
                       class="hidden"
                       accept="image/jpeg,image/png,image/gif,image/webp"
                       onchange="previewImage(this, '<?= $is_edit ? 'edit-preview' : 'add-preview' ?>')"
                       <?= !$is_edit ? 'required' : '' ?>>

                <img id="<?= $is_edit ? 'edit-preview' : 'add-preview' ?>" src="" alt=""
                     class="hidden absolute inset-0 w-full h-full object-cover">

                <div class="text-center p-4" id="<?= $is_edit ? 'edit-preview-placeholder' : 'add-preview-placeholder' ?>">
                    <svg class="w-12 h-12 text-neutral-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-neutral-600">Klik untuk pilih foto</p>
                    <p class="text-xs text-neutral-400 mt-1">JPG, PNG, GIF, WEBP (maks 5MB)</p>
                </div>
            </div>
        </div>

        <!-- Judul -->
        <div>
            <label class="form-label">Judul Foto <span class="text-danger-500">*</span></label>
            <input type="text"
                   name="title"
                   id="<?= $is_edit ? 'edit-gallery-title' : '' ?>"
                   class="form-input"
                   required
                   maxlength="200"
                   placeholder="Contoh: Upacara Bendera HUT RI ke-79">
        </div>

        <!-- Kategori -->
        <div>
            <label class="form-label">Kategori <span class="text-danger-500">*</span></label>
            <select name="category" id="<?= $is_edit ? 'edit-gallery-category' : '' ?>" class="form-select" required>
                <?php foreach ($categories as $key => $label): ?>
                    <option value="<?= html_escape($key) ?>"><?= html_escape($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Deskripsi -->
        <div>
            <label class="form-label">Deskripsi</label>
            <textarea name="description"
                      id="<?= $is_edit ? 'edit-gallery-description' : '' ?>"
                      class="form-textarea"
                      rows="3"
                      maxlength="500"
                      placeholder="Deskripsi singkat tentang foto ini..."></textarea>
        </div>

        <!-- Publish -->
        <div class="flex items-center gap-2">
            <input type="checkbox"
                   name="is_published"
                   id="<?= $is_edit ? 'edit-gallery-published' : 'add-gallery-published' ?>"
                   class="form-checkbox"
                   value="1"
                   checked>
            <label for="<?= $is_edit ? 'edit-gallery-published' : 'add-gallery-published' ?>" class="text-sm text-neutral-700 cursor-pointer">
                Publikasikan sekarang (tampil di halaman publik)
            </label>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('<?= $is_edit ? 'modal-edit-gallery' : 'modal-add-gallery' ?>')" class="btn-outline">
            Batal
        </button>
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <?= $is_edit ? 'Simpan Perubahan' : 'Upload Foto' ?>
        </button>
    </div>
</form>
