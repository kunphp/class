<?php
/**
 * 支付宝支付测试文件
 *
 */
require_once "./alipay.php";

require_once "./config.php";

//订单数组配置参数
$orderConfig = array();

//订单编号
$orderConfig['out_trade_no'] = "O".date('YmdHis').rand(111,999);

//订单金额
$orderConfig['total_amount'] = "0.01";

//订单标题
$orderConfig['subject'] = "测试";

//订单描述
$orderConfig['body'] = "支付测试";

//订单附加参数
$orderConfig['passback_params'] = "test";

//订单过期时间
$orderConfig['timeout_express'] = "45m";

//其他的可以参考支付宝文档 https://docs.open.alipay.com/270/alipay.trade.page.pay

$alipay = new alipayPC($pcPayConfig);

$payUrl = $alipay->createOrderPayUrl($orderConfig);


echo "<a href=".$payUrl.">点击支付</a>";



