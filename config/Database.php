<?php
// config/Database.php
class Database {
    private static ?mysqli $conn = null;

    public static function getConnection(): mysqli {
        if (self::$conn === null) {
            // Azure Flexible Server details
            $host = "myappdb123.mysql.database.azure.com";   // replace with your server name
            $username = "AzureAdmin";             // replace with your admin user
            $password = "Gehan@123";                     // replace with your actual password
            $dbname = "uva_pms";                         // your database name

            // Create connection with SSL
            self::$conn = mysqli_init();
            mysqli_ssl_set(self::$conn, NULL, NULL, "/var/www/html/DigiCertGlobalRootCA.crt.pem", NULL, NULL);

            if (!mysqli_real_connect(self::$conn, $host, $username, $password, $dbname, 3306, MYSQLI_CLIENT_SSL)) {
                http_response_code(500);
                die(json_encode([
                    'success' => false,
                    'message' => 'DB connect failed: ' . mysqli_connect_error()
                ]));
            }

            mysqli_set_charset(self::$conn, "utf8mb4");
        }
        return self::$conn;
    }
}
