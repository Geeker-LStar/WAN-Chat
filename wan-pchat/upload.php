<!--上传文件-->

<html>
    <head>
        <?php
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->

        <meta charset="utf-8">
        <title>上传文件 - WAN</title>
    </head><body>
<?php
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $ta_wid = $_POST["ta_wid"];
    $fname = explode(".",$_FILES["file"]["name"]);
    $filetypename= $fname[1];
    // for example, if file name is ilovexka.mp4, then, $fname[0]= ilovexka, and $fname[1]=mp4, so explode() is really a good function
if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/pjpeg")))
  {
    if (file_exists("uploads/" . $_FILES["file"]["name"]))
      {
         echo "存在同名文件。";
         exit();
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "uploads/" . $_FILES["file"]["name"]);
      $ret= "<img src=\"uploads/" . $_FILES["file"]["name"]."\">";
      }
    
  }
  else if ($_FILES["file"]["type"]=="application/pdf"){
       if (file_exists("uploads/" . $_FILES["file"]["name"]))
      {
         echo "存在同名文件。";
         exit();
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "uploads/" . $_FILES["file"]["name"]);
      $ret="<a href=\"uploads/" . $_FILES["file"]["name"]."\">".$_FILES["file"]["name"]."</a>";
      }
  }
  else if($filetypename=="mp3" || $filetypename == "m4a"){
       if (file_exists("uploads/" . $_FILES["file"]["name"]))
      {
         echo "存在同名文件。";
         exit();
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "uploads/" . $_FILES["file"]["name"]);
      $ret="<audio controls=\"controls\"><source src=\"uploads/" . $_FILES["file"]["name"]."\" /></audio>";
      }
  }
  else if($filetypename=="mp4"){
       if (file_exists("uploads/" . $_FILES["file"]["name"]))
      {
         echo "存在同名文件。";
         exit();
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "uploads/" . $_FILES["file"]["name"]);
      $ret="<video controls=\"controls\"><source src=\"uploads/" . $_FILES["file"]["name"]."\" /></video>";
      }
  }
      
      else
  {
  if (file_exists("uploads/" . $_FILES["file"]["name"]))
      {
         echo "存在同名文件。";
         exit();
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "uploads/" . $_FILES["file"]["name"]);
      $ret= "<a href=\"uploads/" . $_FILES["file"]["name"]."\">".$_FILES["file"]["name"]."</a>";
      }
 
  
}$ret=urlencode($ret);
    ?>
    <script>
        window.location.href="chatroom.php?ta_wid=<?php echo $ta_wid;?>&premsg=<?php echo $ret;?>";
    </script>
    
    <?php
}
  else if($_SERVER["REQUEST_METHOD"]=="GET"){
      ?>
      <center><h1 style="font-family:'nlt';">文件中心</h1></center><br>
      <form action="upload.php" method="post" enctype="multipart/form-data">
          <input type="file" name="file">
          <button type="submit" class="btn btn-primary" value="上传">上传</button>
      </form>
      
      <?php
  }
?></body></html>