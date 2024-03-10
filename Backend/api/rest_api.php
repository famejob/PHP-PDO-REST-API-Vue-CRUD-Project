<?php
require_once "../config/db.php";
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: access');
header('Access-Control-Allow-Methods: GET');
header("Content-Type: application/json; charset=UTF-8");
$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true'); // Allow credentials
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, DELETE , PUT, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}, Content-Type"); // Allow Content-Type header
    } else {
        header("Access-Control-Allow-Headers: Content-Type"); // Fallback to only allow Content-Type header
    }
    exit(0);
}

if ($method === 'GET') {
    switch ($endpoint) {
        case 'customers':
            $sql = "SELECT * FROM customers";
            $query = $conn->prepare($sql);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
            http_response_code(200);
            break;
        case 'customer':
            $cus_id = $_GET['id'] ?? null;
            if ($cus_id !== null) {
                $sql = "SELECT * FROM customers WHERE id = :id";
                $query = $conn->prepare($sql);
                $query->execute(['id' => $cus_id]);
                $result = $query->fetch(PDO::FETCH_ASSOC);
                echo json_encode($result ? $result : []);
                http_response_code(200);
            } else {
                echo json_encode(['error' => 'Customer ID not provided']);
            }
            break;
        default:
            echo json_encode(['error' => 'Endpoint not found']);
            http_response_code(404);
            break;
    }
} elseif ($method === 'POST') {
    switch ($endpoint) {
        case 'customers':
            $data = json_decode(file_get_contents('php://input'), true);
            $first_name = $data['first_name'] ?? '';
            $last_name = $data['last_name'] ?? '';
            $email = $data['email'] ?? '';
            $phone = $data['phone'] ?? '';

            if (empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
                echo json_encode(['error' => 'firstname,lastname,email and phone are required']);
                http_response_code(400); // Bad Request
                exit;
            }

            $existingEmail = $conn->prepare("SELECT COUNT(*) FROM customers WHERE email = ?");
            $existingEmail->execute([$email]);
            $count = $existingEmail->fetchColumn();
            if ($count > 0) {
                echo json_encode(['error' => 'Email already exists']);
                http_response_code(409); // Conflict
                exit;
            }

            $existingPhone = $conn->prepare("SELECT COUNT(*) FROM customers WHERE phone = ?");
            $existingPhone->execute([$phone]);
            $count = $existingPhone->fetchColumn();

            if ($count > 0) {
                echo json_encode(['error' => 'Phone already exists']);
                http_response_code(409); // Conflict
                exit;
            }
            $sql = "INSERT INTO customers(first_name,last_name,email,phone) VALUE(:first_name,:last_name,:email,:phone)";
            $query = $conn->prepare($sql);
            $query->execute(['first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'phone' => $phone]);
            // Return success message
            echo json_encode(['message' => 'Customer added successfully']);
            http_response_code(201);
            break;
        default:
            echo json_encode(['error' => 'Endpoint not found']);
            http_response_code(404);
            break;
    }
} elseif ($method === 'PUT') {
    switch ($endpoint) {
        case 'customer':
            $data = json_decode(file_get_contents("php://input"), true);
            $cus_id = $_GET['id'] ?? null;
            if ($cus_id !== null) {
                $first_name = $data['first_name'] ?? '';
                $last_name = $data['last_name'] ?? '';
                $email = $data['email'] ?? '';
                $phone = $data['phone'] ?? '';
                if (empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
                    echo json_encode(['error' => 'firstname,lastname,email and phone are required']);
                    http_response_code(400); // Bad Request
                    exit;
                }
                $existingEmail = $conn->prepare("SELECT COUNT(*) FROM customers WHERE email = ?");
                $existingEmail->execute([$email]);
                $count = $existingEmail->fetchColumn();

                if ($count > 0) {
                    echo json_encode(['error' => 'Email already exists']);
                    http_response_code(409); // Conflict
                    exit;
                }

                $existingPhone = $conn->prepare("SELECT COUNT(*) FROM customers WHERE phone = ?");
                $existingPhone->execute([$phone]);
                $count = $existingPhone->fetchColumn();

                if ($count > 0) {
                    echo json_encode(['error' => 'Phone already exists']);
                    http_response_code(409); // Conflict
                    exit;
                }
                $sql = "UPDATE customers SET first_name=:first_name,last_name=:last_name,email=:email,phone=:phone WHERE id=:id";
                $query = $conn->prepare($sql);
                $query->execute(['first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'phone' => $phone, 'id' => $cus_id]);                // Return success message
                echo json_encode(['message' => 'Customer updated successfully']);
                http_response_code(201);
            } else {
                echo json_encode(['error' => 'Customer ID not provided']);
            }
            break;
        default:
            echo json_encode(['error' => 'Endpoint not found']);
            http_response_code(404);
            break;
    }
} elseif ($method === 'DELETE') {
    switch ($endpoint) {
        case 'customer':
            $cus_id = $_GET['id'] ?? null;
            if ($cus_id !== null) {
                $sql = "DELETE FROM customers WHERE id=:id";
                $query = $conn->prepare($sql);
                $query->execute(['id' => $cus_id]);
                echo json_encode(['message' => 'Customer deleted successfully']);
                http_response_code(200);
            } else {
                echo json_encode(['error' => 'Customer ID not provided']);
            }
            break;
        default:
            echo json_encode(['error' => 'Endpoint not found']);
            http_response_code(404);
            break;
    }
}
