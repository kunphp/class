<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2017061707511695",

		//商户私钥
		'merchant_private_key' => "MIIEogIBAAKCAQEAzsa9i2NXiT8VwhDXFbjBoO6j+LZsOnFtrc+wIqAk1MK74HADabDeuySRsFSdnqkY57mEREHgK1VsCyG/eYO0qyyYPofzisSLR5QeXejKeCBshYZR+aK+si/lryORil8V7niGUmuInYWx1i6Gp5UguiJzWFolQ30u/qRGWVq1uhbd1G59Mhxp95ppDgNaWecOcQYa9/7qort69FtBNLjOwjgKpz4RU2p+9rj0GsHsdcxOOAooJDmC0Ekjkkog7x32e/luMVQRk90QXFgm5/x8Icjci6FFYy+aLr0bIAG97zCQ8gzyt2bJJQiS90sAOn3xbvf1uqmTO+LkX3gGtUmnzQIDAQABAoIBADBTqLcsJ6hPEzHBJ9PO04peW/pkAFWEbyLhWIQvM4x6WtiwtUt9aAELIfW5QQF0+fomlLAzSUY3d8H+SDcJPi+Hg6mRsqzrFLZM+u/t2WxL/7ERXJVgoPsaUK8nO/vAD2slxhb7RPmz5oVtaFFAfF6kcJrkrrLgThx7nmmB1tk/1Un8dYV/Vs5YSdeLd/oIBgpyNws63wfp++ClX6fqBOUT8hWbMDu8Z7y9yCuvLIfkIDAojtqJR15gY6lmBODr8Rh0bjaX2RM47W5Gn4dDf8MLjzi0aTxP0A7tHVEdBepMGeloNRthsWo4UdqeTJcB2HUxIQNhRo4IEvw9LFlt3IECgYEA7kn3TpMt/tU+2YTGIsMAiysHZAuADmrJRZE3GdMPSPoNHt1ue7TIgWCwR66gse/14pxucRKi5ocRRPVbxZ4FEq8KAJEyQ77RBsjYfByCfqQZmg8mPZ+oOrKRBBPpNNyhKJSlU16zUUkPOmKu4xtiajNDJkeYpsmwedXekXNRa20CgYEA3iUrZRLxFmtCmrhdqnxxasfFS67OvwYSq2g8ENSKCaH65ow74+xoEg6/GjSNQsLuVgJiL+wQfJHTVmbilfcdb4S1J2lx0R+jnhaqPVcd8uKyA1JaI6V+cRr7CC9n22XK/Na1g+jsv6Uf+DY1rGrlUZlgGmacGP+/2Fs70cX4EeECgYBFwW9dGCbBz8kbQgwChxU0qD78oYU2MzqCW/VGYQu7cD/BBk9edViwzw4rnco67KUNVn/aJ/t6ApYimrTnz89qXcVOzzJotIZaNxdwlaP50K4R9FdceM11iWp8SBvftqqSx5jyI+nYZMvJarQJla2kqycAILtmL+qyb9I8wAZYrQKBgEkhhWqt1K5juzaCMVu5wgC8KWhj8O4UCrthAj0sKxFGwPl6+xBquEwRjLoMyOBMmfe4qyhJl5faze8oyblC8+7NinGiFfUZbFzT61FsR5C5Lo9HYiNQDRMhJbkpGhCxLyUTbggx+xaQPrkQ55SOUFYPnwKx+vuD3l1Uk5nYiXUhAoGAW1J6QJK5jt1qNd2T/QrMrzfhQc8C5L/1/w2vsM30G4yuz3E+3n/DHG6U0XZxPX++y0MSSjKVxhSaHLLW31QZa/S1u2+tI9pGpBeTB9+/JBQxcXNDdey8K/WlHz0cgxmh6MSXbEHQy0eL3B1HNq6VC16ZvfQi3/gBjJU/glBsA3M=",
		
		//异步通知地址
		'notify_url' => "http://www.erdangjiade.com/notify_url.php",
		
		//同步跳转
		'return_url' => "http://www.test.com/Application/Lib/Alipay/return_url.php",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiqhJU5tgOd0oN0gY4KS6y9TjMYkDlnnnup4mFu1zRn6qJDL4jjfT3S9IyPSoQErud84YxgtIBmA21mogni9Z1Kb5OVJTZvbXU3iEaL81wjLIbli3hZHtB2RXxsjAl3rZG3f5TVx/XYIOtKvpzNmVUOEwdFPQEv+2M8y+H2MqjzGFAlyVdq1LxI94KXMsypcClnuHKiPiAg6eRmCo6p7POJ5IJ65yoA68ff1/qT5l56eu+84k7OrA/cB5DImJec1fHVuq8TUpvEP14rzQHrE/hQxOF/JfvOf2xD0n23hwOESsf2/Abz6U/Ul5LYQ/4Kf7cCTAzy81moOrbl3OdQ7CsQIDAQAB",
);