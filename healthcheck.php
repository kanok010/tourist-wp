<?php
//error_reporting(0);
//libxml_use_internal_errors(true);

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include dirname(__FILE__)."/gearmandAdmin.php";
define('CONFIG_CURL_CONNECT_TIMEOUT',30);
define('CONFIG_CURL_TIMEOUT',30);

$SERVICE_NAME = "Tourist Application (Content) (http://touristapplication.truecorp.co.th/content)";
$MODULE_NAME = "Tourist Application (Content)";

$CURRENT_URL = "http://touristapplication.truecorp.co.th/content";


// --------------------------------- Database ------------------------------------
$DB_ENABLE = true;
$DB_HOST = "TouristAppDB.ppm.in.th";
$DB_PORT = "3306";
$DB_USER = "touristct_rw";
$DB_PASS = "Ltq4542tiY";
$DB_NAME = "touristapp_content";

// MYSQL TYPE = mysql/mysqli
$DB_MYSQL_TYPE = "mysqli";

$PARAM1 = "DB_check";
// --------------------------------- Database ------------------------------------

// --------------------------------- Memcache ------------------------------------
$MEMCACHE_ENABLE = true;
$MEMCACHE_HOST = "TouristAppMem_content.ppm.in.th";
$MEMCACHE_PORT = "11213";

// MEMCACHE TYPE = memcache/memcached
$MEMCACHE_TYPE = "memcache";

$PARAM2 = "Memcache_check";
// --------------------------------- Memcahce ------------------------------------

// --------------------------------- Redis ------------------------------------
$REDIS_ENABLE = false;
$REDIS_HOST = "rediscounter.ppm.in.th";
$REDIS_PORT = 6380;
//$REDIS_AUTH = "lamok$$$";

$PARAM3 = "Redis_check";
// --------------------------------- Redis ------------------------------------

// --------------------------------- API CALL ------------------------------------
$API_ENABLE = false;
$API_LIST = array(
		array(	'name' => 'TrueYou API', 'url' => 'http://api.platform.truelife.com/cms/content/true_u?condition=categories=4626', 'type' => '')
);

$PARAM4 = "API_check";
// --------------------------------- API CALL ------------------------------------

// --------------------------------- Gearman ------------------------------------
$GEARMAN_ENABLE = false;
$GEARMAN_HOST="TouristGearmand.ppm.in.th";
$GEARMAN_PORT="4731";
$GEARMAN_MAX_QUEUE = 100;
$GEARMAN_FUNC_LIST = array(
	array(
		'name' => 'Test Queue', 'func' => 'testqueue'
	)
);

$PARAM5 = "Gearman_check";
// --------------------------------- Gearman ------------------------------------

if(isset($_GET['param']))
{
	if($_GET['param']==$PARAM1)
	{
		if($DB_ENABLE)
		{
			if($DB_MYSQL_TYPE == "mysql")
			{
				$result = check_connect_mysql($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME,$DB_PORT);
			}
			else
			{
				$result = check_connect_mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME,$DB_PORT);
			}
			echo (isset($result['response_time'])?$result['response_time']:'ERROR');
		}
		else
		{
			echo "Incorrect.";
		}
	}
	else
	{
		if($_GET['param']==$PARAM2)
		{
			if($MEMCACHE_ENABLE)
			{
				if($MEMCACHE_TYPE == 'memcache')
				{
					$result = check_connect_memcache($MEMCACHE_HOST, $MEMCACHE_PORT);
				}
				else
				{
					$result = check_connect_memcached($MEMCACHE_HOST, $MEMCACHE_PORT);
				}
				echo (isset($result['response_time'])?$result['response_time']:'ERROR');
			}
			else
			{
				echo "Incorrect.";
			}
		}
		else
		{
			if($_GET['param']==$PARAM3)
			{
				if($REDIS_ENABLE)
				{
					if(isset($REDIS_AUTH))
					{
						$result = check_connect_redis($REDIS_HOST,$REDIS_PORT,$REDIS_AUTH);
					}
					else
					{
						$result = check_connect_redis($REDIS_HOST,$REDIS_PORT);
					}
					echo (isset($result['response_time'])?$result['response_time']:'ERROR');
				}
				else
				{
					echo "Incorrect.";
				}
			}
			else
			{
				if(strpos($_GET['param'],$PARAM4)!==false)
				{
					if($API_ENABLE)
					{
						$cur_index = str_replace($PARAM4."_","",$_GET['param']);

						if($API_LIST[$cur_index]['type'] != '')
						{
							$result = check_api_call($API_LIST[$cur_index]['url'],$API_LIST[$cur_index]['type']);
						}
						else
						{
							$result = check_api_call($API_LIST[$cur_index]['url']);
						}
						echo (isset($result['response_time'])?$result['response_time']:'ERROR');
					}
					else
					{
						echo "Incorrect.";
					}
				}
				else
				{
					if(strpos($_GET['param'],$PARAM5)!==false)
					{
						if($GEARMAN_ENABLE)
						{
							$cur_index = str_replace($PARAM5."_","",$_GET['param']);

							$result = check_connect_gearman($GEARMAN_HOST, $GEARMAN_PORT, $GEARMAN_FUNC_LIST[$cur_index]['func']);
							echo (isset($result['response_time'])?$result['response_time']:'ERROR');
						}
						else
						{
							echo "Incorrect.";
						}
					}
					else
					{
						echo "Incorrect.";
					}
				}
			}
		}
	}

}
else
{
	$ALL_STATUS = true;
	if($DB_ENABLE)
	{
		if($DB_MYSQL_TYPE == "mysql")
		{
			$result1 = check_connect_mysql($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME,$DB_PORT);
		}
		else
		{
			$result1 = check_connect_mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME,$DB_PORT);
		}
		if($result1['code'] != 0) $ALL_STATUS = false;
	}

	if($MEMCACHE_ENABLE)
	{
		if($MEMCACHE_TYPE == 'memcache')
		{
			$result2 = check_connect_memcache($MEMCACHE_HOST, $MEMCACHE_PORT);
		}
		else
		{
			$result2 = check_connect_memcached($MEMCACHE_HOST, $MEMCACHE_PORT);
		}
		if($result2['code'] != 0) $ALL_STATUS = false;
	}

	if($REDIS_ENABLE)
	{
		if(isset($REDIS_AUTH))
		{
			$result3 = check_connect_redis($REDIS_HOST,$REDIS_PORT,$REDIS_AUTH);
		}
		else
		{
			$result3 = check_connect_redis($REDIS_HOST,$REDIS_PORT);
		}
		if($result3['code'] != 0) $ALL_STATUS = false;
	}

	if($API_ENABLE)
	{
		$result4=array();
		foreach($API_LIST as $API_ITEM)
		{
			if($API_ITEM['type'] != '')
			{
				$cur_result = check_api_call($API_ITEM['url'],$API_ITEM['type']);
			}
			else
			{
				$cur_result = check_api_call($API_ITEM['url']);
			}
			$result4[] = $cur_result;
			if($cur_result['code'] != 0) $ALL_STATUS = false;
		}
	}

	if($GEARMAN_ENABLE)
	{
		$result5=array();
		foreach($GEARMAN_FUNC_LIST as $GEARMAN_FUNC_ITEM)
		{
			$cur_result = check_connect_gearman($GEARMAN_HOST, $GEARMAN_PORT, $GEARMAN_FUNC_ITEM['func']);
			$result5[] = $cur_result;
			if($cur_result['code'] != 0) {
				$ALL_STATUS = false;
			}
			else {
				// Worker == 0
				if($cur_result['gm_info']['workers'] == 0)
				{
					$ALL_STATUS = false;
				}

				// Queue > MAX Queue
				if($cur_result['gm_info']['total'] > $GEARMAN_MAX_QUEUE)
				{
					$ALL_STATUS = false;
				}
			}
		}
	}

	$index_list = 1;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HealthCheck</title>
</head>

<body>
<p> <strong>Service Name :</strong> <?php echo $SERVICE_NAME; ?> </p>
<p> <strong>Module Name :</strong> <?php echo $MODULE_NAME; ?> </p>
<p> <strong>Server : </strong> <?php echo $_SERVER["SERVER_NAME"] . " (IP : " . $_SERVER["SERVER_ADDR"] ." Port : ".$_SERVER["SERVER_PORT"].")"; ?> </p>
<p> <strong>Host : </strong> <?php echo $_SERVER["HTTP_HOST"]; ?> </p>
<p> <strong>Remote IP : </strong> <?php echo $_SERVER["REMOTE_ADDR"]; ?> </p>
<?php if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) { ?><p> <strong>FORWARDED FOR : </strong> <?php echo $_SERVER["HTTP_X_FORWARDED_FOR"]; ?> </p><?php } ?>
<?php if(isset($_SERVER["HTTP_CLIENT_IP"])) { ?><p> <strong>CLIENT IP : </strong> <?php echo $_SERVER["HTTP_CLIENT_IP"]; ?> </p><?php } ?>
<?php if(isset($_SERVER["HTTP_VIA"])) { ?><p> <strong>HTTP VIA : </strong> <?php echo $_SERVER["HTTP_VIA"]; ?> </p><?php } ?>
<p> <strong>Param Link :</strong>
<?php if($DB_ENABLE) { ?> <a href="healthcheck.php?param=<?php echo $PARAM1; ?>" target="_blank"><?php echo $PARAM1; ?></a><?php } ?>
<?php if($MEMCACHE_ENABLE) { ?> <a href="healthcheck.php?param=<?php echo $PARAM2; ?>" target="_blank"><?php echo $PARAM2; ?></a><?php } ?>
<?php if($REDIS_ENABLE) { ?> <a href="healthcheck.php?param=<?php echo $PARAM3; ?>" target="_blank"><?php echo $PARAM3; ?></a><?php } ?>
<?php if($API_ENABLE) { $count_list=0; foreach($API_LIST as $API_ITEM) { ?> <a href="healthcheck.php?param=<?php echo $PARAM4.'_'.$count_list; ?>" target="_blank"><?php echo $PARAM4.'_'.$count_list; ?></a><?php $count_list++; } } ?>
<?php if($GEARMAN_ENABLE) { $count_list=0; foreach($GEARMAN_FUNC_LIST as $GEARMAN_FUNC_ITEM) { ?> <a href="healthcheck.php?param=<?php echo $PARAM5.'_'.$count_list; ?>" target="_blank"><?php echo $PARAM5.'_'.$count_list; ?></a><?php $count_list++; } } ?>
<br/>
<p><strong>Internal Components/Service</strong></p>
<table width="100%" border="0">
  <tr bgcolor="#BDBDBD">
    <td style="text-align:center; width:5%;"><strong>NO</strong></td>
    <td style="text-align:center; width:20%;"><strong>Description</strong></td>
    <td style="text-align:center; width:7%;"><strong>Type</strong></td>
    <td style="text-align:center; width:15%;"><strong>Param</strong></td>
    <td style="text-align:center; width:5%;"><strong>Status</strong></td>
    <td style="text-align:center; width:8%;"><strong>Response Time</strong></td>
    <td style="text-align:center; width:40%;"><strong></strong></td>
  </tr>
  <?php if($DB_ENABLE) { ?>
    <tr bgcolor="#CED8F6">
  	<td style="text-align:center;"><?php echo $index_list; $index_list++; ?></td>
    <td>MYSQL DB<br/>IP: <?php echo $DB_HOST; ?><br/>Database Name: <?php echo $DB_NAME; ?><br/><?php echo gethostname(); ?></td>
    <td style="text-align:center;"><?php echo $DB_MYSQL_TYPE; ?></td>
    <td style="text-align:center;"><?php echo $PARAM1; ?></td>
    <td style="text-align:center;"><?php echo $result1['status']; ?></td>
    <td style="text-align:center;"><?php echo $result1['response_time']; ?></td>
    <td style="text-align:center;"><?php echo (($result1['code']==1)?"(".$result1['error_code'] . ") " . $result1['error_msg']:''); ?></td>
  </tr>
  <?php } ?>
  <?php if($MEMCACHE_ENABLE) { ?>
    <tr bgcolor="#CED8F6">
  	<td style="text-align:center;"><?php echo $index_list; $index_list++; ?></td>
    <td>MEMCACHE<br/>IP: <?php echo $MEMCACHE_HOST; ?> PORT: <?php echo $MEMCACHE_PORT; ?></td>
    <td style="text-align:center;"><?php echo $MEMCACHE_TYPE; ?></td>
    <td style="text-align:center;"><?php echo $PARAM2; ?></td>
    <td style="text-align:center;"><?php echo $result2['status']; ?></td>
    <td style="text-align:center;"><?php echo $result2['response_time']; ?></td>
    <td style="text-align:center;"><?php echo (($result2['code']==1)?"(".$result2['error_code'] . ") " . $result2['error_msg']:''); ?></td>
  </tr>
  <?php } ?>
	<?php if($REDIS_ENABLE) { ?>
    <tr bgcolor="#CED8F6">
  	<td style="text-align:center;"><?php echo $index_list; $index_list++; ?></td>
    <td>REDIS<br/>IP: <?php echo $REDIS_HOST; ?> PORT: <?php echo $REDIS_PORT; ?><?php if(isset($REDIS_AUTH)) { echo " HAVE AUTH KEY"; } ?></td>
    <td style="text-align:center;">Redis</td>
    <td style="text-align:center;"><?php echo $PARAM3; ?></td>
    <td style="text-align:center;"><?php echo $result3['status']; ?></td>
    <td style="text-align:center;"><?php echo $result3['response_time']; ?></td>
    <td style="text-align:center;"><?php echo (($result3['code']==1)?"(".$result3['error_code'] . ") " . $result3['error_msg']:''); ?></td>
  </tr>
  <?php } ?>
	<?php if($API_ENABLE) {
		$counter=0;
		foreach($API_LIST as $cur_item)
		{ ?>

    <tr bgcolor="#CED8F6">
  	<td style="text-align:center;"><?php echo $index_list; $index_list++; ?></td>
    <td>API: <?php echo $cur_item['name']; ?> (<?php echo $cur_item['url']; ?>)<?php if($cur_item=='json') { echo " Format: JSON"; } ?></td>
    <td style="text-align:center;">API</td>
    <td style="text-align:center;"><?php echo $PARAM4."_".$counter; ?></td>
    <td style="text-align:center;"><?php echo $result4[$counter]['status']; ?></td>
    <td style="text-align:center;"><?php echo $result4[$counter]['response_time']; ?></td>
    <td style="text-align:center;"><?php echo (($result4[$counter]['code']==1)?"(".$result4[$counter]['error_code'] . ") " . $result4[$counter]['error_msg']:''); ?></td>
  </tr>
  <?php
			$counter++;
		}
	} ?>

	<?php if($GEARMAN_ENABLE) {
		$counter=0;
		foreach($GEARMAN_FUNC_LIST as $cur_item)
		{ ?>

    <tr bgcolor="#CED8F6">
  	<td style="text-align:center;"><?php echo $index_list; $index_list++; ?></td>
    <td>GEARMAN: <br/>IP: <?php echo $GEARMAN_HOST; ?> PORT: <?php echo $GEARMAN_PORT; ?><br/>Function: <?php echo $cur_item['name']; ?></td>
    <td style="text-align:center;">GEARMAN</td>
    <td style="text-align:center;"><?php echo $PARAM5."_".$counter; ?></td>
    <td style="text-align:center;"><?php echo $result5[$counter]['status']; ?></td>
    <td style="text-align:center;"><?php echo $result5[$counter]['response_time']; ?></td>
    <td style="text-align:center;"><?php echo (($result5[$counter]['code']==0)?"WORKER: ".$result5[$counter]['gm_info']['workers'] .(($result5[$counter]['gm_info']['workers']==0)?" <font color=\"#FF0000\">(MISSING)</font> ":""). " | QUEUE: " . $result5[$counter]['gm_info']['total'].(($result5[$counter]['gm_info']['total']>$GEARMAN_MAX_QUEUE)?" <font color=\"#FF0000\">(WARNING)</font> ":""):''); ?><?php echo (($result5[$counter]['code']==1)?"(".$result5[$counter]['error_code'] . ") " . $result5[$counter]['error_msg']:''); ?></td>
  </tr>
  <?php
			$counter++;
		}
	} ?>
  </table>
<br/>
<p><strong><?php echo (($ALL_STATUS==true)?"THIS_PAGE_IS_COMPLETELY_LOADED":""); ?><br/></strong></p>
</body>
</html><?php
}

function check_connect_mysql($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME)
{
	$result['code'] = 0;
	$result['status'] = 'OK';
	$result['error_code'] = 0;
	$result['error_msg'] = '';

	$time_start = microtime(true);
	$conn = mysql_connect($DB_HOST,$DB_USER,$DB_PASS);
	if(!$conn)
	{
		$result['code'] = 1;
		$result['status'] = 'ERROR';
		$result['error_code'] = mysql_errno($conn);
		$result['error_msg'] = mysql_error($conn);
	}
	else
	{
		$select_db= mysql_select_db($DB_NAME, $conn);
		if(!$select_db)
		{
			$result['code'] = 1;
			$result['status'] = 'ERROR';
			$result['error_code'] = mysql_errno($conn);
			$result['error_msg'] = mysql_error($conn);
		}
		else
		{
			$result['code'] = 0;
			$result['status'] = 'OK';
			$result['error_code'] = 0;
			$result['error_msg'] = '';
		}
		mysql_close($conn);
	}
	$time_end = microtime(true);
	$response_time = $time_end - $time_start;
	$result['response_time'] = $response_time;
	return $result;
}

function check_connect_mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME,$DB_PORT)
{
	$result['code'] = 0;
	$result['status'] = 'OK';
	$result['error_code'] = 0;
	$result['error_msg'] = '';

	$time_start = microtime(true);
	$conn = mysqli_connect($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME,$DB_PORT);
	if(!$conn)
	{
		$result['code'] = 1;
		$result['status'] = 'ERROR';
		$result['error_code'] = mysqli_connect_errno();
		$result['error_msg'] = mysqli_connect_error();
	}
	else
	{
		$result['code'] = 0;
		$result['status'] = 'OK';
		$result['error_code'] = 0;
		$result['error_msg'] = '';
		mysqli_close($conn);
	}
	$time_end = microtime(true);
	$response_time = $time_end - $time_start;
	$result['response_time'] = $response_time;
	return $result;
}

function check_connect_memcache($server, $port)
{
	$time_start = microtime(true);
	$result['code'] = 0;
	$result['status'] = 'OK';
	$result['error_code'] = 0;
	$result['error_msg'] = '';
	$memcache = memcache_connect($server, $port);
	if($memcache === false)
	{
		$result['code'] = 1;
		$result['status'] = 'ERROR';
		$result['error_code'] = 404;
		$result['error_msg'] = 'Cannot connect to memcache server.';
	}
	else
	{
		// Set Key 'memcache_test' with Value 'TEST MESSAGE' (not compression) expire in 30 seconds
		if(memcache_set($memcache, 'memcachetest', 'TEST MESSAGE', 0, 30)==false)
		{
			$result['code'] = 1;
			$result['status'] = 'ERROR';
			$result['error_code'] = 500;
			$result['error_msg'] = 'Cannot set key and value to memcache server.';
		}
		else
		{
			if(memcache_get($memcache, 'memcachetest') != 'TEST MESSAGE')
			{
				$result['code'] = 1;
				$result['status'] = 'ERROR';
				$result['error_code'] = 500;
				$result['error_msg'] = 'Cannot get key and value from memcache server.';
			}
		}
		memcache_close($memcache);
	}
	$time_end = microtime(true);
	$response_time = $time_end - $time_start;
	$result['response_time'] = $response_time;
	return $result;
}

function check_connect_memcached($server, $port)
{
	$time_start = microtime(true);
	$ERROR_MSG = array(
		0 => 'MEMCACHED_SUCCESS',
		1 => 'MEMCACHED_FAILURE',
		2 => 'MEMCACHED_HOST_LOOKUP_FAILURE',
		3 => 'MEMCACHED_CONNECTION_FAILURE',
		4 => 'MEMCACHED_CONNECTION_BIND_FAILURE',
		5 => 'MEMCACHED_WRITE_FAILURE',
		6 => 'MEMCACHED_READ_FAILURE',
		7 => 'MEMCACHED_UNKNOWN_READ_FAILURE',
		8 => 'MEMCACHED_PROTOCOL_ERROR',
		9 => 'MEMCACHED_CLIENT_ERROR',
		10 => 'MEMCACHED_SERVER_ERROR',
		11 => 'MEMCACHED_ERROR',
		12 => 'MEMCACHED_DATA_EXISTS',
		13 => 'MEMCACHED_DATA_DOES_NOT_EXIST',
		14 => 'MEMCACHED_NOTSTORED',
		15 => 'MEMCACHED_STORED',
		16 => 'MEMCACHED_NOTFOUND',
		17 => 'MEMCACHED_MEMORY_ALLOCATION_FAILURE',
		18 => 'MEMCACHED_PARTIAL_READ',
		19 => 'MEMCACHED_SOME_ERRORS',
		20 => 'MEMCACHED_NO_SERVERS',
		21 => 'MEMCACHED_END',
		22 => 'MEMCACHED_DELETED',
		23 => 'MEMCACHED_VALUE',
		24 => 'MEMCACHED_STAT',
		25 => 'MEMCACHED_ITEM',
		26 => 'MEMCACHED_ERRNO',
		27 => 'MEMCACHED_FAIL_UNIX_SOCKET',
		28 => 'MEMCACHED_NOT_SUPPORTED',
		29 => 'MEMCACHED_NO_KEY_PROVIDED',
		30 => 'MEMCACHED_FETCH_NOTFINISHED',
		31 => 'MEMCACHED_TIMEOUT',
		32 => 'MEMCACHED_BUFFERED',
		33 => 'MEMCACHED_BAD_KEY_PROVIDED',
		34 => 'MEMCACHED_INVALID_HOST_PROTOCOL',
		35 => 'MEMCACHED_SERVER_MARKED_DEAD',
		36 => 'MEMCACHED_UNKNOWN_STAT_KEY',
		37 => 'MEMCACHED_E2BIG',
		38 => 'MEMCACHED_INVALID_ARGUMENTS',
		39 => 'MEMCACHED_KEY_TOO_BIG',
		40 => 'MEMCACHED_AUTH_PROBLEM',
		41 => 'MEMCACHED_AUTH_FAILURE',
		42 => 'MEMCACHED_AUTH_CONTINUE',
		43 => 'MEMCACHED_PARSE_ERROR',
		44 => 'MEMCACHED_PARSE_USER_ERROR',
		45 => 'MEMCACHED_DEPRECATED',
		46 => 'MEMCACHED_IN_PROGRESS',
		47 => 'MEMCACHED_SERVER_TEMPORARILY_DISABLED',
		48 => 'MEMCACHED_SERVER_MEMORY_ALLOCATION_FAILURE',
		49 => 'MEMCACHED_MAXIMUM_RETURN'
	);

	$result['code'] = 0;
	$result['status'] = 'OK';
	$result['error_code'] = 0;
	$result['error_msg'] = '';
	$memcached = new Memcached;
	if($memcached->addServer($server,$port) == false)
	{
		$errorcode = $memcached->getResultCode();
		$result['code'] = 1;
		$result['status'] = 'ERROR';
		$result['error_code'] = $errorcode;
		$result['error_msg'] = 'Cannot connect to memcache server ('. $ERROR_MSG[$errorcode] .')';
	}
	else
	{
		if($memcached->set('memcached_test', 'TEST MESSAGE', 30) == false)
		{
			$errorcode = $memcached->getResultCode();
			$result['code'] = 1;
			$result['status'] = 'ERROR';
			$result['error_code'] = $errorcode;
			$result['error_msg'] = 'Cannot set key and value to memcache server ('. $ERROR_MSG[$errorcode] .')';
		}
		else
		{
			if($memcached->get('memcached_test')  != 'TEST MESSAGE')
			{
				$errorcode = $memcached->getResultCode();
				$result['code'] = 1;
				$result['status'] = 'ERROR';
				$result['error_code'] = $errorcode;
				$result['error_msg'] = 'Cannot get key and value from memcache server ('. $ERROR_MSG[$errorcode] .')';
			}
		}
	}
	$time_end = microtime(true);
	$response_time = $time_end - $time_start;
	$result['response_time'] = $response_time;
	return $result;
}

function check_connect_redis($server, $port, $auth=null)
{
	$time_start = microtime(true);
	if(class_exists('Redis'))
	{
		$redis = new Redis();
		if($redis->connect($server, $port))
		{
			if($auth==null)
			{
				if($redis->set('healthcheck','hello'))
				{
					if($redis->get('healthcheck') == 'hello')
					{
						$result['code'] = 0;
						$result['status'] = 'OK';
						$result['error_code'] = 0;
						$result['error_msg'] = '';
					}
					else
					{
						$result['code'] = 1;
						$result['status'] = 'ERROR';
						$result['error_code'] = 500;
						$result['error_msg'] = 'Cannot get data from Redis.';
					}
				}
				else
				{
					$result['code'] = 1;
					$result['status'] = 'ERROR';
					$result['error_code'] = 500;
					$result['error_msg'] = 'Cannot set data to Redis.';
				}
			}
			else
			{
				if($redis->auth($auth))
				{
					if($redis->set('healthcheck','hello'))
					{
						if($redis->get('healthcheck') == 'hello')
						{
							$result['code'] = 0;
							$result['status'] = 'OK';
							$result['error_code'] = 0;
							$result['error_msg'] = '';
						}
						else
						{
							$result['code'] = 1;
							$result['status'] = 'ERROR';
							$result['error_code'] = 500;
							$result['error_msg'] = 'Cannot get data from Redis.';
						}
					}
					else
					{
						$result['code'] = 1;
						$result['status'] = 'ERROR';
						$result['error_code'] = 500;
						$result['error_msg'] = 'Cannot set data to Redis.';
					}
				}
				else
				{
					$result['code'] = 1;
					$result['status'] = 'ERROR';
					$result['error_code'] = 500;
					$result['error_msg'] = 'Redis Auth Failure.';
				}
			}
		}
		else
		{
			$result['code'] = 1;
			$result['status'] = 'ERROR';
			$result['error_code'] = 500;
			$result['error_msg'] = 'Cannot connect Redis ('.$server.':'.$port.')';
		}
		$redis->close();
	}
	else
	{
		$result['code'] = 1;
		$result['status'] = 'ERROR';
		$result['error_code'] = 404;
		$result['error_msg'] = 'Redis module not exists.';
	}

	$time_end = microtime(true);
	$response_time = $time_end - $time_start;
	$result['response_time'] = $response_time;
	return $result;
}

function check_api_call($url,$format_type=null)
{
	$time_start = microtime(true);
	$data_result = post_json_url($url,'');
	if($data_result['error_no']==0)
	{
		if($format_type=='null')
		{
			$result['code'] = 0;
			$result['status'] = 'OK';
			$result['error_code'] = 0;
			$result['error_msg'] = '';
		}
		else
		{
			if($format_type=='json')
			{
				// Check JSON Format
				if(json_decode($data_result['error_no'],true)!==false)
				{
					$result['code'] = 0;
					$result['status'] = 'OK';
					$result['error_code'] = 0;
					$result['error_msg'] = '';
				}
				else
				{
					$result['code'] = 1;
					$result['status'] = 'ERROR';
					$result['error_code'] = 500;
					$result['error_msg'] = 'Response JSON format incorrect.';
				}
			}
			else
			{
				$result['code'] = 0;
				$result['status'] = 'OK';
				$result['error_code'] = 0;
				$result['error_msg'] = '';
			}

		}
	}
	else
	{
		$result['code'] = 1;
		$result['status'] = 'ERROR';
		$result['error_code'] = $data_result['error_no'];
		$result['error_msg'] = $data_result['error_msg'];
	}
	$time_end = microtime(true);
	$response_time = $time_end - $time_start;
	$result['response_time'] = $response_time;
	return $result;
}


function post_json_url($url,$content,$domain=null)
{
	// Check HTTPS Protocol URL
	$detect_url = parse_url($url);
	$HTTPS_CHECK=($detect_url['scheme'] == 'https'?true:false);

	$output = array(
		'status' => 0,
		'result' => '',
		'error_no' => 0,
		'error_msg' => ''
	);
	//$curl = curl_init($url);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	if($domain != null)
	{
		curl_setopt($curl, CURLOPT_HTTPHEADER,
			array(
			"Host: ".$domain,
			//"Content-type: application/json"
			"Content-type: text/json"
			));
	}
	else
	{
		curl_setopt($curl, CURLOPT_HTTPHEADER,
			//array("Content-type: application/json"));
			array("Content-type: text/json"));
	}

	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

	//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

	// TIMEOUT
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, CONFIG_CURL_CONNECT_TIMEOUT);
	curl_setopt($curl, CURLOPT_TIMEOUT, CONFIG_CURL_TIMEOUT);

	// USER AGENT
	if(isset($_SERVER['HTTP_USER_AGENT']))
	{
		curl_setopt($curl,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	}

	// HTTPS
	if($HTTPS_CHECK)
	{
		// SSL
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		//curl_setopt($curl, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
	}

	$json_response = curl_exec($curl);
	if($json_response === false)
	{
		$output['error_no'] = curl_errno($curl);
		$output['error_msg'] = curl_error($curl);
	}
	else
	{
		$output['result'] = $json_response;
	}
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$output['status'] = $status;

	/*if ( $status != 201 ) {
		die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
	}*/
	curl_close($curl);

	return $output;
}

/*
$GEARMAN_FUNC_LIST = array(
	array(
		'name' => 'Photo Job Queue', 'func' => 'photo_job'
	),
	array(
		'name' => 'Media Job Queue', 'func' => 'media_job'
	),
	array(
		'name' => 'Audio Job Queue', 'func' => 'audio_job'
	),
	array(
		'name' => 'Notification Job Queue', 'func' => 'notify_job'
	)
);
*/
function check_connect_gearman($server, $port, $workerfunc)
{
	$time_start = microtime(true);
	if(class_exists('gearmandAdmin'))
	{
		$gmAdmin = new gearmandAdmin($server,$port);
		$gmstatus = $gmAdmin->getStatus();
		$servicecount = 1;

		if($gmstatus === false)
		{
			$result['code'] = 1;
			$result['status'] = 'ERROR';
			$result['error_code'] = 500;
			$result['error_msg'] = 'Cannot connect Gearman.';
		}
		else
		{
			$gm_info=array();
			foreach($gmstatus as $gmworker)
			{
				if($gmworker['function']==$workerfunc)
				{
					$gm_info = $gmworker;
				}
			}
			$result['code'] = 0;
			$result['status'] = 'OK';
			$result['gm_info'] =  $gm_info;
			$result['error_code'] = 0;
			$result['error_msg'] = '';

			$gmAdmin->disconnect();
		}

	}
	else
	{
		$result['code'] = 1;
		$result['status'] = 'ERROR';
		$result['error_code'] = 404;
		$result['error_msg'] = 'Gearmanadmin class not exists.';
	}
	$time_end = microtime(true);
	$response_time = $time_end - $time_start;
	$result['response_time'] = $response_time;
	return $result;
}
?>