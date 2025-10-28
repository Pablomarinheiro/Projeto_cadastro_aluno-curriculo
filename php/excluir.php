<?php
require_once "conn.php";
// require_once 'verifica_login.php'; // Adicione esta linha!

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_aluno = $_GET['id'];
    $sql = "DELETE FROM alunos WHERE id_aluno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_aluno);

    if ($stmt->execute()) {
        header("Location: filtro.php"); // Volta para a lista
        exit();
    } else {
        die("Erro ao excluir: " . $stmt->error);
    }
    $stmt->close();
    $conn->close();
} else {
    die("ID inválido");
}
?>