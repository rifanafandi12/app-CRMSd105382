<?php
$is_edit = $is_edit ?? false;
$action  = $is_edit ? base_url('announcements/edit') : base_url('announcements/add');
?>

<form id="<?= $is_edit ? 'form-edit-ann' : 'form-add-ann' ?>"
      action="<?= $action ?>"
      method="post"
      enctype="multipart/form-data">

    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
    <?php if ($is_edit): ?>
        <input type="hidden" name="id_announcement" id="edit-ann-id">
    <?php endif; ?>

    <div class="space-y-4">

        <!-- Judul -->
        <div>
            <label class="form-label">Judul Pengumuman <span class="text-danger-500">*</span></label>
            <input type="text"
                   name="title"
                   id="<?= $is_edit ? 'edit-ann-title' : '' ?>"
                   class="form-input"
                   required
                   maxlength="200"
                   placeholder="Contoh: Jadwal Ujian Akhir Semester">
        </div>

        <!-- Kategori & Target -->
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Kategori <span class="text-danger-500">*</span></label>
                <select name="category" id="<?= $is_edit ? 'edit-ann-category' : '' ?>" class="form-select" required>
                    <?php foreach ($categories as $key => $label): ?>
                        <option value="<?= html_escape($key) ?>"><?= html_escape($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Target Kelas</label>
                <select name="target_class_id" id="<?= $is_edit ? 'edit-ann-target' : '' ?>" class="form-select">
                    <option value="">Semua (Umum)</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= (int)$class->id_class ?>">
                            <?= html_escape($class->class_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Konten -->
        <div>
            <label class="form-label">Isi Pengumuman <span class="text-danger-500">*</span></label>
            <textarea name="content"
                      id="<?= $is_edit ? 'edit-ann-content' : '' ?>"
                      class="form-textarea"
                      rows="6"
                      required
                      placeholder="Tulis isi pengumuman secara detail..."></textarea>
            <p class="form-help">Boleh menggunakan HTML sederhana seperti &lt;p&gt;, &lt;br&gt;, &lt;ul&gt;&lt;li&gt;</p>
        </div>

        <!-- Tanggal & Lampiran -->
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Tanggal Kedaluwarsa</label>
                <input type="date"
                       name="expiry_date"
                       id="<?= $is_edit ? 'edit-ann-expiry' : '' ?>"
                       class="form-input"
                       min="<?= date('Y-m-d') ?>">
                <p class="form-help">Kosongkan jika tidak ada batas waktu.</p>
            </div>

            <div>
                <label class="form-label">Lampiran (Opsional)</label>
                <input type="file"
                       name="attachment"
                       id="<?= $is_edit ? 'edit-ann-attachment' : '' ?>"
                       class="form-input text-sm"
                       accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx">
                <p class="form-help">Maks 5MB. Format: gambar, PDF, atau dokumen.</p>
                <?php if ($is_edit): ?>
                    <div id="edit-ann-current-attachment" class="mt-2 hidden">
                        <a href="#" target="_blank" class="text-xs text-primary-600 hover:underline flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            Lihat lampiran saat ini
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Publish -->
        <div class="flex items-center gap-2">
            <input type="checkbox"
                   name="is_published"
                   id="<?= $is_edit ? 'edit-ann-published' : 'add-ann-published' ?>"
                   class="form-checkbox"
                   value="1"
                   checked>
            <label for="<?= $is_edit ? 'edit-ann-published' : 'add-ann-published' ?>" class="text-sm text-neutral-700 cursor-pointer">
                Publikasikan sekarang (tampil ke guru & orang tua)
            </label>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-2 mt-6 pt-5 border-t border-neutral-100">
        <button type="button" onclick="closeModal('<?= $is_edit ? 'modal-edit-ann' : 'modal-add-ann' ?>')" class="btn-outline">
            Batal
        </button>
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <?= $is_edit ? 'Simpan Perubahan' : 'Buat Pengumuman' ?>
        </button>
    </div>
</form>
