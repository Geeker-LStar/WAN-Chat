<!--存储用户个人信息-->

<?php
    session_start();
    $_SESSION["wuid"];
?>

<?php
    require_once "wan-config.php";
    // 创建连接
    $conn = new mysqli($servername, $username, $password, $dbname);
    // 检测连接
    if ($conn->connect_error)
        die("连接失败" . $conn->connect_error);
?>

<?php
    $sql = "SELECT * from Users WHERE wan_uid='{$_SESSION['wuid']}'";
    $result = $conn->query($sql);
    $wid = $result->fetch_assoc()["wan_uid"];
    
    $sql = "SELECT * from Users WHERE wan_uid='{$_SESSION['wuid']}'";
    $result = $conn->query($sql);
    $phone = $result->fetch_assoc()["phone"];
    
    $sql = "SELECT * from Users WHERE wan_uid='{$_SESSION['wuid']}'";
    $result = $conn->query($sql);
    $usern = $result->fetch_assoc()["username"];
    
    $sql = "SELECT * from Users WHERE wan_uid='{$_SESSION['wuid']}'";
    $result = $conn->query($sql);
    $shown = $result->fetch_assoc()["showname"];
    
    $sql = "SELECT * from Users WHERE wan_uid='{$_SESSION['wuid']}'";
    $result = $conn->query($sql);
    $mygrp = $result->fetch_assoc()["my_groups"];
    
    $sql = "SELECT * from Users WHERE wan_uid='{$_SESSION['wuid']}'";
    $result = $conn->query($sql);
    $email = $result->fetch_assoc()["email"];
    
    $sql = "SELECT * from Users WHERE wan_uid='{$_SESSION['wuid']}'";
    $result = $conn->query($sql);
    $introd = $result->fetch_assoc()["self_introd"];
?>
