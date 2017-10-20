<?php 
    function code(){
        require_once "phpqrcode.php";
        $object = new \QRcode();
        $key=date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $url="http://www.itkun.cn/msg&$key";//网址或者是文本内容
        $level=6;
        $size=5;
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }
 ?>