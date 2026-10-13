'use strict';

const logger = require('../utils/logger');

function registerStatusHandlers(io, socket, db, connectedUsers) {
    /**
     * User connected — register ke memory & DB
     */
    socket.on('user-connected', async (data) => {
        const userId = parseInt(data?.user_id);
        const userName = data?.user_name;
        const userRole = data?.user_role;

        if (!userId || !userName) {
            logger.warn(`Invalid user-connected payload from ${socket.id}`);
            return;
        }

        connectedUsers.set(userId, {
            socketId: socket.id,
            lastHeartbeat: Date.now(),
            name: userName,
            role: userRole,
        });

        try {
            await db.query(
                `INSERT INTO user_status (id_user, is_online, last_activity, socket_id)
                 VALUES (?, 1, NOW(), ?)
                 ON DUPLICATE KEY UPDATE
                   is_online = 1,
                   last_activity = NOW(),
                   socket_id = VALUES(socket_id)`,
                [userId, socket.id]
            );

            socket.broadcast.emit('user-status-change', {
                user_id: userId,
                is_online: true,
            });

            socket.emit('online-users-list', {
                users: Array.from(connectedUsers.keys()),
            });

            logger.info(`User ${userName} (${userId}) connected`);
        } catch (err) {
            logger.error(`Failed to set user ${userId} online:`, err.message);
        }
    });

    /**
     * Heartbeat
     */
    socket.on('heartbeat', async (data) => {
        const userId = parseInt(data?.user_id);
        if (!userId) return;

        const userData = connectedUsers.get(userId);
        if (userData) {
            userData.lastHeartbeat = Date.now();
        }

        try {
            await db.query(
                'UPDATE user_status SET last_activity = NOW() WHERE id_user = ?',
                [userId]
            );
        } catch (err) {
            logger.error(`Heartbeat update failed for user ${userId}:`, err.message);
        }
    });
}

module.exports = { registerStatusHandlers };
