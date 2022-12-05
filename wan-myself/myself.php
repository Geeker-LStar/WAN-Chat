<!--用户个人中心-->

<!DOCTYPE html>
<html>
    <head>
<?php
    session_start();
?>
<?php require_once "../wan-config.php";?>
<?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->

        <title><?php echo $usern;?>的个人中心</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <!--检测是否登录，未登录则要求登录-->
        <?php
            if (!isset($_SESSION["wuid"]))
                header("location: ../wan-login/signin.php");
        ?>
        
        <!--连接数据库-->
        <?php
            // 创建连接
            $conn = new mysqli($servername, $username, $password, $dbname);
            // 检测连接
            if ($conn->connect_error)
                die("连接失败" . $conn->connect_error);
        ?>
        <div class="container p-5">
        <h1><span id="greeting_auto"></span>好，<?php echo $usern;?></h1>
        <h2>个人信息</h2>
        <?php
            echo "wan-uid：" . $wid . "（用户 id，唯一标识）";
            echo "<br><br>手机号：" . $phone;
            echo "<br><br>用户名：" . $usern;
            echo "<br><br>显示名：" . $shown;
            if (empty($email))
                echo "<br><br>邮箱：未填写";
            else 
                echo "<br><br>邮箱：" . $email;
            if (empty($introd))
                echo "<br><br>个人简介：未填写";
            else 
                echo "<br><br>个人简介：" . $introd;
        ?>
        <br><br><button class="btn btn-primary" onclick="window.location.href='change_info.php';">修改个人信息</button><br><br>
        
        <h2>可用功能</h2>
        <button class="btn btn-primary" onclick="window.location.href='../wan-gchat/create.php';">新建群聊</button>&nbsp;
        <button class="btn btn-primary" onclick="window.location.href='../wan-gchat/join.php';">加入群聊</button>&nbsp;
        <button class="btn btn-primary" onclick="window.location.href='../wan-gchat/mine.php';">查看已加入的群聊</button><br><br>
        <script>
            var nowtime = new Date;
            var hournow = nowtime.getHours();
            var greeting;
            if((hournow >= 0 && hournow <= 4)||(hournow >= 20 && hournow <= 24)){
                greeting = "晚上";    // 本来想写“夜猫子”。。。
            }
            else if(hournow <= 5 && hournow <= 7){
                greeting = "早上";
            }
            else if(hournow >= 8 && hournow <= 11){
                greeting = "上午";
            }
            else if(hournow >= 12 && hournow <= 13){
                greeting = "中午";
            }
            else if(hournow >= 14 && hournow <= 19){
                greeting = "下午";
            }
            else{
                greeting = "你"; // 虽然我不认为会有这样的情况，但是如果真的出错了，获取不到时间，那就显示“你好”。
            }
            document.getElementById("greeting_auto").innerHTML = greeting;
        </script>
        </div>
    </body>
</html>
