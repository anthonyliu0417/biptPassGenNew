<!doctype html>

<?php

if($_COOKIE["ID"]=="")
{
	exit('从cookie读取信息时发生错误！请检查是否成功输入了需要生成的信息或浏览器是否开启cookie。请返回上一级页面并重试。');
}

function get_ip_city($ip)
{
    $ch = curl_init();
    $url = 'https://whois.pconline.com.cn/ipJson.jsp?ip=' . $ip;
    //用curl发送接收数据
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //请求为https
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $location = curl_exec($ch);
    curl_close($ch);
    //转码
    $location = mb_convert_encoding($location, 'utf-8', 'GB2312');
    //var_dump($location);
    //截取{}中的字符串
    $location = substr($location, strlen('({') + strpos($location, '({'), (strlen($location) - strpos($location, '})')) * (-1));
    //将截取的字符串$location中的‘，’替换成‘&’   将字符串中的‘：‘替换成‘=’
    $location = str_replace('"', "", str_replace(":", "=", str_replace(",", "&", $location)));
    //php内置函数，将处理成类似于url参数的格式的字符串  转换成数组
    parse_str($location, $ip_location);
    return $ip_location['addr'];
}

$generateID=$_COOKIE["ID"];
$name=$number=$gender=$dept=$class=$type=$photo_path="";

$servername = "xxxxxx";
$username = "xxxxxx";
$password = "xxxxxx";
$dbname = "xxxxxx";
try
{
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	// 设置 PDO 错误模式，用于抛出异常
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sqlQueryInfo="SELECT name, number, gender, dept, class, request_type, photo_path FROM generate_info WHERE id=:id";
	$stmt=$conn->prepare($sqlQueryInfo);
	$stmt->bindParam(':id',$generateID);
	$stmt->execute();
	while($row=$stmt->fetch())
	{
		$name=$row['name'];
		$number=$row['number'];
		$gender=$row['gender'];
		$dept=$row['dept'];
		$class=$row['class'];
		$type=$row['request_type'];
		$photo_path=$row['photo_path'];
	}
	if($photo_path=="未上传图片")
	{
		if($gender=='男')
		{
			$photo_path="boy.png";
		}
		else
		{
			$photo_path="girl.png";
		}
	}
	else
	{
		$photo_path="../".$photo_path;
	}

	$ip = $_SERVER['REMOTE_ADDR'];
	$ip_belonging=get_ip_city($ip);

	$sqlInsert="INSERT INTO open_info (name, number, gender, dept, class, open_time, ip, ip_belonging, gen_id)
		VALUES (:name, :number, :gender, :dept, :class, now(), :ip, :ip_belonging, :gen_id)";
	$stmt = $conn->prepare($sqlInsert);
	$stmt->bindParam(':name',$name);
	$stmt->bindParam(':number',$number);
	$stmt->bindParam(':gender',$gender);
	$stmt->bindParam(':dept',$dept);
	$stmt->bindParam(':class',$class);
	$stmt->bindParam(':ip',$ip);
	$stmt->bindParam(':ip_belonging',$ip_belonging);
	$stmt->bindParam(':gen_id',$generateID);
	$stmt->execute();
}
catch(PDOException $e)
{
	echo "数据库信息错误，请返回重试。<br>错误信息：";
	echo $e->getMessage();
	return;
}
$conn = null;

?>

<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" /><!--设置宽度，不允许用户缩放-->
<title>校园通行卡</title>
<link href="free.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div class="frame">
		<div class="bg">
			<img src="background.png" class="rectangle" alt=""/>
			<img src="line.jpg" class="upline" alt=""/>
			<img src="line.jpg" class="downline" alt=""/>
			<img src="buttom.png" class="bottom" alt=""/>
		</div>
		<div class="info">
			<p class="pass-title">校园通行码</p>
			<div class="date"><div class="time" id="refresh">xxxx年x月xx日 xx:xx:xx</div></div>
			<div class="div-spot">
				<img src="spot.gif" class="spot" alt=""/>
			</div>
			<div class="div-pic">
				<img src="<?php echo $photo_path;?>" class="pic" alt=""/>
			</div>
			<div class="div-table">
				<ul class="table">
					<li>
						<span class="w-title">姓名</span>：
						<span class="w-con"><?php echo $name;?></span>
					</li>
					<li>
						<span class="w-title">性别</span>：
						<span class="w-con"><?php echo $gender;?></span>
					</li>
					<li>
						<span class="w-title">学工号</span>：
						<span class="w-con"><?php echo $number;?></span>
					</li>
					<li>
						<span class="w-title">学院</span>：
						<span class="w-con"><?php echo $class;?></span>
					</li>
				</ul>
			</div>
			<p class="status"><?php echo $type;?></p>
		</div>
	</div>
	
	<script src="free.js"></script>

</body>
</html>
