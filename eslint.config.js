import path from 'path'
import { fileURLToPath } from 'url'

import { FlatCompat } from '@eslint/eslintrc'
import pluginJs from '@eslint/js'
import pluginVue from 'eslint-plugin-vue'
import globals from 'globals'

// mimic CommonJS variables -- not needed if using CommonJS
const filename = fileURLToPath(import.meta.url)
const dirname = path.dirname(filename)
const compat = new FlatCompat({ baseDirectory: dirname, recommendedConfig: pluginJs.configs.recommended })

export default [
    {
        languageOptions: {
            globals: globals.browser,
            parserOptions: {
                ecmaVersion: 2020, // Allows for the parsing of modern ECMAScript features
                sourceType: 'module', // Allows for the use of imports
            },
        },
    },
    ...compat.extends('airbnb-base'),
    ...pluginVue.configs['flat/essential'],
    {
        // ESLint rules
        rules: {
            indent: ['error', 4], // Set indentation to 4 spaces
            semi: ['error', 'never'], // Disable semicolons
            quotes: ['error', 'single', { avoidEscape: true, allowTemplateLiterals: false }], // Enforce single quotes
            'object-curly-spacing': ['error', 'always'], // Ensure spaces inside braces
            'comma-dangle': ['error', {
                arrays: 'always-multiline',
                objects: 'always-multiline',
                imports: 'always-multiline',
                exports: 'always-multiline',
                functions: 'ignore',
            }],
            'vue/html-closing-bracket-newline': ['error', {
                singleline: 'never',
                multiline: 'never',
            }],
            'vue/component-tags-order': ['error', {
                order: [['template'], ['script[setup]'], ['script:not([setup])'], ['style']],
            }],
            'arrow-parens': ['error', 'always'],
            'vue/multi-word-component-names': 'off',
            'import/no-extraneous-dependencies': 'off',
            'max-len': 'off',
            'no-return-assign': 'off',
            'prefer-destructuring': 'off',
            'import/order': ['error', {
                groups: ['builtin', 'external', 'internal'],
                pathGroups: [
                    {
                        pattern: '@/components/**',
                        group: 'internal',
                        position: 'after',
                    },
                    {
                        pattern: '@/utils/**',
                        group: 'internal',
                        position: 'after',
                    },
                    {
                        pattern: '@/store/**',
                        group: 'internal',
                        position: 'after',
                    },
                ],
                pathGroupsExcludedImportTypes: ['builtin'],
                'newlines-between': 'always',
                alphabetize: {
                    order: 'asc',
                    caseInsensitive: true,
                },
            }],
        },
        settings: {
            'import/resolver': {
                alias: {
                    map: [
                        ['@', './resources/js'], // Adjust this path based on your project's structure
                    ],
                    extensions: ['.js', '.jsx', '.vue'],
                },
            },
        },
    },
]
