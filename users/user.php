

<!DOCTYPE html>
<html>
    <head>
    <?php
        session_start();
    ?>
    <?php require_once "../wan-config.php";?>
    <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->
    <?php
        date_default_timezone_set("PRC");    // 时区
        // 创建连接
        $conn = new mysqli($servername, $username, $password, $dbname);
        // 检测连接
        if ($conn->connect_error)
            die("连接失败：" . $conn->connect_error);
        else
        {
            $sql = "SELECT * from Users WHERE wan_uid='{$_GET['id']}'";
            $result = $conn->query($sql);
            if ($result->num_rows == 0)
                echo "<div class='container p-5'><center><h1>该用户不存在！</h1></center></div>";
            else
            {
                $user_arr = $result->fetch_assoc();
                $uwid = $user_arr["wan_uid"];
                $uphone = $user_arr["phone"];
                $uusern = $user_arr["username"];
                $ushown = $user_arr["showname"];
                $uemail = $user_arr["email"];
                $ugender = $user_arr["gender"];
                $uintrod = $user_arr["self_introd"];
            }
        }
    ?>
        
        <title><?php echo $ushown;?> - 发现用户</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <!--检测是否登录，未登录则要求登录-->
        <?php
            if (!isset($_SESSION["wuid"]))
                header("location: ../wan-login/signin.php");
            if(!isset($_GET["id"]))
                header("location: search.php");
        ?>
        
        <?php if ($uwid) { ?>
            <div class="container p-5">
                <h1><?php echo $ushown;?><span id="gender_auto"></span></h1>
                <h2>个人信息</h2>
                <?php
                
                    echo "<small><p style=\"color: gray\" data-bs-toggle=\"tooltip\" title=\"用户的唯一ID，WAN通过这个区分用户。\">wan-uid：{$uwid}</p></small>
                    <script>
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]'))
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                      return new bootstrap.Tooltip(tooltipTriggerEl)
                    })
                    </script>";
        
                    
                    // echo "手机号：" . $uphone;
                    // echo "<br><br>用户名：" . $uusern;
                    $time = time();    // 当前时间戳（作用：每次都会刷新图片，不会被缓存干扰）
                    echo "个人头像：<br><br>";
                    if (file_exists("../wan-myself/headimg/" . $uwid . ".png"))
                        echo "<img src='../wan-myself/headimg/$uwid.png?timestamp=$time' style='height: 100px; width: 100px;'><br><br>";
                    else
                        echo "<img src='../wan-myself/headimg/wan-default-head.png' style='height: 100px; width: 100px;'><br><br>";
                    echo "显示名：" . $ushown;
                    if (empty($uemail))
                        echo "<br><br>邮箱：未填写";
                    else 
                        echo "<br><br>邮箱：" . $uemail;
                    if (empty($uintrod))
                        echo "<br><br>个人简介：未填写";
                    else 
                        echo "<br><br>个人简介：" . $uintrod;
                ?>
                <br>
                
                
                <script>
                    var gender_db = "<?php echo $ugender;?>";
                    var gender_formatted = ((gender_db=="XX")?"姐姐":"哥哥");
                    document.getElementById("gender_auto").innerHTML = gender_formatted;
                </script>
            </div>
        <?php } ?>
    
        <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link" id="reload_trigger" href="search.php" style="padding-left: 30px;">&larr;返回查找用户</a>
                </li>
            </ul>
        </nav><br>
        
        <?php require "../wan-footer.php";?>
    
    </body>
</html>
