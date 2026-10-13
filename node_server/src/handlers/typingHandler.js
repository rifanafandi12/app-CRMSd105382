'use strict';

function registerTypingHandlers(io, socket, connectedUsers) {
    socket.on('typing', (data) => {
        const toUserId = parseInt(data?.to_user_id);
        const fromUserName = data?.from_user_name;
        const isTyping = !!data?.is_typing;

        if (!toUserId) return;

        const receiver = connectedUsers.get(toUserId);
        if (receiver?.socketId) {
            io.to(receiver.socketId).emit('user-typing', {
                from_user_name: fromUserName,
                is_typing: isTyping,
            });
        }
    });
}

module.exports = { registerTypingHandlers };
