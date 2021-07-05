const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const path = require('path');

const config = {
	...defaultConfig,
	entry: {
		'simple-301-redirects.core.min': './src/index.js',
	},
	output: {
		path: path.resolve(__dirname, 'assets/js'),
		filename: '[name].js',
	},
	plugins: [...defaultConfig.plugins, new CleanWebpackPlugin()],
};

module.exports = config;