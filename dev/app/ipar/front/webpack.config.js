var path = require("path");

module.exports = {
    context: __dirname + "/js/app",
    devtool: 'source-map',
    entry: {
        ipar: 'ipar',
    },
    output: {
        path: path.join(__dirname, 'js'),
        filename: '[name].js',
        chunkFilename: 'ipar.[id].js'
            //publicPath: '/js/'
    },
    module: {
        loaders: [
            {
                test: /\.scss$/,
                loaders: ["style", "css", "sass"]
            },
            {
                test: /\.tpl/,
                loaders: ["html"]
            }
        ]
    },
    resolve: {
        root: [
            //path.resolve('./js/lib'),
            //path.resolve('./js/app'),
            //path.resolve('./js/com')
        ]
    },
    plugins: []
};
