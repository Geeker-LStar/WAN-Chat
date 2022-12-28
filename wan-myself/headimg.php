<!--上传头像-->

<!DOCTYPE html>
<html>
    <head>
        <?php
            session_start();
        ?>
        <?php require_once "../wan-config.php"?>
        <?php require_once "../wan-userinfo.php"?>    <!--用户个人信息-->
        
        <title>上传头像 - WAN</title>
        <meta charset="utf-8">
    </head>
    
    <body>
        <!--检测是否登录，未登录则要求登录-->
        <?php
            if (!isset($_SESSION["wuid"]))
                header("location: ../wan-login/signin.php");
        ?>
        
        <div class="container p-5">
            <center><h1>上传头像</h1><br>
            <p>选择您的头像</p></center>
        </div>
        <nav class="navbar navbar-expand-sm bg-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link" id="reload_trigger" href="myself.php" style="padding-left: 30px;">&larr;返回个人中心</a>
                </li>
            </ul>
        </nav><br>
        <form method="post" action="headimg.php" enctype="multipart/form-data">
            <!--<input type="hidden" name="MAX_FILE_SIZE" value="10000">-->
            <input type="file" name="himg" value="选择一张图片">
            <input type="submit" name="choose" value="选定头像">
        </form>
        
        <?php
            if (isset($_POST["choose"]))
            {   
                $allowsuffix = array("jpg", "png", "jpeg", "gif");    // 允许上传的
                $name_temp = explode(".", $_FILES["himg"]["name"]);
                $extension = end($name_temp);    // 文件后缀名
                if (in_array($extension, $allowsuffix))    // 上传限制
                {
                    if ($_FILES["himg"]["error"] > 0)
                        echo "错误：" . $_FILES["himg"]["error"] . "<br>";
                    else
                    {
                        // 判断目标目录（权限设为 777）是否存在用户头像文件（即：用户是否已设置头像）
                        // 如果有则先删除
                        if (file_exists("headimg/" . $wid . ".png"))
                        {
                            if (unlink("headimg/" . $wid . ".png"))    // 先删除之前的文件（头像），如果删除成功则继续
                            {
                                move_uploaded_file($_FILES["himg"]["tmp_name"], "headimg/" . $wid . ".png");
                                echo "<script>alert('更新成功！')</script>";
                            }
                            else
                                echo "<script>alert('更新失败！')</script>";
                        }
                        else
                        {
                            move_uploaded_file($_FILES["himg"]["tmp_name"], "headimg/" . $wid . ".png");
                            echo "<script>alert('设置成功！')</script>";
                        }
                    }
                }
                else
                    echo "<script>alert('您只能上传后缀为 .png/.jpg/.jpeg/.gif 的文件！')</script>";
            }
        ?>
    </body>
</html>
