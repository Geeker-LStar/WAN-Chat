<!--查找用户-->

<!DOCTYPE html>
<html>
    <head>
        <?php 
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->

        <title>查找用户</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <div class="container p-5">
        <center><h1>查找用户</h1></center></div>
        <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" id="reload_trigger" href="../wan-myself/myself.php" style="padding-left: 30px;">&larr;返回个人中心</a>
                </li>
            </ul>
        </nav><br>
        <!--<div class="form-floating mt-3 mb-3">-->
        <!--  对方的 wan-uid：<input type="text" class="form-control" id="uwid" placeholder="输入" name="uwid">-->
        <!--  <label for="uwid">对方的WAN-UID</label>-->
        <!--</div>-->
        <center>对方的 wan-uid：<br><br><input type="text" class="col-8 col-sm-7 col-md-6 col-lg-4 col-xl-3 col-xxl-3" id="uwid" name="uwid" class="form-control"><br><br>
        
        <!--<label for="uwid">对方的WAN-UID</label>-->
        <button class="btn btn-primary" onclick="go();" name="搜索" value="搜索">搜索</button><br><br>
        <div class="alert alert-warning alert-dismissible col-sm-11 col-md-10 col-lg-8 col-xl-7 col-xxl-6">
            <p style="margin: 0px;">你可能想认识：
            <a href="https://wan.ltx1102.com/wan-users/user.php?wid=218">曹智铭</a>
            <span style="padding-left: 10px;"></span>
            <a href="https://wan.ltx1102.com/wan-users/user.php?wid=1102">李天星</a>
            <span>（本网站的两位开发者）</span></p></center>
        </div>
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
                die("连接失败：" . $conn->connect_error);
        ?>
        <script>
            function go() {
                var whoisthat = document.getElementById("uwid").value;
                var link = "https://wan.ltx1102.com/wan-users/user.php?wid=" + whoisthat;
                window.location.href = link;
            }
        </script>
        
        </div>
        <?php require "../wan-footer.php";?>
    </body>
</html>
