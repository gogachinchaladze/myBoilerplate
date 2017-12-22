const gulp = require('gulp');
const gcmq = require('gulp-group-css-media-queries');
const svgstore = require('gulp-svgstore');
const svgmin = require('gulp-svgmin');
const sourcemaps = require('gulp-sourcemaps');
const gulpLoadPlugins = require('gulp-load-plugins');
const path = require('path');
const $ = gulpLoadPlugins();
const write_dir = "resources/";
const output_dir = "public/";
const addsrc = require('gulp-add-src');


gulp.task('styles', function () {
  // return gulp.src([
  //   write_dir + 'scss/style_en.scss',
  //   write_dir + 'scss/style_ka.scss'
  // ])
  return gulp.src([
    write_dir + 'css/style.scss'
  ])
    .pipe($.plumber())
    .pipe($.sourcemaps.init())
    .pipe($.sass.sync({outputStyle: 'compressed', precision: 10, includePaths: ['.']}).on('error', $.sass.logError))
    .pipe(gcmq())
    .pipe($.autoprefixer({browsers: ['> .5%', 'Firefox ESR']}))
    .pipe($.sass.sync({outputStyle: 'compressed', precision: 10, includePaths: ['.']}).on('error', $.sass.logError))
    .pipe(gulp.dest(output_dir + 'css'));
});

gulp.task('scripts', function () {
  return gulp.src(write_dir + 'js/main.js')
    .pipe(sourcemaps.init())
    .on('error', onError)
    // .pipe($.uglify({mangle:false}))
    .pipe($.concat({path: 'script.min.js', stat: {mode: 0666}}))
    .pipe(sourcemaps.write('',{addComment: false}))
    .pipe(gulp.dest(output_dir + 'js'));
});

gulp.task('lib-js', function () {
  return gulp.src([
    write_dir + 'js/lib/*.js'
  ])
    // .pipe($.uglify())
    .pipe($.concat({path: 'lib.min.js', stat: {mode: 0666}}))
    .pipe(gulp.dest(output_dir + 'js'))
    .on('error', onError);
});

gulp.task('svg-store', function () {
  return gulp
    .src(output_dir + 'img/svg-icons/*.svg')
    .pipe(svgmin(function (file) {
      var prefix = path.basename(file.relative, path.extname(file.relative));
      return {
        plugins: [{
          cleanupIDs: {
            prefix: prefix + '-',
            minify: false
          }
        }]
      }
    }))
    .pipe(svgstore())
    .pipe(gulp.dest(output_dir + 'img'))
    .on('error', onError);
});

gulp.task('watch', function () {
  gulp.watch([
    write_dir + 'js/*.js',
  ], ['scripts']);
  gulp.watch([
    write_dir + 'js/lib/*.js'
  ], ['lib-js']);
  gulp.watch([
    write_dir + 'css/*.scss',
    write_dir + 'css/*/*.scss'
  ], ['styles']);
  gulp.watch([
    output_dir + 'images/svg-icons/*.svg',
    output_dir + 'images/svg-icons/*/*.svg'
  ], ['svg-store']);
});

gulp.task('default', function () {
  gulp.start('watch');
  gulp.start('lib-js');
});


function onError(err) {
  console.log(err.message);
}
