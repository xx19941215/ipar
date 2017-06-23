var path = require("path");

module.exports = {
    context: __dirname + "/js/app",
    devtool: 'source-map',
    entry: {
        user: 'user',
    },
    output: {
        path: path.join(__dirname, 'js'),
        filename: '[name].js',
        chunkFilename: 'user.[id].js'
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
        ]
    },
    plugins: []
};
