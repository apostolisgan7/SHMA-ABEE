/**
 * Theme Toggle Module
 * Handles dark/light theme switching with fallbacks and error handling
 */

export function initThemeToggle() {
    // Helper function to safely toggle classes
    const safeToggleClass = (element, className, force) => {
        if (!element || !element.classList) return;
        if (force === true) element.classList.add(className);
        else if (force === false) element.classList.remove(className);
        else element.classList.toggle(className);
    };

    // Get elements safely
    const getElement = (selector) => {
        try {
            return document.querySelector(selector) || document.getElementById(selector);
        } catch (e) {
            console.warn(`Element not found: ${selector}`, e);
            return null;
        }
    };

    const themeToggle = getElement('theme-toggle');
    const moonIcon = getElement('moon-icon');
    const sunIcon = getElement('sun-icon');

    // If no theme toggle found, exit early
    if (!themeToggle) {
        console.warn('Theme toggle element not found');
        return;
    }

    // Check for saved theme preference or use system preference
    const getSystemPreference = () => {
        try {
            return window.matchMedia('(prefers-color-scheme: dark)').matches;
        } catch (e) {
            console.warn('Could not get system color scheme preference', e);
            return false;
        }
    };

    const prefersDark = getSystemPreference();
    const savedTheme = localStorage.getItem('theme');

    const setTheme = (isDark) => {
        try {
            // Toggle dark class on html element
            if (document.documentElement) {
                document.documentElement.classList.toggle('dark', isDark);
            }

            // Toggle icon visibility if elements exist
            if (moonIcon) safeToggleClass(moonIcon, 'hidden', !isDark);
            if (sunIcon) safeToggleClass(sunIcon, 'hidden', isDark);

            // Save preference
            try {
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            } catch (e) {
                console.warn('Could not save theme preference to localStorage', e);
            }

            // Dispatch custom event for other scripts to listen to
            document.dispatchEvent(new CustomEvent('themeChanged', { 
                detail: { 
                    isDark,
                    theme: isDark ? 'dark' : 'light'
                } 
            }));
        } catch (error) {
            console.error('Error in setTheme:', error);
        }
    };

    // Initialize theme
    try {
        const shouldUseDark = savedTheme === 'dark' || (!savedTheme && prefersDark);
        setTheme(shouldUseDark);
        console.log('Theme initialized:', shouldUseDark ? 'dark' : 'light');
    } catch (error) {
        console.error('Error initializing theme:', error);
        setTheme(prefersDark);
    }

    // Toggle theme on click
    const handleThemeToggle = (e) => {
        try {
            if (e) e.preventDefault();
            const isDark = document.documentElement?.classList?.contains('dark') || false;
            setTheme(!isDark);
        } catch (error) {
            console.error('Error in theme toggle handler:', error);
        }
    };

    // Add event listener safely
    try {
        themeToggle.removeEventListener('click', handleThemeToggle); // Remove existing to avoid duplicates
        themeToggle.addEventListener('click', handleThemeToggle);
    } catch (e) {
        console.error('Could not add theme toggle event listener', e);
    }

    // Watch for system theme changes (only if user hasn't set a preference)
    try {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const handleSystemThemeChange = (e) => {
            try {
                if (!localStorage.getItem('theme')) {
                    setTheme(!!e.matches);
                }
            } catch (e) {
                console.error('Error in system theme change handler', e);
            }
        };
        
        // Modern browsers
        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', handleSystemThemeChange);
        } 
        // For older browsers
        else if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(handleSystemThemeChange);
        }
    } catch (error) {
        console.error('Error setting up system theme listener:', error);
    }

    // Expose public API
    return {
        setTheme,
        getCurrentTheme: () => document.documentElement?.classList?.contains('dark') ? 'dark' : 'light'
    };
}

// Auto-initialize if this script is loaded directly in the browser
if (typeof window !== 'undefined' && !window.__THEME_TOGGLE_INITIALIZED__) {
    window.__THEME_TOGGLE_INITIALIZED__ = true;
    document.addEventListener('DOMContentLoaded', () => {
        initThemeToggle();
    });
}
