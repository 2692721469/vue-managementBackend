<?php
$request_body = file_get_contents("php://input");
$data = json_decode($request_body, true);
header('Content-Type: application/json');

require 'vendor/autoload.php'; // 引入jwt库

use Firebase\JWT\JWT;

$secret_key = 'HHSSPPEEFFMMAA406'; // 你的密钥，应该保持安全

// 获取POST请求中的用户名和密码
$username = $data['username'];
$password = $data['password'];
// echo('username:'.$username);
// echo('password:'.$password);


// 数据库连接信息
$hostname = "localhost";
$username = "login";
$password = "wJPRC874NrrJYyNF";
$database = "login";

// 创建数据库连接
$conn = new mysqli($hostname, $username, $password, $database);

// 检查连接是否成功
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

// 输出收到的 POST 数据
$postData = file_get_contents('php://input');
file_put_contents('post_data.txt', $postData);

// 解析 JSON 数据为关联数组（默认行为）
$dataArray = json_decode($postData, true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 访问解析后的数据
    $loginInput = $dataArray['username'];
    $password = $dataArray['password'];

    // 根据输入的登录信息，构建查询语句
    $query = "SELECT * FROM users WHERE username = ? OR phone = ? OR email = ?";

    // 使用预处理语句来执行查询，防止 SQL 注入
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $loginInput, $loginInput, $loginInput);
    $stmt->execute();

    // 获取查询结果
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row["password"];

        // 验证密码
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['username'] = $loginInput;
            $response = array("message" => "登录成功");
            // 生成JWT令牌
            $issuer_claim = "119.91.64.37"; // 令牌的发行者
            $audience_claim = "WebApp"; // 令牌的受众
            $issuedat_claim = time(); // 令牌的发行时间
            $notbefore_claim = $issuedat_claim; // 令牌的生效时间
            $expire_claim = $issuedat_claim + 3600; // 令牌的过期时间（1小时）

            // 用户信息
            $user_data = array(
                "id" => 1,
                "username" => $username
            );

            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => $user_data
            );

            // 生成JWT令牌
            $jwt = JWT::encode($token, $secret_key, 'HS256');
            // 返回令牌作为响应
            echo json_encode(
                array(
                    "message" => "Successful login.",
                    "token" => $jwt
                )
            );
        } else {
            $response = array("error" => "用户名或密码错误");
        }
    } else {
        $response = array("error" => "用户名或密码错误");
    }

    echo json_encode($response);

    // 关闭数据库连接
    $stmt->close();
    $conn->close();

    // 将响应数据写入日志文件
    $logFilePath = "response_log.txt";
    file_put_contents($logFilePath, json_encode($response) . PHP_EOL, FILE_APPEND);
}


?>