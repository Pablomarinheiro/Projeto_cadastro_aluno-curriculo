<link rel="stylesheet" href="../css/front.css">

<?php

require_once 'conn.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $data_nascimento = trim($_POST['data_nascimento']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);
    $curso = trim($_POST['curso']);
    $ano = trim($_POST['ano']);

    $sql = "INSERT INTO alunos (nome, cpf, data_nascimento, email, telefone, endereco, curso, ano)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt == false) {
        die("ERROR : preparação não concedida" . $conn->error);
    }

    $stmt->bind_param("ssssssss", $nome, $cpf, $data_nascimento, $email, $telefone, $endereco, $curso, $ano);

    if ($stmt->execute()) {
        header("Location: ../html/cadastro_aluno.html");
        $stmt->close();
        $conn->close();
        exit();
    } else {
        echo "<h1>Erro ao cadastrar o aluno</h1>";
        echo "<p>ERROR :" . $stmt->error . "</p>";
        echo "<a id='btn' href='../html/cadastro_aluno.html'>Voltar</a>";
    }

    $stmt->close();
    $conn->close();
}
?>