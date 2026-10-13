/**
 * Socket.io Server for CRM SDN 105382
 * Refactored version with modular structure
 */
'use strict';

require('dotenv').config();

const http = require('http');
const { Server } = require('socket.io');
const mysql = require('mysql2/promise');

const logger = require('./utils/logger');
const { registerChatHandlers } = require('./handlers/chatHandler');
const { registerStatusHandlers } = require('./handlers/statusHandler');
const { registerTypingHandlers } = require('./handlers/typingHandler');
const { createAuthMiddleware } = require('./middleware/authMiddleware');

// ==============================================
// CONFIG
// ==============================================
const PORT = parseInt(process.env.PORT) || 3000;
const CORS_ORIGIN = process.env.CORS_ORIGIN || 'http://localhost:8080';
const HEARTBEAT_TIMEOUT = parseInt(process.env.HEARTBEAT_TIMEOUT) || 45000;
const HEARTBEAT_CHECK_INTERVAL = parseInt(process.env.HEARTBEAT_CHECK_INTERVAL) || 30000;

// ==============================================
// DATABASE POOL
// ==============================================
const db = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    port: parseInt(process.env.DB_PORT) || 3306,
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASS || '',
    database: process.env.DB_NAME || 'db_crm_sd_negeri_105382',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0,
    charset: 'utf8mb4',
    timezone: '+07:00',
});

// Test connection
db.getConnection()
    .then(conn => {
        logger.info('Database connected');
        conn.release();
    })
    .catch(err => {
        logger.error('Database connection failed:', err.message);
        process.exit(1);
    });

// ==============================================
// HTTP & SOCKET.IO SETUP
// ==============================================
const httpServer = http.createServer((req, res) => {
    if (req.url === '/health') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            status: 'ok',
            uptime: process.uptime(),
            connections: connectedUsers.size,
        }));
        return;
    }
    res.writeHead(404);
    res.end();
});

const io = new Server(httpServer, {
    cors: {
        origin: CORS_ORIGIN,
        methods: ['GET', 'POST'],
        credentials: true,
    },
    pingTimeout: parseInt(process.env.SOCKET_PING_TIMEOUT) || 60000,
    pingInterval: parseInt(process.env.SOCKET_PING_INTERVAL) || 25000,
});

// ==============================================
// STATE
// ==============================================
const connectedUsers = new Map(); // userId -> { socketId, lastHeartbeat, name, role }

// ==============================================
// AUTH MIDDLEWARE
// ==============================================
io.use(createAuthMiddleware(db));

// ==============================================
// SOCKET CONNECTION
// ==============================================
io.on('connection', (socket) => {
    logger.info(`Client connected: ${socket.id}`);

    registerStatusHandlers(io, socket, db, connectedUsers);
    registerChatHandlers(io, socket, db, connectedUsers);
    registerTypingHandlers(io, socket, connectedUsers);

    socket.on('disconnect', async (reason) => {
        logger.info(`Client disconnected: ${socket.id} (${reason})`);

        let disconnectedUserId = null;
        for (const [userId, userData] of connectedUsers.entries()) {
            if (userData.socketId === socket.id) {
                disconnectedUserId = userId;
                connectedUsers.delete(userId);
                break;
            }
        }

        if (disconnectedUserId) {
            try {
                await db.query(
                    'UPDATE user_status SET is_online = 0, last_activity = NOW() WHERE id_user = ?',
                    [disconnectedUserId]
                );

                io.emit('user-status-change', {
                    user_id: disconnectedUserId,
                    is_online: false,
                });

                logger.info(`User ${disconnectedUserId} marked offline`);
            } catch (err) {
                logger.error(`Failed to mark user ${disconnectedUserId} offline:`, err.message);
            }
        }
    });
});

// ==============================================
// HEARTBEAT CHECK
// ==============================================
setInterval(async () => {
    const now = Date.now();
    for (const [userId, userData] of connectedUsers.entries()) {
        const elapsed = now - (userData.lastHeartbeat || 0);
        if (elapsed > HEARTBEAT_TIMEOUT) {
            logger.warn(`User ${userId} no heartbeat for ${elapsed}ms`);

            const socket = io.sockets.sockets.get(userData.socketId);
            if (socket) socket.disconnect(true);

            connectedUsers.delete(userId);

            try {
                await db.query(
                    'UPDATE user_status SET is_online = 0, last_activity = NOW() WHERE id_user = ?',
                    [userId]
                );
                io.emit('user-status-change', { user_id: userId, is_online: false });
            } catch (err) {
                logger.error(`Heartbeat cleanup failed for user ${userId}:`, err.message);
            }
        }
    }
}, HEARTBEAT_CHECK_INTERVAL);

// ==============================================
// START SERVER
// ==============================================
httpServer.listen(PORT, () => {
    logger.info(`Socket.io server running on port ${PORT}`);
    logger.info(`CORS origin: ${CORS_ORIGIN}`);
});

// ==============================================
// GRACEFUL SHUTDOWN
// ==============================================
const shutdown = async (signal) => {
    logger.info(`${signal} received, shutting down gracefully...`);

    io.close(() => {
        httpServer.close(async () => {
            try {
                await db.end();
                logger.info('Database pool closed');
            } catch (err) {
                logger.error('Error closing database:', err.message);
            }
            process.exit(0);
        });
    });

    setTimeout(() => {
        logger.error('Forced shutdown after timeout');
        process.exit(1);
    }, 10000);
};

process.on('SIGTERM', () => shutdown('SIGTERM'));
process.on('SIGINT', () => shutdown('SIGINT'));
process.on('uncaughtException', (err) => {
    logger.error('Uncaught exception:', err);
    shutdown('uncaughtException');
});
process.on('unhandledRejection', (reason) => {
    logger.error('Unhandled rejection:', reason);
});
