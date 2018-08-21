'use strict';

module.exports = function (gulp, callback) {
  return gulp.watch(
    ["assets/img/**/*.{png,gif,jpg,jpeg,svg}"], [
      'images:build'
    ]);
};
