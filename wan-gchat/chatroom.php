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
        <!--<meta http-equiv="refresh" content="3">-->
        <script>
            MathJax = {
                tex: {
                    inlineMath: [['$', '$'], ['\\(', '\\)']]
                },
                svg: {
                    fontCache: 'global'
                }
            };
        </script>
        <!--<script type="text/javascript" id="MathJax-script" src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" ansyc></script>-->
        <script type="text/javascript" id="MathJax-script" src="../wan-includes/mathjax/mathjax.js" ansyc></script>
        <link rel="stylesheet" href="../wan-static/css/msg/me.css">
        <link rel="stylesheet" href="../wan-static/css/msg/others.css">
        <link rel="stylesheet" href="../wan-static/css/msg/bots.css">
        <link rel="stylesheet" href="../wan-static/css/bar-box.css">
        <script src="../wan-static/js/bar-box.js"></script>
        <script src="../wan-includes/tinymce/js/tinymce/tinymce.min.js"></script>
        <script>
            tinymce.init({
                selector: '#txt',    //表单控件.样式名称 - 绑定textarea
                height: "250",    //高
                width: "521",    //宽
                toolbar_items_size: 'small',    //控件大小
                menubar: true,    //是否显示菜单栏
                plugins: ["link code"],    //插件区，激活控件
                toolbar: "link code",     //控件区，显示控件
                language: 'zh-Hans',
                branding: false,
                forced_root_block:'',    // 清除行尾的 p 标签
          });
        </script>
        <style>
            html {
                height: 100%;
            }
            body {
                height: 100%;
                overflow: hidden;
            }
            .father {
                position: relative;
                height: 100%;
                overflow-y: auto;
            }
            /* 注：此处的 inphp 仅仅是为了和外部样式的文件名称区分开，没有其他作用和意思 */
            .me-inphp {
                padding: 20px;
            }
            
            .others-inphp {
                padding: 20px;
            }
            
            .bots-inphp {
                padding: 20px;
            }
        </style>
    </head>
    
    <body onload="bottom()">
        <!--<button onclick="bottom()">点我</button>-->
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
        
        <script>
            function emergency() {
                window.location.replace('../temp.php');
                return false;
            }
        </script>
        
        <!--滚动条固定在底部-->
        <script>
            function bottom() {
                var xx = document.getElementById("father");
                if (xx.scrollTop < xx.scrollHeight)
                    xx.scrollTop = xx.scrollHeight;
            }
        </script>

        <div class="father" id="father">
            <div id="reload">
                <!--撤回消息-->
                <?php
                    if(isset($_REQUEST["delid"]))
                    {
                        $del_id = $_REQUEST["delid"];
                        $now=date('Y-m-d H:i:s');
                        $sqldel = "DELETE FROM group_{$_SESSION['gid']} WHERE msgid='{$del_id}'";
                        $conn->query($sqldel);
                        sleep(0.05); // well i know the server will not cause problems even if i dont sleep() here, but i think doing so is better
                        $sqlinsert = "INSERT INTO group_{$_SESSION['gid']} (who, time, msg) VALUES ('1024', '{$now}', '{$shown}撤回了一条消息。')";
                        $conn->query($sqlinsert);
                        // header("location: a.php");
                    }
                ?>
                
                <!--发送消息-->
                <?php
                    if(isset($_REQUEST["msg"]))
                    {
                        if (!empty($_REQUEST["msg"]))
                        {   
                            // 获取消息
                            $msg = $_REQUEST["msg"];
                            // 重复消息检测
                            $sql = "SELECT * FROM group_{$_SESSION['gid']} WHERE who='{$wid}' ORDER BY msgid desc LIMIT 1";
                            $result = $conn->query($sql);
                            $row = $result->fetch_assoc()["msg"];
                            if ($row == $msg)
                                header("location: a.php");
                            else
                            {
                                $time = date("Y-m-d H:i:s");    // 当前时间
                                $sql = "INSERT INTO group_{$_SESSION['gid']} (who, time, msg) VALUES ('{$wid}', '{$time}', '{$msg}')";    // 插入新消息
                                $conn->query($sql);
                                if($_SESSION["gid"] == "928093895" || $_SESSION["gid"] == 928093895)
                                {
                                    $mhbty_mysql = new mysqli("localhost", "czmlab","LabWeb0725", "czmlab");
                                    $sql = "SELECT * FROM Users WHERE wan_uid='{$wid}'";
                                    $result = $conn->query($sql);
                                    $ushown = $result->fetch_assoc()["showname"];
                                    // $sender = $ushown . "（WAN）";
                                    $msg_m = $msg."<br>发自WAN远程服务器。";
                                    $sql_mhbty = "INSERT INTO Chat (name, time, text) VALUES ('{$ushown}', '{$time}', '{$msg_m}')";
                                    $mhbty_mysql->query($sql_mhbty);
                                }
                                header("location: a.php");
                            }
                        }
                        else
                        {
                            echo "<script>
                                alert('不能发送空白消息！');
                                window.location.href='a.php';
                            </script>";
                        }
                    }
                ?>
                
                <!--获取聊天室信息及消息-->
                <?php
                    $isadmin = "";
                    // 获取群消息
                    $sql = "SELECT * FROM group_{$_SESSION['gid']} ORDER BY msgid";    // group_{$_SESSION['gid']} 为该群聊的数据表名
                    $showmsg = $conn->query($sql);
                    if ($showmsg->num_rows > 0)
                    {
                        while ($linemsg = $showmsg->fetch_assoc())
                        {   
                            $w_uid = $linemsg["who"];    // 获取该条消息发送者的 wan_uid
                            // 是自己
                            if ($w_uid == $wid)    
                            {
                                echo "<br><div class='chat_box'><div id='".$linemsg["msgid"]."' class='me'><div class='me-inphp'>" . $linemsg["time"] . " #" . $linemsg["msgid"] . "<br>" ."<b>". "我" . $isadmin . "</b>" ."：". $linemsg["msg"] ."<br><br>
                                    <button class='btn btn-secondary btn-sm' onclick='goto(".$linemsg["msgid"].")' style='display: inline-block;'>引用</button>&nbsp;&nbsp;<form action='chatroom.php' method='post' style='display: inline-block;'><input name='delid' type='hidden' value='".$linemsg["msgid"]."'><button type='submit' class='btn btn-warning btn-sm'>撤回</button></form></div></div></div>";
                            }
                            // 不是自己
                            else
                            {
                                if ($w_uid == null)    // 抄送
                                    echo "<br><div class='chat_box'><div id='".$linemsg["msgid"]."' class='bots'><div class='bots-inphp'>" .$linemsg["time"] . " #" . $linemsg["msgid"] . "<br>" ."<b>". $linemsg["who"] . $isadmin . "</b>" ."：". $linemsg["msg"] ."<br><br>
                                        <button class='btn btn-secondary btn-sm' onclick='goto(".$linemsg["msgid"].")' style='display: inline-block;'>引用</button></div></div></div>";
                                    
                                elseif ($w_uid == 1024)
                                    echo "<br><div class='chat_box'><div id='".$linemsg["msgid"]."' class='bots'><div class='bots-inphp'>" .$linemsg["time"] . " #" . $linemsg["msgid"] . "<br>" ."<b>". "WAN-Bot" . $isadmin . "</b>" ."：". $linemsg["msg"] ."<br><br>
                                        <button class='btn btn-secondary btn-sm' onclick='goto(".$linemsg["msgid"].")' style='display: inline-block;'>引用</button></div></div></div>";
                                    
                                else
                                {
                                    $sql = "SELECT * FROM Users WHERE wan_uid='{$w_uid}'";
                                    $result = $conn->query($sql);
                                    $sender = $result->fetch_assoc()["showname"];
                                    echo "<br><div class='chat_box'><div id='".$linemsg["msgid"]."' class='others'><div class='others-inphp'>" .$linemsg["time"] . " #" . $linemsg["msgid"] . "<br>" ."<b>". $sender . $isadmin ."</b>" ."：". $linemsg["msg"] ."<br><br>
                                        <button class='btn btn-secondary btn-sm' onclick='goto(".$linemsg["msgid"].")' style='display: inline-block;'>引用</button></div></div></div>";
                                }
                            }
                        }
                    }
                    echo "<br>";
                    $chicken_farm = new mysqli("localhost","czmlab","LabWeb0725","czmlab");
                    $sentenceid=rand(1, 17);
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
        </div>
        
        <?php require "../wan-footer.php";?>
    </body>
</html>
