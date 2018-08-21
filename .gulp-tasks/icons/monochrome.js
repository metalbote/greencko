'use strict';

const Color = require("color"),
  gulp = require("gulp"),
  imagemin = require('gulp-imagemin'),
  RecolorSvg = require("gulp-recolor-svg"),
  fs = require('fs');

module.exports = function (gulp, callback) {
  if (!fs.existsSync('./ico')) {
    fs.mkdirSync('./ico');
  }
  return gulp.src(["assets/ico/monochrome/**/*.svg"])
    .pipe(imagemin({
      progressive: true,
      interlaced: true,
      svgoPlugins: [{
        cleanupIDs: false,
        removeViewBox: false,
        removeUselessStrokeAndFill: false,
        removeXMLProcInst: false
      }]
    }))
    .pipe(gulp.dest('./.tmp-ico'))
    .pipe(
      RecolorSvg.GenerateVariants(
        [RecolorSvg.ColorMatcher(Color("#000000"))],
        [
          {suffix: "--white", colors: [Color("#ffffff")]},
          {suffix: "--black", colors: [Color("#000000")]}
        ]
      )
    )
    .pipe(gulp.dest('ico'));
};
