<?php
// require 'vendor/autoload.php'; // 引入jwt库
// use Firebase\JWT\JWT;

//获取Authorization--token
$token = $_SERVER['HTTP_AUTHORIZATION'];
// $secret = "HHSSPPEEFFMMAA406";
// try {
//     $decoded = JWT::decode($token, $secret, ['HS256']);
//     // 解密成功，处理解密后的数据
//     echo "解密成功！";
//     echo $decoded;
// } catch (Exception $e) {
//     // 解密失败，处理异常情况
//     echo "解密失败：" . $e->getMessage();
// }
if (!$token) {
    echo ("<!DOCTYPE html>
        <html>
            <head>
                <title>非法访问</title>
            </head>
            <body>
                <h1 style=\"display: flex;justify-content: center;color:red;\">非法访问！已拒绝！</h1>
            </body>
        </html>");
    // echo('非法访问！已拒绝！');
    // header('Location: ../login');
    die();
}


// 连接到数据库
$conn = new mysqli("localhost", "analyzer", "seXJHnyGxeXGRTcZ", "analyzer");

if ($conn->connect_error) {
    die("连接数据库失败: " . $conn->connect_error);
}

//////////////////////////// 查询设备位置信息
$sql = "SELECT sn_number, lng, lat FROM devices";
$result = $conn->query($sql);
if ($result === false) {
    die(json_encode(['error' => 'Query error']));
}
// 获取查询结果并转换为关联数组
$positionData = [];
while ($row = $result->fetch_assoc()) {
    $positionData[] = $row;
}

//////////////////////////// 查询检测数据
$dection = "SELECT device_sn, detection_value, detection_date FROM device_data";
$result = $conn->query($dection);
if ($result === false) {
    die(json_encode(['error' => 'Query error2']));
}
// 获取查询结果并转换为关联数组
$detectionData = [];
while ($row = $result->fetch_assoc()) {
    $device_sn = $row['device_sn'];

    // 根据 device_sn 查询 devices 表获取 category
    $categoryQuery = "SELECT category FROM devices WHERE sn_number = '$device_sn'";
    $categoryResult = $conn->query($categoryQuery);

    if ($categoryResult === false) {
        die(json_encode(['error' => 'Query error for category']));
    }

    // 获取 category
    $categoryRow = $categoryResult->fetch_assoc();
    $category = ($categoryRow !== null) ? $categoryRow['category'] : null;

    // 将数据添加到结果数组
    $detectionData[] = [
        'device_sn' => $device_sn,
        'detection_value' => $row['detection_value'],
        'detection_date' => $row['detection_date'],
        'category' => $category,
    ];
}
//////////////////////////////////////////////////////

////////////////////////////// // 查询所有设备的检测数据和对应的 category
$query = "SELECT dd.device_sn, dd.detection_value, dd.detection_date, dv.category
          FROM device_data dd
          JOIN devices dv ON dd.device_sn = dv.sn_number";

$result = $conn->query($query);

if ($result === false) {
    die(json_encode(['error' => 'Query error']));
}

// 处理查询结果
$chartDataList = [];
while ($row = $result->fetch_assoc()) {
    $device_sn = $row['device_sn'];
    $detection_value = floatval($row['detection_value']); // 转换为浮点数

    // 检查 chartDataList 数组中是否已经存在该设备的记录
    $index = array_search($device_sn, array_column($chartDataList, 'sn'));

    // 如果不存在，则添加新的记录
    if ($index === false) {
        $chartDataList[] = [
            'detection_date' => $row['detection_date'],
            'data' => [$detection_value],
            'sn' => $device_sn,
            'category' => $row['category'],
        ];
    } else {
        // 如果存在，则追加数据
        $chartDataList[$index]['data'][] = $detection_value;
    }
}















// 创建完整的 JSON 响应
$response = array(
    "positionData" => $positionData, //设备位置信息
    "detectionData" => $detectionData, //设备检测数据
    "chartDataList" => $chartDataList, //设备检测数据
);

// 发送 JSON 响应
header('Content-Type: application/json');
echo json_encode($response);


// 关闭数据库连接
$conn->close();

?>