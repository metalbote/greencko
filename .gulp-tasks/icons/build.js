'use strict';

const runSequence = require('run-sequence');

module.exports = function (gulp, callback) {
  return runSequence(
    ['icons:monochrome', 'icons:multicolor', 'icons:png'], callback);
};
