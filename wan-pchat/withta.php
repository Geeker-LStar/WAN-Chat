<!--用户和好友的专属页面-->

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
        <title>WAN - <?php echo $shown;?>和<?php echo $fn;?>的专属页面</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../wan-static/css/chat_media.css">
        <script src="../wan-includes/tinymce/js/tinymce/tinymce.min.js"></script>
    </head>
    
    <body>
        <div class="p-5">
            <img src="../wan-myself/headimg/<?php echo $wid;?>.png" style="border-radius: 50%; width: 132px; height: 132px; ">
            
            <img src="../wan-myself/headimg/<?php echo $ta_wid;?>.png" style="border-radius: 50%; width: 132px; height: 132px; ">
            <h1 style="display: inline;"><?php echo $shown;?>和<?php echo $fn;?>的专属页面</h1>
        </div>
        
        <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../wan-myself/myself.php" style="padding-left: 30px;">返回个人中心</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../wan-pchat/mine.php" style="padding-left: 30px;">返回好友列表</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../wan-pchat/chat.php?ta_wid=<?php echo $ta_wid;?>" style="padding-left: 30px;">和 TA 聊天</a>
                </li>
            </ul>
        </nav>
        <style>
            .main {
                height: auto;
            }
            .info_card {
                float: left;
                display: inline-block;
                margin-left: 20px;
                padding: 25px;
                width: 30%;
                background-color: skyblue;
                border-radius: 10px;
                box-shadow: 10px 10px 10px gray;
            }
            .pic_carousel {
                margin-left: 34%;
                padding: 25px;
                width: 63.5%;
                background-color: orange;
                border-radius: 10px;
                box-shadow: 10px 10px 10px gray;
            }
            .left {
                width: 20%;
                
                border: 1px solid #333;
                display: inline;
                float: left;
                margin-left: 20px;
                margin-top: 20px;
                height: 95%;
                border-radius: 10px;
                box-shadow: 10px 10px 10px gray;
            }
            .right {
                margin-left: 22.5%;
                width: 76.3%;
                
                border: 1px solid #333;
                overflow: hidden;
                margin-top: 20px;
                height: 95%;
                border-radius: 10px;
                box-shadow: 10px 10px 10px gray;
            }
        </style>
        
        <div class="main" style="margin-top: 20px;">
            <div class="info_card">
                <h1>（开发中）<br>这是我们认识的第 C 天</h1>
                <p>我们互相发了 Z 条消息</p>
                <p>我们最近一次联系是在 M（天前/小时前/分钟前）</p>
                （乱入：不要问我为什么不用 XYZ 而用 CZM，just because I LOVE CZM）
            </div>
            <div class="pic_carousel">
                <h1>照片轮播</h1>
            </div>
        </div>
    </body>
</html>