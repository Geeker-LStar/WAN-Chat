<!--私聊聊天室主页面-->

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
        
        <meta charset="utf-8">
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
            .reload {
                padding-bottom: 300px;
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
            .input_box {
                width: 100%;
                position: fixed;
                bottom: 0;
            }
        </style>
        
        <script>
            tinymce.init({
                selector: '#txt',    //表单控件.样式名称 - 绑定textarea
                height: "200",    // 高
                // width: "850",    // 宽
                toolbar_items_size: 'small',    // 控件大小
                menubar: true,   
                plugins: ['a11ychecker','advlist','advcode','advtable','autolink','checklist','export','code',
                        'lists','link','image','charmap','preview','anchor','searchreplace','visualblocks',
                        'powerpaste','fullscreen','formatpainter','insertdatetime','media','table','help','wordcount'],
                toolbar: 'undo redo | casechange blocks | bold italic backcolor link | alignleft aligncenter alignright alignjustify | bullist numlist        checklist outdent indent | removeformat | code table help', //控件区，显示控件
                language: 'zh-Hans',
                branding: false,
                forced_root_block:''    // 清除行尾的 p 标签
          });
        </script>
    </head>
    
    <body onload="bottom()">
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
        
        <!--获取准确的群聊名称-->
        <?php
            if (!isset($tablename))
            {
                $guess_name = "private_" . $wid . "_" . $ta_wid;
                $sql = "SELECT * FROM information_schema.TABLES WHERE TABLE_NAME='{$guess_name}'";
                $result = $conn->query($sql);
                $isornot = $result->num_rows;
                if ($isornot)
                    $tablename = $guess_name;
                else
                    $tablename = "private_" . $ta_wid . "_" . $wid;
            }
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
                    xx.scrollTop = xx.scrollHeight-300;
            }
        </script>
        
        <div id="upload" class="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">    <!-- 模态框头部 -->
                        <div class="modal-title">上传文件</div>
                        <button type="button" class="btn btn-close" data-bs-dismiss="modal"></button> 
                    </div>
                    
                    <div class="modal-body">    <!-- 模态框“身体”（内容）-->
                        <form action="upload.php" method="post" enctype="multipart/form-data">
                            <input type="file" name="file">
                            <input type="hidden" name="tablename" value="<?php echo $tablename;?>">
                            <input type="hidden" name="ta_wid" value=<?php echo $ta_wid;?>>
                            <button type="submit" class="btn btn-primary" value="上传">上传</button>
                        </form>
                    </div>
                    
                    <div class="modal-footer">    <!-- 模态框底部 -->
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">关闭</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="father" id="father">
            <div id="reload" style="margin-bottom: 300px;">
                <!--撤回消息-->
                <?php
                    if(isset($_REQUEST["delete"]))
                    {
                        $del_id = $_REQUEST["delid"];
                        $now = date('Y-m-d H:i:s');
                        $sqldel = "DELETE FROM {$tablename} WHERE msgid='{$del_id}'";
                        $conn->query($sqldel);
                        echo(mysqli_error($conn));
                        sleep(0.05); // well i know the server will not cause problems even if i dont sleep() here, but i think doing so is better
                        $sqlinsert = "INSERT INTO {$tablename} (who, time, msg) VALUES ('1024', '{$now}', '{$shown}撤回了一条消息。')";
                        $conn->query($sqlinsert);
                    }
                ?>
                
                <!--发送消息-->
                <?php
                    if(isset($_REQUEST["send"]))
                    {
                        if (!empty($_REQUEST["msg"]))
                        {   
                            // 获取消息
                            $msg = $_REQUEST["msg"];
                            // 重复消息检测
                            $sql = "SELECT * FROM {$tablename} WHERE who='{$wid}' ORDER BY msgid desc LIMIT 1";
                            $result = $conn->query($sql);
                            $row = $result->fetch_assoc()["msg"];
                            if ($row == $msg)
                                header("location: chatroom.php?ta_wid=$ta_wid");
                            else
                            {
                                $time = date("Y-m-d H:i:s");    // 当前时间
                                $sql = "INSERT INTO {$tablename} (who, time, msg) VALUES ('{$wid}', '{$time}', '{$msg}')";    // 插入新消息
                                $conn->query($sql);
                            }
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
                    $sql = "SELECT * FROM {$tablename} ORDER BY msgid DESC LIMIT 1";
                    /* GO By 2 STEPS: 1. fetch how many rows are there wholly. (Since we only need how many rows, we only need to SELECT msgid, not *, to reduce time.  2. Fetch some latest stuffs. */
                    $showmsg = $conn->query($sql);
                    if ($showmsg->num_rows > 0)
                    {
                        while ($linemsg = $showmsg->fetch_assoc())
                        {  
                            $rows = $linemsg["msgid"];
                            $rows = $rows - 50;
                            break;
                        }
                        $sql ="SELECT * FROM {$tablename} WHERE msgid > {$rows}";
                        $showmsg = $conn->query($sql);
                    }
                    if ($showmsg->num_rows > 0)
                    {
                        while ($linemsg = $showmsg->fetch_assoc())
                        {   
                            $w_uid = $linemsg["who"];    // 获取该条消息发送者的 wan_uid
                            // 是自己
                            if ($w_uid == $wid)    
                            {
                                echo "<br><div class='chat_box'><div id='".$linemsg["msgid"]."' class='me'><div class='me-inphp'>" . $linemsg["time"] . " #" . $linemsg["msgid"] . "<br>" ."<b>". "我" . "</b>" ."：". $linemsg["msg"] ."<br><br>
                                    <button class='btn btn-secondary btn-sm' onclick='goto(".$linemsg["msgid"].")' style='display: inline-block;'>引用</button>
                                    &nbsp;&nbsp;
                                    <form action='chatroom.php?ta_wid=$ta_wid' method='post' style='display: inline-block;'>
                                        <input type='hidden' value='".$linemsg["msgid"]."' name='delid'>
                                        <input name='delete' type='submit' class='btn btn-danger btn-sm' value='撤回'>
                                    </form></div></div></div>";
                            }
                            // 不是自己
                            else
                            {
                                if ($w_uid == null)    // 抄送
                                    echo "<br><div class='chat_box'><div id='".$linemsg["msgid"]."' class='bots'><div class='bots-inphp'>" .$linemsg["time"] . " #" . $linemsg["msgid"] . "<br>" ."<b>". $linemsg["who"] . "</b>" ."：". $linemsg["msg"] ."<br><br>
                                        <button class='btn btn-secondary btn-sm' onclick='goto(".$linemsg["msgid"].")' style='display: inline-block;'>引用</button></div></div></div>";
                                    
                                elseif ($w_uid == 1024)
                                    echo "<br><div class='chat_box'><div id='".$linemsg["msgid"]."' class='bots'><div class='bots-inphp'>" .$linemsg["time"] . " #" . $linemsg["msgid"] . "<br>" ."<b>". "WAN-Bot" . "</b>" ."：". $linemsg["msg"] ."<br><br>
                                        <button class='btn btn-secondary btn-sm' onclick='goto(".$linemsg["msgid"].")' style='display: inline-block;'>引用</button></div></div></div>";
                                    
                                else
                                {
                                    $sql = "SELECT * FROM Users WHERE wan_uid='{$w_uid}'";
                                    $result = $conn->query($sql);
                                    $sender = $result->fetch_assoc()["showname"];
                                    echo "<br><div class='chat_box'><div id='".$linemsg["msgid"]."' class='others'><div class='others-inphp'>" .$linemsg["time"] . " #" . $linemsg["msgid"] . "<br>" ."<b>". $sender . "</b>" ."：". $linemsg["msg"] ."<br><br>
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
            <center><div class="input_box" style="width: 80%; margin-left: 10%;">
                <br><center>
                <form onkeydown="keySend(event);" id="sendit" action="chatroom.php?ta_wid=<?php echo $ta_wid;?>" method="post">
                <div style="border-radius: 16px; overflow: hidden;"><textarea name="msg" value="msg" style="line-height: 1.5; width: 90%; height: 100px;" class="content form-control" id="txt"></textarea></div><br>
                <button id="send" name="send" type="submit" class="btn btn-primary" style="margin-bottom: 20px;" onclick="bottom()">发送</button><br>
                </form>
                <button class="btn btn-warning btn-sm" style="position: fixed; top: 15px; left: 15px;" data-bs-toggle="modal" data-bs-target="#upload">上传文件</button><br>
                
            </center>
            </div></center>
            
            <img src="../wan-static/img/back_bottom.jpg" style="width: 60px; height: 60px; position: fixed; bottom: 20px; right: 35px;" onclick="bottom()">
        </div>
        
        <script>
            <?php 
                if(isset($_REQUEST["premsg"]))
                {
                    $premsg=urldecode($_REQUEST["premsg"]);
                ?>
                    document.getElementById("txt").value='<?php echo $whoami."正在共享云文件。<br>" .$premsg;?>';
            <?php 
                }
            ?>
        </script>
        <script src="../wan-includes/main-pjax.js"></script>
        <script>
            function goto(msgid) {
                tinyMCE.activeEditor.setContent("<a href=\"#"+ msgid+'">'+"（引用第 "+msgid+" 条消息）</a>");
                // document.getElementById("txt").value="<a href=\"#"+ msgid+'">'+"（引用第 "+msgid+" 条消息）</a><br>";
            }
        </script>
        
        <?php require "../wan-footer.php";?>
    </body>
</html>
