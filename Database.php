<?php

class Database
{
    private $pdo;

    public function __construct() {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';port=' . DB_PORT;
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Підключення до бази даних не вдалося: ' . $e->getMessage());
        }
    }
    public function savePayment($card, $type, $amount)
    {
        $stmt = $this->pdo->prepare('INSERT INTO payments (card, type, amount, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$card, $type, $amount]);
    }

    public function getPaymentsByDay()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM payments WHERE DATE(created_at) = CURDATE() ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaymentsByMonth()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM payments WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE()) ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
