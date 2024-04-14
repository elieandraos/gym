import globals from "globals";
import pluginVue from "eslint-plugin-vue";

import path from "path";
import { fileURLToPath } from "url";
import { FlatCompat } from "@eslint/eslintrc";
import pluginJs from "@eslint/js";

// mimic CommonJS variables -- not needed if using CommonJS
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const compat = new FlatCompat({baseDirectory: __dirname, recommendedConfig: pluginJs.configs.recommended});

export default [
    {
        languageOptions: {
            globals: globals.browser,
            parserOptions: {
                ecmaVersion: 2020,  // Allows for the parsing of modern ECMAScript features
                sourceType: 'module',  // Allows for the use of imports
            }
        }
    },
    ...compat.extends("airbnb-base"),
    ...pluginVue.configs["flat/essential"],
    {
        // ESLint rules
        rules: {
            "indent": ["error", 4], // Set indentation to 4 spaces
            "semi": ["error", "never"], // Disable semicolons
            "quotes": ["error", "single", { "avoidEscape": true, "allowTemplateLiterals": false }], // Enforce single quotes
            "object-curly-spacing": ["error", "always"], // Ensure spaces inside braces
            "comma-dangle": ["error", {
                "arrays": "always-multiline",
                "objects": "always-multiline",
                "imports": "always-multiline",
                "exports": "always-multiline",
                "functions": "ignore"
            }], // Enforce trailing commas in multiline objects
            "vue/html-closing-bracket-newline": ["error", {
                "singleline": "never",
                "multiline": "never"
            }], // Handling brackets in Vue similar to JSX
            "arrow-parens": ["error", "always"], // Always use parentheses around arrow function arguments
            "vue/multi-word-component-names": "off", // Disable multi-word component names rule
            "import/no-extraneous-dependencies": "off", // Disable no-extraneous-dependencies rule
            "max-len": "off", // Disable max length rule
            "no-return-assign": "off", // Disable no-return-assign rule
            "prefer-destructuring": "off" // Disable prefer-destructuring rule
        },
        settings: {
            "import/resolver": {
                alias: {
                    map: [
                        ["@", "./resources/js"] // Adjust this path based on your project's structure
                    ],
                    extensions: [".js", ".jsx", ".vue"]
                }
            }
        }
    }
];
