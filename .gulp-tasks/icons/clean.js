'use strict';

const clean = require('gulp-clean');

module.exports = function (gulp, callback) {
  return gulp.src([
    './.tmp-ico',
    './ico'
  ], {read: false})
    .pipe(clean({force: true}));
};
