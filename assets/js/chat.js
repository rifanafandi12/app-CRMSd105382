/**
 * Chat Realtime dengan Socket.io
 * Menggunakan Sound.js untuk notifikasi
 */
(function () {
	"use strict";

	// ==============================================
	// STATE
	// ==============================================
	let chatSocket = null;
	let currentContact = null;
	let isSocketConnected = false;
	let reconnectAttempts = 0;
	let typingTimer = null;
	let typingDebounceTimer = null;
	let lastSoundTime = 0;
	const MAX_RECONNECT_ATTEMPTS = 10;
	const SOUND_THROTTLE_MS = 500; // Hindari spam sound

	// ==============================================
	// SOCKET INITIALIZATION
	// ==============================================
	function initChatSocket() {
		return new Promise((resolve, reject) => {
			if (typeof io === "undefined") {
				updateConnectionStatus("Library Socket.io tidak terload", "danger");
				return reject("Socket.io not loaded");
			}

			if (!window.CURRENT_USER_ID || window.CURRENT_USER_ID <= 0) {
				return reject("User not logged in");
			}

			updateConnectionStatus("Menghubungkan...", "info");

			chatSocket = io(window.SOCKET_URL || "http://localhost:3000", {
				transports: ["websocket", "polling"],
				reconnection: true,
				reconnectionAttempts: MAX_RECONNECT_ATTEMPTS,
				reconnectionDelay: 1000,
				reconnectionDelayMax: 5000,
				timeout: 20000,
				auth: {
					userId: window.CURRENT_USER_ID,
				},
			});

			// ==============================================
			// SOCKET EVENT: CONNECT
			// ==============================================
			chatSocket.on("connect", function () {
				isSocketConnected = true;
				reconnectAttempts = 0;

				chatSocket.emit("user-connected", {
					user_id: window.CURRENT_USER_ID,
					user_name: window.CURRENT_USER_NAME,
					user_role: window.CURRENT_USER_ROLE,
				});

				updateConnectionStatus("Terhubung", "success");
				startHeartbeat();
				resolve();
			});

			// ==============================================
			// SOCKET EVENT: CONNECT ERROR
			// ==============================================
			chatSocket.on("connect_error", function (error) {
				isSocketConnected = false;
				reconnectAttempts++;

				let msg = "Gagal terhubung ke server chat. ";
				if (reconnectAttempts <= MAX_RECONNECT_ATTEMPTS) {
					msg += `Mencoba ulang (${reconnectAttempts}/${MAX_RECONNECT_ATTEMPTS})...`;
					updateConnectionStatus(msg, "warning");
				} else {
					msg += "Pastikan server Node.js berjalan di port 3000.";
					updateConnectionStatus(msg, "danger");
				}
				reject(error);
			});

			// ==============================================
			// SOCKET EVENT: DISCONNECT
			// ==============================================
			chatSocket.on("disconnect", function (reason) {
				isSocketConnected = false;
				updateConnectionStatus("Terputus, mencoba menyambung...", "warning");
			});

			// ==============================================
			// SOCKET EVENT: RECONNECT
			// ==============================================
			chatSocket.on("reconnect", function () {
				isSocketConnected = true;
				updateConnectionStatus("Tersambung kembali", "success");

				chatSocket.emit("user-connected", {
					user_id: window.CURRENT_USER_ID,
					user_name: window.CURRENT_USER_NAME,
					user_role: window.CURRENT_USER_ROLE,
				});
			});

			// ==============================================
			// SOCKET EVENT: NEW MESSAGE
			// ==============================================
			chatSocket.on("new-private-message", function (data) {
				handleIncomingMessage(data);
			});

			// ==============================================
			// SOCKET EVENT: TYPING
			// ==============================================
			chatSocket.on("user-typing", function (data) {
				handleTypingIndicator(data);
			});

			// ==============================================
			// SOCKET EVENT: MESSAGE SENT
			// ==============================================
			chatSocket.on("message-sent", function (data) {
				// Konfirmasi pesan terkirim — play sound kecil
				playSoundThrottled("message-sent");
				updateMessageStatus(data.message_id, "sent");
			});

			// ==============================================
			// SOCKET EVENT: MESSAGE ERROR
			// ==============================================
			chatSocket.on("message-error", function (data) {
				playSoundThrottled("error");
				updateConnectionStatus(data.error || "Gagal mengirim pesan", "danger");
			});

			// ==============================================
			// SOCKET EVENT: USER STATUS CHANGE
			// ==============================================
			chatSocket.on("user-status-change", function (data) {
				updateUserStatusUI(data.user_id, data.is_online);
			});

			// ==============================================
			// SOCKET EVENT: ONLINE USERS LIST
			// ==============================================
			chatSocket.on("online-users-list", function (data) {
				if (Array.isArray(data.users)) {
					data.users.forEach((userId) => {
						updateUserStatusUI(userId, true);
					});
				}
			});

			// ==============================================
			// SOCKET EVENT: MESSAGES READ
			// ==============================================
			chatSocket.on("messages-read", function (data) {
				// Update UI: centang dua untuk pesan yang sudah dibaca
				markMessagesAsRead(data.from_user);
			});
		});
	}

	// ==============================================
	// SOUND THROTTLE
	// Mencegah sound spam kalau banyak event berturut-turut
	// ==============================================
	function playSoundThrottled(soundName) {
		const now = Date.now();
		if (now - lastSoundTime < SOUND_THROTTLE_MS) return;
		lastSoundTime = now;

		if (window.Sound && typeof window.Sound.play === "function") {
			window.Sound.play(soundName);
		}
	}

	// ==============================================
	// HEARTBEAT
	// ==============================================
	let heartbeatInterval = null;

	function startHeartbeat() {
		if (heartbeatInterval) clearInterval(heartbeatInterval);

		heartbeatInterval = setInterval(function () {
			if (chatSocket && isSocketConnected) {
				chatSocket.emit("heartbeat", { user_id: window.CURRENT_USER_ID });
			}
		}, 25000); // setiap 25 detik
	}

	// ==============================================
	// CONNECTION STATUS UI
	// ==============================================
	function updateConnectionStatus(message, type = "info") {
		const el = document.getElementById("chat-connection-status");
		if (!el) return;

		const colors = {
			success: "text-success-600",
			danger: "text-danger-600",
			warning: "text-warning-600",
			info: "text-primary-600",
		};

		const icons = {
			success:
				'<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
			danger:
				'<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
			warning:
				'<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
			info: '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
		};

		el.className = `px-4 py-2 text-xs flex items-center gap-1.5 ${colors[type] || colors.info}`;
		el.innerHTML =
			(icons[type] || icons.info) + "<span>" + escapeHtml(message) + "</span>";

		if (type === "success") {
			setTimeout(() => {
				if (el) el.innerHTML = "";
			}, 3000);
		}
	}

	// ==============================================
	// LOAD CONVERSATION
	// ==============================================
	function loadConversation(contactId, contactName, contactRole) {
		if (!contactId) return;

		currentContact = { id: contactId, name: contactName, role: contactRole };

		// Update header
		const header = document.getElementById("chat-header");
		if (header) {
			header.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center font-semibold text-sm">
                    ${escapeHtml((contactName || "U").charAt(0).toUpperCase())}
                </div>
                <div>
                    <div class="font-display font-semibold text-neutral-900">${escapeHtml(contactName)}</div>
                    <div class="text-xs text-neutral-500 flex items-center gap-1.5">
                        <span class="online-indicator ${getContactOnlineStatus(contactId) ? "online" : "offline"}"></span>
                        <span>${getContactOnlineStatus(contactId) ? "Online" : "Offline"}</span>
                        <span class="text-neutral-300">•</span>
                        <span class="uppercase tracking-wide text-[10px]">${escapeHtml(contactRole || "")}</span>
                    </div>
                </div>
            </div>
        `;
		}

		// Show input area
		const inputArea = document.getElementById("chat-input-area");
		if (inputArea) inputArea.classList.remove("hidden");

		// Show loading
		const messagesEl = document.getElementById("chat-messages");
		messagesEl.innerHTML = `
        <div class="h-full flex items-center justify-center">
            <div class="text-center">
                <div class="w-8 h-8 border-2 border-neutral-200 border-t-primary-600 rounded-full animate-spin mx-auto mb-3"></div>
                <p class="text-xs text-neutral-500">Memuat percakapan...</p>
            </div>
        </div>
    `;

		// Mark active contact
		document
			.querySelectorAll(".contact-item")
			.forEach((el) =>
				el.classList.remove(
					"bg-primary-50",
					"border-l-4",
					"border-primary-600",
				),
			);
		const activeEl = document.querySelector(
			`.contact-item[data-contact-id="${contactId}"]`,
		);
		if (activeEl) {
			activeEl.classList.add(
				"bg-primary-50",
				"border-l-4",
				"border-primary-600",
			);
		}

		// Fetch messages
		fetch(window.BASE_URL + "chat/get_messages?with_user=" + contactId, {
			headers: { "X-Requested-With": "XMLHttpRequest" },
		})
			.then((r) => r.json())
			.then((response) => {
				// Handle kedua format: {data: [...]} atau [...]
				let messages = [];
				if (Array.isArray(response)) {
					messages = response;
				} else if (response && response.data && Array.isArray(response.data)) {
					messages = response.data;
				}

				messagesEl.innerHTML = "";

				if (messages.length === 0) {
					messagesEl.innerHTML = `
                    <div class="h-full flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-12 h-12 text-neutral-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="text-sm text-neutral-500">Belum ada pesan</p>
                            <p class="text-xs text-neutral-400 mt-1">Kirim pesan pertama Anda</p>
                        </div>
                    </div>
                `;
				} else {
					messages.forEach((msg) => {
						const isSent = msg.id_sender == window.CURRENT_USER_ID;
						appendMessageToChat(msg, isSent, false);
					});
					scrollChatToBottom();
				}

				// Mark as read
				if (chatSocket && isSocketConnected) {
					chatSocket.emit("mark-as-read", {
						from_user_id: contactId,
						to_user_id: window.CURRENT_USER_ID,
					});
				}
			})
			.catch((err) => {
				console.error("Failed to load messages:", err);
				messagesEl.innerHTML = `
                <div class="h-full flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-sm text-danger-600">Gagal memuat percakapan</p>
                        <p class="text-xs text-neutral-500 mt-1">Silakan coba lagi</p>
                    </div>
                </div>
            `;
			});
	}

	// ==============================================
	// APPEND MESSAGE TO CHAT
	// ==============================================
	function appendMessageToChat(msg, isSent, animated = true) {
		const messagesEl = document.getElementById("chat-messages");
		if (!messagesEl) return;

		// Hapus placeholder jika ada
		const placeholder = messagesEl.querySelector(
			".h-full.flex.items-center.justify-center",
		);
		if (placeholder) placeholder.remove();

		const time = msg.created_at
			? new Date(msg.created_at).toLocaleTimeString("id-ID", {
					hour: "2-digit",
					minute: "2-digit",
				})
			: new Date().toLocaleTimeString("id-ID", {
					hour: "2-digit",
					minute: "2-digit",
				});

		const wrapper = document.createElement("div");
		wrapper.className = `flex mb-3 ${isSent ? "justify-end" : "justify-start"}`;
		if (animated) {
			wrapper.style.opacity = "0";
			wrapper.style.transform = "translateY(8px)";
		}

		wrapper.innerHTML = `
            <div class="${isSent ? "message-sent" : "message-received"}">
                <div class="whitespace-pre-wrap break-words">${escapeHtml(msg.message).replace(/\n/g, "<br>")}</div>
                <div class="flex items-center justify-end gap-1 mt-1 ${isSent ? "text-primary-100" : "text-neutral-400"} text-[10px]">
                    <span>${time}</span>
                    ${
											isSent
												? `
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    `
												: ""
										}
                </div>
            </div>
        `;

		messagesEl.appendChild(wrapper);

		if (animated) {
			requestAnimationFrame(() => {
				wrapper.style.transition = "opacity 0.2s ease, transform 0.2s ease";
				wrapper.style.opacity = "1";
				wrapper.style.transform = "translateY(0)";
			});
		}
	}

	// ==============================================
	// HANDLE INCOMING MESSAGE
	// ==============================================
	function handleIncomingMessage(data) {
		const fromId = data.from_user_id;
		const fromName = data.from_user_name;

		if (currentContact && currentContact.id == fromId) {
			// Pesan untuk chat yang sedang dibuka
			appendMessageToChat(
				{
					id_sender: fromId,
					sender_name: fromName,
					message: data.message,
					created_at: data.timestamp || new Date().toISOString(),
				},
				false,
				true,
			);
			scrollChatToBottom();
			playSoundThrottled("message-in");

			// Tandai sebagai dibaca
			if (chatSocket && isSocketConnected) {
				chatSocket.emit("mark-as-read", {
					from_user_id: fromId,
					to_user_id: window.CURRENT_USER_ID,
				});
			}
		} else {
			// Pesan dari kontak lain — update badge & play sound
			updateUnreadBadge();
			playSoundThrottled("message-in");

			// Update badge di contact list
			const contactEl = document.querySelector(
				`.contact-item[data-contact-id="${fromId}"]`,
			);
			if (contactEl) {
				let badge = contactEl.querySelector(".badge-danger");
				if (badge) {
					let count = parseInt(badge.textContent) + 1;
					badge.textContent = count;
				} else {
					const nameEl = contactEl.querySelector(
						".flex.items-center.justify-between",
					);
					if (nameEl) {
						const span = document.createElement("span");
						span.className =
							"badge-danger text-[10px] px-1.5 py-0 min-w-[18px] h-[18px] flex items-center justify-center";
						span.textContent = "1";
						nameEl.appendChild(span);
					}
				}

				// Highlight contact
				contactEl.classList.add("bg-accent-50");
				setTimeout(() => contactEl.classList.remove("bg-accent-50"), 2000);
			}
		}
	}

	// ==============================================
	// SEND MESSAGE
	// ==============================================
	function sendChatMessage() {
		const inputEl = document.getElementById("message-input");
		if (!inputEl) return false;

		const message = inputEl.value.trim();
		if (!message) return false;

		if (!currentContact) {
			updateConnectionStatus("Pilih kontak terlebih dahulu", "warning");
			return false;
		}

		if (!chatSocket || !isSocketConnected) {
			updateConnectionStatus(
				"Koneksi terputus, silakan refresh halaman",
				"danger",
			);
			return false;
		}

		// Tampilkan pesan di UI
		appendMessageToChat(
			{
				id_sender: window.CURRENT_USER_ID,
				sender_name: window.CURRENT_USER_NAME,
				message: message,
				created_at: new Date().toISOString(),
			},
			true,
			true,
		);

		// Kirim via socket
		chatSocket.emit("send-private-message", {
			from_user_id: window.CURRENT_USER_ID,
			to_user_id: currentContact.id,
			message: message,
			from_user_name: window.CURRENT_USER_NAME,
		});

		// Reset input
		inputEl.value = "";
		inputEl.style.height = "44px";
		scrollChatToBottom();

		return true;
	}

	// ==============================================
	// TYPING INDICATOR
	// ==============================================
	function sendTypingIndicator(isTyping) {
		if (!currentContact || !chatSocket || !isSocketConnected) return;

		if (typingDebounceTimer) clearTimeout(typingDebounceTimer);

		typingDebounceTimer = setTimeout(() => {
			chatSocket.emit("typing", {
				to_user_id: currentContact.id,
				from_user_name: window.CURRENT_USER_NAME,
				is_typing: isTyping,
			});
		}, 100);
	}

	function handleTypingIndicator(data) {
		if (!currentContact) return;
		if (currentContact.name !== data.from_user_name) return;

		const el = document.getElementById("typing-indicator");
		if (!el) return;

		if (data.is_typing) {
			el.classList.remove("hidden");
			el.innerHTML = `
                <div class="flex items-center gap-2">
                    <div class="flex gap-0.5">
                        <span class="w-1.5 h-1.5 bg-neutral-400 rounded-full animate-pulse" style="animation-delay: 0ms"></span>
                        <span class="w-1.5 h-1.5 bg-neutral-400 rounded-full animate-pulse" style="animation-delay: 150ms"></span>
                        <span class="w-1.5 h-1.5 bg-neutral-400 rounded-full animate-pulse" style="animation-delay: 300ms"></span>
                    </div>
                    <span>${escapeHtml(data.from_user_name)} sedang mengetik...</span>
                </div>
            `;
		} else {
			el.classList.add("hidden");
		}
	}

	// ==============================================
	// UNREAD BADGE
	// ==============================================
	function updateUnreadBadge() {
		fetch(`${window.BASE_URL}chat/get_unread_count`, {
			headers: { "X-Requested-With": "XMLHttpRequest" },
		})
			.then((r) => r.json())
			.then((data) => {
				const count = data.unread_count || data.count || 0;
				const badge = document.getElementById("unread-badge");
				const sidebarBadge = document.getElementById("sidebar-unread");

				[badge, sidebarBadge].forEach((el) => {
					if (!el) return;
					if (count > 0) {
						el.textContent = count > 99 ? "99+" : count;
						el.classList.remove("hidden");
					} else {
						el.classList.add("hidden");
					}
				});
			})
			.catch(() => {});
	}

	// ==============================================
	// USER STATUS UI
	// ==============================================
	function updateUserStatusUI(userId, isOnline) {
		const contactEl = document.querySelector(
			`.contact-item[data-contact-id="${userId}"]`,
		);
		if (!contactEl) return;

		const indicator = contactEl.querySelector(".online-indicator");
		if (indicator) {
			indicator.classList.remove("online", "offline");
			indicator.classList.add(isOnline ? "online" : "offline");
		}

		// Update header jika sedang dibuka
		if (currentContact && currentContact.id == userId) {
			const headerIndicator = document.querySelector(
				"#chat-header .online-indicator",
			);
			if (headerIndicator) {
				headerIndicator.classList.remove("online", "offline");
				headerIndicator.classList.add(isOnline ? "online" : "offline");
			}
			const statusText = document.querySelector(
				"#chat-header .text-xs.text-neutral-500 span:nth-child(2)",
			);
			if (statusText) {
				statusText.textContent = isOnline ? "Online" : "Offline";
			}
		}
	}

	function getContactOnlineStatus(userId) {
		const el = document.querySelector(
			`.contact-item[data-contact-id="${userId}"] .online-indicator`,
		);
		return el && el.classList.contains("online");
	}

	// ==============================================
	// UTILITIES
	// ==============================================
	function scrollChatToBottom() {
		const el = document.getElementById("chat-messages");
		if (el) {
			requestAnimationFrame(() => {
				el.scrollTop = el.scrollHeight;
			});
		}
	}

	function escapeHtml(text) {
		if (text === null || text === undefined) return "";
		const div = document.createElement("div");
		div.textContent = String(text);
		return div.innerHTML;
	}

	function markMessagesAsRead(userId) {
		// Placeholder — update UI centang dua
	}

	function updateMessageStatus(messageId, status) {
		// Placeholder
	}

	// ==============================================
	// EVENT LISTENERS
	// ==============================================
	document.addEventListener("DOMContentLoaded", function () {
		if (!window.CURRENT_USER_ID || window.CURRENT_USER_ID <= 0) {
			window.location.href = window.BASE_URL + "auth/login";
			return;
		}

		// Init socket
		initChatSocket().catch(function () {});

		// Contact click
		document.addEventListener("click", function (e) {
			const contactItem = e.target.closest(".contact-item");
			if (contactItem) {
				const id = contactItem.dataset.contactId;
				const name = contactItem.dataset.contactName;
				const role = contactItem.dataset.contactRole;
				loadConversation(id, name, role);

				// Hapus badge unread untuk kontak ini
				const badge = contactItem.querySelector(".badge-danger");
				if (badge) badge.remove();
			}
		});

		// Send button
		const sendBtn = document.getElementById("send-btn");
		if (sendBtn) sendBtn.addEventListener("click", sendChatMessage);

		// Enter to send
		const inputEl = document.getElementById("message-input");
		if (inputEl) {
			inputEl.addEventListener("keydown", function (e) {
				if (e.which === 13 && !e.shiftKey) {
					e.preventDefault();
					sendChatMessage();
				}
			});

			// Auto-resize
			inputEl.addEventListener("input", function () {
				this.style.height = "auto";
				this.style.height = Math.min(this.scrollHeight, 128) + "px";
			});

			// Typing indicator
			inputEl.addEventListener("keyup", function () {
				if (typingTimer) clearTimeout(typingTimer);
				sendTypingIndicator(true);
				typingTimer = setTimeout(() => sendTypingIndicator(false), 1000);
			});
		}

		// Contact search
		const searchEl = document.getElementById("contact-search");
		if (searchEl) {
			searchEl.addEventListener("input", function () {
				const q = this.value.toLowerCase().trim();
				document
					.querySelectorAll("#contact-list .contact-item")
					.forEach((item) => {
						const name = (item.dataset.contactName || "").toLowerCase();
						const li = item.closest("li");
						if (li) li.style.display = name.includes(q) ? "" : "none";
					});
			});
		}

		// Update badge tiap 30 detik
		setInterval(updateUnreadBadge, 30000);
		updateUnreadBadge();

		// Enable sound saat pertama kali user klik
		document.addEventListener(
			"click",
			function once() {
				if (window.Sound && window.Sound.isReady()) {
					// Sound sudah siap
				}
				document.removeEventListener("click", once);
			},
			{ once: true },
		);
	});

	// Expose ke global
	window.sendChatMessage = sendChatMessage;
})();
