// 正在开发中！！未开发完毕！！！不要直接用！！
// IT CANT WORK NOW!! DONT USE IT!!

<!DOCTYPE html>
<html>
    <head>
        <?php
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
        <script src="../wan-includes/tinymce/js/tinymce/tinymce.min.js"></script>
        <script>
            tinymce.init({
                selector: '#txt',    //表单控件.样式名称 - 绑定textarea
                height: "200",    //高
                // width: "521",    //宽
                toolbar_items_size: 'small',    //控件大小
                menubar: true,    //是否显示菜单栏
                plugins: ["link code"],    //插件区，激活控件
                toolbar: "link code",     //控件区，显示控件
                language: 'zh-Hans',
                branding: false,
                forced_root_block:'',    // 清除行尾的 p 标签
          });
        </script>
    </head>
    
    <body>
        <script>
            function bottom() {
                // alert(11);
                var div = document.getElementById("a");
                // div.innerHTML = div.innerHTML + 'news_' + new Date().getTime() + '<br />';
                // alert(div.scrollHeight);
                // alert(div.scrollTop);
                div.scrollTop = div.scrollHeight;
            }
        </script>
        
        <style>
            html, body {
                padding-top: 3px;
                height: 100%;
            }
            .left {
                display: inline;
                float: left;
                margin-left: 10px;
                width: 24%;
                height: 98.8%;
                border-radius: 10px;
                box-shadow: 0px 0px 10px black;
            }
            .right {
                overflow: hidden;
                margin-left: 25.5%;
                width: 73.5%;
                height: 98.8%;
                border-radius: 10px;
                box-shadow: 0px 0px 10px black;
            }
            .outer {
                height: 100vh;
                display: flex;
                flex-direction: column;
            }
            .head {
                flex-shrink: 0;
            }
            
            .content {
                flex: auto;
            }
        </style>
        
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
        <script src="//wan.ltx1102.com/wan-includes/main-pjax.js"></script>
        <script>
                    function goto(msgid) {
                        tinyMCE.activeEditor.setContent("<a href=\"#"+ msgid+'">'+"（引用第 "+msgid+" 条消息）</a>");
                    }
                </script>
        
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
        
        <div class="left">
            <div class="container p-5">
                <center><h1><?php echo $gn;?></h1><br>
                <p><?php echo $gintrod;?></p><br><hr></center>
            </div>
            <nav class="navbar navbar-expand-sm bg-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="mine.php" style="padding-left: 30px;">&larr;返回群聊列表</a>
                    </li>
                </ul>
            </nav>
            <nav class="navbar navbar-expand-sm bg-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="a.php" id="reload_trigger" style="padding-left: 30px;">刷新</a>
                    </li>
                </ul>
            </nav>
            <nav class="navbar navbar-expand-sm bg-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="upload.php" style="padding-left: 30px;">上传文件</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="right">
            <iframe src="chatroom.php?gid=<?php echo $_SESSION['gid'];?>" frameborder="0" scrolling="auto" class="content" style="width: 100%;"></iframe>
            <br><center><form onkeydown="keySend(event);" id="sendit" action="chatroom.php" method="post">
            <div style="border-radius: 10px; overflow: hidden;"><textarea name="msg" value="msg" style="line-height: 1.5; width: 80%;" class="form-control" id="txt"></textarea></div><br><br>
            <!--<div style="border-radius: 16px; overflow: hidden;"><textarea name="msg" value="msg" style="line-height: 1.5; width: 70%; height: 100px;" class="content form-control"></textarea></div><br>-->
            <button id="send" type="submit" class="btn btn-primary" style="margin-bottom: 20px;" onclick="bottom()">发送</button><br>
        </form></center>
        </div>
    
    </body>
</html>
