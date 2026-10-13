/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./application/views/**/*.php",
    "./assets/js/**/*.js",
    "./*.php",
  ],
  theme: {
    extend: {
      colors: {
        // ==============================================
        // PRIMARY — Biru Tut Wuri Handayani (#1E65B8)
        // Melambangkan kepercayaan, stabilitas, dan profesionalisme
        // ==============================================
        primary: {
          50:  '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#4DA3FF',  // Biru muda (secondary)
          500: '#2E7DE0',
          600: '#1E65B8',  // Biru utama (dari logo)
          700: '#1A5399',
          800: '#164278',
          900: '#123159',
          950: '#0a1f3d',
        },

        // ==============================================
        // ACCENT — Kuning/Oranye (Api Semangat Belajar)
        // ==============================================
        accent: {
          50:  '#fffbeb',
          100: '#fef3c7',
          200: '#fde68a',
          300: '#fcd34d',
          400: '#fbbf24',
          500: '#F59E0B',  // Kuning utama (dari logo)
          600: '#d97706',
          700: '#b45309',
          800: '#92400e',
          900: '#78350f',
        },

        // ==============================================
        // NEUTRAL — Dark & Light
        // ==============================================
        neutral: {
          50:  '#F8FAFC',  // Background utama
          100: '#f1f5f9',
          200: '#e2e8f0',
          300: '#cbd5e1',
          400: '#94a3b8',
          500: '#64748b',
          600: '#475569',
          700: '#334155',
          800: '#1F2937',  // Teks utama
          900: '#111827',
          950: '#030712',
        },

        // ==============================================
        // STATUS COLORS
        // ==============================================
        success: {
          50:  '#f0fdf4',
          100: '#dcfce7',
          500: '#22c55e',
          600: '#16a34a',
          700: '#15803d',
        },
        warning: {
          50:  '#fffbeb',
          100: '#fef3c7',
          500: '#f59e0b',
          600: '#d97706',
          700: '#b45309',
        },
        danger: {
          50:  '#fef2f2',
          100: '#fee2e2',
          500: '#ef4444',
          600: '#dc2626',
          700: '#b91c1c',
        },
        info: {
          50:  '#eff6ff',
          100: '#dbeafe',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
        },
      },
      fontFamily: {
        sans:    ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
        display: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
      },
      boxShadow: {
        'soft': '0 2px 15px -3px rgba(30, 101, 184, 0.08), 0 10px 20px -2px rgba(30, 101, 184, 0.04)',
        'card': '0 1px 3px 0 rgba(31, 41, 55, 0.08), 0 1px 2px 0 rgba(31, 41, 55, 0.04)',
        'elevated': '0 10px 25px -5px rgba(30, 101, 184, 0.15), 0 8px 10px -6px rgba(30, 101, 184, 0.08)',
      },
      borderRadius: {
        'xl':  '0.75rem',
        '2xl': '1rem',
      },
      animation: {
        'fade-in':     'fadeIn 0.3s ease-out',
        'slide-up':    'slideUp 0.3s ease-out',
        'slide-down':  'slideDown 0.3s ease-out',
        'pulse-soft':  'pulseSoft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
      keyframes: {
        fadeIn: {
          '0%':   { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%':   { opacity: '0', transform: 'translateY(10px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        slideDown: {
          '0%':   { opacity: '0', transform: 'translateY(-10px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        pulseSoft: {
          '0%, 100%': { opacity: '1' },
          '50%':      { opacity: '0.7' },
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
