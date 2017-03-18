## 本地资源服务器外网化

#### 一、让本地资源服务器化
两种方式如下：
* 1、MAMP或其他集成服务器环境启动
* 2、启动`node app.js`
```
'use strict'

var koa = require('koa');
var sha1 = require('sha1');

var config = {
  wechat: {
    appID: 'wxxxxxxxb',
    appSecret: 'xxxxxxxx2f6f4xxxxxx5dadxxxx',
    token: 'weixin'
  }
};

var app = new koa();

app.use(function *(next) {
  console.log(this.query);

  var token = config.wechat.token;
  var signature = this.query.signature;
  var nonce = this.query.nonce;
  var timestamp = this.query.timestamp;
  var echostr = this.query.echostr;

  var str = [token, timestamp, nonce].sort().join('');
  var sha = sha1(str);

  // this.body = sha + ';' + signature;
  if (sha === signature) {
    this.body = echostr + '';
  } else {
    this.body = 'wrong';
  }
})

app.listen(1234);

console.log('Listen: 1234');

```

#### 二、本地服务器外网化
参考《[微信开发之Ngrok环境准备](http://www.cnblogs.com/MuYunyun/p/5874924.html)》
* 1、下载外网管理内网服务器，[下载地址](https://www.ngrok.cc/#down-client)
* 2、访问ngrok授权管理系统（http://www.ngrok.cc/login），点击注册，注册帐号
* 3、登录系统后开通隧道，填写端口等和本地服务器参数保持一致
* 4、启动本地服务器，MAMP启动或者`node app.js` node启动
* 5、启动ngrok服务：id号就是隧道管理上的客户端id，`./sunny clientid 0bxxxxs5xxxx`
* 6、外网访问，http://xxxx.ngrok.cc 。xxxx是隧道设置中的前缀域名

#### 三、配置公众号
点击开发栏下的'基本配置'
![这里写图片描述](http://img.blog.csdn.net/20170318105625085?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvbXF5MTAyMw==/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/SouthEast)


其他方案1: http://www.ultrahook.com/

## [原文连接](http://blog.csdn.net/mqy1023/article/details/63251663)
