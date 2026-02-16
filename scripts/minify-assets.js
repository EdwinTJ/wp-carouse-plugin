const fs = require('fs');
const path = require('path');
const CleanCSS = require('clean-css');
const terser = require('terser');

const ROOT = path.resolve(__dirname, '..');
const CSS_EXT = '.css';
const JS_EXT = '.js';
const MIN_SUFFIX = '.min';

const includeDirs = [
  'admin',
  'assets/css',
  'styles',
  'assets/js'
];

const shouldMinify = (filePath) => {
  if (filePath.endsWith(`${MIN_SUFFIX}${CSS_EXT}`)) return false;
  if (filePath.endsWith(`${MIN_SUFFIX}${JS_EXT}`)) return false;
  return filePath.endsWith(CSS_EXT) || filePath.endsWith(JS_EXT);
};

const walk = (dirPath, results = []) => {
  if (!fs.existsSync(dirPath)) return results;
  const entries = fs.readdirSync(dirPath, { withFileTypes: true });
  for (const entry of entries) {
    const fullPath = path.join(dirPath, entry.name);
    if (entry.isDirectory()) {
      walk(fullPath, results);
    } else if (entry.isFile() && shouldMinify(fullPath)) {
      results.push(fullPath);
    }
  }
  return results;
};

const minifyCss = (filePath) => {
  const source = fs.readFileSync(filePath, 'utf8');
  const output = new CleanCSS({ level: 2 }).minify(source);
  if (output.errors && output.errors.length) {
    throw new Error(`CSS minify failed for ${filePath}: ${output.errors.join('; ')}`);
  }
  const target = filePath.replace(/\.css$/i, `${MIN_SUFFIX}${CSS_EXT}`);
  fs.writeFileSync(target, output.styles, 'utf8');
};

const minifyJs = async (filePath) => {
  const source = fs.readFileSync(filePath, 'utf8');
  const output = await terser.minify(source, {
    compress: true,
    mangle: true
  });
  if (output.error) {
    throw output.error;
  }
  const target = filePath.replace(/\.js$/i, `${MIN_SUFFIX}${JS_EXT}`);
  fs.writeFileSync(target, output.code || '', 'utf8');
};

const run = async () => {
  const files = [];
  for (const dir of includeDirs) {
    walk(path.join(ROOT, dir), files);
  }

  const cssFiles = files.filter((file) => file.endsWith(CSS_EXT));
  const jsFiles = files.filter((file) => file.endsWith(JS_EXT));

  for (const file of cssFiles) {
    minifyCss(file);
  }

  for (const file of jsFiles) {
    await minifyJs(file);
  }

  console.log(`Minified ${cssFiles.length} CSS files and ${jsFiles.length} JS files.`);
};

run().catch((error) => {
  console.error(error);
  process.exit(1);
});
