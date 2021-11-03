const gulp = require('gulp');
const del = require('del');
const gulpLoadPlugins = require('gulp-load-plugins');

const $ = gulpLoadPlugins();

gulp.task('scss', function() {
	del('./css_files/simple-gallery.css');
	del('./css_files/simple-gallery.css.map');

	return gulp.src('./scss/simple-gallery.scss')
		.pipe($.sourcemaps.init())
		.pipe($.sass().on('error', $.sass.logError))
		.pipe($.autoprefixer({
            browsers: ['last 2 versions']
		}))
		.pipe($.concat('simple-gallery.css'))
		.pipe($.csso())
		.pipe($.sourcemaps.write('.'))
		.pipe(gulp.dest('./css_files'))
});

gulp.task('default', function() {
	console.log('Gulp watch started...');
	gulp.watch('./scss/**/*.scss', {usePolling: true}, gulp.parallel('scss'));
});