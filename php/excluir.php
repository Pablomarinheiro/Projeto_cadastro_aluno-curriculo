<?php
require_once "conn.php";
// require_once 'verifica_login.php'; // Adicione esta linha!

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_aluno = $_GET['id'];
    $sql = "DELETE FROM alunos WHERE id_aluno = ?";
    $stmt_excluir = $conn->prepare($sql);

    // Boa prática: Verificar se o prepare() falhou
    if ($stmt_excluir === false) {
        $conn->close();
        die("Erro na preparação do SQL: " . $conn->error);
    }

    $stmt_excluir->bind_param("i", $id_aluno);

    if ($stmt_excluir->execute()) {
        // Sucesso: Feche tudo ANTES de redirecionar
        $stmt_excluir->close();
        $conn->close();
        header("Location: filtro.php"); // Volta para a lista
        exit();
    } else {
        // Falha: Guarde o erro, feche tudo, DEPOIS mostre o erro
        $erro_msg = $stmt_excluir->error;
        $stmt_excluir->close();
        $conn->close();
        die("Erro ao excluir: " . $erro_msg);
    }
    
    // O código inacessível foi removido daqui.

} else {
    // ID inválido: Feche a conexão ANTES de "morrer"
    $conn->close();
    die("ID inválido");
}
?>