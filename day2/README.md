## 订阅自动回复功能的实现

#### 一、存储和读取和更新access_token
* [为什么？](https://mp.weixin.qq.com/wiki/home/)

公众平台以access_token为接口调用凭据，来调用接口，所有接口的调用需要先获取access_token，access_token在2小时内有效，过期需要重新获取，但1天内获取次数有限，开发者需自行存储，详见获取接口调用凭据（access_token）文档。

* 步骤
  * 1、参考day1和公众号建立连接
  * 2、看`wechat/wechat.js` 文件中读取，更新，保存逻辑

#### 二、自动回复功能
* 1、getRawBody获取请求原生body
* 2、看`wechat/util.js` 文件中xml解析和格式化请求消息
* 3、订阅时间事件，返回回复文本消息
```
<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>12345678</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[你好]]></Content>
</xml>
```

#### 三、启动项目
* 更改`app.js`中`appID、appSecret、token`
* `npm start`
