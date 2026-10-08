<?php


require '../config/conexao.php'; 


$urlRetorno = '../pages/contato.html';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $urlRetorno);
    exit;
}

$nome     = trim(filter_input(INPUT_POST, 'nome', FILTER_UNSAFE_RAW) ?? '');
$email    = trim(filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW) ?? '');
$assunto  = trim(filter_input(INPUT_POST, 'assunto', FILTER_UNSAFE_RAW) ?? '');
$mensagem = trim(filter_input(INPUT_POST, 'mensagem', FILTER_UNSAFE_RAW) ?? '');

$valido =
    mb_strlen($nome) >= 3 &&
    filter_var($email, FILTER_VALIDATE_EMAIL) !== false &&
    mb_strlen($assunto) >= 3 &&
    mb_strlen($mensagem) >= 10;

if (!$valido) {

    header('Location: ' . $urlRetorno . '?status=erro');
    exit;
}

try {
    
    $sql = 'INSERT INTO mensagens (nome, email, assunto, mensagem) VALUES (?, ?, ?, ?)';
    $comando = $pdo->prepare($sql);

    $comando->bindValue(1, $nome, PDO::PARAM_STR);
    $comando->bindValue(2, $email, PDO::PARAM_STR);
    $comando->bindValue(3, $assunto, PDO::PARAM_STR);
    $comando->bindValue(4, $mensagem, PDO::PARAM_STR);

    $comando->execute();

    header('Location: ' . $urlRetorno . '?status=sucesso');
    exit;

} catch (PDOException $erro) {
 
    header('Location: ' . $urlRetorno . '?status=erro');
    exit;
}
