<?php
require_once "../config/db.php";
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: access');
header('Access-Control-Allow-Methods: GET');
header("Content-Type: application/json; charset=UTF-8");

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

function isEmailUnique($email, $conn)
{
    $query = $conn->prepare("SELECT COUNT(*) FROM customers WHERE email = ?");
    $query->execute([$email]);
    $count = $query->fetchColumn();
    return $count == 0; // Returns true if email is unique, false otherwise
}

function isPhoneUnique($phone, $conn)
{
    $query = $conn->prepare("SELECT COUNT(*) FROM customers WHERE phone = ?");
    $query->execute([$phone]);
    $count = $query->fetchColumn();
    return $count == 0; // Returns true if phone is unique, false otherwise
}

function isEmailUniqueforUpdate($email, $id, $conn)
{
    $query = $conn->prepare("SELECT COUNT(*) FROM customers WHERE email = ? AND id = ?");
    $query->execute([$email, $id]);
    $count = $query->fetchColumn();
    return $count;
}

function isPhoneUniqueforUpdate($phone, $id, $conn)
{
    $query = $conn->prepare("SELECT COUNT(*) FROM customers WHERE phone = ? AND id = ?");
    $query->execute([$phone, $id]);
    $count = $query->fetchColumn();
    return $count;
}

// Handle incoming requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if email is provided in the request
    //Check for update
    $cus_id = $_GET['id'] ?? null;
    if ($cus_id !== null) {
        if (isset($_POST['email'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            // Check if email is unique
            $isUnique = isEmailUnique($email, $conn);
            $isUnique_update = isEmailUniqueforUpdate($email, $cus_id, $conn);

            if ($isUnique == true || $isUnique_update == 1) {
                $response = true;
            } elseif ($isUnique == false) {
                $response = false;
            }
            header('Content-Type: application/json');
            echo json_encode($response);
        } elseif (isset($_POST['phone'])) {
            $phone = filter_var($_POST['phone'], FILTER_SANITIZE_EMAIL);
            // Check if phone is unique
            $isUnique = isPhoneUnique($phone, $conn);
            $isUnique_update = isPhoneUniqueforUpdate($phone, $cus_id, $conn);

            if ($isUnique == true || $isUnique_update == 1) {
                $response = true;
            } elseif ($isUnique == false) {
                $response = false;
            }

            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // Return error response if email is not provided
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['error' => 'Email or Phone is missing']);
        }
    } else {
        //check for add
        if (isset($_POST['email'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            // Check if email is unique
            $isUnique = isEmailUnique($email, $conn);

            $isUnique ? $response = true : $response = false;

            header('Content-Type: application/json');
            echo json_encode($response);
        } elseif (isset($_POST['phone'])) {
            $phone = filter_var($_POST['phone'], FILTER_SANITIZE_EMAIL);
            // Check if phone is unique
            $isUnique = isPhoneUnique($phone, $conn);

            $isUnique ? $response = true : $response = false;

            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // Return error response if email is not provided
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['error' => 'Email or Phone is missing']);
        }
    }
    // if (isset($_POST['email'])) {
    //     $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    //     // Check if email is unique
    //     $isUnique = isEmailUnique($email, $conn);

    //     $isUnique ? $response = true : $response = false;

    //     header('Content-Type: application/json');
    //     echo json_encode($response);
    // } elseif (isset($_POST['phone'])) {
    //     $phone = filter_var($_POST['phone'], FILTER_SANITIZE_EMAIL);
    //     // Check if phone is unique
    //     $isUnique = isPhoneUnique($phone, $conn);

    //     $isUnique ? $response = true : $response = false;

    //     header('Content-Type: application/json');
    //     echo json_encode($response);
    // } else {
    //     // Return error response if email is not provided
    //     header("HTTP/1.1 400 Bad Request");
    //     echo json_encode(['error' => 'Email is missing']);
    // }
} else {
    // Return error response if request method is not POST
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['error' => 'Method Not Allowed']);
}
