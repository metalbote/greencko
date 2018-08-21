'use strict';

module.exports = function (gulp, callback) {
  return gulp.watch(["assets/ico/**/*.{png,svg}"], [
    'icons:build'
  ]);
};
