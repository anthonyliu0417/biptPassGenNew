<!DOCTYPE html>

<?php

$nameErr = $classErr = $numberErr = "";
$name = $sex = $dept = $class = $number = "";

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

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (empty($_POST["name"]))
    {
        $nameErr = "姓名是必需的";
    }
    else
    {
        $name = $_POST["name"];
    }
    

    $sex = $_POST["sex"];
    $dept = $_POST["department"];
    $type = $_POST["type"];

    
    if (empty($_POST["class"]))
    {
        $classErr = "班级是必需的";
    }
    else
    {
        $class = $_POST["class"];
    }
    

    if (empty($_POST["number"]))
    {
        $numberErr = "学号是必需的";
    }
    else
    {
        $number = $_POST["number"];
        if(!is_numeric($number))
        {
            $numberErr = "学号必须全部为数字";
        }
    }

    if($nameErr == "" && $classErr == "" && $numberErr == "")//输入正确
    {
        echo "<h2>正在处理中，请稍后......</h2>";
        $ip = $_SERVER['REMOTE_ADDR'];
        $ip_belonging=get_ip_city($ip);
        $filePath="";
        if($_FILES["photo"]["name"]!="")//上传了图片，php.ini设置限制上传大小为8m
        {
            $fileName=$_FILES["photo"]["name"];
            $filePath=$fileName;
            move_uploaded_file($_FILES["photo"]["tmp_name"], $filePath);
        }
        else
        {
            $filePath="未上传图片";
        }
        $agent=$_SERVER['HTTP_USER_AGENT'];

        $servername = "xxxxxx";
        $username = "xxxxxx";
        $password = "xxxxxx";
        $dbname = "xxxxxx";
        
        try
        {
            $id="";
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // 设置 PDO 错误模式，用于抛出异常
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sqlInsert="INSERT INTO generate_info (name, number, gender, dept, class, generate_time, ip, ip_belonging, request_type, photo_path, agent)
                VALUES (:name, :number, :gender, :dept, :class, now(), :ip, :ip_belonging, :request_type, :photo_path, :agent)";
            $stmt = $conn->prepare($sqlInsert);
            $stmt->bindParam(':name',$name);
            $stmt->bindParam(':number',$number);
            $stmt->bindParam(':gender',$sex);
            $stmt->bindParam(':dept',$dept);
            $stmt->bindParam(':class',$class);
            $stmt->bindParam(':ip',$ip);
            $stmt->bindParam(':ip_belonging',$ip_belonging);
            $stmt->bindParam(':request_type',$type);
            $stmt->bindParam(':photo_path',$filePath);
            $stmt->bindParam(':agent',$agent);
            $stmt->execute();

            $sqlGetID="SELECT id FROM generate_info WHERE name=:name AND number=:number AND ip=:ip AND photo_path=:photo_path";
            $stmt=$conn->prepare($sqlGetID);
            $stmt->bindParam(':name',$name);
            $stmt->bindParam(':number',$number);
            $stmt->bindParam(':ip',$ip);
            $stmt->bindParam(':photo_path',$filePath);
            $stmt->execute();
            while($row=$stmt->fetch())
            {
                $id=$row['id'];
            }
            setcookie("ID",$id);//设置cookie
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return;
        }
        $conn = null;

        $nextPage="free";//跳转下一页
        header("location:$nextPage");
        return;

    }

}

if(isset($_COOKIE["ID"]))
{
    $servername = "xxxxxx";
    $username = "xxxxxx";
    $password = "xxxxxx";
    $dbname = "xxxxxx";

    $genDate="";
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $sqlGetTime="SELECT generate_time FROM generate_info WHERE id=".$_COOKIE["ID"];
    $stmt=$conn->prepare($sqlGetTime);
    $stmt->execute();
    while($row=$stmt->fetch())
    {
        $genDate=$row['generate_time'];
    }

    if($genDate!="")
    {
        echo "<script type='text/javascript'>
        var exist=confirm(\"您于".$genDate."已生成过通行页面，是否要跳转至这个页面？点击“取消”将生成新的页面\");
        if (exist==true){
            window.location.href=\"free\";
        }
        </script>";
    }
}

?>

<html lang="zh-CN">
<head>
<meta charset="utf-8">
<title>北石化出入证生成工具New</title>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <img src="title_background.jpg" class="title-bg" alt="">

    <div class="input_frame">
        <p>带<span class="error">*</span>的为必填项<br/>如果你的通行页面上带有自己的照片可以上传。<br/>上传照片建议不要超过1M，不然可能不会正确显示<br/>
            默认头像显示学校系统的男生或女生图片<br/>
            <span class='error'>本项目仅供用于学习Web前后端开发技术，<br/>
            程序源代码已于<a href="https://github.com/"><span style="color:blue;text-decoration:underline;">GitHub</span></a>开源，欢迎互相交流进步，<br/>
            请勿将其用于任何非法用途，否则出现一切问题本站概不负责</span></p>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
            <span class="error">*</span>姓名：<input class="input" name="name" type="text"><span class="error"><?php echo $nameErr;?></span><br/><p class="space"><br></p>
            <span class="error">*</span>性别：<input name="sex" type="radio" checked="checked" value="男" />男<input name="sex" type="radio" value="女" />女<br/><p class="space"><br></p>
                <span class="error">*</span>学院：
                <select class="select" name="department">
                    <option value="信息工程学院">信息工程学院</option>
                    <option value="化学工程学院">化学工程学院</option>
                    <option value="机械工程学院">机械工程学院</option>
                    <option value="经济管理学院">经济管理学院</option>
                    <option value="人文社科学院">人文社科学院</option>
                    <option value="新材料与化工学院">新材料与化工学院</option>
                    <option value="材料科学与工程学院">材料科学与工程学院</option>
                    <option value="人工智能研究院">人工智能研究院</option>
                    <option value="致远学院">致远学院</option>
                    <option value="安全工程学院">安全工程学院</option>
                </select><br/><p class="space"><br></p>
            <span class="error">*</span>班级：<input class="input" name="class" type="text"><span class="error"><?php echo $classErr;?></span><br/><p class="space"><br></p>
            <span class="error">*</span>学号：<input class="input" name="number" type="text"><span class="error"><?php echo $numberErr;?></span><br/><p class="space"><br></p>
            照片：<input class="upload" name="photo" type="file" id="photo" accept="image/*"><br/><p class="space"><br></p>
            <span class="error">*</span>类型：
                <select class="select" name="type">
                    <option value="事假">事假</option>
                    <option value="病假">病假</option>
                    <option value="入校">入校</option>
                    <option value="研究生事假">研究生事假</option>
                    <option value="研究生病假">研究生病假</option>
                    <option value="往返两校区和校外住宿报备">往返两校区和校外住宿报备</option>
                    <option value="允许在康庄-主校间通勤">允许在康庄-主校间通勤</option>
                </select><br/><br/>
            <input class="submit" type="submit" name="btn" value="提交"/>
        </form>
        <p id='status'></p>
        <p>在使用过程中如果程序出现了bug或者一些其他问题，欢迎您<span style="color:blue;text-decoration:underline;"><a href="https://free-bipt.tk/bug%e6%8f%90%e4%ba%a4%e5%8c%ba/">点击这里</a></span>进行反馈，我在看到后会尽快进行处理，谢谢~</p>
    </div>

    <script type='text/javascript'>
    document.getElementsByClassName("submit")[0].onclick=function()
    {
        document.getElementById("status").innerHTML="正在处理中，请稍后...";
    }
    </script>

</body>
</html>