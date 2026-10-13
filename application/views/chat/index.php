<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Data untuk JS -->
<script>
    window.CURRENT_USER_ID   = <?= (int)($user_id ?? 0) ?>;
    window.CURRENT_USER_NAME = "<?= html_escape($user_name ?? '') ?>";
    window.CURRENT_USER_ROLE = "<?= html_escape($user_role ?? '') ?>";
    window.BASE_URL          = "<?= base_url() ?>";
    window.SOCKET_URL        = "<?= getenv('SOCKET_URL') ?: 'http://localhost:3000' ?>";
</script>

<!-- ==============================================
     CHAT PAGE
     ============================================== -->
<div class="h-[calc(100vh-8rem)] lg:h-[calc(100vh-9rem)] flex flex-col lg:flex-row gap-4">

    <!-- ==========================================
         SIDEBAR KONTAK
         ========================================== -->
    <aside class="w-full lg:w-80 flex-shrink-0 card flex flex-col overflow-hidden">
        <!-- Search -->
        <div class="p-4 border-b border-neutral-100">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" id="contact-search" class="form-input pl-9 text-sm" placeholder="Cari kontak...">
            </div>
        </div>

        <!-- Connection Status -->
        <div id="chat-connection-status" class="px-4 py-2 text-xs"></div>

        <!-- Daftar Kontak -->
        <div class="flex-1 overflow-y-auto">
            <div class="px-3 py-2">
                <div class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider px-2 mb-1">
                    Kontak (<?= count($contact_list ?? []) ?>)
                </div>
            </div>

            <ul id="contact-list" class="pb-4">
                <?php if (!empty($contact_list)): ?>
                    <?php foreach ($contact_list as $contact): ?>
                        <li>
                            <button type="button"
                                    class="contact-item w-full flex items-center gap-3 px-3 py-2.5 hover:bg-neutral-50 text-left transition-colors"
                                    data-contact-id="<?= (int)$contact->id_user ?>"
                                    data-contact-name="<?= html_escape($contact->name) ?>"
                                    data-contact-role="<?= html_escape($contact->role) ?>">
                                <!-- Avatar -->
                                <div class="relative flex-shrink-0">
                                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center font-semibold text-sm">
                                        <?= strtoupper(substr($contact->name ?? 'U', 0, 1)) ?>
                                    </div>
                                    <span class="online-indicator <?= !empty($contact->is_online) ? 'online' : 'offline' ?> absolute -bottom-0.5 -right-0.5"></span>
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-medium text-sm text-neutral-900 truncate">
                                            <?= html_escape($contact->name) ?>
                                        </span>
                                        <?php if (!empty($unread_counts[$contact->id_user])): ?>
                                            <span class="badge-danger text-[10px] px-1.5 py-0 min-w-[18px] h-[18px] flex items-center justify-center">
                                                <?= (int)$unread_counts[$contact->id_user] ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-[10px] text-neutral-400 uppercase tracking-wide">
                                            <?= html_escape($contact->role) ?>
                                        </span>
                                    </div>
                                </div>
                            </button>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="px-4 py-6 text-center">
                        <svg class="w-10 h-10 text-neutral-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="text-xs text-neutral-500">Belum ada kontak tersedia</p>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </aside>

    <!-- ==========================================
         AREA CHAT
         ========================================== -->
    <section class="flex-1 card flex flex-col overflow-hidden">

        <!-- Chat Header -->
        <div id="chat-header" class="px-5 py-3 border-b border-neutral-100 flex items-center justify-between bg-white flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-neutral-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-display font-semibold text-neutral-900">Pilih Kontak</div>
                    <div class="text-xs text-neutral-500">Mulai percakapan dengan memilih kontak</div>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="flex-1 overflow-y-auto p-5 bg-neutral-50/50">
            <div class="h-full flex items-center justify-center">
                <div class="text-center max-w-sm">
                    <svg class="w-16 h-16 text-neutral-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="font-display font-semibold text-neutral-900 mb-1">Selamat Datang di Pusat Komunikasi</h3>
                    <p class="text-sm text-neutral-500">Pilih kontak dari daftar di sebelah kiri untuk mulai mengobrol.</p>
                </div>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div id="typing-indicator" class="hidden px-5 py-2 text-xs text-neutral-500 italic bg-white border-t border-neutral-100 flex-shrink-0"></div>

        <!-- Chat Input -->
        <div id="chat-input-area" class="hidden border-t border-neutral-100 bg-white p-3 flex-shrink-0">
            <div class="flex items-end gap-2">
                <textarea id="message-input"
                          rows="1"
                          class="form-textarea flex-1 resize-none min-h-[44px] max-h-32 py-2.5"
                          placeholder="Tulis pesan... (Enter untuk kirim, Shift+Enter untuk baris baru)"
                          style="height: 44px;"></textarea>
                <button type="button" id="send-btn" class="btn-primary h-11 w-11 p-0 flex-shrink-0" title="Kirim pesan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>
</div>

<!-- Socket.io Client -->
<script src="<?= base_url('assets/js/vendor/socket.io.min.js') ?>"></script>

<!-- Custom JS -->
<script src="<?= base_url('assets/js/sound.js') ?>"></script>
<script src="<?= base_url('assets/js/chat.js') ?>"></script>
