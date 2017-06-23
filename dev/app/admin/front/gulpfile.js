'use strict';

var gulp = require('gulp');
var $ = require('gulp-load-plugins')();
var argv = require('yargs').argv;

var webpack_stream = require('webpack-stream');
var webpack = require('webpack');
var extender = require('gulp-html-extend');

// check for --production flag
var is_production = !!(argv.production);
var is_nomaps = !!(argv.nomaps);
var is_minifyjs = !!(argv.minifyjs);
var is_cleancss = !!(argv.cleancss);
var is_uncss = !!(argv.uncss);

// Browsers to target when prefixing CSS
var copatibility = ['last 2 versions', 'ie >= 9'];

var paths = {
    scss: [
        'bower_components/foundation-sites/scss',
        'bower_components/motion-ui/src/',
        'scss/com/'
    ]
};

var base_dir = require("path").join(__dirname, './../../../../');

gulp.task('scss', function() {
    //console.log(is_cleancss);
    var p,
        uncss,
        cleancss;

    p = gulp.src('./scss/app/**/*.scss');

    if (!is_nomaps) {
        p= p.pipe($.sourcemaps.init());
    }

    p = p.pipe(
            $.sass({includePaths: paths.scss})
            .on('error', $.sass.logError)
        )
        .pipe($.autoprefixer({browsers: copatibility}));

    if (is_uncss) {
        uncss = $.uncss({
            html: [
                'src/html/lib/**/*.html',
                'html/flow/**/*.html'
            ],
            ignore: [
                new RegExp('^meta\..*'),
                new RegExp('^\.is-.*')
            ]
        });
        p = p.pipe(uncss);
    }

    if (is_cleancss) {
        p = p.pipe($.cleanCss());
    }

    if (!is_nomaps) {
        p = p.pipe($.sourcemaps.write('maps'));
    }


    return p.pipe(gulp.dest(base_dir + 'site/static/css/'));

});

gulp.task('webpack', function() {
    var config = require('./webpack.config.js');
    //console.log(config);
    if (is_minifyjs) {
        var uglify = new webpack.optimize.UglifyJsPlugin({
            minimize: true
        })
        if (config.plugins) {
            config.plugins.push(uglify);
        } else {
            config.plugins = [uglify];
        }
    }
    if (is_nomaps) {
        config.devtool = false;
    }
    if (is_production) {
        config.output.publicPath = 'http://static.ideapar.com/js/';
    } else {
        config.output.publicPath = 'http://static.ideapar.do/js/';
    }
    return gulp.src('src/js/app/*.js')
        .pipe(webpack_stream(config))
        .pipe(gulp.dest(base_dir + 'site/static/js/'));
});

gulp.task('watch', function() {
    gulp.watch('scss/**/*.scss', ['scss']);
    gulp.watch('js/**/*.js', ['webpack']);
    gulp.watch('js/**/*.tpl', ['webpack']);
});
