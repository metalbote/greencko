'use strict';

const imagemin = require('gulp-imagemin');


module.exports = function (gulp, callback) {

  return gulp.src(["assets/ico/multicolor/**/*.svg"])
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
    .pipe(gulp.dest('ico'));
};
