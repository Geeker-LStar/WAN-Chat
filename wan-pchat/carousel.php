<!--轮播——文件解析-->
<!--总规则：一组三行，第一行是图片路径（时间），第二行是描述文字，第三行为空行。图片路径为 ./YYYY-MM-DD-XXXX.png。其中 XXXX 为图片编号，从 0001 到 9999。-->

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
    $table_name = "private_218_1102";
    $filename = "memory_carousel/$table_name/text.html";
    $handle = fopen($filename, "r");
    $contents = fread($handle, filesize($filename));    // 获取文件内容（一个字符串）
    fclose($handle);
    $temp = explode("<br>", $contents);
    // 即：1-3 为第一组，4-6 为第二组，1/2 分别为第一组的图片和文字，4/5 分别为二组的图片和文字。
    for ($i = 1; $i < count($temp)-1; $i++)
    {
        if ($i % 3 == 1)    // 即：是图片路径时
        {
            if ($i == 1)
            {
                $sql = "SELECT * FROM {$table_name} WHERE msgid=1";
                $result = $conn->query($sql);
                $first_time = $result->fetch_assoc["time"];    // 获取第一条消息（自动打招呼）的发送时间，也就是两人成为好友的时间
                $first_pic_time = substr($first_time, 0, 10);    // 获取前 10 位，也就是 YYYY-MM-DD，便于和 text.html 文件中的进行对比
                $first_time_in_doc = substr($temp[$i], 0, 10);    // 文档中第一张图片的时间
                if ($first_time_in_doc == $first_pic_time)    // 二者一样，说明用户已经替换了默认的第一张图片，否则没有
                    echo "<img src='memory_carousel/$table_name/$temp[$i]' style='height: 60px;'><p style='margin: 0px;'>$temp[$i]</p>";
                else    // 使用默认第一张
                    echo "<img src='memory_carousel/default_first.png' style='height: 256px;'><p style='margin: 0px;'>$temp[$i]</p>";
            }
            else
                echo "<img src='memory_carousel/$table_name/$temp[$i]'><p style='margin: 0px;'>$temp[$i]</p>";
        }
            
        if ($i % 3 == 2)
            echo $temp[$i] . "<br><br>";
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
        $new_after = $new_after . $arr[count($arr)];    // 这里必须分开，否则最后会多一个 <br>
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
        // 比第一个早，则直接在第一个之前插入
        if ($pic_time < $first_time)
        {
            // 在最开始插入（相当于在第一个 <br> 后插入）
            $pic_time = date("Y-m-d", $pic_time);
            $pic_url = $pic_time . "-0001.png";
            $all_info = $pic_url . "<br>" . $describe_text . "<br><br>";
            // 插入部分
            $new = insert($filename, $temp, 1, $all_info);
            echo($new);
        } 
        // 比最后一个晚，则直接追加到末尾
        else if ($pic_time > $last_time)
        {
            // 在末尾追加（相当于在最后一个 <br> 后插入）
            $pic_time = date("Y-m-d", $pic_time);
            $pic_url = $pic_time . "-0001.png";
            $all_info = $pic_url . "<br>" . $describe_text . "<br><br>";
            // 插入部分
            $new = insert($filename, $temp, count($temp)-1, $all_info);
            echo($new);
        } 
        else {
            // 其他情况，则遍历，找到合适的位置
            for ($i = count($temp)-4; $i > 0; $i-=3)    // 从最后一个日期开始（这样如果有相同的直接加 id 即可，不用再找 id），每次减 3 到前一个日期
            {
                $this_time = strtotime(substr($temp[$i], 0, 10));    // 这一张的时间
                $j = $i;    // 临时变量
                if ($i != 1) $pre_time = strtotime(substr($temp[$j-3], 0, 10));    // 前一张的时间（要排除 $i 为 1 的情况，否则会报错，不过当 $i 为 1 时，它要么已经在第一个 if 处处理完了，要么就是时间相等，也就是下一个 if。总之它不会涉及到下边的 elseif，所以排除 $i = 1 也不影响）
                // // 和这个一样
                if ($pic_time == $this_time)
                {
                    // 获取 id，加 1，插入（相当于在第 i 个 <br> 后插入）
                    $old_id = substr($temp[$i], 11, 15);    // 该项的最后一个 id
                    $pic_id = $old_id += 1;
                    for ($j = 0; $j = 4-(strlen($pic_id)); $j++) {$pic_id = "0" . $pic_id;}    // 自动补齐前边的 0
                    $pic_time = date("Y-m-d", $pic_time);
                    $pic_url = $pic_time . "-" . $pic_id . ".png";
                    $all_info = $pic_url . "<br>" . $describe_text . "<br><br>";
                    // 插入部分
                    $new = insert($filename, $temp, $i+3, $all_info);    // for 循环开始的部分减了 4，所以这里要加上 3（因为不考虑末尾空元素）
                    echo($new);
                    break;
                }
                // 比这个晚比前一个早
                elseif (($pic_time < $this_time) and ($pic_time > $pre_time))
                {
                    // 在这个之前插入（相当于在第 i 个 <br> 后插入）
                    $pic_time = date("Y-m-d", $pic_time);
                    $pic_url = $pic_time . "-0001.png";
                    $all_info = $pic_url . "<br>" . $describe_text . "<br><br>";
                    // 插入部分
                    $new = insert($filename, $temp, $i, $all_info);
                    echo($new);
                    break;
                } 
            }
        }
    }
    
    // add_memory("private_218_1102", "2023-01-09", "date");
?>
