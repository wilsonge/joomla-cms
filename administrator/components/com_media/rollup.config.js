import VuePlugin from 'rollup-plugin-vue'
import resolve from '@rollup/plugin-node-resolve';
import replace from '@rollup/plugin-replace';
import {dependencies} from './../../../package.json';

const isProduction = !process.env.ROLLUP_WATCH;
const globals = { vue: 'Vue' };
const external = Object.keys(dependencies);
let plugins = [
  resolve(),
  VuePlugin({
    template: {
      isProduction,
      compilerOptions: { preserveWhitespace: false }
    },
    css: true
  }),
  replace({ __buildEnv__: 'production' }),
];

export default   {
  external,
  input: 'administrator/components/com_media/resources/scripts/mediamanager.js',
  output: {
    globals,
    file: 'media/com_media/js/mediamanager.min.js',
    sourcemap: process.env.NODE_ENV !== 'prod',
  },
  plugins: plugins,
}
