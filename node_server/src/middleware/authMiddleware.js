'use strict';

const logger = require('../utils/logger');

/**
 * Socket.io authentication middleware
 * Validasi user_id yang dikirim saat handshake
 */
function createAuthMiddleware(db) {
    return async (socket, next) => {
        try {
            const userId = socket.handshake.auth?.userId
                || socket.handshake.query?.userId;

            if (!userId) {
                // Biarkan connect tanpa auth — user-connected event akan set
                return next();
            }

            const [rows] = await db.query(
                'SELECT id_user, name, role, is_active FROM users WHERE id_user = ? LIMIT 1',
                [userId]
            );

            if (rows.length === 0 || !rows[0].is_active) {
                logger.warn(`Auth rejected: user ${userId} not found or inactive`);
                return next(new Error('Unauthorized'));
            }

            socket.user = rows[0];
            next();
        } catch (err) {
            logger.error('Auth middleware error:', err.message);
            next(new Error('Auth error'));
        }
    };
}

module.exports = { createAuthMiddleware };
