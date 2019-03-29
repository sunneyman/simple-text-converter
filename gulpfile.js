'use strict';

// @todo add copyright changer

// Caminhos
const paths = {

  "input": {
    "styles": "assets/styles",
    "scripts": "assets/scripts"
  },

  "output": {
    "resources": "dist",
    "build": "dist/build",
    "styles": "public/css",
    "scripts": "public/js"
  }

};

// Módulos comuns
const gulp       = require('gulp');
const watch      = require('gulp-watch');
const buffer     = require('vinyl-buffer');
const sourcemaps = require('gulp-sourcemaps');
const gulpif     = require('gulp-if');
const replace    = require('replace-in-file');

// Versionamento de ficheiros (cache busting)
const rev    = require('gulp-rev');
const revDel = require('rev-del');
const del    = require('del');

// Estilos
const sass     = require('gulp-sass');
const bulksass = require('gulp-sass-bulk-import');
const cleancss = require('gulp-clean-css');
const prefixer = require('gulp-autoprefixer');

// Scripts
const babel    = require('gulp-babel');
const include  = require('gulp-include');
const uglify   = require('gulp-uglify-es').default;

// Variáveis adicionais
let deploy = false;

/**
 * Tasks
 */
// Cleanup styles and scripts build folder
gulp.task('cleanup', function() {

  return del([
    paths.output.resources + '/scripts/**',
    paths.output.resources + '/styles/**'
  ]);

});



// Styles
gulp.task('styles', function() {

  return gulp.src(paths.input.styles + '/[^_]*.scss')
  .pipe(bulksass())
  .pipe(gulpif(!deploy, sourcemaps.init()))
  .pipe(sass({
    includePaths: ['./styles']
  }).on('error', sass.logError))
  .pipe(prefixer({
    browsers: ['last 5 versions', 'IOS 7'],
  }))
  .pipe(gulpif(deploy, cleancss()))
  .pipe(gulpif(!deploy, sourcemaps.write('/sourcemaps')))
  .pipe(gulp.dest(paths.output.styles));

});



// Scripts
gulp.task('scripts', function() {

  return gulp.src(paths.input.scripts + '/[^_]*.js')
  .pipe(gulpif(!deploy, sourcemaps.init()))
  .pipe(include())
  .pipe(babel())
  .pipe(uglify())
  .pipe(gulpif(!deploy, sourcemaps.write('/sourcemaps')))
  .pipe(gulp.dest(paths.output.scripts));

});

/**
 * Default/Watch
 */

// Task default, inicia watchers
gulp.task('default', function() {

  // Watcher de styles
  gulp.watch(paths.input.styles + '/**', gulp.series('styles'));

  // Watcher de scripts
  gulp.watch(paths.input.scripts + '/**', gulp.series('scripts'));

});


// Deploy
gulp.task('build', gulp.series(function(done){
  deploy = true;
  done();
}, 'cleanup', 'styles', 'scripts'));