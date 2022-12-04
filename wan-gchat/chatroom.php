<!--群聊聊天室主页面-->

<!DOCTYPE html>
<?php
    $time_limit = 60*60*24*30;    // 过期时间为一个月
    session_set_cookie_params($time_limit);
    session_start();
?>
<?php require "../wan-config.php";?>
<?php require "../wan-userinfo.php";?>    <!--用户个人信息-->
<!--未设置 session（即：未选择群聊）时，跳转回选择群聊页面-->
<?php
    if (!isset($_SESSION['gid']))
        header("location: mine.php");
?>

<?php
    if (isset($_GET["gid"]))
        $_SESSION['gid'] = $_GET["gid"];
?>

<html>
    <head>
        <!--获取群名称-->
        <?php
            $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$_SESSION['gid']}'";
            $result = $conn->query($sql);
            $gn = $result->fetch_assoc()["g_name"];    // 群名称
        ?>
        <title><?php echo $gn;?></title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../wan-static/css/me.css">
        <link rel="stylesheet" href="../wan-static/css/others.css">
        <style>
            /* 注：此处的 inphp 仅仅是为了和外部样式的文件名称区分开，没有其他作用和意思 */
            .me-inphp {
                margin-top: 10px;
                padding: 20px;
            }
            
            .others-inphp {
                margin-top: 10px;
                padding: 20px;
            }
        </style>
    </head>
    
    <body>
        <h1><?php echo $gn?></h1>
        <a href="mine.php">返回群聊列表</a>
        <center><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <textarea name="msg" value="msg" style="height: 150px; width: 350px; line-height: 1.5;"></textarea><br><br>
            <input type="submit" name="发送" value="发送">
        </form></center>
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
        
        <!--发送消息-->
        <?php
            if(isset($_POST["发送"]))
            {   
                if (!empty($_POST["msg"]))
                {
                    $msg = $_POST["msg"];    // 获取消息
                    $time = date("Y-m-d H:i:s");    // 当前时间
                    $sql = "INSERT INTO group_{$_SESSION['gid']} (who, time, msg) VALUES ('{$shown}', '{$time}', '{$msg}')";    // 插入新消息
                    $conn->query($sql);
                    echo(mysqli_error($conn));
                }
                else
                {
                    echo "<script>
                        alert('不能发送空白消息！');
                    </script>";
                }
                
            }
        ?>
        
        <!--获取聊天室信息及消息-->
        <?php
            // 获取群消息
            $sql = "SELECT * FROM group_{$_SESSION['gid']} ORDER BY msgid desc LIMIT 75";    // group_{$_SESSION['gid']} 为该群聊的数据表名
            $showmsg = $conn->query($sql);
            if ($showmsg->num_rows > 0)
            {
                while ($linemsg = $showmsg->fetch_assoc())
                {   
                    $sql = "SELECT * FROM Users WHERE showname='{$linemsg['who']}'";
                    $result = $conn->query($sql);
                    $w_uid = $result->fetch_assoc()["wan_uid"];    // 获取该条消息发送者的 wan_uid
                    // 是自己
                    if ($w_uid == $wid)    
                    {
                        echo "<br><div class='chat_box'><div id='".$linemsg["msgid"]."' class='me'><div class='me-inphp'>" .$linemsg["time"] . " #" . $linemsg["msgid"] . "<br>" ."<b>". "我" . "</b>" ."：". $linemsg["msg"] ."</div></div></div>";
                    }
                    // 不是自己
                    else
                    {
                        echo "<br><div class='chat_box'><div id='".$linemsg["msgid"]."' class='others'><div class='others-inphp'>" .$linemsg["time"] . " #" . $linemsg["msgid"] . "<br>" ."<b>". $linemsg["who"]. "</b>" ."：". $linemsg["msg"] ."</div></div></div>";
                    }
                }
            }
        ?>
    </body>
</html>
