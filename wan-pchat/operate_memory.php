<!--处理 carousel 相关操作（添加/修改/删除）-->
<!--数据从 withta.php 传过来-->

<?php require_once("carousel.php"); ?>

<!--检测是否登录，未登录则要求登录（避免远程上传导致的不安全）-->
<?php 
    session_start();
?>
<?php
    if (!isset($_SESSION["wuid"]))
        header("location: ../wan-login/signin.php");
?>
<!--没有设置参数（没有设置 $ta_wid，所以直接跳转到选择好友页面）-->
<?php
    if (((!isset($_POST["add"])) and (!isset($_POST["modify"])) and (!isset($_POST["remove"]))) or (!isset($_POST["ta_wid"])))
        header("location: mine.php");
?>

<!--添加-->
<?php
    // 程序运行到这里，说明不满足第二个 if 的条件，也就是设定了 $ta_wid，所以出现错误时可直接跳转
    if (isset($_POST["add"]))
    {
        $ta_wid = $_POST["ta_wid"];
        if ((!isset($_POST["dscb"])) or (!isset($_POST["tbname"])))
        {
            $error = "ERROR: missing parameter.（错误：缺少参数）";
            header("location: withta.php?ta_wid=$ta_wid&error=$error");
        }
        if ($_FILES["pic"]["error"] > 0)
        {
            $error = "ERROR: " . $_FILES["pic"]["error"];
            header("location: withta.php?ta_wid=$ta_wid&error=$error");
        }
        $dscb_text = $_POST["dscb"];
        $tablename = $_POST["tbname"];
        $mmr_time = $_POST["mmr_time"];
        add_memory($tablename, $mmr_time, $_FILES["pic"]["tmp_name"], $dscb_text);
        $back_info = "add_success";
        header("location: withta.php?ta_wid=$ta_wid&response=$back_info");
    }
?>

<!--修改-->
<?php
    // 程序运行到这里，说明不满足第二个 if 的条件，也就是设定了 $ta_wid，所以出现错误时可直接跳转
    if (isset($_POST["modify"]))
    {
        $ta_wid = $_POST["ta_wid"];
        if ((!isset($_POST["new_dscb"])) or (!isset($_POST["tbname"])) or (!isset($_POST["pic_url"])))
        {
            $error = "ERROR: missing parameter.（错误：缺少参数）";
            header("location: withta.php?ta_wid=$ta_wid&error=$error");
        }
        if ($_FILES["new_pic"]["error"] > 0)
        {
            $error = "ERROR: " . $_FILES["new_pic"]["error"];
            header("location: withta.php?ta_wid=$ta_wid&error=$error");
        }
        $new_dscb_text = $_POST["new_dscb"];    // 新的描述文字
        $tablename = $_POST["tbname"];    // 数据表名（文件存储目录名）
        $pic_url = $_POST["pic_url"];    // 图片路径
        modify_memory($tablename, $_FILES["new_pic"]["tmp_name"], $pic_url, $new_dscb_text);
        $back_info = "modify_success";
        header("location: withta.php?ta_wid=$ta_wid&response=$back_info");
    }
?>

<!--删除-->
<?php
    // 程序运行到这里，说明不满足第二个 if 的条件，也就是设定了 $ta_wid，所以出现错误时可直接跳转
    if (isset($_POST["remove"]))
    {   
        $ta_wid = $_POST["ta_wid"];
        if ((!isset($_POST["tbname"])) or (!isset($_POST["pic_url"])))
        {
            $error = "ERROR: missing parameter.（错误：缺少参数）";
            header("location: withta.php?ta_wid=$ta_wid&error=$error");
        }
        $tablename = $_POST["tbname"];    // 数据表名（文件存储目录名）
        $pic_url = $_POST["pic_url"];    // 图片路径
        remove_memory($tablename, $pic_url);
        $back_info = "remove_success";
        header("location: withta.php?ta_wid=$ta_wid&response=$back_info");
    }
?>
