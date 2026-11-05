
<?php
include 'conexao.php';

$mensagem = '';
$tipoMensagem = '';

// Cadastrar livro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $editora = trim($_POST['editora'] ?? '');
    $ano = trim($_POST['ano'] ?? '');

    if ($titulo === '' || $autor === '') {
        $mensagem = 'Preencha o título e o autor do livro.';
        $tipoMensagem = 'danger';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO livros (titulo, autor, editora, ano)
             VALUES (?, ?, ?, ?)"
        );

        if ($stmt) {
            $anoBanco = $ano === '' ? null : (int) $ano;

            $stmt->bind_param(
                "sssi",
                $titulo,
                $autor,
                $editora,
                $anoBanco
            );

            if ($stmt->execute()) {
                $stmt->close();

                header('Location: livros.php?sucesso=1');
                exit;
            } else {
                $mensagem = 'Erro ao cadastrar o livro.';
                $tipoMensagem = 'danger';
            }

            $stmt->close();
        } else {
            $mensagem = 'Erro ao preparar o cadastro.';
            $tipoMensagem = 'danger';
        }
    }
}

// Mensagem de sucesso
if (isset($_GET['sucesso'])) {
    $mensagem = 'Livro cadastrado com sucesso!';
    $tipoMensagem = 'success';
}

// Buscar os livros cadastrados
$sql = "SELECT * FROM livros ORDER BY id ASC";
$resultado = $conn->query($sql);

// Escapar texto para exibição no HTML
function e($valor) {
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Livros - Livraria Bem-te-vi</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- CSS desacoplado -->
    <link rel="stylesheet" href="estilo.css">
</head>

<body>

    <header class="cabecalho">
        <div class="container">
            <a href="livros.php" class="marca">
                <span class="marca-icone">📚</span>
                <span>Livraria Bem-te-vi</span>
            </a>

            <nav class="navegacao">
                <a href="livros.php" class="link-nav ativo">
                    Cadastro de livros
                </a>
            </nav>
        </div>
    </header>

    <main class="container conteudo-principal">

        <section class="introducao">
            <span class="etiqueta">GERENCIAMENTO</span>

            <h1 class="titulo-pagina">
                Cadastro de livros
            </h1>

            <p class="subtitulo">
                Cadastre, organize e consulte os livros da sua livraria.
            </p>
        </section>

        <?php if ($mensagem !== ''): ?>
            <div
                class="alert alert-<?= e($tipoMensagem) ?> alerta-personalizado"
                role="alert"
            >
                <?= e($mensagem) ?>
            </div>
        <?php endif; ?>

        <section class="card-formulario">

            <div class="card-cabecalho">
                <div>
                    <h2>Novo livro</h2>
                    <p>Preencha os dados abaixo para cadastrar um livro.</p>
                </div>

                <span class="icone-card">＋</span>
            </div>

            <div class="card-corpo">

                <form action="livros.php" method="POST">

                    <div class="row g-3">

                        <div class="col-12">
                            <label for="titulo" class="form-label">
                                Título do livro *
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="titulo"
                                name="titulo"
                                placeholder="Ex.: O Pequeno Príncipe"
                                maxlength="255"
                                value="<?= e($_POST['titulo'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="autor" class="form-label">
                                Autor *
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="autor"
                                name="autor"
                                placeholder="Nome do autor"
                                maxlength="255"
                                value="<?= e($_POST['autor'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="editora" class="form-label">
                                Editora
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="editora"
                                name="editora"
                                placeholder="Nome da editora"
                                maxlength="255"
                                value="<?= e($_POST['editora'] ?? '') ?>"
                            >
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="ano" class="form-label">
                                Ano de publicação
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                id="ano"
                                name="ano"
                                placeholder="Ex.: 2024"
                                min="1"
                                max="9999"
                                value="<?= e($_POST['ano'] ?? '') ?>"
                            >
                        </div>

                    </div>

                    <div class="acoes-formulario">
                        <button
                            type="reset"
                            class="btn btn-limpar"
                        >
                            Limpar
                        </button>

                        <button
                            type="submit"
                            class="btn btn-cadastrar"
                        >
                            Cadastrar livro
                        </button>
                    </div>

                    <p class="observacao-formulario">
                        * Campos obrigatórios.
                    </p>

                </form>

            </div>
        </section>

        <section class="secao-listagem">

            <div class="cabecalho-listagem">
                <div>
                    <h2>Livros cadastrados</h2>
                    <p>Confira os registros salvos no sistema.</p>
                </div>

                <span class="contador-livros">
                    <?php
                    echo $resultado ? $resultado->num_rows : 0;
                    ?>
                    livro(s)
                </span>
            </div>

            <div class="tabela-container">

                <table class="table tabela-livros">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Editora</th>
                            <th>Ano</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($resultado && $resultado->num_rows > 0): ?>

                            <?php while ($livro = $resultado->fetch_assoc()): ?>

                                <tr>
                                    <td>
                                        <span class="id-livro">
                                            <?= e($livro['id']) ?>
                                        </span>
                                    </td>

                                    <td class="celula-titulo">
                                        <?= e($livro['titulo']) ?>
                                    </td>

                                    <td>
                                        <?= e($livro['autor']) ?>
                                    </td>

                                    <td>
                                        <?= e($livro['editora'] ?? '') ?: '—' ?>
                                    </td>

                                    <td>
                                        <?= e($livro['ano'] ?? '') ?: '—' ?>
                                    </td>
                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="5" class="tabela-vazia">
                                    <span class="livros-vazios-icone">📖</span>

                                    <strong>Nenhum livro cadastrado</strong>

                                    <span>
                                        Utilize o formulário acima para adicionar
                                        o primeiro livro.
                                    </span>
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>
        </section>

    </main>

    <footer class="rodape">
        <div class="container">
            <p>
                &copy; <?= date('Y') ?> Livraria Bem-te-vi.
                Todos os direitos reservados.
            </p>
        </div>
    </footer>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

</body>
</html>
