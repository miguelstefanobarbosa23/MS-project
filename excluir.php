<?php

require '../config/conexao.php';

$tabelasPermitidas = [
    'projeto'  => 'projetos',
    'mensagem' => 'mensagens',
];

$tipo = $_GET['tipo'] ?? '';
$id   = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int) $_GET['id'] : 0;
$voltarParaContato = ($_GET['voltar'] ?? '') === 'contato';

if (!array_key_exists($tipo, $tabelasPermitidas) || $id === 0) {
    header('Location: admin.php?status=erro');
    exit;
}

$tabela = $tabelasPermitidas[$tipo]; 

try {
    
    $sql = "DELETE FROM {$tabela} WHERE id = :id";
    $comando = $pdo->prepare($sql);
    $comando->bindValue(':id', $id, PDO::PARAM_INT);
    $comando->execute();

    if ($tipo === 'mensagem' && $voltarParaContato) {
        header('Location: contato.php?status=excluido');
    } elseif ($tipo === 'mensagem') {
        header('Location: admin.php?status=excluido#mensagens');
    } else {
        header('Location: admin.php?status=excluido#projetos');
    }
    exit;

} catch (PDOException $erro) {
    $destino = $voltarParaContato ? 'contato.php' : 'admin.php';
    header('Location: ' . $destino . '?status=erro');
    exit;
}
