const Encore = require("@symfony/webpack-encore");

if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore
  .setOutputPath("public/build/")
  .setPublicPath("/build")
  .splitEntryChunks()
  .enableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableBuildNotifications()
  .enableSourceMaps(false)
  .configureBabelPresetEnv((config) => {
    config.useBuiltIns = "usage";
    config.corejs = "3.23";
  })
  .enableSassLoader()
  .enableLessLoader()
  .autoProvidejQuery()
  .addStyleEntry("appStyle", [
    "./node_modules/bootstrap/dist/css/bootstrap.css",
    "./node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.css",
    "./public/assets/styles/app.css",
    "./public/assets/css/shop-homepage.css"
  ])
  .addEntry("app", [
    "./node_modules/bootstrap/dist/js/bootstrap.bundle.js",
    "./node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.js",
    "./public/assets/app.js"
  ]);

module.exports = Encore.getWebpackConfig();
