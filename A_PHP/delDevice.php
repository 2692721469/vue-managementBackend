<?php
// 连接到数据库
$conn = new mysqli("localhost", "analyzer", "seXJHnyGxeXGRTcZ", "analyzer");

if ($conn->connect_error) {
    die("连接数据库失败: " . $conn->connect_error);
}

// 获取前端传来的设备唯一标识符
$sn_device = $_GET['sn_device'];

// 防止 SQL 注入
$sn_device = $conn->real_escape_string($sn_device);

// 开始事务
$conn->begin_transaction();

// 获取设备ID
$sql_get_device_id = "SELECT id FROM devices WHERE sn_number = '$sn_device'";
$result = $conn->query($sql_get_device_id);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $device_id = $row['id'];

    // 删除相关的检测数据
    $sql_delete_data = "DELETE FROM device_data WHERE device_id = $device_id";

    if ($conn->query($sql_delete_data) !== TRUE) {
        // 删除相关的检测数据失败
        $conn->rollback();
        $response = array("error" => false, "message" => "设备及其关联的数据删除失败: " . $conn->error);
    } else {
        // 删除相关的检测数据成功

        // 删除设备信息
        $sql_delete_device = "DELETE FROM devices WHERE sn_number = '$sn_device";

        if ($conn->query($sql_delete_device) === TRUE) {
            $conn->commit();
            $response = array("success" => true, "message" => "设备及其关联的数据删除成功");
        } else {
            $conn->rollback();
            $response = array("error" => false, "message" => "设备及其关联的数据删除失败: " . $conn->error);
        }
    }
} else {
    $conn->rollback();
    $response = array("error" => false, "message" => "设备信息未找到");
}

// 关闭数据库连接
$conn->close();

// 返回 JSON 响应
header('Content-Type: application/json');
echo json_encode($response);
?>