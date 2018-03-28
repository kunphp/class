<?php

// 优化方案4：使用redis队列，因为pop操作是原子的，即使有很多用户同时到达，也是依次执行
// 1.先将商品库存如队列
$store=500;  
$redis=new Redis();  
$result=$redis->connect('127.0.0.1',6379);  
$res=$redis->llen('goods_store');

echo $res;  
echo '<hr>';

for($i=0;$i<$store;$i++)
{  
    $redis->lpush('goods_store',1);  
}  

echo $redis->llen('goods_store');  
