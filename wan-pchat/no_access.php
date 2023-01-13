<!--无权访问时显示的页面-->

<!DOCTYPE html>
<html>
    <head>
        <?php 
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->
        
        <title>WAN - 无权访问！</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <!--检测是否登录，未登录则要求登录-->
        <?php
            if (!isset($_SESSION["wuid"]))
                header("location: ../wan-login/signin.php");
        ?>
        
        <!--获取 get 的值-->
        <?php
            if (isset($_GET["wid"]))
                $ta_wid = $_GET["wid"];
            else 
                header("location: mine.php");
        ?>
        <!--显示框-->
        
        <center><div class="mt-3 p-5">
            <div class="mt-4 p-5 rounded" style="background-color: #999;">
                <h1>您不是对方的好友，添加对方为好友后才可与对方私聊。</h1><br>
                <h1><a href="../wan-users/user.php?wid=<?php echo $ta_wid;?>">去添加</a></h1>
            </div>
        </div></center>
    </body>
</html>
