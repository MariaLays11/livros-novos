
<?php
include 'conexao.php';

// Buscar livros cadastrados no banco de dados
$sql = "SELECT * FROM livros ORDER BY id ASC";
$resultado = $conn->query($sql);

// Capas definidas diretamente no index.php, sem alterar o banco
$capas = [
    'Maldição do Mar' =>
        'assets/maldicao.jpg',

    'Era Uma Vez um Coração Partido' =>
        'assets/coracao.jpg',

    'Caçador Sem Coração' =>
        'assets/cacador.jpg',

    'Príncipe Cruel' =>
        'assets/principe.jpg',

    'Bruxa Rebelde' =>
        'assets/bruxa.jpg',

    'Trono de Vidro' =>
        'assets/trono.jpg',
    
    
    'Irmãos Karamazov' =>
        'assets/irmaos.jpg',

    'Quincas Borba' =>
        'assets/quincas.jpg',

    
    'Noites Brancas' =>
        'assets/noites.jpg',

];

$imagemPadrao = 'assets/sem-capa.png';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Livraria Bem-te-vi | Catálogo de Livros</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- CSS separado -->
    <link rel="stylesheet" href="estilo.css">
</head>

<body>

<!-- =====================================
     MENU SUPERIOR
===================================== -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand fw-bold" href="index.php">
            📚 Livraria Bem-te-vi
        </a>

        <a href="listar.php" class="btn btn-outline-light">
            Cadastrar livros
        </a>

    </div>
</nav>

<!-- =====================================
     BANNER PRINCIPAL
===================================== -->

<header class="hero text-center">
    <div class="container">

        <h1>
            Entre em um mundo de histórias fantásticas e inesquecíveis!
        </h1>

        <p>
            Descubra romances, magia, mistérios e aventuras
            na nossa coleção de livros.
        </p>

        <a href="#catalogo" class="btn btn-warning btn-lg">
            Explorar catálogo mágico ✨
        </a>

    </div>
</header>

<!-- =====================================
     CATÁLOGO DE LIVROS
===================================== -->

<main class="container py-5" id="catalogo">

    <div class="text-center mb-5">

        <h2 class="fw-bold">
            Nosso catálogo ✨
        </h2>

        <p class="text-secondary">
            Encontre sua próxima leitura favorita.
        </p>

    </div>

    <div class="row g-4">

        <?php if ($resultado && $resultado->num_rows > 0): ?>

            <?php while ($livro = $resultado->fetch_assoc()): ?>

                <?php
                // Dados do livro
                $tituloOriginal = trim((string) ($livro['titulo'] ?? ''));
                $autorOriginal = (string) ($livro['autor'] ?? 'Autor não informado');

                // Encontrar a capa pelo título, sem consultar o ID
                $imagem = $capas[$tituloOriginal] ?? $imagemPadrao;

                // Se a imagem não existir, usar a capa padrão
                if (!is_file(__DIR__ . '/' . $imagem)) {
                    $imagem = $imagemPadrao;
                }

                // Proteger textos exibidos no HTML
                $titulo = htmlspecialchars(
                    $tituloOriginal !== '' ? $tituloOriginal : 'Livro sem título',
                    ENT_QUOTES,
                    'UTF-8'
                );

                $autor = htmlspecialchars(
                    $autorOriginal,
                    ENT_QUOTES,
                    'UTF-8'
                );

                $preco = number_format(
                    (float) ($livro['preco'] ?? 0),
                    2,
                    ',',
                    '.'
                );

                $idLivro = (int) ($livro['id'] ?? 0);
                $estoque = (int) ($livro['estoque'] ?? 0);
                ?>

                <div class="col-12 col-sm-6 col-lg-4 col-xl-4">

                    <div class="card livro-card h-100 shadow-sm">

                        <!-- CAPA DO LIVRO -->

                        <img
                            src="<?= htmlspecialchars(
                                $imagem,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="card-img-top capa"
                            alt="Capa de <?= $titulo ?>"
                            loading="lazy"
                            onerror="this.onerror=null;this.src='assets/sem-capa.png';"
                        >

                        <!-- INFORMAÇÕES DO LIVRO -->

                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title fw-bold">
                                <?= $titulo ?>
                            </h5>

                            <p class="text-secondary mb-2">
                                Autor: <?= $autor ?>
                            </p>

                            <p class="preco mt-auto">
                                R$ <?= $preco ?>
                            </p>

                            <!-- ESTOQUE E COMPRA -->

                            <?php if ($estoque > 0): ?>

                                <p class="text-success small">
                                    Disponível em estoque
                                </p>

                                <a
                                    href="comprar.php?id=<?= $idLivro ?>"
                                    class="btn btn-success w-100"
                                >
                                    Comprar livro
                                </a>

                            <?php else: ?>

                                <p class="text-danger small">
                                    Livro esgotado
                                </p>

                                <button
                                    type="button"
                                    class="btn btn-secondary w-100"
                                    disabled
                                >
                                    Indisponível
                                </button>

                            <?php endif; ?>

                        </div>
                    </div>
                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <!-- NENHUM LIVRO CADASTRADO -->

            <div class="col-12 text-center">

                <h4>Nenhum livro cadastrado.</h4>

                <p>
                    Cadastre os livros para que eles apareçam na loja.
                </p>

                <a href="listar.php" class="btn btn-primary">
                    Cadastrar primeiro livro
                </a>

            </div>

        <?php endif; ?>

    </div>
</main>

<!-- =====================================
     RODAPÉ
===================================== -->

<footer class="text-center py-4 bg-dark text-white">

    <h5>📚 Livraria Bem-te-vi</h5>

    <p class="mb-0">
        Seu próximo mundo começa em uma página.
    </p>

    <small>
        &copy; 2026 Livraria Bem-te-vi. Todos os direitos reservados.
    </small>

</footer>

<?php
$conn->close();
?>

</body>
</html>
