var path              = require( 'path' );
var UglifyJSPlugin    = require( 'uglifyjs-webpack-plugin' );
var ExtractTextPlugin = require( 'extract-text-webpack-plugin' );

module.exports = {
    entry: './src/index.js',
    devtool : 'source-map',
	output : {
		path 	 : path.resolve( __dirname , 'dist' ),
		filename : 'gutenberg-support.js'
	},
	module: {
		loaders: [
			{
                test: /.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader',
            },
            {
                test    : /\.scss$/,
                exclude : '/node_modules/',
                use     : ExtractTextPlugin.extract( {
                    use : [ { loader : 'css-loader' , options : { minimize : true } } , 'sass-loader' ]
                } )
            }
		],
	},
    plugins : [
         new UglifyJSPlugin({ sourceMap: true }),
         new ExtractTextPlugin( 'gutenberg-support.css' )
    ]
};
