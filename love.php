<?php
    session_start();
    $ln = $_SESSION["loginname"];
    $time = $_SESSION["nowtime"];
?>

<html>
    <head><?php require "./config.php";?>
        <meta charset="utf-8">
        <title><?php echo $pagetitle;?></title>
        <link rel="stylesheet" href="<?php echo $_SESSION['loginname'];?>.css">
        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="https://ltx1102.com/<?php echo $line;?>/pjax/pjax.min.js"></script>
        <link rel="stylesheet" href="appbar/bar-box.css">
        <script src="appbar/bar-box.js"></script>
        <script src="time.js"></script>
    </head>
    
    <body>
        <script>
            function signout()
            {
                // alert("aiya~还没做好啦~我的小帅锅~❤️💖💕");
                // session.invalidate();
                // sessionStorage.clear();
                window.location.replace("login/login.php");
            }
        </script>
        
        <?php
            if (!isset($_SESSION["loginname"])) {
                header("location: login/login.php");
            }
        ?>
        <script>
        const mins_for_one_reload = 3;
            function heartbeat(){
                document.getElementById("reload_trigger").click();
            }
            setInterval(heartbeat,mins_for_one_reload*60*1000);
        </script>
        <style>
            body {
                background-image: url(bg/home_bg2.jpg);    /* 背景图片 */
                /* background-repeat: no-repeat;    /* 图片不重复显示 */
                /* background-attachment: fixed;    /* 图片不随页面滚动而滚动 */
                background-position: center;    /* 图片居中显示 */
                background-size: 100%;    /* 图片自动适应页面大小（随屏幕缩放） */
                padding: 0 30px;
                background-color: white;
            }

            div.xsg {
                font-family: "zyy_xmx";
            }
            
            div.xka{
                font-family: "nlt";
            }
            
            div.guest{
                font-family: "微软雅黑";
            }
            
            .me {
                margin-top: 10px;
                padding: 20px;
            }
            
            .others {
                margin-top: 10px;
                padding: 20px;
            }
            
            #pic {
                display: inline;
                margin-bottom: -25px;
                right: 10px;
                /*margin-right: 30px;*/
                width: 25px;
                height: 25px;
            }
            
            a {
                color: aqua;
            }
            
            a:hover {
                color: red;
            }
            
            #yy{
                color: red;
            }
            
        </style>
        <script>/*
            var instanceNotification = Notification || window.Notification;
            console.log(instanceNotification);
            if (instanceNotification) {
                var permissionNow = instanceNotification.permission;
                if (permissionNow === 'granted') {//允许通知
                   console.log('OK...');
                } else if (permissionNow === 'default') {
                    setPermission();
                } else if (permissionNow === 'denied') {
                    console.log('用户拒绝了你!!!');
                }  else {
                    setPermission();
                }
            }
        
            function setPermission() {
                //请求获取通知权限
                instanceNotification.requestPermission(function (PERMISSION) {
                    if (PERMISSION === 'granted') {
                        console.log('用户允许通知，耶。❤️');
                        createNotification();
                    } else {
                        console.log('用户拒绝，烦。委屈。呜呜呜。');
                    }
                });
            }
        
            function createNotification() {
                if (!window.Notification) {
                    alert("浏览器不支持通知");
                    return false;
                }
                console.log(window.Notification.permission);
                if (window.Notification.permission != 'granted') {
                    console.log('用户未开启通知权限!!!');
                    return false;
                }
                var instanceNotification = new Notification("你收到新消息了！", { "icon": "", "body": "快打开看看吧。","requireInteraction":true });
                instanceNotification.onshow = function () {
                    console.log("显示通知");
                    //3s后自动关闭通知
                    setTimeout(function () {
                        instanceNotification.close()
                    }, 3*1000);
                };
                instanceNotification.onclick = function () {
                    
                    window.open("https://ltx1102.com/<?php /*echo $line;*/?>/love.php");
                    instanceNotification.close();
                };
                instanceNotification.onclose = function () {
                    console.log("通知关闭");
                };
                instanceNotification.onerror = function () {
                    console.log('错误');
                };
            }
            //createNotification();*/
        </script>
        <?php
           date_default_timezone_set("PRC");
        $servername = "localhost";
      //李天星内心OS：我去？？username、password、dbname咋没了还不报错？哈哈，因为它们在./config.php里面啦～
 
        // 创建连接
        $conn = new mysqli($servername, $username, $password, $dbname);
        mysqli_query($conn,"SET NAMES UTF8MB4");
        // 检测连接
        if ($conn->connect_error)
        {
            die("连接失败: " . $conn->connect_error);
        } 
        ?>
        <div class="bar-box">
            <!-- 默认为 show，即第一次点击后会显示 -->
            <div class="dropbtn">
                <button onclick="show_bar()" id="showBar"><img src="appbar/show.jpg" class="dropbtn" id="#s"></button>
            </div>
            <div class="side" id="show">   
                <div class="sidebar">
                    <div class="sidebar-content">
                        <a href="https://xhcm.ltx1102.com/HomePage/homepage.php">首页</a>
                    </div>
                </div>
            </div>
        </div>
        <center><h1 style="font-family: 'zyy_xmx'; padding-top: 20px"><?php echo $title;?></h1>
        <?php echo $des;?></center>
        <script>
            setInterval(function(){clock(<?php $_SESSION["nowtime"]?>}, 1000);
        </script>
        <!--<center><span style="color: blue;">您已登录：<span id="time"></span></span></center><br>-->
        <center><button style="background-color: skyblue;" onclick="signout();"><h1>退出登录</h1></button></center>
        
        <form action="https://ltx1102.com/<?php echo $line;?>/love.php" method="post">
            <br><center>你是谁？<input type="text" name="who" id="who" value="<?php echo $_COOKIE['name'];?>"></center><br><br>
            <center>内容：<textarea name="txt" style="height: 150px; width: 350px; line-height: 1.5;" id="txt"></textarea><br><?php 
            if($_SESSION["loginname"]=="caozhiming"){
                
            echo "<span id='q'></span><br>";
            echo "<input type='hidden' id='kid' name='kid'><br>";
            echo "<textarea name='useranswer'></textarea><br>";
            }
            ?>
            </center>  <center><input type="submit" value="发送❤️"></center><br><br>
           
        </form><br><br>
        <script>
            <?php
            if ($_SESSION["loginname"]=="litianxing")
            {
                $whoami = "李天星";
            ?>
            document.getElementById("who").value="李天星";  
            <?php } 
            else if ($_SESSION["loginname"]=="caozhiming")
            { $whoami="曹智铭";
            ?>
             document.getElementById("who").value="曹智铭";
            <?php }
            ?>
            </script>
        <script>
           <?php if($_REQUEST["premsg"]!=''){
               $premsg=urldecode($_REQUEST["premsg"])
               ?>
            document.getElementById("txt").value="<?php echo $whoami."正在共享云文件。<br>" .$premsg;}?>";
           </script>
        <div id="reload">
            
        <?php
      //  if($_REQUEST['premsg']!=''){echo $premsg;}
     
        if(isset($_POST["delid"]))
        {
            $del_id=$_POST["delid"];
            $now=date('Y-m-d H:i:s');
            if($_SESSION["loginname"]=="litianxing"){
                $who="李天星";
            }
            else if ($_SESSION["loginname"]=="caozhiming"){
                $who="曹智铭";
            }
            else{
                header("location: https://ltx1102.com/xka-xsg/login/login.php");
            }
            $sqldel = "DELETE FROM Chat WHERE id='{$del_id}'";
            mysqli_query($conn, $sqldel);
            sleep(0.05); // well i know the server will not cause problems even if i dont sleep() here, but i think doing so is better
            $sqlinsert = "INSERT INTO Chat (name, time, text) VALUES ('系统', '{$now}', '{$who}撤回了一条消息。')";
          
            mysqli_query($conn, $sqlinsert);
            
            // header("location: love.php");
        }
        
        if(!empty($_POST["txt"]))
        {
            $who=$_POST['who'];
            $now=date('Y-m-d H:i:s');
            $txt=$_POST['txt'];
            
            // 判断是否重复消息，重复则直接 header 到本页面，不再发送。
            $sql0 = "SELECT * FROM Chat WHERE name='$who' ORDER BY id desc LIMIT 1";
            $result0 = $conn->query($sql0);
            $row0 = $result0->fetch_assoc();
            // 重复
            if ($row0["text"] == $txt)
                header("location: love.php");
            // 不重复
            else
            {
                if($_SESSION["loginname"]=="caozhiming"){
                    $send_kid=$_POST["kid"];
                    $sql_assert_k = "SELECT text FROM czm_knowledge WHERE id='{$send_kid}'";
                    $resultsk = $conn->query($sql_assert_k);
            $rowsk= $resultsk->fetch_assoc();
            $should_key = $rowsk["text"];
            $uans= $_POST["useranswer"];
            if(preg_match($should_key,$uans) || $uans=="love-magic"){
                $sql1 = "INSERT INTO Chat (name, time, text) VALUES ('{$who}', '{$now}', '{$txt}')";
                $conn->query($sql1);
            }
            else{
                echo "知识点回答错误，先去背完知识点，再来跟小可爱聊天。。。";
            }
            
                    
                }       
                else{
                $sql1 = "INSERT INTO Chat (name, time, text) VALUES ('{$who}', '{$now}', '{$txt}')";
                $conn->query($sql1);
                }
                // if ($conn->query($sql1) === TRUE)
                // {   
                //     echo "<script>
                //             alert('发送成功！');
                //         </script>";
                // } 
                // else 
                //     echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
        ?>
        <!--<div class="style">-->
        <!--    <div class="chatroom">-->
                <?php
                    if ($whoami == "曹智铭")
                    {
                        $sql2 = "SELECT * FROM Chat ORDER BY id desc LIMIT 75";
                        $result = $conn->query($sql2);
             
                        if ($result->num_rows > 0)
                        {
                            // 输出数据
                            while($row = $result->fetch_assoc())
                            {
                                if ($row["name"]=="李天星")
                                {
                                    echo "<br><img src='ltx.jpg' id='pic'><div class='chat_box'><div id='".$row["id"]."' class='xka'><div class= 'others'>" .$row["time"] . " #" . $row["id"] . "<br>" ."<b>".$row["name"]. "</b>" ."：". $row["text"] ."<br><a href='javascript:goto(".$row['id'].");' id='yy'>引用</a></div></div></div>";
                                }
                                else if ($row["name"]=="曹智铭")
                                {
                                    echo "<br><div class='chat_box'><div id='".$row["id"]."' class='xsg'><img src='czm.jpg' id='pic' style='float: right'><div class= 'me'>" .$row["time"] . " #" . $row["id"] . "<br>" ."<b>". "我" . "</b>" ."：". $row["text"] ."<br><a href='javascript:goto(".$row['id'].");' id='yy'>引用</a><form action='https://ltx1102.com/xka-xsg/love.php' method='post' style='
    margin-block-end: 0;'><input type='hidden' name='delid' value='".$row["id"]."'><input type='hidden' name='loginname' value='".$_SESSION["loginname"]."'><input type='submit' value='撤回'></form></div></div></div>";
                                }
                                else if ($row["name"]=="xsg")
                                {
                                    echo "<br><div class='chat_box'><div id='".$row["id"]."' class='xsg'><img src='czm.jpg' id='pic' style='float: right'><div class= 'me'>" .$row["time"]  . " #" . $row["id"] ."<br>" ."<b>". "我" . "</b>" ."：". $row["text"] ."<br><a href='javascript:goto(".$row['id'].");' id='yy'>引用</a></div></div></div>";
                                }
                                else if ($row["name"]=="xka")
                                {
                                    echo "<br><img src='ltx.jpg' id='pic'><div class='chat_box'><div id='".$row["id"]."' class='xka'><div class= 'others'>" .$row["time"]  . " #" . $row["id"] ."<br>" ."<b>".$row["name"]. "</b>" ."：". $row["text"] ."<br><a href='javascript:goto(".$row['id'].");' id='yy'>引用</a></div></div></div>";
                                }
                                else
                                {
                                    echo "<br><div class='chat_box'><div id='".$row["id"]."' class='guest'><div class= 'others'>" .$row["time"]  . " #" . $row["id"] ."<br>" ."<b>".$row["name"]. "</b>" ."：". $row["text"] ."<br><a href='javascript:goto(".$row['id'].");' id='yy'>引用</a></div></div></div>";
                                }
                            }
                        } 
                    }
                    elseif ($whoami == "李天星")
                    {
                        $sql2 = "SELECT * FROM Chat ORDER BY id desc LIMIT 75";
                        $result = $conn->query($sql2);
                 
                        if ($result->num_rows > 0)
                        {
                            // 输出数据
                            while($row = $result->fetch_assoc())
                            {
                                if ($row["name"]=="李天星")
                                {
                                    echo "<br><div class='chat_box'><div id='".$row["id"]."' class='xka'><img src='ltx.jpg' id='pic' style='float: right'><div class= 'me'>" .$row["time"] . " #" . $row["id"] . "<br>" ."<b>". "我" . "</b>" ."：". $row["text"] ."<br><a href='javascript:goto(".$row['id'].");' id='yy'>引用</a><form action='https://ltx1102.com/xka-xsg/love.php' method='post' style='
    margin-block-end: 0;'><input type='hidden' name='delid' value='".$row["id"]."'><input type='hidden' name='loginname' value='".$_SESSION["loginname"]."'><input type='submit' value='撤回'></form></div></div></div>";
                                }
                                else if ($row["name"]=="曹智铭")
                                {
                                    echo "<br><img src='czm.jpg' id='pic'><div class='chat_box'><div id='".$row["id"]."' class='xsg'><div class= 'others'>" .$row["time"] . " #" . $row["id"] . "<br>" ."<b>".$row["name"]. "</b>" ."：". $row["text"] ."<br><a href='javascript:goto(".$row['id'].");' id='yy'>引用</a></div></div></div>";
                                }
                                else if ($row["name"]=="xsg")
                                {
                                    echo "<br><img src='czm.jpg' id='pic'><div class='chat_box'><div id='".$row["id"]."' class='xsg'><div class= 'others'>" .$row["time"]  . " #" . $row["id"] ."<br>" ."<b>".$row["name"]. "</b>" ."：". $row["text"] ."<br><a href='javascript:goto(".$row['id'].");' id='yy'>引用</a></div></div></div>";
                                }
                                else if ($row["name"]=="xka")
                                {
                                    echo "<br><img src='ltx.jpg' id='pic'><div class='chat_box'><div id='".$row["id"]."' class='xka'><img src='ltx.jpg' id='pic' style='float: right'><div class= 'me'>" .$row["time"]  . " #" . $row["id"] ."<br>" ."<b>". "我" . "</b>" ."：". $row["text"] ."<br><a href='javascript:goto(".$row['id'].");' id='yy'>引用</a></div></div></div>";
                                }
                                else
                                {
                                    echo "<br><div class='chat_box'><div id='".$row["id"]."' class='guest'><div class= 'others'>" .$row["time"]  . " #" . $row["id"] ."<br>" ."<b>".$row["name"]. "</b>" ."：". $row["text"] ."<br><a href='javascript:goto(".$row['id'].");' id='yy'>引用</a></div></div></div>";
                                }
                            }
                        } 
                    }
                    else 
                    {
                        echo "[NO DATA]!!!";
                    }
                    $sentenceid=rand(1,17);
                     $chickensoup = "SELECT * FROM keepfighting WHERE id='{$sentenceid}'";
                  $a_bowl_of_soup = $conn->query($chickensoup);
                 
                        if ($a_bowl_of_soup->num_rows > 0)
                        {
                            // 输出数据
                            while($drink = $a_bowl_of_soup->fetch_assoc())
                            {?>
                                <script>
                                    document.getElementById("txt").placeholder="<?php echo $drink['text'].'——'.$drink['who'];?>";
                                    
                                </script>
                                
                            <?php
                                 if($_SESSION["loginname"]=="caozhiming"){
                
            $which_k= rand(1,7);
            $knowledge = "SELECT id,question FROM czm_knowledge WHERE id='{$which_k}'";
            $result_k = $conn->query($knowledge);
            $rk = $result_k->fetch_assoc();
            $kid=$rk["id"];
            $kq= $rk["question"];
                                     
                                 }
                            }
                        }?>
                    <script>
                        document.getElementById("q").innerHTML="<?php echo $kq;?>";
                        document.getElementById("kid").value="<?php echo $kid; ?>";
                    </script>
                    <?php
                    $conn->close();
                ?>
        <!--    </div>-->
        <!--</div>-->
        
        
        <script>
            function goto(msgid){
                document.getElementById("txt").value="<a href=\"#"+ msgid+'">'+"#"+msgid+"</a>";            }
        </script>
<script>
           <?php if($_REQUEST["premsg"]!=''){
               $premsg=urldecode($_REQUEST["premsg"])
               ?>
            document.getElementById("txt").value='<?php echo $whoami."正在共享云文件。<br>" .$premsg;}?>';
           </script>
        <center><h1><a href="https://ltx1102.com/<?php echo $line;?>/love.php" style="font-family: 'zyy_xmx';">点我刷新~&rarr;</a></h1></center>
         <script src="https://ltx1102.com/<?php echo $line;?>/main-pjax.js"></script>
         
         </div>
    </body>
</html>
