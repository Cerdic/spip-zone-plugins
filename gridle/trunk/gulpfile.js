/**
 * Déclaration des tâches nécessaire à la compilation du thème
 *
 * La commande « gulp watch » va surveiller tous les changements effectués
 * dans les dossiers /scss et lancera les tâches nécessaires.
 *
 * La tâche principale est 'gulp css'
 *
 * Liste des tâches disponibles : gulp --tasks
 *
 */

// Include dependancies
var
  gulp = require('gulp'),
  sass = require('gulp-sass'),
  postcss = require('gulp-postcss'),
  autoprefixer = require('autoprefixer'),
  minify = require('gulp-minify'),
  cleanCSS = require('gulp-clean-css'),
  rename = require('gulp-rename'),
  stripCssComments = require('gulp-strip-css-comments'),
  cssbeautify = require('gulp-cssbeautify')
;

// Surveiller le fichier source et lancer les tâches
gulp.task('watch', function () {
  gulp.watch('./scss/gridle.scss', gulp.series('css'));
});

// Compiler le SCSS
gulp.task('sass-compile', function () {
  return gulp.src('./scss/gridle.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./css'));
});

// Optimiser la CSS compilée
gulp.task('css-optimize', function () {
  return gulp.src('./css/gridle.css')
    // Enlever les commentaires
    .pipe(stripCssComments())
    // Formater le code
    .pipe(cssbeautify())
    // Ajouter les préfixes navigateurs
    /*.pipe(postcss(
      [autoprefixer()]
    ))*/
    .pipe(gulp.dest('./css'));
});

// Minifier la CSS compilée
gulp.task('css-minify', function () {
  return gulp.src('./css/gridle.css')
    .pipe(cleanCSS({
      compatibility: 'ie9'
    }))
    .pipe(rename({
      suffix: ".min"
    }))
    .pipe(gulp.dest('./css'));
});

// Traitement du CSS (compilation, minification, etc.)
gulp.task('css', gulp.series(
  'sass-compile',
  'css-optimize',
  'css-minify'
));

// Tâche par défaut
gulp.task('default', gulp.series('watch'));