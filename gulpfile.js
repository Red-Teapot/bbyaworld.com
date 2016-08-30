var gulp = require('gulp');
var exists = require('path-exists').sync;
var mainBowerFiles = require('main-bower-files');
var bowerMain = require('bower-main');
var uglify = require('gulp-uglify');
var cleanCSS = require('gulp-clean-css');
var concat = require('gulp-concat');
var sass = require('gulp-ruby-sass');
var del = require('del');
var runSequence = require('run-sequence');
var browserSync = require('browser-sync');

var c = {
    tempDir: 'temp/',
    tempCSSDir: 'temp/css/',
    tempJSDir: 'temp/js/',

    tempVendorCSS: '1vendor.css',
    tempVendorJS: '1vendor.js',

    tempThirdpartyCSS: '2thirdparty.css',
    tempThirdpartyJS: '2thirdparty.js',

    thirdpartyCSSDir: 'thirdparty/css/',
    thirdpartyJSDir: 'thirdparty/js/',

    sassDir: 'src/sass/',
    sassMainFile: 'src/sass/main.scss',
    tempMainCSS: '3main.css',

    jsDir: 'src/js/',
    tempMainJS: '3main.js',

    srcDir: 'src/',
    templateDir: 'templates/',

    resultCSS: 'main.css',
    resultJS: 'main.js',

    resultDir: 'public/assets/',
    resultCSSDir: 'public/assets/',
    resultJSDir: 'public/assets/',

    fontsResultDir: 'public/fonts/',
};

gulp.task('clean-temp', function() {
    return del([c.tempDir + '/**/*', c.tempDir + '/*', c.tempDir]);
});

gulp.task('clean-assets', function() {
    return del([c.resultDir + '*.js', c.resultDir + '*.css']);
});

gulp.task('build-bower-assets-css', function() {
    var minifiedCSS = bowerMain('css', 'min.css').minified;

    // Force Gulp to use Bootstrap minified CSS
    minifiedCSS.push('bower_components/bootstrap/dist/css/bootstrap.min.css');

    return gulp.src(minifiedCSS)
        .pipe(concat(c.tempVendorCSS))
        .pipe(gulp.dest(c.tempCSSDir));
});

gulp.task('build-bower-assets-js', function() {
    var minifiedJS = bowerMain('js', 'min.js').minified;

    return gulp.src(minifiedJS)
        .pipe(concat(c.tempVendorJS))
        .pipe(gulp.dest(c.tempJSDir));
});

gulp.task('copy-bootstrap-font', function() {
    return gulp.src(['bower_components/bootstrap/dist/fonts/*.*'])
        .pipe(gulp.dest(c.fontsResultDir));
});

gulp.task('sass', function() {
    return sass(c.sassMainFile, {style: 'compressed'})
        .pipe(concat(c.tempMainCSS))
        .pipe(gulp.dest(c.tempCSSDir));
});

gulp.task('js', function() {
    return gulp.src([c.jsDir + '*.js', c.jsDir + '**/*.js'])
        .pipe(concat(c.tempMainJS))
        .pipe(gulp.dest(c.tempJSDir));
});

gulp.task('third-party-css', function() {
    return gulp.src([c.thirdpartyCSSDir + '*.css', c.thirdpartyCSSDir + '**/*.css'])
        .pipe(concat(c.tempThirdpartyCSS))
        .pipe(gulp.dest(c.tempCSSDir));
});

gulp.task('third-party-js', function() {
    return gulp.src([c.thirdpartyJSDir + '*.js', c.thirdpartyJSDir + '**/*.js'])
        .pipe(concat(c.tempThirdpartyJS))
        .pipe(gulp.dest(c.tempJSDir));
});

gulp.task('third-party', ['third-party-css', 'third-party-js']);

gulp.task('merge-css', function() {
    return gulp.src(c.tempCSSDir + '*.css')
        .pipe(cleanCSS({keepSpecialComments: 0}))
        .pipe(concat(c.resultCSS))
        .pipe(gulp.dest(c.resultCSSDir));
});

gulp.task('merge-js', function() {
    return gulp.src(c.tempJSDir + '*.js')
        .pipe(uglify())
        .pipe(concat(c.resultJS))
        .pipe(gulp.dest(c.resultJSDir));
});

gulp.task('build-bower-assets', ['build-bower-assets-css', 'build-bower-assets-js']);

gulp.task('build-assets', ['sass', 'js']);

gulp.task('default', function(cb) {
    runSequence(['clean-temp', 'clean-assets'],
                ['build-bower-assets', 'build-assets', 'third-party', 'copy-bootstrap-font'],
                ['merge-css', 'merge-js'],
               cb);
});

gulp.task('updateCSS', function(cb) {
    del(c.tempDir + c.tempMainCSS);
    del(c.resultCSSDir + '*.css')
    runSequence('sass',
                'merge-css',
                cb);
    browserSync.reload();
});

gulp.task('updateJS', function(cb) {
    del(c.tempDir + c.tempMainJS);
    del(c.resultJSDir + '*.js');
    runSequence('js',
                'merge-js',
                cb);
    browserSync.reload();
});

gulp.task('watch', function() {
    browserSync.init({
        server: false,
    });

    gulp.watch([c.sassDir + '*.*', c.sassDir + '**/*.*'], ['updateCSS']);
    gulp.watch([c.jsDir + '*.*', c.jsDir + '**/*.*'], ['updateJS']);
    gulp.watch([c.srcDir + '*.php', c.srcDir + '**/*.php',
                c.templateDir + '*.html', c.templateDir + '**/*.phtml',]).on('change', browserSync.reload);
});
