<!--用户个人主页-->

<!DOCTYPE html>
<html>
    <head>
        <?php
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->
        
        <title><?php echo $shown;?>的个人中心 - WAN</title>
        <link rel="stylesheet" href="../wan-static/css/myself_media10.css">
        <link rel="stylesheet" href="../wan-includes/font-awesome-4.7.0/css/font-awesome.css">
        <meta http-equiv="refresh" content="60*10">    <!-- 十分钟刷新一次 -->
    </head>
    
    <body>
        <!--检测是否登录，未登录则要求登录-->
        <?php
            if (!isset($_SESSION["wuid"]))
                header("location: ../wan-login/signin.php");
        ?>
        
        <style>
            html, body {
                padding: 0;
                margin: 0;
            }
        </style>
        
        <!--连接数据库-->
        <?php
            date_default_timezone_set("PRC");    // 时区
            // 创建连接
            $conn = new mysqli($servername, $username, $password, $dbname);
            // 检测连接
            if ($conn->connect_error)
                die("连接失败：" . $conn->connect_error);
            /*$sql = "SELECT * FROM Group_Verify WHERE receiver='$wid' and state='待确认'";    // 该用户的未回应群验证消息条数
            $result = $conn->query($sql);
            $tobecfm = $result->num_rows;*/
        ?>
        
        <?php
            // 该用户的未读群验证消息条数
            $sqlnmsg = "SELECT * FROM Group_Verify WHERE receiver='{$wid}' and isread='0'";    
            $result = $conn->query($sqlnmsg);
            $gmsg_nums = $result->num_rows;
            if($gmsg_nums > 0)
                $hasnewgmsg = "<span class='badge rounded-pill bg-danger'>$gmsg_nums</span>";
            else
                $hasnewgmsg = "";
            // 该用户的未读新朋友验证消息条数
            $sqlnmsg = "SELECT * FROM Private_Verify WHERE receiver='{$wid}' and isread='0'";    
            $result = $conn->query($sqlnmsg);
            $pmsg_nums = $result->num_rows;
            if($pmsg_nums > 0)
                $hasnewpmsg = "<span class='badge rounded-pill bg-danger'>$pmsg_nums</span>";
            else
                $hasnewpmsg = "";
        ?>
        
        <!-- 函数 -->
        <?php
            function fetch_avatar($wid){
                if(file_exists("../wan-myself/headimg/" . $wid . ".png")){
                    return "../wan-myself/headimg/" . $wid . ".png?magic_string=".rand(1,900000);
                }
                else{
                    return "../wan-myself/headimg/wan-default-head.png";
                }
            }
        ?>
        <nav class="navbar navbar-expand-md bg-light fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><span id="greeting_auto"></span>好，<?php echo $shown;?><span id="gender_auto"></span></a>
                <!--<div style="right: 50px;">-->
                    <div class="dropdown" style="margin-right: 50px;">
                        <a href="#" class="align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown">
                        <img style="display: inline;" src="<?php echo fetch_avatar($wid); ?>" alt="" width="32" height="32" class="rounded-circle me-2">
                        <strong><?php echo $usern;?></strong>
                        </a>
                        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
                            <li><a class="dropdown-item" href="change_info.php">编辑个人信息</a></li>
                            <li><a class="dropdown-item" href="headimg.php">修改头像</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="https://wan.ltx1102.com/wan-login/signout.php?signout=1">登出</a></li>
                        </ul>
                    </div>
                <!--</div>-->
            </div>
        </nav>
  
        <div class="left" style="top: 5rem;">
            <center><img class="bi me-2" width="60" height="60" src="../wan-website/img/wan-logo.png"><br><br>
            <span class="fs-4">WAN 聊天室</span></center>
            <div class="list">   
            <ul class="nav nav-pills flex-column mb-auto" style="margin-top: 32px;">
                <li class="nav-item">
                    <a href="../wan-website/index.php" class="nav-link link-dark" aria-current="page">
                    <svg class="bi me-2" width="16" height="16"><use xlink:href="#home"/></svg>
                    <i class="fa fa-fw fa-globe"></i> 首页
                    </a>
                </li>
                <li>
                    <a href="myself.php" class="nav-link active">
                    <svg class="bi me-2" width="16" height="16"><use xlink:href="#speedometer2"/></svg>
                    <i class="fa fa-fw fa-home"></i> 个人中心
                    </a>
                </li>
                <li>
                    <a href="../wan-gchat/mine.php" class="nav-link link-dark">
                    <svg class="bi me-2" width="16" height="16"><use xlink:href="#table"/></svg>
                    <i class="fa fa-fw fa-users"></i> 我的群聊
                    </a>
                </li>
                <li>
                    <a href="../wan-pchat/mine.php" class="nav-link link-dark">
                    <svg class="bi me-2" width="16" height="16"><use xlink:href="#table"/></svg>
                    <i class="fa fa-fw fa-child"></i> 我的好友
                    </a>
                </li>
                <li>
                    <a href="../wan-gchat/create.php" class="nav-link link-dark">
                    <svg class="bi me-2" width="16" height="16"><use xlink:href="#table"/></svg>
                    <i class="fa fa-fw fa-plus-square-o"></i> 新建群聊
                    </a>
                </li>
                <li>
                    <a href="../wan-gchat/join.php" class="nav-link link-dark">
                    <svg class="bi me-2" width="16" height="16"><use xlink:href="#table"/></svg>
                    <i class="fa fa-fw fa-handshake-o"></i> 加入群聊
                    </a>
                </li>
               
                <li>
                    <a href="../wan-gchat/groupvrf.php" class="nav-link link-dark">
                    <svg class="bi me-2" width="16" height="16"><use xlink:href="#grid"/></svg>
                    <i class="fa fa-fw fa-commenting"></i> 群验证消息<?php echo $hasnewgmsg; ?>
                    </a>
                </li>
                <li>
                    <a href="../wan-pchat/friendsvrf.php" class="nav-link link-dark">
                    <svg class="bi me-2" width="16" height="16"><use xlink:href="#people-circle"/></svg>
                    <i class="fa fa-fw fa-user-plus"></i> 新的朋友<?php echo $hasnewpmsg; ?>
                    </a>
                </li>
                <li>
                    <a href="../wan-users/search.php" class="nav-link link-dark">
                    <svg class="bi me-2" width="16" height="16"><use xlink:href="#people-circle"/></svg>
                    <i class="fa fa-fw fa-search-plus"></i> 查找用户
                    </a>
                </li>
            </ul>
            </div>
        </div>
        
        <div class="right">
  
            <div style="padding: 48px;">
                <h2 style="padding-top: 32px;">个人信息</h2>
        <?php
        
            echo "<small><p style=\"color: gray\" data-bs-toggle=\"tooltip\" title=\"用户的唯一 ID，WAN 通过这个区分用户。\">wan-uid：{$wid}</p></small>
                <script>
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                  return new bootstrap.Tooltip(tooltipTriggerEl)
                })
                </script>";
            $time = time();    // 当前时间戳（作用：每次都会刷新图片，不会被缓存干扰）
            
            echo "手机号：" . $phone;
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
        <br><br><button class="btn btn-primary" onclick="window.location.href='change_info.php';">编辑个人信息</button><br><br><br>
            <h2>最新消息</h2>
        <?php 
            /* Emmm... Not an easy task...;)
            We would get a few groups of this user, and then fetch the latest msg in those groups, put them here and give them a link to directly go to the groups. Ahhhhhhhh...*/
            $sql = "SELECT * FROM Users WHERE wan_uid='{$wid}'";
            $result2 = $conn->query($sql);
            $all_group = $result2->fetch_assoc()["my_groups"];
            if (empty($all_group))
                echo "<br />您暂未加入任何群聊。不妨<a href='//wan.ltx1102.com/wan-gchat/join.php'>加入一个群组</a>！<br />";
            else
            {
                echo '<div class="list-group">';
                
                $g_arr = explode("//", $all_group);
                $g_arr_len = count($g_arr);
                for($i = 0; $i <= $g_arr_len-2; $i++)
                {  
                    $msg = "";
                    $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$g_arr[$i]}'";
                    $result = $conn->query($sql);
                    $gn = $result->fetch_assoc()["g_name"];
                    $sql = "SELECT * FROM group_{$g_arr[$i]} ORDER BY msgid desc LIMIT 1";
                    $result = $conn->query($sql);
                    $arr = $result->fetch_assoc();
                    $who = $arr["who"];
                    $temp_arr = explode("<br>", $arr["msg"]);
                    for ($j = 0; $j < count($temp_arr); $j++)
                        $msg = $msg . $temp_arr[$j] . " ";
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$who}'";
                    $result = $conn->query($sql);
                    $who_shown = $result->fetch_assoc()["showname"];
                    echo "<div onclick=\" window.location.href='../wan-gchat/chat.php?gid=$g_arr[$i]'\">
                        <span class=\"list-group-item list-group-item-action d-flex gap-3 py-3\" aria-current=\"true\">";
                    echo '<img src="'.fetch_avatar($who).'" alt="" width="32" height="32" class="rounded-circle flex-shrink-0">
                        <div class="d-flex gap-2 w-100 justify-content-between">';
                    echo '<div><h6 class="mb-0">'.$gn.'</h6>
                        <p class="mb-0 opacity-75 short"><b>'.$msg.'</b></p>
                        </div><small class="opacity-50 text-nowrap">'.$who_shown.'</small></div></span></div>';
                }
                echo "</div>";
            }
        ?>
        </div>
        </div>
        
        <script>
            var nowtime = new Date;
            var hournow = nowtime.getHours();
            var greeting;
            if((hournow >= 0 && hournow <= 4)||(hournow >= 20 && hournow <= 24)) {
                greeting = "晚上";    // 本来想写“夜猫子”。。。
            }
            else if(hournow >= 5 && hournow <= 7) {
                greeting = "早上";
            }
            else if(hournow >= 8 && hournow <= 11) {
                greeting = "上午";
            }
            else if(hournow >= 12 && hournow <= 13) {
                greeting = "中午";
            }
            else if(hournow >= 14 && hournow <= 19) {
                greeting = "下午";
            }
            else{
                greeting = "你"; // 虽然我不认为会有这样的情况，但是如果真的出错了，获取不到时间，那就显示“你好”。
            }
            document.getElementById("greeting_auto").innerHTML = greeting;
            var gender_db = "<?php echo $gender;?>";
            var gender_formatted = ((gender_db=="XX")?"姐姐":"哥哥");
            document.getElementById("gender_auto").innerHTML = gender_formatted;
        </script>
        
    </body>
</html>
