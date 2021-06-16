var gulp = require('gulp');
var sass = require("gulp-sass");
watch = require('gulp-watch'),
browserSync = require('browser-sync').create();

gulp.task("style", function() {
    return gulp
        .src(["resources/sass/**/**/*.scss"])
        .pipe(sass())
        .pipe(gulp.dest("public/css"))
        .pipe(
            browserSync.reload({
                stream: true
            })
        );
});

gulp.task('watch', function() {
    browserSync.init(null, {
        notify: false,
        proxy: 'localhost:8000',
        open: false,
        files: [
            'app/**/*.php',
            'resources/views/**/*.php',
            'resources/sass/*.scss',
            'resources/js/**/*.js',
            'public/js/**/*.js',
            'public/css/**/*.css'
        ],
        watchOptions: {
            usePolling: true,
            interval: 500
        }
    });
    gulp.watch("resources/sass/**/*.scss", gulp.series("style"));

    watch('./resources/**/*.php', function() {
        browserSync.reload();
    });
});