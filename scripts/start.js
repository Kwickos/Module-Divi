const webpack = require('webpack');
const path = require('path');

const config = {
    mode: 'development',
    entry: {
        'faq-module': './includes/modules/FaqModule/FaqModule.jsx'
    },
    output: {
        path: path.resolve(__dirname, '../dist'),
        filename: '[name].js'
    },
    module: {
        rules: [
            {
                test: /\.(js|jsx)$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env', '@babel/preset-react']
                    }
                }
            },
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader']
            }
        ]
    },
    resolve: {
        extensions: ['.js', '.jsx']
    },
    externals: {
        react: 'React',
        'react-dom': 'ReactDOM'
    },
    devtool: 'source-map',
    watch: true,
    watchOptions: {
        ignored: /node_modules/,
        aggregateTimeout: 300,
        poll: 1000
    }
};

const compiler = webpack(config);

compiler.watch({}, (err, stats) => {
    if (err || stats.hasErrors()) {
        console.error(err || stats.toString({
            chunks: false,
            colors: true
        }));
        return;
    }

    console.log(stats.toString({
        chunks: false,
        colors: true
    }));
    console.log('\nWatching for changes...');
}); 