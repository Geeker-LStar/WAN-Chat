<!--群聊聊天室——显示页面-->

<!DOCTYPE html>
<html>
    <head>
        <?php
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->
        
        <?php
            if (isset($_REQUEST["ta_wid"]))
                $ta_wid = $_REQUEST["ta_wid"];
        ?>
        <!--未设置 request（即：未选择好友）时，跳转回选择好友页面-->
        <?php
            if (!isset($_REQUEST['ta_wid']))
                header("location: mine.php");
        ?>
        <!--与该用户不是好友-->
        <?php
            if (!in_array($ta_wid, explode("//", $myfrd)))
                header("location: no_access.php?wid=$ta_wid");
        ?>
        
        <!--获取好友显示名-->
        <?php
            $sql = "SELECT * FROM Users WHERE wan_uid='{$ta_wid}'";
            $result = $conn->query($sql);
            $getn = $result->fetch_assoc();
            echo(mysqli_error($conn));
            $fn = $getn["showname"];    // 好友的显示名
            $fintrod = $getn["self_introd"];
        ?>
        <title><?php echo $fn;?> - WAN</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../wan-static/css/chat_media.css">
        <script src="../wan-includes/tinymce/js/tinymce/tinymce.min.js"></script>
    </head>
    
    <body>
        <style>
            html, body {
                padding-top: 3px;
                height: 100%;
            }
        </style>
        
        <!--连接数据库-->
        <?php
            // 创建连接
            $conn = new mysqli($servername, $username, $password, $dbname);
            // 检测连接
            if ($conn->connect_error)
                die("连接失败：" . $conn->connect_error);
        ?>
        
        <!--检测是否登录，未登录则要求登录-->
        <?php
            if (!isset($_SESSION["wuid"]))
                header("location: ../wan-login/signin.php");
        ?>
        
        <!--上传文件-->
        <?php
            if (isset($_GET["premsg"])) {
                $premsg = $_GET["premsg"];
        ?>
            <div class="right">
                <iframe src="chatroom.php?gid=<?php echo $_SESSION['gid'];?>?premsg=<?php echo $premsg;?>" frameborder="0" scrolling="auto" class="content" style="width: 100%; height: 100%"></iframe>
            </div>
        <?php
            }
        ?>
        
        <div class="left">
            <div style="padding-top: 48px; padding-left: 48px; padding-right: 48px; padding-bottom: 10px; position: relative;">
                <img src="../wan-myself/headimg/<?php echo $ta_wid;?>.png" style="height: 40px; width: 40px; position: absolute; top: 10px; left: 10px;">
                <center><h1 style="margin-top: 10px;"><?php echo $fn;?></h1><br>
                <p><?php echo $fintrod;?></p><br></center>
            </div>
            <nav class="navbar navbar-expand-sm bg-light" style="">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="mine.php" style="padding-left: 30px;">&larr;返回好友列表</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chat.php?ta_wid=<?php echo $ta_wid;?>" id="reload_trigger" style="padding-left: 30px;">刷新</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="right">
            <iframe src="chatroom.php?ta_wid=<?php echo $ta_wid;?>" frameborder="0" scrolling="auto" class="content" style="width: 100%; height: 100%"></iframe>
        </div>
        
        <script>
            // const mins_for_one_reload = 0.3;
            // function heartbeat() {
            //     document.getElementById("reload_trigger").click();
            // }
            // setInterval(heartbeat,mins_for_one_reload*60*1000);
        </script>
    </body>
</html>