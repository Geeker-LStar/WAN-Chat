<!--用户和好友的专属页面-->

<!DOCTYPE html>
<html>
    <head>
        <?php 
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->
        <?php require_once "carousel.php";?>    <!-- 轮播处理 -->

        <?php
            if (isset($_REQUEST["ta_wid"]))
                $ta_wid = $_REQUEST["ta_wid"];
        ?>
        <!--未设置 $ta_wid（即：未选择好友）时，跳转回选择好友页面-->
        <?php
            if (!isset($_REQUEST['ta_wid']))
                header("location: mine.php");
        ?>
        <!--与该用户不是好友-->
        <?php
            if (!in_array($ta_wid, explode("//", $myfrd)))
                header("location: no_access.php?wid=$ta_wid");
        ?>
        <!--获取好友显示名-->
        <?php
            $sql = "SELECT * FROM Users WHERE wan_uid='{$ta_wid}'";
            $result = $conn->query($sql);
            $getn = $result->fetch_assoc();
            echo(mysqli_error($conn));
            $fn = $getn["showname"];    // 好友的显示名
            $fintrod = $getn["self_introd"];
        ?>
        <title>WAN - <?php echo $shown;?>和<?php echo $fn;?>的专属页面</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../wan-static/css/chat_media.css">
        <script src="../wan-includes/tinymce/js/tinymce/tinymce.min.js"></script>
    </head>
    
    <body>
        <!--打开模态框函数-->
        <script>
            function open_modal(modal_name) {
                $(document).ready(function(){ $('#'+modal_name).modal('show'); });
            }
        </script>
        
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
        
        <?php
            // 事件
            if (isset($_GET["event"]))
            {
                if ($_GET["event"] == "add")
                    echo "<script>open_modal('add_mmr')</script>";
                // 修改
                if ($_GET["event"] == "modify")
                    echo "<script>open_modal('modify_mmr')</script>";
                // 删除
                if ($_GET["event"] == "remove")
                    echo "<script>open_modal('remove_mmr')</script>";
            }
            // 事件执行成功
            if (isset($_GET["response"]))
            {
                $rsps = $_GET["response"];
                echo "<script>open_modal('$rsps')</script>";
            }
            // if (isset($_GET["error"]))
            // {
                
            // }
        ?>
        
        <?php
            // 认识天数
            function days($db_obj, $tbname)
            {
                $sql = "SELECT * FROM {$tbname} WHERE msgid=1";
                $result = $db_obj->query($sql);
                $first_day = $result->fetch_assoc()["time"];
                $days = (strtotime(date("Y-m-d"))-strtotime(substr($first_day, 0, 10))) / (60*60*24);
                return $days;
            }
            // 获取互相发消息的条数
            function msg_num($db_obj, $tbname)
            {
                $sql = "SELECT * FROM {$tbname} ORDER BY msgid DESC LIMIT 1";
                $result = $db_obj->query($sql);
                $msg_num = $result->fetch_assoc()["msgid"];
                return $msg_num;
            }
            // 上次发消息时间
            function last_time($db_obj, $tbname)
            {
                $sql = "SELECT * FROM {$tbname} ORDER BY msgid DESC LIMIT 1";
                $result = $db_obj->query($sql);
                $last_time = $result->fetch_assoc()["time"];
                return $last_time;
            }
            
        ?>
        
        <div class="p-5">
            <img src="../wan-myself/headimg/<?php echo $wid;?>.png" style="border-radius: 50%; width: 132px; height: 132px; ">
            <img src="../wan-myself/headimg/<?php echo $ta_wid;?>.png" style="border-radius: 50%; width: 132px; height: 132px; ">
            <h1 style="display: inline;"><?php echo $shown;?>和<?php echo $fn;?>的专属页面</h1>
        </div>
        
        <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../wan-myself/myself.php" style="padding-left: 30px;">&larr;返回个人中心</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../wan-pchat/mine.php" style="padding-left: 30px;">&larr;返回好友列表</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../wan-pchat/chat.php?ta_wid=<?php echo $ta_wid;?>" style="padding-left: 30px;">和 TA 聊天</a>
                </li>
            </ul>
        </nav>
        <style>
            .main {
                height: auto;
            }
            .info_card {
                float: left;
                display: inline-block;
                margin-left: 20px;
                padding: 25px;
                width: 30%;
                background-color: #f9f0ee;
                border-radius: 10px;
                box-shadow: 10px 10px 10px gray;
            }
            .pic_carousel {
                margin-left: 34%;
                padding: 25px;
                width: 63.5%;
                background-color: skyblue;
                border-radius: 10px;
                box-shadow: 10px 10px 10px gray;
            }
        
        </style>
        
        <div class="main" style="margin-top: 20px;">
            <div class="info_card">
                <h1>这是我们认识的第 <?php echo $days=days($conn, $tablename);?> 天</h1>
                <p>我们互相发了 <?php echo $msg=msg_num($conn, $tablename);?> 条消息</p>
                <p>我们最近一次联系是在 <?php echo $last=last_time($conn, $tablename);?></p>
            </div>
            <div class="pic_carousel">
                <h1>照片轮播（标题待定，但不是这个）</h1>
                <p>我们的回忆：</p>
                <!--大概思路：php 读取 text.html 文件，以<br>作为分隔符，显示图片，文字覆盖在图片上方-->
                <!--轮播-->
                <div id="memory" class="carousel slide">
                    <!--指示符和轮播图片（另一个 PHP 中实现）-->
                    <?php show_memory($tablename); ?>
                    <!--左右切换按钮-->
                    <button class="carousel-control-prev" type="button" data-bs-target="#memory" data-bs-slide="prev" style="color: black;">< 上一张</button>
                    <button class="carousel-control-next" type="button" data-bs-target="#memory" data-bs-slide="next" style="color: black;">下一张 ></button>
                </div>
            </div>
            <button type="button" class="btn btn-info" onclick="window.location.href = 'withta.php?ta_wid=<?php echo $ta_wid;?>&event=add'" style="position: relative; z-index: 1320;">添加回忆</button>
        </div>
        
        <!--添加记忆——模态框-->
        <div class="modal fade" id="add_mmr">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">添加</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="window.location.href='withta.php?ta_wid=<?php echo $ta_wid;?>'"></button>
                    </div>
                    <div class="modal-body">
                        <p>和 TA 有什么新的回忆呀~？</p>
                        <form action="operate_memory.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="ta_wid" value=<?php echo $ta_wid;?>>
                            <label for="time" class="mb-1">这个回忆是在什么时候呀（选择时间）？</label>
                            <br><input type="date" name="mmr_time"><br><br>
                            <label for="pic" class="mb-1">定格一个瞬间吧~（图片）：</label>
                            <br><input type="file" name="pic" id="pic"><br><br>
                            <label for="dscb" class="mb-1">简单记录一下这个回忆吧~（文字）：</label>
                            <br><input type="text" name="dscb" class="form-control" id="dscb"><br>
                            <input type="hidden" name="tbname" value=<?php echo $tablename;?>>
                            <input type="submit" name="add" value="添加">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="window.location.href='withta.php?ta_wid=<?php echo $ta_wid;?>'">取消</button>
                    </div>
                </div>
            </div>
        </div>
        <!--添加成功——模态框-->
        <div class="modal fade" id="add_success">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">成功！</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="window.location.href='withta.php?ta_wid=<?php echo $ta_wid;?>'"></button>
                    </div>
                    <div class="modal-body">
                        <p>添加成功啦~快邀请 TA 一起来看吧~</p>
                        <!--加一个和 ta 分享的功能-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="window.location.href='withta.php?ta_wid=<?php echo $ta_wid;?>'">关闭</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!--修改记忆——模态框-->
        <div class="modal fade" id="modify_mmr">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">修改</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="window.location.href='withta.php?ta_wid=<?php echo $ta_wid;?>'"></button>
                    </div>
                    <div class="modal-body">
                        <p>请填写新的信息并点击 “确认修改” 按钮。</p>
                        <form action="operate_memory.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="ta_wid" value=<?php echo $ta_wid;?>>
                            <label for="new_pic" class="mb-1">新的图片：</label>
                            <br><input type="file" name="new_pic" id="new_pic" value=<?php echo $_GET["pic_url"]?>><br><br>
                            <label for="new_dscb" class="mb-1">新的文字描述：</label>
                            <br><input type="text" name="new_dscb" class="form-control" id="new_dscb" value=<?php echo $_GET["old_dscb"]?>>
                            <input type="hidden" name="tbname" value=<?php echo $tablename;?>>
                            <input type="hidden" name="pic_url" value=<?php echo $_GET["pic_url"]?>><br>
                            <input type="submit" name="modify" value="确认修改">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="window.location.href='withta.php?ta_wid=<?php echo $ta_wid;?>'">取消</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!--修改成功——模态框-->
        <div class="modal fade" id="modify_success">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">成功！</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="window.location.href='withta.php?ta_wid=<?php echo $ta_wid;?>'"></button>
                    </div>
                    <div class="modal-body">
                        <p>修改成功~</p>
                        <!--加一个和 ta 分享的功能-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="window.location.href='withta.php?ta_wid=<?php echo $ta_wid;?>'">关闭</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!--删除记忆——模态框-->
        <div class="modal fade" id="remove_mmr">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">删除</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="window.location.href='withta.php?ta_wid=<?php echo $ta_wid;?>'"></button>
                    </div>
                    <div class="modal-body">
                        <p>您真的要删除这条回忆吗？</p>
                        <form action="operate_memory.php" method="post" style="display: inline-block;">
                            <input type="hidden" name="ta_wid" value=<?php echo $ta_wid;?>>
                            <input type="hidden" name="tbname" value=<?php echo $tablename;?>>
                            <input type="hidden" name="pic_url" value=<?php echo $_GET["pic_url"]?>>
                            <input type="submit" name="remove" value="确认删除">
                        </form>
                        <input type="button" value="暂不删除" data-bs-dismiss="modal" onclick="window.location.href='withta.php?ta_wid=<?php echo $ta_wid;?>'">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="window.location.href='withta.php?ta_wid=<?php echo $ta_wid;?>'">取消</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!--删除成功——模态框-->
        <div class="modal fade" id="remove_success">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">成功！</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="window.location.href='withta.php?ta_wid=<?php echo $ta_wid;?>'"></button>
                    </div>
                    <div class="modal-body">
                        <p>删除成功。</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="window.location.href='withta.php?ta_wid=<?php echo $ta_wid;?>'">关闭</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
