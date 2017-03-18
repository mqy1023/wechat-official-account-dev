'use strict'

var fs = require('fs');
var Promise = require('bluebird');

exports.readFileAsync = function(fPath, encoding) {
  return new Promise(function(resolve, reject) {
    fs.readFile(fPath, encoding, function(err, content) {
      if (err) {
        reject(err);
      } else {
        resolve(content);
      }
    })
  });
}

exports.writeFileAsync = function(fPath, content) {
  return new Promise(function(resolve, reject) {
    fs.writeFile(fPath, content, function(err) {
      if (err) {
        reject(err);
      } else {
        resolve();
      }
    })
  });
}
