'use strict'

var sha1 = require('sha1');
var getRawBody = require('raw-body');
var Wechat = require('./wechat');
var util = require('./util');

module.exports = function(opts) {

  var wechat = new Wechat(opts);

  return function *(next) {
    var that = this;
    // console.log(this.query);

    var token = opts.token;
    var signature = this.query.signature;
    var nonce = this.query.nonce;
    var timestamp = this.query.timestamp;
    var echostr = this.query.echostr;

    var str = [token, timestamp, nonce].sort().join('');
    var sha = sha1(str);

    if (this.method === 'GET') {
      if (sha === signature) {
        this.body = echostr + '';
      } else {
        this.body = 'wrong';
      }
    } else if (this.method === 'POST') {
      if (sha !== signature) {
        this.body = 'wrong';
        return false;
      }
      // 通过raw-body模块,可以把这个this上request对象。也就是http模块中的request对象，
      // 去拼装它的数据，最终可以拿到一个buffer的xml数据
      var data = yield getRawBody(this.req, {
        length: this.length,
        limit: '1mb',
        encoding: this.charset
      })
      // console.log('req data', data.toString());
      var content = yield util.parseXMLAsync(data); // 调用异步解析数据

      // console.log('content', content);

      var message = util.formatMessage(content.xml);

      if (message.MsgType === 'event') {
        if (message.Event === 'subscribe') { // 订阅事件
          var now = new Date().getTime();
          that.status = 200;
          that.type = 'application/xml';
          that.body = '<xml>' +
                        '<ToUserName><![CDATA['+ message.FromUserName + ']]></ToUserName>' +
                        '<FromUserName><![CDATA['+ message.ToUserName + ']]></FromUserName>' +
                        '<CreateTime>'+ now +'</CreateTime>' +
                        '<MsgType><![CDATA[text]]></MsgType>' +
                        '<Content><![CDATA[你好, 谢谢关注我们]]></Content>' +
                      '</xml>';
        }
      }
    }
  }
};
