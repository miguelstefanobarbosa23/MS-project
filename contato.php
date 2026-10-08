<?php


require '../config/conexao.php';


if (isset($_GET['marcar']) && ctype_digit($_GET['marcar'])) {
    $idMensagem = (int) $_GET['marcar'];

    try {
        $sql = 'UPDATE mensagens SET lida = NOT lida WHERE id = :id';
        $comando = $pdo->prepare($sql);
        $comando->bindValue(':id', $idMensagem, PDO::PARAM_INT);
        $comando->execute();

        header('Location: contato.php?status=atualizado');
        exit;
    } catch (PDOException $erro) {
        header('Location: contato.php?status=erro');
        exit;
    }
}

$mensagens = $pdo->query('SELECT * FROM mensagens ORDER BY data_envio DESC')->fetchAll();

$mensagensStatus = [
    'atualizado' => ['tipo' => 'sucesso', 'texto' => 'Status da mensagem atualizado.'],
    'excluido'   => ['tipo' => 'sucesso', 'texto' => 'Mensagem excluída com sucesso.'],
    'erro'       => ['tipo' => 'erro',    'texto' => 'Ocorreu um erro ao processar a operação.'],
];
$statusAtual = $_GET['status'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens Recebidas — MS Projects</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/animacoes.css">
</head>
<body>

    <header class="cabecalho">
        <nav class="nav">
            <a href="../index.html" class="nav__marca">MS<span>.</span>Projects</a>
            <button class="nav__hamburguer" aria-label="Abrir menu de navegação" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
            <ul class="nav__lista">
                <li><a href="../index.html">Início</a></li>
                <li><a href="../pages/projetos.html">Projetos</a></li>
                <li><a href="../pages/contato.html">Contato</a></li>
                <li><a href="admin.php" class="ativo">Área Restrita</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="secao" style="border-top: none;">
            <div class="container">
                <h1 class="revelar">Mensagens Recebidas</h1>
                <p class="secao__intro revelar">
                    Todas as mensagens enviadas pelo formulário de contato do site,
                    lidas diretamente da tabela <code>mensagens</code> via PDO.
                    <a href="admin.php">&larr; Voltar ao painel</a>
                </p>

                <?php if ($statusAtual && isset($mensagensStatus[$statusAtual])): ?>
                    <div class="alerta alerta--<?= $mensagensStatus[$statusAtual]['tipo'] ?>" data-auto-fechar>
                        <?= htmlspecialchars($mensagensStatus[$statusAtual]['texto']) ?>
                    </div>
                <?php endif; ?>

                <?php if (count($mensagens) === 0): ?>
                    <p class="vazio revelar">Nenhuma mensagem recebida até o momento.</p>
                <?php else: ?>
                    <div class="grade-projetos revelar" style="grid-template-columns: 1fr;">
                        <?php foreach ($mensagens as $msg): ?>
                            <article class="painel" style="margin-bottom: 0;">
                                <div class="painel__cabecalho">
                                    <div>
                                        <h3 style="margin-bottom: 0.2rem;"><?= htmlspecialchars($msg['assunto']) ?></h3>
                                        <span class="contador">
                                            <?= htmlspecialchars($msg['nome']) ?> · <?= htmlspecialchars($msg['email']) ?> ·
                                            <?= htmlspecialchars(date('d/m/Y H:i', strtotime($msg['data_envio']))) ?>
                                        </span>
                                    </div>
                                    <?php if ((int) $msg['lida'] === 1): ?>
                                        <span class="selo selo--lida">lida</span>
                                    <?php else: ?>
                                        <span class="selo selo--nao-lida">não lida</span>
                                    <?php endif; ?>
                                </div>

                                <p style="max-width: 100%;"><?= nl2br(htmlspecialchars($msg['mensagem'])) ?></p>

                                <div class="acoes-tabela" style="margin-top: var(--espaco-sm);">
                                    <a class="botao botao--secundario botao--pequeno" href="contato.php?marcar=<?= (int) $msg['id'] ?>">
                                        Marcar como <?= (int) $msg['lida'] === 1 ? 'não lida' : 'lida' ?>
                                    </a>
                                    <a class="botao botao--perigo link-excluir"
                                       href="excluir.php?tipo=mensagem&id=<?= (int) $msg['id'] ?>&voltar=contato"
                                       data-confirmar="Excluir a mensagem de &quot;<?= htmlspecialchars($msg['nome']) ?>&quot;?">
                                       Excluir
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="rodape">
        <div class="container">
            <p>&copy; <span id="ano-atual">2026</span> MS Projects — Projeto acadêmico de desenvolvimento web.</p>
            <div class="rodape__links">
                <a href="../index.html">Início</a>
                <a href="admin.php">Painel</a>
            </div>
        </div>
    </footer>

    <script src="../js/main.js"></script>
</body>
</html>
