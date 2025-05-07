const path = require('path');
const Encore = require('@symfony/webpack-encore');
const FosRouting = require('fos-router/webpack/FosRouting');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.ts')
    .addEntry('menu', './assets/menu.js')
    .addEntries({
        security: './assets/security.js',
        profile: './assets/profile.js'
    })
    .splitEntryChunks()
    .enableVueLoader(() => {}, { version: 3 })
    .copyFiles({
            from: './assets/images',
            to: 'images/[path][name].[ext]',
    })

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })
    .enableSassLoader()
    .enablePostCssLoader()
    .enableTypeScriptLoader()
    .addAliases({
        '@app': path.resolve(__dirname, 'assets/vue'),
        '@js': path.resolve(__dirname, 'assets/js'),
    })
    .addPlugin(
        new FosRouting(
            { target: './assets/js/routes.json' },
            false
        )
    )
    .configureWatchOptions((watchOptions) => {
        watchOptions.ignored = '/public/build/'
    })
;

module.exports = Encore.getWebpackConfig();
