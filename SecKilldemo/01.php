<?php
//方案一：库存减少（将库存number设置为无符号）

error_reporting(E_ALL ^E_DEPRECATED);

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
  
//生成唯一订单  
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
  
//模拟下单操作  
//库存是否大于0  
$sql="select number from ih_store where goods_id='$goods_id' and sku_id='$sku_id'";
$rs=mysql_query($sql,$conn);  
$row=mysql_fetch_assoc($rs);  
//高并发下会导致超卖  
if($row['number']>0)
{
    $order_sn=build_order_no();  
    //生成订单    
    $sql="insert into ih_order(order_sn,user_id,goods_id,sku_id,price) values ('$order_sn','$user_id','$goods_id','$sku_id','$price')";    

    $order_rs=mysql_query($sql,$conn);   
      
    //库存减少  

    $sql="update ih_store set number=number-{$number} where sku_id='$sku_id' and number>0";  

    $store_rs=mysql_query($sql,$conn);    
    if(mysql_affected_rows())
    {    
        insertLog('库存减少成功');  
    }
    else
    {    
        insertLog('库存减少失败');  
    }   
}
else
{  
    insertLog('库存不够');  
}  
?>  
