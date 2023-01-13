<!--轮播——文件解析规则（协议）CAROUSEL_FILE_RESOLUTION_PROTOCOL，简称 CFRP-->
<!--总规则：text.html 文件第一行为总规则，其余的一组三行，第一行是图片路径（时间），第二行是描述文字，第三行为空行。图片路径为 ./YYYY-MM-DD-XXXX.png。其中 XXXX 为图片编号，从 0001 到 9999。-->

<?php require_once "../wan-config.php";?>

<!--连接数据库-->
<?php
    // 创建连接
    $conn = new mysqli($servername, $username, $password, $dbname);
    // 检测连接
    if ($conn->connect_error)
        die("连接失败：" . $conn->connect_error);
?>
 
<!--显示-->
<?php
    function show_memory($table_name)
    {
        $filename = "memory_carousel/$table_name/text.html";
        $handle = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));    // 获取文件内容（一个字符串）
        fclose($handle);
        $temp = explode("<br>", $contents);
        // 轮播——指示符
        echo "<div class='carousel-indicators'>";
        for ($i = 1; $i < count($temp)-1; $i += 3)    // 一次一组（三个）
        {
            $index = ($i-1)/3;    // 轮播索引（也就是第几组）
            if ($index == 0)    // 默认第一个是显示（active）的
                echo "<br><button type='button' data-bs-target='#memory' data-bs-slide-to='$index' class='active'>";
            else
                echo "<br><button type='button' data-bs-target='#memory' data-bs-slide-to='$index'>";
        }
        echo "</div>";
        // 轮播——图片
        // 即：1-3 为第一组，4-6 为第二组，1/2 分别为第一组的图片和文字，4/5 分别为二组的图片和文字。
        echo "<div class='carousel-inner'>";
        for ($i = 1; $i < count($temp)-1; $i += 3)    // 一次一组（三个）
        {
            $j = $i + 1;    // 图片对应的文字的索引
            $index = ($i-1)/3;    // 轮播索引（也就是第几组）
            if ($index == 0)    // 默认第一个是显示（active）的
            {
                $ta_wid = $_GET["ta_wid"];
                echo "<div class='carousel-item active'><img decoding='async' src='memory_carousel/$table_name/$temp[$i]' class='d-block' style='height: 256px; margin: 0 auto;'>
                    <center><br><p>$temp[$j]</p><br><br></center>
                    <form action='withta.php' method='get' style='display: inline-block;'>
                        <input type='hidden' name='ta_wid' value=$ta_wid>
                        <input type='hidden' name='event' value='modify'>
                        <input type='hidden' name='pic_url' value=$temp[$i]>
                        <input type=\"submit\" class=\"btn btn-warning\" data-bs-toggle='modal' data-bs-target='#modify_mmr' style='z-index: 1320; position: relative;' name='modify' value='修改'>
                    </form>
                    <form action='withta.php' method='get' style='display: inline-block;'>
                        <input type='hidden' name='ta_wid' value=$ta_wid>
                        <input type='hidden' name='event' value='remove'>
                        <input type='hidden' name='pic_url' value=$temp[$i]>
                        
                        <input type=\"submit\" class=\"btn btn-danger\" data-bs-toggle='modal' data-bs-target='#remove_mmr' style='z-index: 1320; position: relative;' name='remove' value='删除'>
                    </div>";
            }
            else
            {
                $ta_wid = $_GET["ta_wid"];
                echo "<div class='carousel-item'><img decoding='async' src='memory_carousel/$table_name/$temp[$i]' class='d-block' style='height: 256px; margin: 0 auto;'>
                    <center><br><p>$temp[$j]</p><br><br></center>
                    <form action='withta.php' method='get' style='display: inline-block;'>
                        <input type='hidden' name='ta_wid' value=$ta_wid>
                        <input type='hidden' name='event' value='modify'>
                        <input type='hidden' name='pic_url' value=$temp[$i]>
                        <input type=\"submit\" class=\"btn btn-warning\" data-bs-toggle='modal' data-bs-target='#modify_mmr' style='z-index: 1320; position: relative;' name='modify' value='修改'>
                    </form>
                    <form action='withta.php' method='get' style='display: inline-block;'>
                        <input type='hidden' name='ta_wid' value=$ta_wid>
                        <input type='hidden' name='event' value='remove'>
                        <input type='hidden' name='pic_url' value=$temp[$i]>
                        <input type=\"submit\" class=\"btn btn-danger\" data-bs-toggle='modal' data-bs-target='#remove_mmr' style='z-index: 1320; position: relative;' name='remove' value='删除'>
                    </div>";
            }
        }
        echo "</div>";
    }
?>

<!--添加-->
<?php
    function insert($fn, $arr, $br_id, $new_info)    // $br_id 是 <br> 标签的位置，即在第几个 <br> 后插入，$new_info 是新插入的内容
    {
        for ($i = 0; $i < $br_id; $i++)
            $new_before = $new_before . $arr[$i] . "<br>";    // 新插入的之前的
        for ($i = $br_id; $i < count($arr)-1; $i++)
            $new_after = $new_after . $arr[$i] . "<br>";    // 新插入的之后的
        $new_file_contents = $new_before . $new_info . $new_after;
        $open_link = fopen($fn, "w");
        fwrite($open_link, $new_file_contents);
        fclose($open_link);
        return $new_file_contents;    // 返回
    }
    // 在 withta.php 获取所有数据，该部分代码只负责找位置 & 写入。
    // 逻辑：获取用户选择的时间和输入的描述文字，随后按总规则和该项规则写入到这两个用户单独的文件夹下的 text.html 中
    // 该项规则：判断时间，插入到合适位置（即：比前一个晚比后一个早，时间一样就用 id）
    function add_memory($table_name, $pic_time, $describe_text)
    {
        // 通常情况用户下会按照时间顺序进行添加（不是所有人都像我一样乱七八糟（（（），所以从后往前比较时间可以有效减少比较次数
        $filename = "memory_carousel/$table_name/text.html";
        $handle = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        $temp = explode("<br>", $contents);
        fclose($handle);
        // 比较所需要的数据
        $pic_time = strtotime($pic_time);    // 这张的时间（转换为时间戳，便于比较大小（早晚））
        $last_time = strtotime(substr($temp[count($temp)-4], 0, 10));    // 最后一张的时间
        $first_time = strtotime(substr($temp[1], 0, 10));    // 第一张的时间
        // 该处使用的数据结构类似于链表
        // 【情况一】比第一个早，则直接在第一个之前插入
        if ($pic_time < $first_time)
        {
            // 在最开始插入（相当于在第一个 <br> 后插入）
            $pic_time = date("Y-m-d", $pic_time);
            $pic_url = $pic_time . "-0001.png";
            $all_info = $pic_url . "<br>" . $describe_text . "<br><br>";
            // 插入部分
            $new = insert($filename, $temp, 1, $all_info);
            // echo($new);
        } 
        // 【情况二】比最后一个晚，则直接追加到末尾
        else if ($pic_time > $last_time)
        {
            // 在末尾追加（相当于在最后一个 <br> 后插入）
            $pic_time = date("Y-m-d", $pic_time);
            $pic_url = $pic_time . "-0001.png";
            $all_info = $pic_url . "<br>" . $describe_text . "<br><br>";
            // 插入部分
            $new = insert($filename, $temp, count($temp)-1, $all_info);
            // echo($new);
        } 
        else {
            // 其他情况，则遍历，找到合适的位置
            for ($i = count($temp)-1; $i > 0; $i-=3)    // 从最后一个日期开始（这样如果有相同的直接加 id 即可，不用再找 id），每次减 3 到前一个日期
            {
                $this_time = strtotime(substr($temp[$i], 0, 10));    // 这一张的时间
                $j = $i;    // 临时变量
                if ($i != 1) $pre_time = strtotime(substr($temp[$j-3], 0, 10));    // 前一张的时间（要排除 $i 为 1 的情况，否则会报错，不过当 $i 为 1 时，它要么已经在第一个 if 处处理完了，要么就是时间相等，也就是下一个 if。总之它不会涉及到下边的 elseif，所以排除 $i = 1 也不影响）
                // 找插入的位置
                // 【情况三】和这个一样
                if ($pic_time == $this_time)
                {
                    // // 获取 id，加 1，插入（相当于在第 i 个 <br> 后插入）
                    $old_id = substr($temp[$i], 11, 15);    // 该项的最后一个 id
                    $pic_id = $old_id += 1;
                    for ($j = 0; $j = 4-(strlen($pic_id)); $j++) {$pic_id = "0" . $pic_id;}    // 自动补齐前边的 0
                    $pic_time = date("Y-m-d", $pic_time);
                    $pic_url = $pic_time . "-" . $pic_id . ".png";
                    $all_info = $pic_url . "<br>" . $describe_text . "<br><br>";
                    // // 插入部分
                    $new = insert($filename, $temp, $i+3, $all_info);    // 向后走三个元素（也就是一组）
                    // echo($new);
                    break;
                }
                // 【情况四】比这个晚比前一个早
                if (($pic_time < $this_time) and ($pic_time > $pre_time))
                {
                    // 在这个之前插入（相当于在第 i 个 <br> 后插入）
                    $pic_time = date("Y-m-d", $pic_time);
                    $pic_url = $pic_time . "-0001.png";
                    $all_info = $pic_url . "<br>" . $describe_text . "<br><br>";
                    // 插入部分
                    $new = insert($filename, $temp, $i, $all_info);
                    // echo($new);
                    break;
                } 
            }
        }
    }
?>

<!--修改-->
<?php
    // 在 withta.php 获取所有数据，该部分代码只负责找位置 & 写入。
    // 逻辑：获取原先那张（即：要被替换掉的）图片的路径（$pic_url），并将作为新图片（原路径为 $new_img）的路径（相当于覆盖），再在 text.html 中找到路径所在的位置以及所对应的文字描述，将文字替换为新文字（$new_d...）。
    function modify_memory($table_name, $new_img, $pic_url, $new_describe_text)
    {
        $filename = "memory_carousel/$table_name/text.html";    // 需要更改的文件的路径
        $handle = fopen($filename, "r");    // 读取文件内容
        $old_contents = explode("<br>", fread($handle, filesize($filename)));    // 原先的内容，用 <br> 分割成数组
        $pic_index = array_search($pic_url, $old_contents);    // 图片路径在数组中的索引
        $text_index = $pic_index + 1;    // 图片对应的文字的索引
        fclose($handle);
        // 更新
        move_uploaded_file($new_img, "memory_carousel/$table_name/$pic_url");    // 替换图片
        $handle = fopen($filename, "w");    // 写入文件
        $old_contents[$text_index] = $new_describe_text;
        for ($i = 0; $i < count($old_contents)-1; $i++)
            $new_contents = $new_contents . $old_contents[$i] . "<br>";
        fwrite($handle, $new_contents);
        fclose($handle);
    }
?>

<!--删除-->
<?php
    // 在 withta.php 获取所有数据，该部分代码只负责找位置 & 删除。
    // 逻辑：重新生成数组，排除需要删除的三项（路径 & 文字 & 空行）。
    function remove_memory($table_name, $pic_url)
    {
        $filename = "memory_carousel/$table_name/text.html";
        $handle = fopen($filename, "r");    // 读或写
        $old_contents = explode("<br>", fread($handle, filesize($filename)));    // 原先的内容，用 <br> 分割成数组
        $pic_index = array_search($pic_url, $old_contents);    // 图片路径在数组中的索引
        echo($pic_index);
        $text_index = $pic_index + 1;    // 图片对应的文字的索引
        echo($text_index);
        fclose($handle);
        // 更新
        $handle = fopen($filename, "w");    // 写入文件
        for ($i = 0; $i < count($old_contents)-1; $i++)
        {
            if (($i != $pic_index) and ($i != $text_index) and ($i != $text_index + 1))    // 相当于删掉了这三个（一组）
                $new_contents = $new_contents . $old_contents[$i] . "<br>";
        }
        fwrite($handle, $new_contents);
        fclose($handle);
    }
?>
