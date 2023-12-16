<?php
// 数据库连接信息
$host = "localhost";
$username = "analyzer";
$password = "seXJHnyGxeXGRTcZ";
$database = "analyzer";

// SN编号
$sn_number = "SN001";

// 连接到数据库
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

// 查询数据
$sql = "SELECT d.sn_number, d.imei_number, dd.detection_date, dd.detection_value
        FROM devices AS d
        INNER JOIN device_data AS dd ON d.id = dd.device_id
        WHERE d.sn_number = '$sn_number'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = array(
            'sn_number' => $row['sn_number'],
            'imei_number' => $row['imei_number'],
            'detection_date' => $row['detection_date'],
            'detection_value' => $row['detection_value']
        );
    }
    // 将结果以JSON格式返回
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    // 返回没有找到匹配的设备数据
    echo json_encode(array('message' => '没有找到匹配的设备数据'));
}

// 关闭数据库连接
$conn->close();
?>
