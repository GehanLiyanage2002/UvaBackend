<?php
// config/Database.php
class Database {
    private static ?mysqli $conn = null;

    public static function getConnection(): mysqli {
        if (self::$conn === null) {
            self::$conn = new mysqli('localhost', 'root', '', 'uva_pms');
            if (self::$conn->connect_error) {
                http_response_code(500);
                die(json_encode(['success'=>false, 'message'=>'DB connect failed']));
            }
            self::$conn->set_charset('utf8mb4');
        }
        return self::$conn;
    }
}
