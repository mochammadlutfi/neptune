/**
 * Utility untuk parsing CSS dari tweakcn.com menjadi theme variables
 * Mendukung parsing CSS variables dari :root dan .dark selectors
 */

export class CSSThemeParser {
    /**
     * Parse CSS string menjadi theme object
     * @param {string} cssString - CSS string dari tweakcn.com
     * @returns {object} Theme object dengan light dan dark variables
     */
    static parseCSSToTheme(cssString) {
        try {
            const theme = {
                name: 'Custom Theme',
                type: 'registry:style',
                cssVars: {
                    theme: {},
                    light: {},
                    dark: {}
                }
            };

            // Parse :root selector (light mode)
            const rootMatch = cssString.match(/:root\s*{([^}]*)}/);
            if (rootMatch) {
                const rootVars = this.extractCSSVariables(rootMatch[1]);
                Object.assign(theme.cssVars.light, rootVars);
                
                // Extract theme-level variables
                this.extractThemeVariables(rootVars, theme.cssVars.theme);
            }

            // Parse .dark selector (dark mode)
            const darkMatch = cssString.match(/\.dark\s*{([^}]*)}/);
            if (darkMatch) {
                const darkVars = this.extractCSSVariables(darkMatch[1]);
                Object.assign(theme.cssVars.dark, darkVars);
            }

            // Parse @theme inline block jika ada
            const themeInlineMatch = cssString.match(/@theme\s+inline\s*{([^}]*)}/);
            if (themeInlineMatch) {
                const inlineVars = this.extractThemeInlineVariables(themeInlineMatch[1]);
                Object.assign(theme.cssVars.theme, inlineVars);
            }

            return theme;
        } catch (error) {
            console.error('Error parsing CSS:', error);
            throw new Error('Format CSS tidak valid');
        }
    }

    /**
     * Extract CSS variables dari CSS block
     * @param {string} cssBlock - CSS block content
     * @returns {object} Object dengan CSS variables
     */
    static extractCSSVariables(cssBlock) {
        const variables = {};
        
        // Match CSS variables (--variable-name: value;)
        const varMatches = cssBlock.match(/--[\w-]+\s*:\s*[^;]+/g);
        
        if (varMatches) {
            varMatches.forEach(match => {
                const [name, value] = match.split(':').map(s => s.trim());
                const cleanName = name.replace('--', '');
                const cleanValue = value.replace(/;$/, '').trim();
                
                variables[cleanName] = cleanValue;
            });
        }

        return variables;
    }

    /**
     * Extract theme variables dari @theme inline block
     * @param {string} themeBlock - @theme inline block content
     * @returns {object} Object dengan theme variables
     */
    static extractThemeInlineVariables(themeBlock) {
        const variables = {};
        
        // Match property mappings (property: var(--variable);)
        const propMatches = themeBlock.match(/[\w-]+\s*:\s*var\(--[\w-]+\)/g);
        
        if (propMatches) {
            propMatches.forEach(match => {
                const [prop, varRef] = match.split(':').map(s => s.trim());
                const varName = varRef.match(/var\(--([^)]+)\)/)?.[1];
                
                if (varName) {
                    variables[prop] = `var(--${varName})`;
                }
            });
        }

        return variables;
    }

    /**
     * Extract theme-level variables dari light mode variables
     * @param {object} lightVars - Light mode variables
     * @param {object} themeVars - Theme variables object to populate
     */
    static extractThemeVariables(lightVars, themeVars) {
        // Map common theme variables
        const themeMapping = {
            'radius': 'radius',
            'font-sans': 'font-sans',
            'font-serif': 'font-serif', 
            'font-mono': 'font-mono',
            'font-family': 'font-sans',
            'letter-spacing': 'tracking'
        };

        Object.entries(themeMapping).forEach(([cssVar, themeVar]) => {
            if (lightVars[cssVar]) {
                themeVars[themeVar] = lightVars[cssVar];
            }
        });
    }

    /**
     * Convert theme object kembali menjadi CSS string
     * @param {object} theme - Theme object
     * @returns {string} CSS string
     */
    static themeToCSS(theme) {
        let css = '';

        // Generate :root selector
        if (theme.cssVars?.light && Object.keys(theme.cssVars.light).length > 0) {
            css += ':root {\n';
            Object.entries(theme.cssVars.light).forEach(([key, value]) => {
                css += `  --${key}: ${value};\n`;
            });
            css += '}\n\n';
        }

        // Generate .dark selector
        if (theme.cssVars?.dark && Object.keys(theme.cssVars.dark).length > 0) {
            css += '.dark {\n';
            Object.entries(theme.cssVars.dark).forEach(([key, value]) => {
                css += `  --${key}: ${value};\n`;
            });
            css += '}\n\n';
        }

        // Generate @theme inline block jika ada theme variables
        if (theme.cssVars?.theme && Object.keys(theme.cssVars.theme).length > 0) {
            css += '@theme inline {\n';
            Object.entries(theme.cssVars.theme).forEach(([key, value]) => {
                css += `  ${key}: ${value};\n`;
            });
            css += '}\n\n';
        }

        // Add body styles jika ada letter-spacing
        if (theme.cssVars?.theme?.tracking) {
            css += `body {\n  letter-spacing: ${theme.cssVars.theme.tracking};\n}\n`;
        }

        return css;
    }

    /**
     * Validate CSS string format
     * @param {string} cssString - CSS string to validate
     * @returns {boolean} True jika valid
     */
    static validateCSS(cssString) {
        try {
            // Check for basic CSS structure
            const hasRoot = /:root\s*{/.test(cssString);
            const hasDark = /\.dark\s*{/.test(cssString);
            const hasVariables = /--[\w-]+\s*:/.test(cssString);

            return hasRoot || hasDark || hasVariables;
        } catch (error) {
            return false;
        }
    }

    /**
     * Extract primary color dari theme variables
     * @param {object} theme - Theme object
     * @returns {string} Primary color value
     */
    static extractPrimaryColor(theme) {
        const lightVars = theme.cssVars?.light || {};
        
        // Common primary color variable names
        const primaryKeys = [
            'primary',
            'primary-500',
            'blue-500',
            'indigo-500',
            'color-primary'
        ];

        for (const key of primaryKeys) {
            if (lightVars[key]) {
                return lightVars[key];
            }
        }

        return null;
    }

    /**
     * Extract border radius dari theme variables
     * @param {object} theme - Theme object
     * @returns {string} Border radius value
     */
    static extractRadius(theme) {
        const themeVars = theme.cssVars?.theme || {};
        const lightVars = theme.cssVars?.light || {};

        return themeVars.radius || lightVars.radius || null;
    }

    /**
     * Extract font family dari theme variables
     * @param {object} theme - Theme object
     * @returns {object} Font families object
     */
    static extractFonts(theme) {
        const themeVars = theme.cssVars?.theme || {};
        
        return {
            sans: themeVars['font-sans'] || null,
            serif: themeVars['font-serif'] || null,
            mono: themeVars['font-mono'] || null
        };
    }
}

export default CSSThemeParser;