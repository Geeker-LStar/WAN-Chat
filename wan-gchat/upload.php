<html>
    <head>
        <?php
            session_start();
        ?>
        <?php require_once "../wan-config.php";?>
        <?php require_once "../wan-userinfo.php";?>    <!--用户个人信息-->

        <meta charset="utf-8">
        <title>上传文件</title>
    </head><body>
<?php
if($_SERVER["REQUEST_METHOD"]=="POST"){
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
      $ret= "<img src=\"https://wan.ltx1102.com/wan-gchat/uploads/" . $_FILES["file"]["name"]."\">";
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
      $ret="<a href=\"https://wan.ltx1102.com/wan-gchat/uploads/" . $_FILES["file"]["name"]."\">".$_FILES["file"]["name"]."</a>";
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
      $ret="<audio controls=\"controls\"><source src=\"https://wan.ltx1102.com/wan-gchat/uploads/" . $_FILES["file"]["name"]."\" /></audio>";
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
      $ret="<video controls=\"controls\"><source src=\"https://wan.ltx1102.com/wan-gchat/uploads/" . $_FILES["file"]["name"]."\" /></video>";
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
      $ret= "<a href=\"https://wan.ltx1102.com/wan-gchat/uploads/" . $_FILES["file"]["name"]."\">".$_FILES["file"]["name"]."</a>";
      }
 
  
}$ret=urlencode($ret);
    ?>
    <script>
        window.location.href="https://wan.ltx1102.com/wan-gchat/chatroom.php?premsg=<?php echo $ret;?>";
    </script>
    
    <?php
}
  else if($_SERVER["REQUEST_METHOD"]=="GET"){
      ?>
      <center><h1 style="font-family:'nlt';">文件中心</h1></center><br>
      <form action="https://wan.ltx1102.com/wan-gchat/upload.php" method="post" enctype="multipart/form-data">
          <input type="file" name="file">
          <input type="submit" value="上传">
      </form>
      
      <?php
  }
?></body></html>
