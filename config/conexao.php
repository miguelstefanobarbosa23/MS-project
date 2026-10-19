<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'ms_projects');
define('DB_USER', 'root');   
define('DB_PASS', '');       
define('DB_CHARSET', 'utf8mb4');

try {
    
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $opcoesPdo = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcoesPdo);

} catch (PDOException $erro) {
    die('Erro na conexão com o banco de dados: ' . $erro->getMessage());
}
