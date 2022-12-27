<!--用户个人中心-->

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
        ?>
        <title><?php echo $usern;?> 的个人中心 - WAN</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <!--检测是否登录，未登录则要求登录-->
        <?php
            if (!isset($_SESSION["wuid"]))
                header("location: ../wan-login/signin.php");
        ?>
        
        <!--连接数据库-->
        <div class="container p-5">
        <h1><span id="greeting_auto"></span>好，<?php echo $shown;?><span id="gender_auto"></span></h1>    <!-- 问好 -->
        <h2>个人信息</h2>
        <?php
        
        echo "<small><p style=\"color: gray\" data-bs-toggle=\"tooltip\" title=\"用户的唯一 ID，WAN 通过这个区分用户。\">wan-uid：{$wid}</p></small>
            <script>
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
              return new bootstrap.Tooltip(tooltipTriggerEl)
            })
            </script>";

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
        <br><br><button class="btn btn-primary" onclick="window.location.href='change_info.php';">修改个人信息</button><br><br>
        
        <h2><?php echo $shown;?>，来做些什么？</h2>
        <?php
            $sql = "SELECT * FROM Group_Verify WHERE receiver='$wid' and isread='0'";    // 该用户的未读群验证消息条数
            $result = $conn->query($sql);
            $msg_nums = $result->num_rows;
            $sql = "SELECT * FROM Group_Verify WHERE receiver='$wid' and state='待确认'";    // 该用户的未回应群验证消息条数
            $result = $conn->query($sql);
            $tobecfm = $result->num_rows;
        ?>
        <button class="btn btn-primary" onclick="window.location.href='../wan-gchat/create.php';">新建群聊</button><br><br>
        <button class="btn btn-primary" onclick="window.location.href='../wan-gchat/join.php';">加入群聊</button><br><br>
        <button class="btn btn-primary" onclick="window.location.href='../wan-gchat/mine.php';">已加入的群聊</button><br><br>
        <button class="btn btn-primary" onclick="window.location.href='../wan-gchat/groupvrf.php';">群验证消息</button><span> （未读 <?php echo($msg_nums);?> 条，未确认 <?php echo($tobecfm);?> 条）</span><br><br>
        <button class="btn btn-primary" onclick="window.location.href='../wan-users/search.php';">查找用户</button><br><br>
        <h2>最新的消息</h2>
        <?php 
            /* Emmm... Not an easy task...;)
            We would get a few groups of this user, and then fetch the latest msg in those groups, put them here and give them a link to directly go to the groups. Ahhhhhhhh...*/
            $sql = "SELECT * FROM Users WHERE wan_uid='{$wid}'";
            $result2 = $conn->query($sql);
            $all_group = $result2->fetch_assoc()["my_groups"];
            if (empty($all_group))
                echo "您暂未加入任何群聊。不妨<a href='//wan.ltx1102.com/wan-gchat/join.php'>加入一个群组</a>！";
            else
            {
                $g_arr = explode("//", $all_group);
                $g_arr_len = count($g_arr);
                for($i = 0; $i <= $g_arr_len-2; $i++)
                {  
                    $sql = "SELECT * FROM group_{$g_arr[$i]} ORDER BY msgid desc LIMIT 1";
                    $result = $conn->query($sql);
                    $arr = $result->fetch_assoc();
                    $who = $arr["who"];
                    $msg = $arr["msg"];
                    $sql = "SELECT * FROM Users WHERE wan_uid='{$who}'";
                    $result = $conn->query($sql);
                    $who_shown = $result->fetch_assoc()["showname"];
                    echo "<h4>{$gname}</h4><b>{$who_shown}</b>说：{$msg}<br>";
                }
                echo "<a href='../wan-gchat/mine.php'>去看看</a><br>";
            }
        ?>
        
        
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
            // var gender_db = "<?php echo $gender;?>";
            // var gender_formatted = ((gender_db=="XX")?"姐姐":"哥哥");
            // document.getElementById("gender_auto").innerHTML = gender_formatted;
        </script>
        </div>
        
      <?php require "../wan-footer.php";?>

    </body>
</html>
