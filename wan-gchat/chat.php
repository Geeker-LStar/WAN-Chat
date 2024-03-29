<!--群聊聊天室——显示页面-->

<!DOCTYPE html>
<html>
    <head>
        <?php
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->
        
        <!--未设置 session（即：未选择群聊）时，跳转回选择群聊页面-->
        <?php
            if (!isset($_SESSION['gid']))
                header("location: mine.php");
        ?>
        
        <?php
            if (isset($_REQUEST["gid"]))
                $_SESSION['gid'] = $_REQUEST["gid"];
        ?>
        <!--获取群名称-->
        <?php
            $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$_SESSION['gid']}'";
            $result = $conn->query($sql);
            $getg = $result->fetch_assoc();
            $gn = $getg["g_name"];    // 群名称
            $gintrod = $getg["g_introd"];
           
        ?>
        <title><?php echo $gn;?> - WAN</title>
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
                <iframe src="chatroom.php?gid=<?php echo $_SESSION['gid'];?>?premsg=<?php echo $premsg;?>" frameborder="0" scrolling="auto" class="content" style="width: 100%; height: 100%;"></iframe>
            </div>
        <?php
            }
        ?>
        
        <div class="small_left">
            <center style="padding-top: 5px; padding-bottom: 8px;"><h1 style="display: inline;"><?php echo $gn;?></h1></center>
            <a style="text-decoration: none; color: black; float: left; padding-left: 35%;" href="mine.php">&larr;返回</a>
            <a style="text-decoration: none; color: black; float: right; padding-right: 35%;" href="chat.php" id="reload_trigger">刷新</a>
        </div>
        <div class="left">
            <div style="padding-top: 48px; padding-left: 48px; padding-right: 48px; padding-bottom: 10px;">
                <center><h1><?php echo $gn;?></h1><br>
                <p><?php echo $gintrod;?></p><br></center>
            </div>
            <nav class="navbar navbar-expand-sm bg-light">
                <ul class="navbar-nav">
                    <li class="nav-item" style="display: inline;">
                        <a class="nav-link" href="mine.php" style="padding-left: 30px;">&larr;返回群聊列表</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chat.php" id="reload_trigger" style="padding-left: 30px;">刷新</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="right">
            <iframe src="chatroom.php?gid=<?php echo $_SESSION['gid'];?>" frameborder="0" scrolling="auto" class="content" style="width: 100%; height: 100%"></iframe>
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
