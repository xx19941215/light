const path = require('path');

module.exports = (env) => {
    let options = {
        devtool: "source-map",
        entry: {
            app: path.resolve(__dirname, 'resources/assets/js') + '/app.js'
        },
        output: {
            filename: '[name].js',
            chunkFilename: '[hash].js',
            path: path.resolve(__dirname, 'public/js')
        },
        module: {
            rules: [
                {
                    test: /\.s?css$/,
                    use: [
                        {
                            loader: "sass-loader",
                            options: {
                                includePaths: path.resolve(__dirname, 'resources/assets/sass/'),
                                outFile: path.resolve(__dirname, 'public/css/app.css')
                            }
                        }]
                }
            ],
        }
    };

    return options;
}
