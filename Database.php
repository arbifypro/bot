<?php

class Database
{
    private $pdo;

    public function __construct($config)
    {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
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
