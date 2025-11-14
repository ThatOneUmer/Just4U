<?php
// Database connection helper for Just4U Gaming
// Adjust credentials as needed for your local environment

if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'gaming_store');

function db_connect(): mysqli {
    static $conn = null;
    if ($conn instanceof mysqli) {
        return $conn;
    }

    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_errno) {
        http_response_code(500);
        die('Database connection failed: ' . htmlspecialchars($conn->connect_error));
    }

    // Ensure proper charset
    $conn->set_charset('utf8mb4');
    return $conn;
}

function db_find_user_by_email_or_username(string $identifier): ?array {
    $conn = db_connect();
    $sql = 'SELECT id, username, email, password_hash, role, first_name, last_name, avatar_url, is_verified, created_at FROM users WHERE email = ? OR username = ? LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $identifier, $identifier);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

function db_user_exists(string $email, string $username): bool {
    $conn = db_connect();
    $sql = 'SELECT 1 FROM users WHERE email = ? OR username = ? LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $email, $username);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

function db_create_user(string $username, string $email, string $password, string $role = 'customer'): array {
    $conn = db_connect();

    // Hash password
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    // sanitize role
    $role = ($role === 'seller') ? 'seller' : 'customer';

    // Customer status default active
    $isVerified = ($role === 'customer') ? 1 : 0;

    $sql = 'INSERT INTO users (username, email, password_hash, role, is_verified, created_at) VALUES (?, ?, ?, ?, ?, NOW())';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssi', $username, $email, $hash, $role, $isVerified);
    $ok = $stmt->execute();
    $err = $ok ? null : $stmt->error;
    $newId = $ok ? $stmt->insert_id : null;
    $stmt->close();

    return ['ok' => $ok, 'id' => $newId, 'error' => $err];
}

function db_create_seller(int $user_id, string $store_name, string $store_description = null): array {
    $conn = db_connect();
    $sql = 'INSERT INTO sellers (user_id, store_name, store_description, created_at) VALUES (?, ?, ?, NOW())';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $user_id, $store_name, $store_description);
    $ok = $stmt->execute();
    $err = $ok ? null : $stmt->error;
    $id = $ok ? $stmt->insert_id : null;
    $stmt->close();
    return ['ok' => $ok, 'id' => $id, 'error' => $err];
}

function db_get_user_by_id(int $id): ?array {
    $conn = db_connect();
    $sql = 'SELECT id, username, email, role, first_name, last_name, phone, avatar_url, is_verified, created_at, updated_at FROM users WHERE id = ? LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

function db_update_user_profile(int $id, string $username, string $email, ?string $first_name, ?string $last_name, ?string $phone, ?string $avatar_url): array {
    $conn = db_connect();
    $sql = 'SELECT id FROM users WHERE (email = ? OR username = ?) AND id <> ? LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $email, $username, $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return ['ok' => false, 'error' => 'Email or username already in use.'];
    }
    $stmt->close();

    $sql = 'UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ?, phone = ?, avatar_url = ?, updated_at = NOW() WHERE id = ? LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssi', $username, $email, $first_name, $last_name, $phone, $avatar_url, $id);
    $ok = $stmt->execute();
    $err = $ok ? null : $stmt->error;
    $stmt->close();
    return ['ok' => $ok, 'error' => $err];
}

function db_update_user_password(int $id, string $new_password): array {
    $conn = db_connect();
    $hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
    $sql = 'UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ? LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $hash, $id);
    $ok = $stmt->execute();
    $err = $ok ? null : $stmt->error;
    $stmt->close();
    return ['ok' => $ok, 'error' => $err];
}
