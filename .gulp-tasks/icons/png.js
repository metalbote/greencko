'use strict';

const rename = require('gulp-rename'),
  imagemin = require('gulp-imagemin');


module.exports = function (gulp, callback) {

  return gulp.src(["assets/ico/png/**/*.png"])
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
    .pipe(gulp.dest("ico/png"))
};
