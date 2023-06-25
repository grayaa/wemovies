const Encore = require('@symfony/webpack-encore');
const webpack = require('webpack');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .enableSingleRuntimeChunk()
    .addEntry('app', './assets/app.js')
    .addStyleEntry('css/app', './assets/styles/app.css')
    .addStyleEntry('fontawesome', './node_modules/@fortawesome/fontawesome-free/scss/fontawesome.scss')
    .addStyleEntry('css/slick', 'slick-carousel/slick/slick.css')
    .addStyleEntry('css/slick-theme', 'slick-carousel/slick/slick-theme.css')
    .addEntry('jquery', 'jquery/dist/jquery.min.js')
    .addEntry('js/bootstrap', 'bootstrap/dist/js/bootstrap.min.js')
    .addEntry('js/slick', 'slick-carousel/slick/slick.js')
    .configureLoaderRule('css', (loaderRule) => {
        loaderRule.test = /\.css$/;
        loaderRule.use = ['style-loader', 'css-loader'];
    })
    .configureLoaderRule('fonts', (loaderRule) => {
        loaderRule.test = /\.(woff|woff2|eot|ttf|otf)$/;
        loaderRule.use = [{
            loader: 'file-loader',
            options: {
                outputPath: 'fonts/',
                publicPath: '/build/fonts',
            },
        }];
    })
    .copyFiles({
        from: './node_modules/@fortawesome/fontawesome-free/webfonts',
        to: 'webfonts/[path][name].[ext]',
    })
    .enableSassLoader()
    .autoProvidejQuery()
    .addPlugin(
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
        })
    );

module.exports = Encore.getWebpackConfig();
