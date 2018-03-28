<?php  
// 优化方案3：使用文件锁机制，（非阻塞的文件排他锁）

$conn=mysql_connect("localhost","root","");    
if(!$conn){    
    echo "connect failed";    
    exit;    
}   
mysql_select_db("test",$conn);   
mysql_query("set names utf8");  
  
$price=10;  
$user_id=1;  
$goods_id=1;  
$sku_id=11;  
$number=1;  
  
//生成唯一订单号  
function build_order_no(){  
    return date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);  
}  
//记录日志  
function insertLog($event,$type=0){  
    global $conn;  
    $sql="insert into ih_log(event,type)   
    values('$event','$type')";    
    mysql_query($sql,$conn);    
}  
  
$fp = fopen("lock.txt", "w+");  
if(!flock($fp,LOCK_EX | LOCK_NB)){  
    echo "系统繁忙，请稍后再试";  
    return;  
}  

//下单  
$sql="select number from ih_store where goods_id='$goods_id' and sku_id='$sku_id'";  
$rs=mysql_query($sql,$conn);  
$row=mysql_fetch_assoc($rs);  
if($row['number']>0){//库存是否大于0  
    //模拟下单操作   
    $order_sn=build_order_no();   
    $sql="insert into ih_order(order_sn,user_id,goods_id,sku_id,price)   
    values('$order_sn','$user_id','$goods_id','$sku_id','$price')";    
    $order_rs=mysql_query($sql,$conn);   
      
    //库存减少  
    $sql="update ih_store set number=number-{$number} where sku_id='$sku_id'";  
    $store_rs=mysql_query($sql,$conn);    
    if(mysql_affected_rows()){    
        insertLog('库存减少成功');  
        flock($fp,LOCK_UN);//释放锁  
    }else{    
        insertLog('库存减少失败');  
    }   
}else{  
    insertLog('库存不够');  
}

  
fclose($fp); 
