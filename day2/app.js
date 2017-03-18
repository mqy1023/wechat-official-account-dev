'use strict'

var wechatInit = require('./wechat/g');
var koa = require('koa');
var path = require('path');
var util = require('./lib/util');
var wechat_file = path.join(__dirname, './config/wechat.txt'); // access_token存储在文本中

var config = {
  wechat: {
    appID: 'xxxxxxxxx',
    appSecret: 'xxxxxxxxxxxxxxxxx',
    token: 'weixin',
    getAccessToken: function() {
      console.log('hahahah1111')
      return util.readFileAsync(wechat_file);
    },
    saveAccessToken: function(data) {
      console.log('hahahah2222')
      data = JSON.stringify(data);
      return util.writeFileAsync(wechat_file, data);
    }
  }
};

var app = new koa();

app.use(wechatInit(config.wechat));

app.listen(1234);

console.log('Listen: 1234');
