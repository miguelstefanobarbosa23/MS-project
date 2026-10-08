<?php

require '../config/conexao.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id          = (int) ($_POST['id'] ?? 0);
    $titulo      = trim($_POST['titulo'] ?? '');
    $descricao   = trim($_POST['descricao'] ?? '');
    $tecnologias = trim($_POST['tecnologias'] ?? '');
    $linkProjeto = trim($_POST['link_projeto'] ?? '');
    $imagemUrl   = trim($_POST['imagem_url'] ?? '');

    if ($id > 0 && $titulo !== '' && $descricao !== '' && $tecnologias !== '') {
        try {
            $sql = 'UPDATE projetos
                       SET titulo = :titulo,
                           descricao = :descricao,
                           tecnologias = :tecnologias,
                           link_projeto = :link_projeto,
                           imagem_url = :imagem_url
                     WHERE id = :id';
            $comando = $pdo->prepare($sql);

            $comando->bindValue(':titulo', $titulo, PDO::PARAM_STR);
            $comando->bindValue(':descricao', $descricao, PDO::PARAM_STR);
            $comando->bindValue(':tecnologias', $tecnologias, PDO::PARAM_STR);
            $comando->bindValue(':link_projeto', $linkProjeto !== '' ? $linkProjeto : null, PDO::PARAM_STR);
            $comando->bindValue(':imagem_url', $imagemUrl !== '' ? $imagemUrl : null, PDO::PARAM_STR);
            $comando->bindValue(':id', $id, PDO::PARAM_INT);

            $comando->execute();

            header('Location: admin.php?status=atualizado#projetos');
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

$tipo = $_GET['tipo'] ?? '';
$id   = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int) $_GET['id'] : 0;

if ($tipo !== 'projeto' || $id === 0) {
    header('Location: admin.php?status=erro#projetos');
    exit;
}

$comandoBusca = $pdo->prepare('SELECT * FROM projetos WHERE id = :id');
$comandoBusca->bindValue(':id', $id, PDO::PARAM_INT);
$comandoBusca->execute();
$projeto = $comandoBusca->fetch();

if (!$projeto) {
    header('Location: admin.php?status=erro#projetos');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Projeto — MS Projects</title>

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
            <div class="container" style="max-width: 720px;">
                <h1 class="revelar">Editar Projeto</h1>
                <p class="secao__intro revelar">
                    Alterando o registro #<?= (int) $projeto['id'] ?>. <a href="admin.php">&larr; Cancelar e voltar</a>
                </p>

                <form class="formulario painel revelar" method="POST" action="editar.php">
                    <!-- Campos ocultos: identificam QUAL registro será atualizado -->
                    <input type="hidden" name="id" value="<?= (int) $projeto['id'] ?>">
                    <input type="hidden" name="tipo" value="projeto">

                    <div class="campo">
                        <label for="titulo">Título</label>
                        <input type="text" id="titulo" name="titulo" required
                               value="<?= htmlspecialchars($projeto['titulo']) ?>">
                    </div>

                    <div class="campo">
                        <label for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao" required><?= htmlspecialchars($projeto['descricao']) ?></textarea>
                    </div>

                    <div class="campo">
                        <label for="tecnologias">Tecnologias</label>
                        <input type="text" id="tecnologias" name="tecnologias" required
                               value="<?= htmlspecialchars($projeto['tecnologias']) ?>">
                    </div>

                    <div class="campo">
                        <label for="link_projeto">Link do projeto</label>
                        <input type="url" id="link_projeto" name="link_projeto"
                               value="<?= htmlspecialchars($projeto['link_projeto'] ?? '') ?>">
                    </div>

                    <div class="campo">
                        <label for="imagem_url">URL da imagem de capa</label>
                        <input type="url" id="imagem_url" name="imagem_url"
                               value="<?= htmlspecialchars($projeto['imagem_url'] ?? '') ?>">
                    </div>

                    <div class="hero__acoes">
                        <button type="submit" class="botao botao--primario">Salvar alterações</button>
                        <a href="admin.php" class="botao botao--secundario">Cancelar</a>
                    </div>
                </form>
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
