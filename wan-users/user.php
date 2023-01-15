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
        else
        {
            $sql = "SELECT * from Users WHERE wan_uid='{$_GET['wid']}'";
            $result = $conn->query($sql);
            if ($result->num_rows == 0)
                echo "<div class='container p-5'><center><h1>该用户不存在！</h1></center></div>";
            else
            {
                $user_arr = $result->fetch_assoc();
                $uwid = $user_arr["wan_uid"];
                $uphone = $user_arr["phone"];
                $uusern = $user_arr["username"];
                $ushown = $user_arr["showname"];
                $uemail = $user_arr["email"];
                $ugender = $user_arr["gender"];
                $uintrod = $user_arr["self_introd"];
            }
        }
    ?>
        
        <title><?php echo $ushown;?> - WAN 发现用户</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <!--打开模态框函数-->
        <script>
            function open_modal(modal_name) {
                $(document).ready(function(){ $('#'+modal_name).modal('show'); });
            }
        </script>
        <!--检测是否登录，未登录则要求登录-->
        <?php
            if (!isset($_SESSION["wuid"]))
                header("location: ../wan-login/signin.php");
            if(!isset($_GET["wid"]))
                header("location: search.php");
        ?>
    
        <!-- 填写验证消息模态框 -->
        <div class="modal fade" id="verification">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">验证</h4>
                        <button type="button" class="btn btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>请填写您的验证消息，并点击确认按钮：</p>
                        <form method="GET" action="user.php">
                            <input type="hidden" name="wid" value=<?php echo $uwid; ?>>
                            <input type="text" name="verify_itd" value="我是<?php echo $shown;?>...">
                            <input type="submit" name="add_as_frd" value="确认">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">取消</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 申请发送成功模态框 -->
        <div class="modal fade" id="vrf_ok">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">成功！</h4>
                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" onclick="window.location.href = 'user.php?wid=<?php echo $uwid; ?>'"></button>
                    </div>
                    <div class="modal-body">
                        <p>您的好友请求已发送，等待对方确认啦~</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="window.location.href = 'user.php?wid=<?php echo $uwid; ?>'">关闭</button>
                    </div>
                </div>
            </div>
        </div>
        
        <br>
        <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link" id="reload_trigger" href="search.php" style="padding-left: 30px;">&larr;返回查找用户</a>
                </li>
            </ul>
        </nav><br>
        
        <?php if ($uwid) { ?>
            <div class="container p-5">
                <h1><?php echo $ushown;?><span id="gender_auto"></span></h1>
                <h2>个人信息</h2>
                <?php
                
                    echo "<small><p style=\"color: gray\" data-bs-toggle=\"tooltip\" title=\"用户的唯一ID，WAN通过这个区分用户。\">wan-uid：{$uwid}</p></small>
                    <script>
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]'))
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                      return new bootstrap.Tooltip(tooltipTriggerEl)
                    })
                    </script>";
        
                    
                    // echo "手机号：" . $uphone;
                    // echo "<br><br>用户名：" . $uusern;
                    $time = time();    // 当前时间戳（作用：每次都会刷新图片，不会被缓存干扰）
                    echo "个人头像：<br><br>";
                    if (file_exists("../wan-myself/headimg/" . $uwid . ".png"))
                        echo "<img src='../wan-myself/headimg/$uwid.png?timestamp=$time' style='height: 100px; width: 100px;'><br><br>";
                    else
                        echo "<img src='../wan-myself/headimg/wan-default-head.png' style='height: 100px; width: 100px;'><br><br>";
                    echo "显示名：" . $ushown;
                    if (empty($uemail))
                        echo "<br><br>邮箱：未填写";
                    else 
                        echo "<br><br>邮箱：" . $uemail;
                    if (empty($uintrod))
                        echo "<br><br>个人简介：未填写";
                    else 
                        echo "<br><br>个人简介：" . $uintrod;
                ?>
                <br><br>
                
                <?php
                    // 检索该人是否为该用户的好友，若不是，则显示添加好友，否则显示进入聊天
                    $all_frd = explode("//", $myfrd);
                    if (!in_array($uwid, $all_frd))
                        echo "<button type=\"button\" class=\"btn btn-warning\" onclick=\"open_modal('verification')\">加为好友</button>";
                    else 
                        echo "<button type=\"button\" class=\"btn btn-warning\" onclick=\"window.location.href='../wan-pchat/chat.php?ta_wid=$uwid'\">进入聊天</button>";
                ?>
                <script>
                    var gender_db = "<?php echo $ugender;?>";
                    var gender_formatted = ((gender_db=="XX")?"姐姐":"哥哥");
                    document.getElementById("gender_auto").innerHTML = gender_formatted;
                </script>
            </div>
        <?php } ?>
        
        <!--申请加为好友-->
        <?php
            if (isset($_GET["add_as_frd"]))
            {
                // 在消息表中插入待确认的消息
                $time = date("Y-m-d H:i:s");
                $msg = "“<a href=\"../wan-users/user.php?wid=$wid\">" . $shown . "</a>” 申请添加你为好友。";
                $vrf = $_GET["verify_itd"];    // 身份信息
                $adding = "验证信息：" . $vrf;
                $sql = "INSERT INTO Private_Verify (sender, receiver, vrfmsg, detail, state, kind, isread, time) VALUES ('{$wid}', '{$uwid}', '{$msg}', '{$adding}', '待确认', 'add_friend', '0', '{$time}')";
                if ($conn->query($sql))
                    echo "<script>open_modal('vrf_ok')</script>";
                else
                    header("location: ../wan-static/html/failed.html");
            }
        ?>
        
        <?php require "../wan-footer.php";?>
    
    </body>
</html>
