<?php
// 数据库连接信息
$host = "localhost";
$username = "analyzer";
$password = "seXJHnyGxeXGRTcZ";
$database = "analyzer";


// 连接到数据库
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

// 设备的唯一标识 (可以使用SN编号或IMEI编号)
$device_id = 1; // 替换为您要添加数据的设备的ID

// 数据
$detection_date = '2023-10-29'; // 替换为您要添加的日期
$detection_value = 25.5; // 替换为您要添加的数值

// 插入数据
$sql = "INSERT INTO device_data (device_id, detection_date, detection_value)
        VALUES ($device_id, '$detection_date', $detection_value)";

if ($conn->query($sql) === TRUE) {
    echo "成功添加数据。";
} else {
    echo "添加数据时发生错误：" . $conn->error;
}

// 关闭数据库连接
$conn->close();
?>
