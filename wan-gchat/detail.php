<!--群聊详细信息-->

<!DOCTYPE html>
<html>
    <head>
        <?php
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->
        
        <title></title>
        <meta charset="utf-8">
    </head>
    
    <body>
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
            else
            {
                $sql = "SELECT * FROM Users WHERE wan_uid='{$wid}'";
                $result = $conn->query($sql);
                if ($result->num_rows == 0)
                {   
                    unset($_SESSION["wuid"]);
                    header("location: ../wan-login/signin.php");
                }
            }
        ?>
        
        <!--显示详细的群信息-->
        <?php
            if (isset($_POST["详细信息"]))
            {
                $gid = $_POST["gid"];
                $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$gid}'";
                $result = $conn->query($sql);
                $detail_ar = $result->fetch_assoc();
                // 详细信息
                $gn = $detail_ar["g_name"];    // 群名
                $gms = $detail_ar["g_members"];
                $gmems = explode("//", $gms);    // 群成员
                $gmst = $detail_ar["g_master"];    // 群主
                $gmngs = $detail_ar["g_managers"];
                $mngs = explode("//", $gmngs);    // 群管理
                $gitd = $detail_ar["g_introd"];    // 群介绍
                $gtime = $detail_ar["g_time"];    // 成立时间
            }
            
            else
                header("location: mine.php");
        ?>
        
        <div class="container p-5">
            <center><h1><?php echo($gn);?></h1><br>
            <p>群聊详细信息</p></center>
        </div>
        
        <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" id="reload_trigger" href="../wan-gchat/mine.php" style="padding-left: 30px;">&larr;返回群聊列表</a>
                </li>
            </ul>
        </nav><br>
        
        <center>
            <p style="padding-bottom: 10px;">群 id：<?php echo($gid);?></p>
            <p style="padding-bottom: 10px;">群名称：<?php echo($gn);?></p>
            <p style="padding-bottom: 10px;">群主：
                <?php 
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$gmst}'";
                    $result = $conn->query($sql);
                    $gm_shown = $result->fetch_assoc()["showname"];
                    echo $gm_shown;
                ?>
            </p>
            <p style="padding-bottom: 10px;">群管理员：
                <!--减 3 原因：避免多 1 的索引、去掉最后一个空元素、去掉倒数第二个元素（那个元素后面没有顿号，所以单独输出），下同-->
                <?php 
                    for($i = 0; $i <= count($mngs)-3; $i++)
                    {
                        $sql = "SELECT * FROM Users WHERE wan_uid='{$mngs[$i]}'";
                        $result = $conn->query($sql);
                        $gm_shown = $result->fetch_assoc()["showname"];
                        echo $gm_shown . "、"; 
                    }
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$mngs[count($mngs)-2]}'";
                    $result = $conn->query($sql);
                    $gm_shown = $result->fetch_assoc()["showname"];
                    echo $gm_shown;
                ?>
            </p>
            <p style="padding-bottom: 10px;">群成员：
                <?php 
                    for($i = 0; $i <= count($gmems)-3; $i++)
                    {
                        $sql = "SELECT * FROM Users WHERE wan_uid='{$gmems[$i]}'";
                        $result = $conn->query($sql);
                        $gm_shown = $result->fetch_assoc()["showname"];
                        echo $gm_shown . "、"; 
                    }
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$gmems[count($gmems)-2]}'";
                    $result = $conn->query($sql);
                    $gm_shown = $result->fetch_assoc()["showname"];
                    echo $gm_shown;
                ?>
            </p>
            <p style="padding-bottom: 10px;">群介绍：<?php echo($gitd);?></p>
            <p style="padding-bottom: 10px;">群成立时间：<?php echo($gtime);?></p>
        </center>
        
    </body>
</html>
