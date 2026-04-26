/**
 * Rollup configuration for the Graphing admin assets.
 *
 * Bundles Chart.js and the Entwine integration into a single IIFE.
 * CSS is compiled separately via the Sass CLI to guarantee a stable filename.
 */
import resolve from '@rollup/plugin-node-resolve';

export default [
    {
        input: 'client/admin/src/js/chart-field.js',
        output: {
            file: 'client/admin/dist/bundle.js',
            format: 'iife',
            globals: {
                jquery: 'jQuery',
            },
            // No hashing — filename must be stable so it can be referenced in PHP
        },
        external: ['jquery'],
        plugins: [
            resolve(),
        ],
    },
];

