'use strict';

const logger = require('../utils/logger');

function registerChatHandlers(io, socket, db, connectedUsers) {
    /**
     * Kirim pesan privat
     */
    socket.on('send-private-message', async (data) => {
        const fromUserId = parseInt(data?.from_user_id);
        const toUserId = parseInt(data?.to_user_id);
        const message = String(data?.message || '').trim();
        const fromUserName = data?.from_user_name;

        // Validasi
        if (!fromUserId || !toUserId || !message) {
            socket.emit('message-error', { error: 'Data pesan tidak lengkap' });
            return;
        }

        if (message.length > 5000) {
            socket.emit('message-error', { error: 'Pesan terlalu panjang (max 5000 karakter)' });
            return;
        }

        try {
            const [result] = await db.query(
                `INSERT INTO chat_messages (id_sender, id_receiver, message, created_at)
                 VALUES (?, ?, ?, NOW())`,
                [fromUserId, toUserId, message]
            );

            const messageId = result.insertId;

            // Kirim ke penerima jika online
            const receiver = connectedUsers.get(toUserId);
            if (receiver?.socketId) {
                io.to(receiver.socketId).emit('new-private-message', {
                    from_user_id: fromUserId,
                    from_user_name: fromUserName,
                    message,
                    message_id: messageId,
                    timestamp: new Date().toISOString(),
                });
                logger.info(`Message ${messageId} delivered to user ${toUserId}`);
            } else {
                logger.info(`User ${toUserId} offline — message ${messageId} saved to DB`);
            }

            socket.emit('message-sent', {
                to_user_id: toUserId,
                message_id: messageId,
                status: 'delivered',
            });
        } catch (err) {
            logger.error('Failed to save message:', err.message);
            socket.emit('message-error', { error: 'Gagal menyimpan pesan' });
        }
    });

    /**
     * Tandai pesan sebagai sudah dibaca
     */
    socket.on('mark-as-read', async (data) => {
        const fromUserId = parseInt(data?.from_user_id);
        const toUserId = parseInt(data?.to_user_id);

        if (!fromUserId || !toUserId) return;

        try {
            await db.query(
                `UPDATE chat_messages
                 SET is_read = 1, read_at = NOW()
                 WHERE id_sender = ? AND id_receiver = ? AND is_read = 0`,
                [fromUserId, toUserId]
            );

            const sender = connectedUsers.get(fromUserId);
            if (sender?.socketId) {
                io.to(sender.socketId).emit('messages-read', {
                    by_user: toUserId,
                    from_user: fromUserId,
                });
            }
        } catch (err) {
            logger.error('Failed to mark messages as read:', err.message);
        }
    });
}

module.exports = { registerChatHandlers };
