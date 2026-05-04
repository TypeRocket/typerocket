import crypto from 'node:crypto';
import fs from 'node:fs';
import path from 'node:path';

export function mixManifestPlugin({ publicPath, entries, styles }) {
    const entryFiles = Object.entries(entries).map(([output, source]) => {
        return path.posix.join(output, path.basename(source));
    });
    const manifestFiles = [
        ...entryFiles,
        ...Object.keys(styles),
    ];
    let mode = 'production';

    return {
        name: 'mix-manifest',
        apply: 'build',
        configResolved(config) {
            mode = config.mode;
        },
        closeBundle() {
            const manifest = {};
            const production = mode === 'production';

            for (const [output, source] of Object.entries(styles)) {
                const css = fs.readFileSync(path.resolve(source));
                const absolutePath = path.resolve(publicPath, output);

                fs.mkdirSync(path.dirname(absolutePath), { recursive: true });
                fs.writeFileSync(absolutePath, css.length === 0 ? '\n' : css);
            }

            for (const file of entryFiles) {
                const absolutePath = path.resolve(publicPath, file);

                if (
                    fs.existsSync(absolutePath) &&
                    fs.readFileSync(absolutePath, 'utf8').trim() === ''
                ) {
                    fs.writeFileSync(absolutePath, '');
                }
            }

            const staleFiles = [
                ...Object.keys(styles).map((file) => `${file}.js`),
                ...entryFiles.map((file) => `${file}.map`),
            ];

            for (const staleFile of staleFiles) {
                const absolutePath = path.resolve(publicPath, staleFile);

                if (fs.existsSync(absolutePath)) {
                    fs.unlinkSync(absolutePath);
                }
            }

            for (const file of manifestFiles) {
                const absolutePath = path.resolve(publicPath, file);
                const key = `/${file}`;
                let value = key;

                if (production) {
                    const hash = crypto
                        .createHash('md5')
                        .update(fs.readFileSync(absolutePath))
                        .digest('hex');

                    value = `${key}?id=${hash}`;
                }

                manifest[key] = value;
            }

            fs.writeFileSync(
                path.resolve(publicPath, 'mix-manifest.json'),
                `${JSON.stringify(manifest, null, 4)}\n`
            );
        },
    };
}
