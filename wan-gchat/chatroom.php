<!--群聊聊天室主页面-->

<!DOCTYPE html>
<html>
    <head>
        <?php
            $time_limit = 60*60*24*30;    // 过期时间为一个月
            session_set_cookie_params($time_limit);
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
            if (isset($_POST["gid"]))
                $_SESSION['gid'] = $_POST["gid"];
                $gid = $_POST["gid"];
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
        <link rel="stylesheet" href="../wan-static/css/me.css">
        <link rel="stylesheet" href="../wan-static/css/others.css">
        <link rel="stylesheet" href="../wan-static/css/bar-box.css">
        <script src="../wan-static/js/bar-box.js"></script>
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
        <!--导航栏-->
        <div class="bar-box">
            <!-- 默认为 show，即第一次点击后会显示 -->
            <div class="dropbtn">
                <button onclick="show_bar()" id="showBar"><img src="../wan-static/img/show.jpg" class="dropbtn" id="#s"></button>
            </div>
            <div class="side" id="show">   
                <div class="sidebar">
                    <div class="sidebar-content">
                        <!--<a href="https://xhcm.ltx1102.com/HomePage/homepage.php">首页</a>-->
                    </div>
                </div>
            </div>
        </div>
        
        <div class="container p-5">
            <center><h1><?php echo $gn;?></h1></center><br>
            <center><p><?php echo $gintrod; ?></p></center>
        </div>
        <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="mine.php" style="padding-left: 30px;">&larr;返回群聊列表</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="chatroom.php" id="reload_trigger" style="padding-left: 30px;">刷新</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="upload.php" style="padding-left: 30px;">上传文件</a>
                </li>
            </ul>
        </nav>
        
        <br><center><form onkeydown="keySend(event);" id="sendit" action="chatroom.php" method="post">
            <textarea name="msg" value="msg" style="height: 150px; width: 350px; line-height: 1.5;" class="form-control" id="txt"></textarea><br><br>
            <button id="send" type="submit" class="btn btn-primary">发送</button>
        </form></center>
        
        <script>
            function sbFrm() {
                var sendit=document.getElementById("sendit");
                var txtAr = sendit.getElementsByTagName("textarea")[0];
                if (txtAr.value == "") {
                    txtAr.focus();
                    return false;
                }
                sendit.submit();
                window.opener.afterReload();
                return false;
            }
            
            function keySend(event) {
                if (event.ctrlKey && event.keyCode == 13) {
                    sbFrm();
                }
            }
        </script>
        
        <!--检测是否登录，未登录则要求登录-->
        <?php
            if (!isset($_SESSION["wuid"]))
                header("location: ../wan-login/signin.php");
        ?>
        <div id="reload">
            <!--连接数据库-->
            <?php
                  if(isset($_POST["delid"]))
        {
            $del_id=$_POST["delid"];
            $now=date('Y-m-d H:i:s');
            $sqldel = "DELETE FROM group_{$gid} WHERE msgid='{$del_id}'";
            $conn->query($sqldel);
            sleep(0.05); // well i know the server will not cause problems even if i dont sleep() here, but i think doing so is better
            $sqlinsert = "INSERT INTO group_{$gid} (who, time, msg) VALUES ('系统', '{$now}', '{$shown}撤回了一条消息。')";
          
            $conn->query($sqlinsert);
            
        }
            ?>
            
            <!--发送消息-->
            <?php
                if(isset($_POST["msg"]))
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
            $isadmin = "";
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
                            echo "<br><div class='chat_box'><div id='".$linemsg["msgid"]."' class='me'><div class='me-inphp'>" .$linemsg["time"] . " #" . $linemsg["msgid"] . "<br>" ."<b>". "我" . $isadmin . "</b>" ."：". $linemsg["msg"] ."<br><button class='btn btn-secondary' onclick='goto(".$linemsg["msgid"].")'>引用</button>&nbsp;&nbsp;<form action='chatroom.php' method='post'><input name='delid' type='hidden' value='".$linemsg["msgid"]."'><button type='submit' class='btn btn-danger'>撤回</button></form></div></div></div>";
                        }
                        // 不是自己
                        else
                        {
                            echo "<br><div class='chat_box'><div id='".$linemsg["msgid"]."' class='others'><div class='others-inphp'>" .$linemsg["time"] . " #" . $linemsg["msgid"] . "<br>" ."<b>". $linemsg["who"]. $isadmin . "</b>" ."：". $linemsg["msg"] ."<br><button class='btn btn-secondary' onclick='goto(".$linemsg["msgid"].")'>引用</button></div></div></div>";
                        }
                    }
                }
                $chicken_farm = new mysqli("localhost","czmlab","LabWeb0725","czmlab");
                 $sentenceid=rand(1,17);
                     $chickensoup = "SELECT * FROM keepfighting WHERE id='{$sentenceid}'";
                  $a_bowl_of_soup = $chicken_farm->query($chickensoup);
                 
                        if ($a_bowl_of_soup->num_rows > 0)
                        {
                            // 输出数据
                            while($drink = $a_bowl_of_soup->fetch_assoc())
                            {?>
                                <script>
                                    document.getElementById("txt").placeholder="<?php echo $drink['text'].'——'.$drink['who'];?>";
                                    <?php }} ?>
                                    
                                </script>
            
        </div>
        
        <script>
                   <?php if($_REQUEST["premsg"]!=''){
                       $premsg=urldecode($_REQUEST["premsg"])
                       ?>
                    document.getElementById("txt").value='<?php echo $whoami."正在共享云文件。<br>" .$premsg;?>';<?php } ?>
                   </script>
        <script src="//wan.ltx1102.com/wan-includes/main-pjax.js"></script>
        <script>
                    function goto(msgid){
                        document.getElementById("txt").value="<a href=\"#"+ msgid+'">'+"#"+msgid+"</a>";            }
                        
                </script>
                 <script>
                 function ctrl_enter(){
                     document.getElementById("send").click();
                 }
                const mins_for_one_reload = 1;
                    function heartbeat(){
                        document.getElementById("reload_trigger").click();
                    }
                    setInterval(heartbeat,mins_for_one_reload*60*1000);
        </script>
        <?php require "../wan-footer.php";?>

    </body>
</html>
