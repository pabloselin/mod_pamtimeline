var gulp = require('gulp');
var less = require('gulp-less');
var path = require('path');
var autoprefixer = require('gulp-autoprefixer');
var concat = require('gulp-concat');
var cleanCss = require('gulp-clean-css');
var watch = require('gulp-watch');
var browserSync = require('browser-sync').create();
var reload = browserSync.reload;


gulp.task('default', ['less-timeline', 'less-relations']);

gulp.task('less-timeline', function() {
	 return gulp.src('less/lineatiempo.less')
		  		.pipe(less() )
		  		.pipe(autoprefixer())
		  		.pipe(gulp.dest('css'))
		  		.pipe(browserSync.stream());
  	});

gulp.task('less-relations', function() {
	 return gulp.src('less/relaciones.less')
		  		.pipe(less() )
		  		.pipe(autoprefixer())
		  		.pipe(gulp.dest('css'))
		  		.pipe(browserSync.stream());
  	});


gulp.task('watch', function() {
	browserSync.init({
		proxy: 'localhost'
	});
	gulp.watch([
		'less/*.less',
		'less/timeline/*.less'
		], 
		[
		'less-timeline',
		'less-relations'
		]);
});

gulp.watch(['less/*.less']).on('change', reload);