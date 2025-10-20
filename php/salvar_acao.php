<?php

require_once "conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['salvar_anotacoes'])) {
        if (empty($_POST['id_aluno'])) {
            die("ERROR : ID não encontrado.");
        }
        $id_aluno = $_POST['id_aluno'];
        $conteudo = trim($_POST['conteudo']);
        $tipo = $_POST['tipo'];
        $sql_inserir = "INSERT INTO anotacoes (id_aluno, conteudo, tipo)
                        VALUES (?, ?, ?)";
        $stmt_inserir = $conn->prepare($sql_inserir);
        $stmt_inserir->bind_param("iss", $id_aluno, $conteudo, $tipo);
        if ($stmt_inserir->execute()) {
            $stmt_inserir->close();
            header("Location : editar.php?id=" . $id_aluno);
            exit();
        }
    }

    if (isset($_POST['salvar_alteracoes'])) {
        if (empty($_POST['id_aluno'])) {
            die("ERROR : ID não encontrado.");
        }
        $id_aluno = $_POST['id_aluno'];
        $nome = trim($_POST['nome']);
        $cpf = trim($_POST['cpf']);
        $email = trim($_POST['email']);
        $telefone = trim($_POST['telefone']);
        $curso = trim($_POST['curso']);
        $ano = trim($_POST['ano']);

        if (isset($_FILES['curriculo']) && $_FILES['curriculo']['error'] == 0) {
            $upload_dir = '../uploads/';
            $nome_curriculo = basename($_FILES(['curriculo']['name']));
            $nome_curriculo_id = uniqid() . '_' . $nome_curriculo;
            $dir_curriculo = $upload_dir . $nome_curriculo_id;

            if (move_uploaded_file($_FILES['curriculo']['temp_nome'], $dir_curriculo)) {
                $sql_curriculo = "INSERT INTO curriculos (id_aluno, arquivo, nome_original)
                                VALUES  (?, ?, ?)";
                $stmt_curriculo = $conn->prepare($sql_curriculo);
                $stmt_curriculo->bind_param("iss", $id_aluno, $dir_curriculo, $nome_curriculom);
                $stmt_curriculo->execute();
                $stmt_curriculo->close();
            }
        }
        
        $sql_update = "UPDATE alunos SET (id_aluno, cpf, data_nascimento, email, telefone, endereco, curso, ano)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssssss", $id_aluno, $nome, $cpf, $email, $telefone, $curso, $ano);
        $stmt_update = $conn->prepare($sql_update);
        
        if ($stmt_curriculo->execute()) {
            $stmt_update->close();
            header("Location : editar.php?id=" . $id_aluno);
            exit();
        }
    }
}

header("Location : editar.php?id=" . $id_aluno);
exit();