<?php
/**
 * 支付宝PC支付类 (新版支付宝支付)
 *
 */
 
class alipayPC {

    //支付宝网关地址
    public $gateway_url = "";

    //支付宝公钥
    public $alipay_public_key;

    //商户私钥
    public $private_key;

    //应用id
    public $appid;

    //支付宝接口名称
    public $method = "alipay.trade.page.pay";

    //支付宝接口版本
    public $version = "1.0";

    //编码格式
    public $charset = "UTF-8";

    public $token = NULL;
    
    //返回数据格式
    public $format = "json";

    //签名方式
    public $signtype = "RSA2";

    //支付宝签约支付产品编码
    public $productCode = "FAST_INSTANT_TRADE_PAY";

    //异步回调地址
    public $notifyUrl = "";

    //同步跳转地址
    public $returnUrl = "";

    
    /**
     * 构造函数
     */
    function __construct( $alipayConfig = array() ){
        
        //检测是否修改支付网关
        if( isset($alipayConfig['gateway_url']) && !empty($alipayConfig['gateway_url']) ){
            $this->gateway_url = $alipayConfig['gateway_url'];
        }

        //检测编码格式
        if( isset($alipayConfig['charset']) && !empty($alipayConfig['charset']) ){
            $this->charset = $alipayConfig['charset'];
        }

        //检测签名方式
        if( isset($alipayConfig['sign_type']) && !empty($alipayConfig['sign_type']) ){
            $this->signtype = $alipayConfig['sign_type'];
        }

        //检测接口版本
        if( isset($alipayConfig['version']) && !empty($alipayConfig['version']) ){
            $this->signtype = $alipayConfig['version'];
        }
        
        //检验异步处理回调地址
        if( isset($alipayConfig['notify_url']) && !empty($alipayConfig['notify_url']) ){
            $this->notifyUrl = $alipayConfig['notify_url'];
        }
        
        //检测同步跳转地址
        if( isset($alipayConfig['return_url']) && !empty($alipayConfig['return_url']) ){
            $this->returnUrl = $alipayConfig['return_url'];
        }
        
        if( !isset($alipayConfig['app_id']) || empty($alipayConfig['app_id']) ){
            $this->showError(" app_id 参数不能为空");
        }

        if( !isset($alipayConfig['merchant_private_key']) || empty($alipayConfig['merchant_private_key']) ){
            $this->showError(" merchant_private_key 参数不能为空");
        }

        if( !isset($alipayConfig['alipay_public_key']) || empty($alipayConfig['alipay_public_key']) ){
            $this->showError(" alipay_public_key 参数不能为空");
        }

        // if( !isset($alipayConfig['notify_url']) || empty($alipayConfig['notify_url']) ){
        //     $this->showError(" notify_url 参数不能为空");
        // }

        // if( !isset($alipayConfig['return_url']) || empty($alipayConfig['return_url']) ){
        //     $this->showError(" return_url 参数不能为空");
        // }

        $this->appid = $alipayConfig['app_id'];

        $this->private_key = $alipayConfig['merchant_private_key'];

        $this->alipay_public_key = $alipayConfig['alipay_public_key'];
    }



    /**
     * 创建生成订单支付的URL链接
     *
     * @param     array     $orderConfig    订单配置参数
     * @return    string    $orderPayUrl    带有参数的支付链接地址
     */
    public function createOrderPayUrl( $orderConfig = array() ){

        $orderPayUrl = '';

        $payConfig = array();

        //拼装支付参数
        $payConfig = $this->createPayConfigArr($orderConfig);

        //value做urlencode
        $preString = $this->getSignContentUrlencode($payConfig);
        
        //拼接GET请求串
        $orderPayUrl = $this->gateway_url . "?" . $preString;
        
        return $orderPayUrl;
    }



    /**
     * 创建支付订单配置数组
     * 
     * @param    array    $orderConfig     订单配置参数
     * @return   array    $payConfigArr    支付配置参数
     */
    public function createPayConfigArr( $orderConfig = array() ) {

        $bizContent = '';
        
        //公共必填参数数组
        $commonParamsArr = array();

        //业务请求必填参数数组
        $requestParamsArr = array();

        //校验必填参数是否为空
        if( !isset($orderConfig['out_trade_no']) || empty($orderConfig['out_trade_no']) ){
            $this->showError('out_trade_no 参数缺失');
        }

        if( !isset($orderConfig['product_code']) || empty($orderConfig['product_code']) ){
            $orderConfig['product_code'] = $this->productCode;
        }

        if( !isset($orderConfig['total_amount']) || empty($orderConfig['total_amount']) ){
            $this->showError('total_amount 参数缺失');
        }

        if( !isset($orderConfig['subject']) || empty($orderConfig['subject']) ){
            $this->showError('subject 参数缺失');
        }
        
        //过滤数组中空数值
        $requestParamsArr = array_filter($orderConfig);
        
        //转换成json数组
        $bizContent = json_encode($requestParamsArr,JSON_UNESCAPED_UNICODE);

        $commonParamsArr['app_id'] = $this->appid;
        $commonParamsArr['method'] = $this->method;
        $commonParamsArr['format'] = $this->format;
        $commonParamsArr['return_url'] = $this->returnUrl;
        $commonParamsArr['notify_url'] = $this->notifyUrl;
        $commonParamsArr['charset'] = $this->charset;
        $commonParamsArr['sign_type'] = $this->signtype;
        $commonParamsArr['timestamp'] = date('Y-m-d H:i:s');
        $commonParamsArr['version'] = $this->version;
        $commonParamsArr['biz_content'] = $bizContent;

        $sign = '';

        $sign = $this->generateSign($commonParamsArr);
 
        $commonParamsArr['sign'] = $sign;

        return $commonParamsArr;
    }



    /**
     * 按照支付宝加密规则，获取支付宝签名
     * 
     * @param     array     $orderConfig    订单配置参数
     * @return    string    $sign           统一下单接口sign  
     */
    public function generateSign( $paramsArr = array() , $signType = 'RSA2' ){

        $signContent = $this->getSignContent($paramsArr);
        
        $sign = $this->rsaSign( $signContent , $this->private_key , $signType );

        return $sign;
    }

    
    
    /**
     * 获取按照ascii码升序排列的字符串拼接后的数组
     * 
     * @param     array     $params 数组
     * @return    string    $sin
     */
    public function getSignContent($params) {

        //数组排序
        ksort($params);

        $stringToBeSigned = "";

        $i = 0;
        
        foreach ($params as $k => $v) {
            
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                
                $i++;
            }
        }
        
        unset($k, $v);

        return $stringToBeSigned;
    }



    //此方法对value做urlencode
    public function getSignContentUrlencode($params) {
        
        ksort($params);

        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . urlencode($v);
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . urlencode($v);
                }
                $i++;
            }
        }

        unset ($k, $v);
        
        return $stringToBeSigned;
    }



    /**
     * 校验$value是否非空
     */
    public function checkEmpty($value) {

        if ( !isset($value) ){
            return true;
        }

        if ($value === null) {
            return true;
        }

        if (trim($value) === "") {
            return true;
        }
            
        return false;
    }



    /**
     * 转换字符集编码
     * 
     * @param     mixed     $data             原数据
     * @param     string    $targetCharset    转换编码后的数据
     * @return    string    $result           结果 
     */
    public function characet($data, $targetCharset) {
        if ( !empty($data) ) {
            $fileType = $this->charset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }



    /**
     * RSA签名
     * 
     * @param     string    $data           待签名数据
     * @param     string    $private_key    私钥字符串
     * @return    bool     $result         签名结果
     */
    public function rsaSign($data, $private_key , $type = 'RSA2') {

        $search = array(
            "-----BEGIN RSA PRIVATE KEY-----",
            "-----END RSA PRIVATE KEY-----",
            "\n",
            "\r",
            "\r\n"
        );

        $private_key = str_replace($search,"",$private_key);

        $private_key = $search[0] . PHP_EOL . wordwrap($private_key, 64, "\n", true) . PHP_EOL . $search[1];
        
        $res = openssl_get_privatekey($private_key);

        if($res){
            
            if($type == 'RSA'){
                openssl_sign($data, $sign,$res);
            }elseif($type == 'RSA2'){
                //OPENSSL_ALGO_SHA256
                openssl_sign($data, $sign,$res,OPENSSL_ALGO_SHA256);
            }

            openssl_free_key($res);
        }else {
            $this->showError("私钥格式有误");
        }
        
        $sign = base64_encode($sign);
        return $sign;
    }



    /**
     * RSA验签
     * 
     * @param     string    $data          待签名数据
     * @param     string    $public_key    公钥字符串
     * @param     string    $sign          要校对的的签名结果
     * @return    bool      $result        验证结果
     */
    public function rsaCheck($data, $public_key, $sign,$type = 'RSA') {

        $search = array(
            "-----BEGIN PUBLIC KEY-----",
            "-----END PUBLIC KEY-----",
            "\n",
            "\r",
            "\r\n"
        );

        $public_key = str_replace($search,"",$public_key);

        $public_key = $search[0] . PHP_EOL . wordwrap($public_key, 64, "\n", true) . PHP_EOL . $search[1];
        
        $res = openssl_get_publickey($public_key);

        if( $res ){
            if($type == 'RSA'){
                $result = (bool)openssl_verify($data, base64_decode($sign), $res);
            }elseif($type == 'RSA2'){
                $result = (bool)openssl_verify($data, base64_decode($sign), $res,OPENSSL_ALGO_SHA256);
            }
                openssl_free_key($res);
        }else{
            $this->showError("公钥格式有误!");
        }
        return $result;
    }



    /**
     * 输出错误信息
     * 
     * @param     string    $errorText    错误详细信息
     * @return    NULL
     */
    public function showError($errorText = ''){

        if( empty($errorText) ) {
            return false;
        }

        echo $errorText;
        die();
    }
}
