'use strict';

const LEVELS = {
    error: 0,
    warn: 1,
    info: 2,
    debug: 3,
};

const COLORS = {
    error: '\x1b[31m',
    warn: '\x1b[33m',
    info: '\x1b[36m',
    debug: '\x1b[90m',
    reset: '\x1b[0m',
};

const currentLevel = LEVELS[process.env.LOG_LEVEL] ?? LEVELS.info;

function log(level, ...args) {
    if (LEVELS[level] > currentLevel) return;

    const timestamp = new Date().toISOString();
    const color = COLORS[level] || '';
    const reset = COLORS.reset;
    const label = level.toUpperCase().padEnd(5);

    console.log(`${color}[${timestamp}] ${label}${reset}`, ...args);
}

module.exports = {
    error: (...args) => log('error', ...args),
    warn:  (...args) => log('warn',  ...args),
    info:  (...args) => log('info',  ...args),
    debug: (...args) => log('debug', ...args),
};
