<?php
/**
 * wechat php test
 */
include_once ('./IndexModel.php');

//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();
//$wechatObj->http_curl();
//$wechatObj->getWxAccessToken();


class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"]; // 配置环境才会返回该随机数

        //valid signature , option
        if ($this->checkSignature() && $echoStr) { // 环境搭建
            echo $echoStr;
            exit;
        } else {
//            $this->responseMsg();
            $this->responseMessage();
        }
    }

    public function responseMessage() {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $indexModel = new IndexModel();

        //extract post data
        if (!empty($postStr)) {

            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

            $indexModel->responseSubscribe($postObj);
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)) {
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $msgType = "text";
            $contentStr = '';

            if (strtolower($postObj->MsgType) === 'event' && strtolower($postObj->Event) === 'subscribe') {
                $contentStr = "<a href='http://baidu.com'>谢谢关注我们!请点击登录公司系统</a>";
            } else if( strtolower($postObj->MsgType) === 'text' && $keyword === 'tuwen' ){ // 多图文自动回复

                $msgType = "news";

                $arr = array(
                    array(
                        'title'=>'imooc',
                        'description'=>"imooc is very cool",
                        'picUrl'=>'http://www.imooc.com/static/img/common/logo.png',
                        'url'=>'http://www.imooc.com',
                    ),
                    array(
                        'title'=>'hao123',
                        'description'=>"hao123 is very cool",
                        'picUrl'=>'https://www.baidu.com/img/bdlogo.png',
                        'url'=>'http://www.hao123.com',
                    ),
                    array(
                        'title'=>'qq',
                        'description'=>"qq is very cool",
                        'picUrl'=>'http://www.imooc.com/static/img/common/logo.png',
                        'url'=>'http://www.qq.com',
                    ),
                );
                $template = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<ArticleCount>".count($arr)."</ArticleCount>
						<Articles>";
                foreach($arr as $k=>$v){
                    $template .="<item>
							<Title><![CDATA[".$v['title']."]]></Title>
							<Description><![CDATA[".$v['description']."]]></Description>
							<PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
							<Url><![CDATA[".$v['url']."]]></Url>
							</item>";
                }

                $template .="</Articles>
						</xml> ";
                echo sprintf($template, $fromUsername, $toUsername, $time, $msgType);
                exit;

                //注意：进行多图文发送时，子图文个数不能超过10个
            } else if (strtolower($postObj->MsgType) === 'text') {
                $contentStr = "你输入了文字，谢谢!";
            }

            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
//            if (!empty($keyword)) {

            echo sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
//            } else {
//                echo "Input something...";
//            }

        }
        echo "error";
        exit;

    }

    private function checkSignature() // 配置微信公众号环境
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function http_curl() { // 强大的采集工具
        // 获取imooc
        // 1.初始化curl
        $ch = curl_init();
        $url = 'http://www.baidu.com';
        // 2.设置curl的参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 3.采集
        $output = curl_exec($ch);
        // 4.关闭
        curl_close($ch);
        var_dump($output);
    }

    public function getWxAccessToken() { // 获取token
        // 1.请求url地址
        $appid = 'wxed1111111111b'; // 请输入您的appid
        $appsecret =  '0211111f11111115dad'; // 请输入您的appsecret
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        // 2.初始化
        $ch = curl_init();
        // 3.设置参数
        curl_setopt($ch , CURLOPT_URL, $url);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
        // 4.调用接口
        $res = curl_exec($ch);
        // 5.关闭curl
        curl_close( $ch );
        if( curl_errno($ch) ){ // 错误处理
            var_dump( curl_error($ch) );
        }
        $arr = json_decode($res, true); // json转数组
        var_dump( $arr );
    }

    public function getWxServerIp() { // 获取服务器地址
        // 填写上面getWxAccessToken() 获取token
        $accessToken = "6vOl11111111111111111111116kRfo";
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$accessToken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        $res = curl_exec($ch);

        curl_close($ch);
        if(curl_errno($ch)){
            var_dump(curl_error($ch));
        }
        $arr = json_decode($res,true);
        echo "<pre>";
        var_dump( $arr );
        echo "</pre>";


    }
}

?>
