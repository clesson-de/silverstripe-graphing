/**
 * Rollup configuration for the Graphing admin assets.
 *
 * Bundles Chart.js and the Entwine integration into a single IIFE.
 * CSS is compiled separately via the Sass CLI to guarantee a stable filename.
 */
import resolve from '@rollup/plugin-node-resolve';
import license from 'rollup-plugin-license';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

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
            license({
                thirdParty: {
                    // Writes a separate file listing all third-party licenses (MIT requirement)
                    output: {
                        file: path.join(__dirname, 'client/admin/dist/bundle.js.LICENSES.txt'),
                        template(dependencies) {
                            return dependencies
                                .map(dep =>
                                    `${dep.name} v${dep.version} — ${dep.license}\n${dep.licenseText ?? ''}`
                                )
                                .join('\n\n---\n\n');
                        },
                    },
                },
                banner: {
                    commentStyle: 'regular',
                    content: `Bundle includes third-party software. See bundle.js.LICENSES.txt for license notices.`,
                },
            }),
        ],
    },
];

