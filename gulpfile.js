/*---------- gulpfile.js ------------*/
var gulp = require('gulp'),
    less = require('gulp-less'),
    clean = require('gulp-clean'),
    concatJs = require('gulp-concat'),
    minifyJs = require('gulp-uglify');

var bower_components_path = 'web/assets/vendor',
    assets_path = 'web/assets';

gulp.task('less', function() {
    return gulp.src(['web-src/less/*.less'])
        .pipe(less({compress: true}))
        .pipe(gulp.dest(assets_path + '/css/'));
});

gulp.task('images', function () {
    return gulp.src([
            'web-src/images/*'
        ])
        .pipe(gulp.dest(assets_path + '/images/'))
});

gulp.task('fonts', function () {
    return gulp.src([bower_components_path + '/bootstrap/fonts/*'])
        .pipe(gulp.dest(assets_path + '/fonts/'))
});

gulp.task('lib-js', function() {
    return gulp.src([
            bower_components_path + '/jquery/dist/jquery.js',
            bower_components_path + '/bootstrap/dist/js/bootstrap.js'
        ])
        .pipe(concatJs('app.js'))
        .pipe(minifyJs())
        .pipe(gulp.dest(assets_path + '/js/'));
});

gulp.task('pages-js', function() {
    return gulp.src([
            'web-src/js/*.js'
        ])
        .pipe(minifyJs())
        .pipe(gulp.dest('web/js/'));
});

gulp.task('clean', function () {
    return gulp.src([assets_path + '/css/*', assets_path + '/js/*', assets_path + '/images/*', assets_path + '/fonts/*'])
        .pipe(clean());
});

gulp.task('default', ['clean'], function () {
    var tasks = ['images', 'fonts', 'less', 'lib-js', 'pages-js'];
    tasks.forEach(function (val) {
        gulp.start(val);
    });
});

gulp.task('watch', function () {
    var less = gulp.watch('web-src/less/*.less', ['less']),
        js = gulp.watch('web-src/js/*.js', ['pages-js']);
});
