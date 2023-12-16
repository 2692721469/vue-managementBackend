<?php
// 连接到数据库
$conn = new mysqli("localhost", "analyzer", "seXJHnyGxeXGRTcZ", "analyzer");

if ($conn->connect_error) {
    die("连接数据库失败: " . $conn->connect_error);
}

// 查询在线设备数量
$queryOnline = "SELECT COUNT(*) AS online_count FROM devices WHERE online_status = 1";
$resultOnline = $conn->query($queryOnline);
$onlineCount = $resultOnline->fetch_assoc()['online_count'];

// 查询离线设备数量
$queryOffline = "SELECT COUNT(*) AS offline_count FROM devices WHERE online_status = 0";
$resultOffline = $conn->query($queryOffline);
$offlineCount = $resultOffline->fetch_assoc()['offline_count'];

// 查询上报事件数和故障信息数
$queryCountReportEvents = "SELECT COUNT(*) AS report_event_count FROM report_events";
$resultCountReportEvents = $conn->query($queryCountReportEvents);
$reportEventCount = $resultCountReportEvents->fetch_assoc()['report_event_count'];

$queryCountFaultEvents = "SELECT COUNT(*) AS fault_event_count FROM fault_events";
$resultCountFaultEvents = $conn->query($queryCountFaultEvents);
$faultEventCount = $resultCountFaultEvents->fetch_assoc()['fault_event_count'];

// 查询信息栏数据
$queryTableData = "SELECT devices.sn_number, devices.category, report_events.event_description AS message, report_events.event_time AS date
FROM devices
INNER JOIN report_events ON devices.id = report_events.device_id";
$resultTableData = $conn->query($queryTableData);

// 初始化 countData 数组
$countData = array(
    array(
        "name" => "onlineCount",
        "value" => $onlineCount,
    ),
    array(
        "name" => "offLineCount",
        "value" => $offlineCount,
    ),
    array(
        "name" => "reportEventCount",
        "value" => $reportEventCount,
    ),
    array(
        "name" => "error",
        "value" => $faultEventCount,
    )
);

// 初始化 tableData 数组
$tableData = array();

// 检查查询结果是否成功
if ($resultTableData) {
    // 循环获取查询结果的每一行数据
    while ($row = $resultTableData->fetch_assoc()) {
        $tableData[] = $row;
    }
}


/////////////////////////////////////// 查询在线设备列表 ///////////////////////////////////////
$queryOnlineDevices = "SELECT * FROM devices WHERE online_status = 1";
$resultOnlineDevices = $conn->query($queryOnlineDevices);

// 初始化一个数组来存储在线设备信息
$onlineDeviceList = array();

if ($resultOnlineDevices->num_rows > 0) {
    // 遍历查询结果，将设备信息添加到数组中
    while ($row = $resultOnlineDevices->fetch_assoc()) {
        $onlineDeviceList[] = array(
            "SNsn" => $row["sn_number"],
            "IMEI" => $row["imei_number"],
            "classification" => $row["category"],
            "notes" => $row["notes"],
            "firstDetectionTime" => $row["firstStartTime"],
            "timeInterval" => $row["timeInterval"]
        );
    }
}

/////////////////////////////////////// 查询离线设备列表 ///////////////////////////////////////
$queryOfflineDevices = "SELECT * FROM devices WHERE online_status = 0";
$resultOfflineDevices = $conn->query($queryOfflineDevices);

// 初始化一个数组来存储在线设备信息
$offlineDeviceData = array();

if ($resultOfflineDevices->num_rows > 0) {
    // 遍历查询结果，将设备信息添加到数组中
    while ($row = $resultOfflineDevices->fetch_assoc()) {
        $offlineDeviceData[] = array(
            "SNsn" => $row["sn_number"],
            "IMEI" => $row["imei_number"],
            "classification" => $row["category"],
            "notes" => $row["notes"],
            "firstDetectionTime" => $row["firstStartTime"],
            "timeInterval" => $row["timeInterval"]
        );
    }
}

/////////////////////////////////////// 查询故障信息列表 ///////////////////////////////////////
$queryerrorInfoData = "SELECT devices.sn_number, devices.category, fault_events.fault_description AS message, fault_events.fault_time AS date
FROM devices
INNER JOIN fault_events ON devices.id = fault_events.device_id";
$resulterrorInfoData = $conn->query($queryerrorInfoData);

// 初始化一个数组来存储在线设备信息
$errorInfoData = array();

// 检查查询结果是否成功
if ($resulterrorInfoData) {
    // 循环获取查询结果的每一行数据
    while ($row = $resulterrorInfoData->fetch_assoc()) {
        $errorInfoData[] = $row;
    }
}



// 创建完整的 JSON 响应
$response = array(
    "countData" => $countData,
    // 设备统计数据
    "onlineDeviceData" => $onlineDeviceList,
    // 在线设备列表数据
    "offlineDeviceData" => $offlineDeviceData,
    // 离线设备列表数据
    "errorInfoData" => $errorInfoData,
    //故障信息列表数据
    "tableData" => $tableData // 信息栏数据
);

// 发送 JSON 响应
header('Content-Type: application/json');
echo json_encode($response);


// 关闭数据库连接
$conn->close();

?>