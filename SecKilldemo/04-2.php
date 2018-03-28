<?php  
// 优化方案4：使用redis队列，因为pop操作是原子的，即使有很多用户同时到达，也是依次执行 
// 2. 抢购、描述逻辑

$dsn = 'mysql:dbname=test;host=127.0.0.1;charset=utf8';
$user = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}


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
    global $dbh;  
    $sql="insert into ih_log(event,type) values ('$event','$type')";    
    $dbh->exec($sql);  
}  
  
//模拟下单操作  
//下单前判断redis队列库存量  
$redis=new Redis();  
$result=$redis->connect('127.0.0.1',6379);  
$count=$redis->lpop('goods_store');

if(!$count){  
    insertLog('error:no store redis');  
    return;  
}  
  
//生成订单    
$order_sn=build_order_no();  

$sql="insert into ih_order(order_sn,user_id,goods_id,sku_id,price) values ('$order_sn','$user_id','$goods_id','$sku_id','$price')";    
$order_rs=$dbh->exec($sql);   
  
//库存减少  
$sql="update ih_store set number=number-{$number} where sku_id='$sku_id'";  
$store_rs=$dbh->exec($sql);    
if($store_rs){    
    insertLog('库存减少成功');  
}else{    
    insertLog('库存减少失败');  
}   
