<?php
// Elimina o comenta la siguiente línea si no tienes config.php
// require_once __DIR__ . '/php/database.php';

// Define aquí tus constantes si no tienes config.php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_DATABASE', 'tienda_online'); // <-- Cambia esto por el nombre real de tu base
define('DB_USERNAME', 'root');              // <-- Cambia si tu usuario es diferente
define('DB_PASSWORD', ''); 

class Database {
    private static $pdo = null;

    public static function getInstance() {
        if (self::$pdo === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_DATABASE . ';charset=utf8mb4';
                self::$pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public static function query($sql, $params = []) {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Inserta un registro y retorna el ID insertado
    public static function insert($table, $data) {
        $fields = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO `$table` ($fields) VALUES ($placeholders)";
        self::query($sql, array_values($data));
        return self::getInstance()->lastInsertId();
    }

    // Actualiza registros
    public static function update($table, $data, $where, $whereParams) {
        $fields = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE `$table` SET $fields WHERE $where";
        self::query($sql, array_merge(array_values($data), $whereParams));
    }

    // Elimina registros
    public static function delete($table, $where, $whereParams) {
        $sql = "DELETE FROM `$table` WHERE $where";
        self::query($sql, $whereParams);
    }

    // Selecciona registros (fetchAll)
    public static function select($sql, $params = []) {
        return self::query($sql, $params)->fetchAll();
    }

    // Transacciones
    public static function beginTransaction() {
        self::getInstance()->beginTransaction();
    }

    public static function commit() {
        self::getInstance()->commit();
    }

    public static function rollBack() {
        self::getInstance()->rollBack();
    }

    // Utilidad segura para consultas IN con múltiples IDs
    public static function inClause($field, $values) {
        if (empty($values)) return "1=0";
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        return [$field . " IN ($placeholders)", $values];
    }
}
