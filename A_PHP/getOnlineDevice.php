<?php
//用于首页获取在线设备列表信息
// 连接到数据库
$conn = new mysqli("localhost", "username", "password", "your_database");

if ($conn->connect_error) {
    die("连接数据库失败: " . $conn->connect_error);
}

// 查询在线设备列表
$queryOnlineDevices = "SELECT * FROM devices WHERE is_online = 1";
$resultOnlineDevices = $conn->query($queryOnlineDevices);

// 初始化一个数组来存储在线设备信息
$onlineDeviceList = array();

if ($resultOnlineDevices->num_rows > 0) {
    // 遍历查询结果，将设备信息添加到数组中
    while ($row = $resultOnlineDevices->fetch_assoc()) {
        $onlineDeviceList[] = array(
            "SNsn" => $row["SNsn"],
            "IMEI" => $row["IMEI"],
            "classification" => $row["classification"],
            "notes" => $row["notes"],
            "firstDetectionTime" => $row["firstDetectionTime"],
            "timeInterval" => $row["timeInterval"]
        );
    }
}

// 关闭数据库连接
$conn->close();

// 发送 JSON 响应
header('Content-Type: application/json');
// 将在线设备列表数据以 JSON 格式返回
echo json_encode($onlineDeviceList);
?>