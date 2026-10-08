<?php

require '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'criar_projeto') {

    $titulo      = trim($_POST['titulo'] ?? '');
    $descricao   = trim($_POST['descricao'] ?? '');
    $tecnologias = trim($_POST['tecnologias'] ?? '');
    $linkProjeto = trim($_POST['link_projeto'] ?? '');
    $imagemUrl   = trim($_POST['imagem_url'] ?? '');

    if ($titulo !== '' && $descricao !== '' && $tecnologias !== '') {
        try {
            $sql = 'INSERT INTO projetos (titulo, descricao, tecnologias, link_projeto, imagem_url)
                    VALUES (:titulo, :descricao, :tecnologias, :link_projeto, :imagem_url)';
            $comando = $pdo->prepare($sql);

           
            $comando->bindValue(':titulo', $titulo, PDO::PARAM_STR);
            $comando->bindValue(':descricao', $descricao, PDO::PARAM_STR);
            $comando->bindValue(':tecnologias', $tecnologias, PDO::PARAM_STR);
            $comando->bindValue(':link_projeto', $linkProjeto !== '' ? $linkProjeto : null, PDO::PARAM_STR);
            $comando->bindValue(':imagem_url', $imagemUrl !== '' ? $imagemUrl : null, PDO::PARAM_STR);

            $comando->execute();

            header('Location: admin.php?status=criado#projetos');
            exit;

        } catch (PDOException $erro) {
            header('Location: admin.php?status=erro#projetos');
            exit;
        }
    } else {
        header('Location: admin.php?status=camposvazios#projetos');
        exit;
    }
}

$projetos  = $pdo->query('SELECT * FROM projetos ORDER BY id DESC')->fetchAll();
$mensagens = $pdo->query('SELECT * FROM mensagens ORDER BY data_envio DESC LIMIT 5')->fetchAll();
$totalMensagens = (int) $pdo->query('SELECT COUNT(*) FROM mensagens')->fetchColumn();

$mensagensStatus = [
    'criado'       => ['tipo' => 'sucesso', 'texto' => 'Projeto cadastrado com sucesso!'],
    'atualizado'   => ['tipo' => 'sucesso', 'texto' => 'Projeto atualizado com sucesso!'],
    'excluido'     => ['tipo' => 'sucesso', 'texto' => 'Registro excluído com sucesso!'],
    'erro'         => ['tipo' => 'erro',    'texto' => 'Ocorreu um erro ao processar a operação.'],
    'camposvazios' => ['tipo' => 'erro',    'texto' => 'Preencha ao menos título, descrição e tecnologias.'],
];
$statusAtual = $_GET['status'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo — MS Projects</title>

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
                <h1 class="revelar">Painel Administrativo</h1>
                <p class="secao__intro revelar">
                    Cadastre novos projetos e gerencie os projetos e mensagens já
                    salvos no banco de dados <code>ms_projects</code> via PDO.
                </p>

                <?php if ($statusAtual && isset($mensagensStatus[$statusAtual])): ?>
                    <div class="alerta alerta--<?= $mensagensStatus[$statusAtual]['tipo'] ?>" data-auto-fechar>
                        <?= htmlspecialchars($mensagensStatus[$statusAtual]['texto']) ?>
                    </div>
                <?php endif; ?>

               
                <div class="painel revelar">
                    <div class="painel__cabecalho">
                        <h2 style="margin:0;">Cadastrar novo projeto</h2>
                    </div>

                    <form class="formulario" method="POST" action="admin.php#projetos">
                        <input type="hidden" name="acao" value="criar_projeto">

                        <div class="campo">
                            <label for="titulo">Título</label>
                            <input type="text" id="titulo" name="titulo" required placeholder="Ex: Sistema de Biblioteca Escolar">
                        </div>

                        <div class="campo">
                            <label for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao" required placeholder="Breve resumo do projeto"></textarea>
                        </div>

                        <div class="campo">
                            <label for="tecnologias">Tecnologias</label>
                            <span class="dica">Separe por vírgulas, ex: HTML, CSS, PHP, MySQL</span>
                            <input type="text" id="tecnologias" name="tecnologias" required placeholder="HTML, CSS, PHP, MySQL">
                        </div>

                        <div class="campo">
                            <label for="link_projeto">Link do projeto (opcional)</label>
                            <input type="url" id="link_projeto" name="link_projeto" placeholder="https://github.com/...">
                        </div>

                        <div class="campo">
                            <label for="imagem_url">URL da imagem de capa (opcional)</label>
                            <input type="url" id="imagem_url" name="imagem_url" placeholder="https://...">
                        </div>

                        <button type="submit" class="botao botao--primario" style="justify-self: start;">Cadastrar projeto</button>
                    </form>
                </div>

               
                <div class="painel revelar" id="projetos">
                    <div class="painel__cabecalho">
                        <h2 style="margin:0;">Projetos cadastrados</h2>
                        <span class="contador"><?= count($projetos) ?> projeto(s)</span>
                    </div>

                    <?php if (count($projetos) === 0): ?>
                        <p class="vazio">Nenhum projeto cadastrado ainda. Use o formulário acima.</p>
                    <?php else: ?>
                        <div class="tabela-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Título</th>
                                        <th>Tecnologias</th>
                                        <th>Cadastrado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($projetos as $projeto): ?>
                                        <tr>
                                            <td><?= (int) $projeto['id'] ?></td>
                                           
                                            <td><?= htmlspecialchars($projeto['titulo']) ?></td>
                                            <td><?= htmlspecialchars($projeto['tecnologias']) ?></td>
                                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($projeto['data_criacao']))) ?></td>
                                            <td class="acoes-tabela">
                                                <a class="botao botao--secundario botao--pequeno" href="editar.php?tipo=projeto&id=<?= (int) $projeto['id'] ?>">Editar</a>
                                                <a class="botao botao--perigo link-excluir"
                                                   href="excluir.php?tipo=projeto&id=<?= (int) $projeto['id'] ?>"
                                                   data-confirmar="Excluir o projeto &quot;<?= htmlspecialchars($projeto['titulo']) ?>&quot;? Esta ação não pode ser desfeita.">
                                                   Excluir
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

               
                <div class="painel revelar" id="mensagens">
                    <div class="painel__cabecalho">
                        <h2 style="margin:0;">Mensagens recebidas</h2>
                        <span class="contador"><?= $totalMensagens ?> mensagem(ns) no total</span>
                    </div>

                    <?php if (count($mensagens) === 0): ?>
                        <p class="vazio">Nenhuma mensagem recebida ainda.</p>
                    <?php else: ?>
                        <div class="tabela-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Assunto</th>
                                        <th>Recebida em</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mensagens as $msg): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($msg['nome']) ?></td>
                                            <td><?= htmlspecialchars($msg['assunto']) ?></td>
                                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($msg['data_envio']))) ?></td>
                                            <td>
                                                <?php if ((int) $msg['lida'] === 1): ?>
                                                    <span class="selo selo--lida">lida</span>
                                                <?php else: ?>
                                                    <span class="selo selo--nao-lida">não lida</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="acoes-tabela">
                                                <a class="botao botao--perigo link-excluir"
                                                   href="excluir.php?tipo=mensagem&id=<?= (int) $msg['id'] ?>"
                                                   data-confirmar="Excluir a mensagem de &quot;<?= htmlspecialchars($msg['nome']) ?>&quot;?">
                                                   Excluir
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <p style="margin-top: var(--espaco-sm);">
                            <a href="contato.php">Ver a listagem completa de mensagens →</a>
                        </p>
                    <?php endif; ?>
                </div>

            </div>
        </section>
    </main>

    <footer class="rodape">
        <div class="container">
            <p>&copy; <span id="ano-atual">2026</span> MS Projects — Projeto acadêmico de desenvolvimento web.</p>
            <div class="rodape__links">
                <a href="../index.html">Início</a>
                <a href="../pages/projetos.html">Projetos</a>
                <a href="../pages/contato.html">Contato</a>
            </div>
        </div>
    </footer>

    <script src="../js/main.js"></script>
</body>
</html>
