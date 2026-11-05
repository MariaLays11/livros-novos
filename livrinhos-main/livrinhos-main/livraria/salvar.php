
<?php
include 'conexao.php';

// Receber os dados do formulário
$titulo = trim($_POST['titulo'] ?? '');
$autor = trim($_POST['autor'] ?? '');
$preco = filter_var($_POST['preco'] ?? null, FILTER_VALIDATE_FLOAT);
$estoque = filter_var($_POST['estoque'] ?? null, FILTER_VALIDATE_INT);
$imagem = trim($_POST['imagem'] ?? '');

// Verificar se os dados são válidos
if (
    $titulo === '' ||
    $autor === '' ||
    $preco === false || $preco <= 0 ||
    $estoque === false || $estoque < 0
) {
    echo "<script>
            alert('Erro! Verifique os dados do livro.');
            window.location.href = 'listar.php';
          </script>";
    exit;
}

// Preparar o comando SQL para salvar o livro
$sql = "INSERT INTO livros (titulo, autor, preco, estoque, imagem)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssdis",
    $titulo,
    $autor,
    $preco,
    $estoque,
    $imagem
);

// Salvar no banco de dados
if ($stmt->execute()) {
    echo "<script>
            alert('Sucesso! O livro foi cadastrado.');
            window.location.href = 'index.php';
          </script>";
} else {
    echo "Erro ao cadastrar livro: " . htmlspecialchars($stmt->error);
}

$stmt->close();
$conn->close();
?>
