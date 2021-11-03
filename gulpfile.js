const gulp = require('gulp');
const del = require('del');
const gulpLoadPlugins = require('gulp-load-plugins');

const plugins = gulpLoadPlugins();

const scssSourcePath = './blocks/simple_gallery/scss';
const scssDestPath = './blocks/simple_gallery/css_files';

gulp.task('scss', function () {
    del(scssDestPath + '/simple-gallery.css');
    del(scssDestPath + '/simple-gallery.css.map');

    return gulp.src(scssSourcePath + '/simple-gallery.scss')
        .pipe(plugins.sourcemaps.init())
        .pipe(plugins.sass().on('error', plugins.sass.logError))
        .pipe(plugins.autoprefixer({
            browsers: ['last 2 versions']
        }))
        .pipe(plugins.concat('simple-gallery.css'))
        .pipe(plugins.csso())
        .pipe(plugins.sourcemaps.write('.'))
        .pipe(gulp.dest(scssDestPath))
});

gulp.task('default', function () {
    console.log('Gulp watch has started...');
    gulp.watch(scssSourcePath + '/**/*.scss', {usePolling: true}, gulp.parallel('scss'));
});