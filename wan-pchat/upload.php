<!--上传文件-->
<!--两个用户会有一个单独的文件夹，避免所有文件都在 uploads 文件夹中，不安全且很乱-->

<html>
    <head>
        <?php
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->

        <meta charset="utf-8">
        <title>上传文件 - WAN</title>
    </head>
    
    <body>
        <?php 
            function upload_func ($position) 
            {
                if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/pjpeg")))
                {
                    if (file_exists("uploads/$position/" . $_FILES["file"]["name"]))
                    {
                        echo "存在同名文件。";
                        exit();
                    }
                    else
                    {
                        move_uploaded_file($_FILES["file"]["tmp_name"],
                        "uploads/$position/" . $_FILES["file"]["name"]);
                        $ret= "<img src=\"uploads/$position/" . $_FILES["file"]["name"]."\">";
                    }
                    
                }
                else if ($_FILES["file"]["type"]=="application/pdf"){
                    if (file_exists("uploads/$position/" . $_FILES["file"]["name"]))
                    {
                        echo "存在同名文件。";
                        exit();
                    }
                    else
                    {
                        move_uploaded_file($_FILES["file"]["tmp_name"],
                        "uploads/$position/" . $_FILES["file"]["name"]);
                        $ret="<a href=\"uploads/$position/" . $_FILES["file"]["name"]."\">".$_FILES["file"]["name"]."</a>";
                    }
                }
                else if($filetypename=="mp3" || $filetypename == "m4a"){
                    if (file_exists("uploads/$position/" . $_FILES["file"]["name"]))
                    {
                        echo "存在同名文件。";
                        exit();
                    }
                    else
                    {
                        move_uploaded_file($_FILES["file"]["tmp_name"],
                        "uploads/$position/" . $_FILES["file"]["name"]);
                        $ret="<audio controls=\"controls\"><source src=\"uploads/$position/" . $_FILES["file"]["name"]."\" /></audio>";
                    }
                }
                else if($filetypename=="mp4") 
                {
                    if (file_exists("uploads/$position/" . $_FILES["file"]["name"]))
                    {
                        echo "存在同名文件。";
                        exit();
                    }
                    else
                    {
                        move_uploaded_file($_FILES["file"]["tmp_name"],
                        "uploads/$position/" . $_FILES["file"]["name"]);
                        $ret="<video controls=\"controls\"><source src=\"uploads/$position/" . $_FILES["file"]["name"]."\" /></video>";
                    }
                }
                      
                else
                {
                    if (file_exists("uploads/$position/" . $_FILES["file"]["name"]))
                    {
                        echo "存在同名文件。";
                        exit();
                    }
                    else
                    {
                        move_uploaded_file($_FILES["file"]["tmp_name"],
                        "uploads/$position/" . $_FILES["file"]["name"]);
                        $ret= "<a href=\"uploads/$position/" . $_FILES["file"]["name"]."\">".$_FILES["file"]["name"]."</a>";
                    }
                }
                $ret = urlencode($ret);
                return $ret;
            }
        ?>
        
        <?php
            if($_SERVER["REQUEST_METHOD"]=="POST")
            {
                $tablename = $_POST["tablename"];    // 数据表名称，也就是存储这两个人私聊中文件的文件夹的名称（啊好绕）
                $ta_wid = $_POST["ta_wid"];
                $fname = explode(".", $_FILES["file"]["name"]);
                $filetypename= $fname[1];
                // for example, if file name is ilovexka.mp4, then, $fname[0]= ilovexka, and $fname[1]=mp4, so explode() is really a good function
                if (is_dir("uploads/$tablename/"))
                    $pre_ret = upload_func($tablename);
                else {
                    mkdir("uploads/$tablename/");
                    $pre_ret = upload_func($tablename);
                }
            }
            else if ($_SERVER["REQUEST_METHOD"] == "GET")
            {
        ?>
                <center><h1 style="font-family:'nlt';">文件中心</h1></center><br>
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="file">
                    <button type="submit" class="btn btn-primary" value="上传">上传</button>
                </form>
              
        <?php
            }
        ?>
        
        <script>
            window.location.href="chatroom.php?ta_wid=<?php echo $ta_wid;?>&premsg=<?php echo $pre_ret;?>";
        </script>
        
    </body>
</html>
