<!--退出群聊的逻辑-->
<!--此处仅为（逻辑）代码，并不是真正的页面，POST 请求还没有完成-->

<?php
    if (isset($_POST["确定退出"]))
    {
        $exit_gid = $_POST["gid"];    // 获取退出的群聊的 gid
        // 从该群聊的群成员列表中删除该用户
        $sql = "SELECT * FROM All_Groups_Info WHERE wan_gid='{$exit_gid}'";
        $result = $conn->query($sql);
        $all_users = $result->fetch_assoc()["g_members"];
        $user = explode("//", $all_users);
        if (in_array($wid, $user))
            unset($user[$wid]);
        for ($i = 0; $i <= count($user); $i++)
            $new_users = $user[$i] . "//";
        $sql = "UPDATE All_Groups_Info SET g_members='{$new_users}' WHERE wan_gid='{$exit_gid}'";
        // 从该成员的【个人信息——已加入的群聊】中删除该群聊
        $sql = "SELECT * FROM Users WHERE wan_uid='{$wid}'";    // 查找该用户
        $result = $conn->query($sql);
        $mygrps = $result->fetch_assoc()["my_groups"];
        $grp = explode("//", $mygrps);    // 获取该用户加入的所有群聊（数组）
        unset($grp[$exit_gid]);    // 从数组中删除该群聊
        for ($i = 0; $i <= count($grp); $i++)
            $new_grps = $grp[$i] . "//";    // 更新【已加入的群聊】
        $sql = "UPDATE Users SET my_groups='{$new_grps}' WHERE wan_uid='{$wid}'";    // 写入数据库
    }
?>
