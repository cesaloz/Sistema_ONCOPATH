

<?php 

    require_once __DIR__ . '/env.php';

    class conexion {

        private static $conn = null;

            public static function getConnection(){

                if(self::$conn === null){
                    try{
                        $host = getenv('DB_HOST') ?:  '127.0.0.1';
                        $port = getenv('DB_PORT') ?: '5432';
                        $dbname = getenv('DB_NAME') ?: 'Oncologia_db';
                        $user = getenv('DB_USER') ?: 'postgres';
                        $password = getenv('DB_PASSWORD');

                        if (empty($password)){
                            die("❌ Error: No se encontró DB_PASSWORD en el archivo .env");
                        }
                        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

                        self::$conn = new PDO($dsn, $user, $password);
                        self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                        self::$conn->exec("SET client_encoding TO 'UTF8'");
                        self::$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                    } catch(PDOException $e){
                        die("❌ Error: " . $e->getMessage());
                    }

                }

                return self::$conn;

            }

            public static function closeConnection(){
                self::$conn = null;
            }

    }

?>