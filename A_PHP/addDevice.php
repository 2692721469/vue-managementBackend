<?php
// 连接到数据库
$conn = new mysqli("localhost", "analyzer", "seXJHnyGxeXGRTcZ", "analyzer");

if ($conn->connect_error) {
    die("连接数据库失败: " . $conn->connect_error);
}

// 解析前端发送的 JSON 数据
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// 接收前端发送的数据
$On = $data['On'];
$notes = $data['notes'];
$classification = $data['classification'];
$SNsn = $data['SNsn'];
$IMEI = $data['IMEI'];

if ($On == true) {
    $On = '是';
} else {
    $On = '否';
}

if ($notes == '') {
    $notes = '-';
}
if ($classification == '') {
    $classification = '-';
}
if ($SNsn == '') {
    $SNsn = '-';
}
if ($IMEI == '') {
    $IMEI = '-';
}

// 防止 SQL 注入
$On = $conn->real_escape_string($On);
$notes = $conn->real_escape_string($notes);
$classification = $conn->real_escape_string($classification);
$SNsn = $conn->real_escape_string($SNsn);

// 开始事务
$conn->begin_transaction();

// 插入设备信息到 devices 表格
$sql = "INSERT INTO devices (whetherOn, notes, category, sn_number, imei_number) VALUES ('$On', '$notes', '$classification', '$SNsn', '$IMEI')";

if ($conn->query($sql) !== TRUE) {
    // 插入设备信息失败
    $conn->rollback();
    $response = array("success" => false, "message" => "设备添加失败: " . $conn->error);
} else {
    // 插入设备信息成功，获取插入的设备ID
    $device_id = $conn->insert_id;

    // 插入检测数据到 device_data 表格（示例数据）
    $detection_date = date('Y-m-d H:i:s'); // 当前日期和时间
    $detection_value = 0; // 您的检测数值

    $sql = "INSERT INTO device_data (device_id, detection_date, detection_value) VALUES ($device_id, '$detection_date', $detection_value)";

    if ($conn->query($sql) === TRUE) {
        // 插入检测数据成功
        $conn->commit();
        $response = array("success" => true, "message" => "设备添加成功");
    } else {
        // 插入检测数据失败
        $conn->rollback();
        $response = array("success" => false, "message" => "设备添加失败: " . $conn->error);
    }
}

// 关闭数据库连接
$conn->close();

// 返回 JSON 响应
header('Content-Type: application/json');
echo json_encode($response);
?>