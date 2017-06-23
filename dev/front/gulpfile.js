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

var path = require("path");
var base_dir = path.join(__dirname, './../../');
var front_dir = path.join(base_dir, 'dev/front');

var paths = {
    scss: [
        front_dir + '/bower_components/foundation-sites/scss',
        front_dir + '/bower_components/motion-ui/src',
        //'scss/com'
    ]
};

var exec = require('child_process').exec;
var front_config = require(path.join(base_dir, 'setting/config/config.front.js'));
var local_config = require(path.join(front_dir, 'js/config_local/z.config.js'));


function change_working_dir(app) {
    process.chdir(path.join(base_dir, 'dev/app/' + app + '/front'));
}
function create_scss_handle(app) {

    return function () {
        var p,
            uncss,
            cleancss,
            scss_paths = paths.scss;

        change_working_dir(app);

        p = gulp.src('./scss/app/**/*.scss');

        if (!is_nomaps) {
            p= p.pipe($.sourcemaps.init());
        }

        scss_paths.push('scss/com');

        p = p.pipe(
                $.sass({includePaths: scss_paths})
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
    }
}

function create_webpack_handle(app) {
    return function () {
        var config,
            app_front_dir;

        app_front_dir = path.join(base_dir, '/dev/app/' + app + '/front');

        change_working_dir(app);

        config = require(app_front_dir + '/webpack.config.js');
        config.resolve.root.push(path.resolve(front_dir + '/js/lib'));
        config.resolve.root.push(path.resolve(front_dir + '/js/config_local'));
        config.resolve.root.push(path.resolve(front_dir + '/node_modules'));

        config.resolve.root.push(path.resolve(app_front_dir + '/js/app'));
        config.resolve.root.push(path.resolve(app_front_dir + '/js/com'));

        config.resolveLoader = {modulesDirectories: [path.resolve(front_dir + '/node_modules')]};

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
        /*
        if (is_production) {
            config.output.publicPath = 'http://static.ideapar.com/js/';
        } else {
            config.output.publicPath = 'http://static.ideapar.do/js/';
        }
        */
        config.output.publicPath = 'http://' + local_config.static_host + '/js/';

        return gulp.src('dev/js/app/*.js')
            .pipe(webpack_stream(config))
            .pipe(gulp.dest(base_dir + 'site/static/js/'));
        }
}

var i = 0;
var len = front_config.app.length;
var app;
for (i = 0; i < len; i++) {
    app = front_config.app[i];
    gulp.task(app + '-scss', create_scss_handle(app));
    gulp.task(app + '-webpack', create_webpack_handle(app));
}

gulp.task('jsconfig', function() {
    exec(path.join(base_dir, '/cmd/jsconfig'), function (error, stdout, stderror) {
        if (error !== null) {
            console.log('exec error: ' + error);
            return false;
        }
        front_config = require(path.join(front_dir, 'js/config_local/z.config.js'));
        console.log('generate js config base php');
    });
});

gulp.task('watch', function() {
    var i = 0;
    var len = front_config.app.length;
    var app;
    var app_front_dir;

    for (i = 0; i < len; i++) {
        app = front_config.app[i];
        app_front_dir = path.join(base_dir, 'dev/app/' + app + '/front');
        console.log('watch ' + app_front_dir);
        gulp.watch(app_front_dir + '/scss/**/*.scss', [app + '-scss']);
        gulp.watch(app_front_dir + '/js/**/*.js', [app + '-webpack']);
        gulp.watch(app_front_dir + '/js/**/*.tpl', [app + '-webpack']);

        gulp.watch(base_dir + 'dev/front/js/**/*.js', [app + '-webpack']);
    }

    console.log('php config');
    gulp.watch(base_dir + 'dev/app/**/setting/**/*.php', ['jsconfig']);
});
