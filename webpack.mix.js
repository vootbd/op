const mix = require('laravel-mix')
require('laravel-mix-react-css-modules')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
*/

/////
//  Watching Target Files Definition
/////

const glob = require('glob')

// Sass Compilation
glob.sync('resources/sass/**/*.scss').map(file => {

    // set base index for root path "/sass"
    const baseIndex = file.indexOf('/sass')

    // prepare css generating path
    const baseDir = file.slice(baseIndex + 1);
    const newDir = baseDir.split('/').reverse().slice(1).reverse().join('/');
    const cssPath = newDir.replace('sass', 'css')

    mix.sass(file, 'public/' + cssPath)
})

// React Bundling
glob.sync('resources/js/react/**/*.js').map(file => {

    // set base index for root path "/js"
    const baseIndex = file.indexOf('/js')

    // prepare js generating path
    const baseDir = file.slice(baseIndex + 1);
    const jsPath = baseDir.split('/').reverse().slice(1).reverse().join('/');

    mix.react(file, 'public/' + jsPath)
        .reactCSSModules()
})

/////
//  Webpack and BrowserSync Setting
/////

mix.options({
  hmrOptions: {
    host: 'localhost',
    port: '3080'
  }
});

mix.webpackConfig({
    output: {
        publicPath: 'http://localhost:3080/'
    },
    devServer: {
        contentBase: path.join(__dirname, "public"),
        host: '0.0.0.0',
        port: 3080,
        proxy: {
            // Proxying to BrowserSync Port
            '/': 'http://localhost:3000'
        },
        disableHostCheck: true
    },
    watchOptions: {
        poll: 2000,
        aggregateTimeout: 300,
        ignored: /node_modules/
    }
});

mix.browserSync({
  files: [
      './resources/sass/**/*.scss',
      './resources/views/**/*.blade.php'
  ],
  watchOptions: {
      usePolling: true,
      interval: 2000
  },
  open: false,
  host: 'localhost',
  port: 3000,
  ui: {
      port: 3001
  },
  // Proxying to Nginx
  proxy: 'webserver'
});