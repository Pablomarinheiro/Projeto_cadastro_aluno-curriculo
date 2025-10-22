<?php
require_once "conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['id_aluno'])) {
        die("ERROR : ID do aluno não fornecido");
    }

    $id_aluno = $_POST['id_aluno'];
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $data_nascimento = trim($_POST['data_nascimento']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);
    $curso = trim($_POST['curso']);
    $ano = trim($_POST['ano']);

    $sql_aluno = "UPDATE alunos SET nome = ?, cpf = ?, data_nascimento = ?, email = ?, telefone = ?, endereco = ?, curso = ?, ano = ? 
            WHERE  id_aluno = ?";

    $stmt_aluno = $conn->prepare($sql);

    if ($stmt_aluno === false) {
        die("ERROR : A preparação não foi executada" . $conn->error);
    }

    $stmt_aluno->bind_param("ssssssssi", $nome, $cpf, $data_nascimento, $email, $telefone, $endereco, $curso, $ano, $id_aluno);

    if ($stmt_aluno->execute()) {
        header("Location : filtro.php");
        exit();
    } else {
        $erro = "Erro ao autualizar os dados: " . $stmt_aluno->error;
    }

    $stmt_aluno->close();
}

$aluno = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_aluno = $_GET['id'];

    $sql_select = "SELECT * FROM alunos WHERE id_aluno = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $id_aluno);
    $stmt_select->execute();
    $resultado_aluno = $stmt_select->get_result();

    if ($resultado_aluno->num_rows === 1) {
        $aluno = $resultado_aluno->fetch_assoc();
    } else {
        die("Aluno não encontrado");
    }
    $stmt_select->close();
} else {
    die("ID de aluno inválido ou não fornecido");
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/front.css" />
    <link
        rel="stylesheet"
        type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <title>Editar Aluno</title>
</head>

<body>
    <header>
        <nav class="navegacao">
            <a href="../index.html" class="logo"><i class="fa-solid fa-house"></i> NAE</a>
            <div class="links">
                <a href="../html/cadastro_aluno.html">Cadastro</a>
                <a href="filtro.php">Pesquisa</a>
            </div>
        </nav>
    </header>
    <main>
        <div class="container">
            <section class="container_forms">
                <h2>Editar Aluno</h2>
                <?php
                if (isset($erro)): ?>
                    <p class="erro"><?php echo htmlspecialchars($erro); ?></p>
                <?php endif; ?>
                <?php if ($aluno):  ?>
                    <form action="form_aluno.php?id=<?php echo $aluno['id_aluno']; ?>" method="POST" enctype="multipart/form-data">
                        <div class="cadastro_aluno">
                            <label for="nome">NOME COMPLETO:</label>
                            <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($aluno['nome']); ?>" required />
                        </div>
                        <div class="cadastro_aluno">
                            <label for="cpf">CPF :</label><input type="text" name="cpf" id="cpf" value="<?php echo htmlspecialchars($aluno['cpf']); ?>" required maxlength="14" />
                        </div>
                        <div class="cadastro_aluno">
                            <label for="data_nascimento">DATA DE NASCIMENTO :</label>
                            <input
                                type="date"
                                name="data_nascimento"
                                id="data_nascimento"
                                value="<?php echo htmlspecialchars($aluno['data_nascimento']); ?>"
                                required />
                        </div>
                        <div class="cadastro_aluno">
                            <label for="email">EMAIL :</label><input type="email" name="email" id="email" value="<?php echo htmlspecialchars($aluno['email']) ?>;" required />
                        </div>
                        <div class="cadastro_aluno">
                            <label for="telefone">TELEFONE :</label><input type="tel" name="telefone" id="telefone" value="<?php echo htmlspecialchars($aluno['telefone']) ?>;" required />
                        </div>
                        <div class="cadastro_aluno">
                            <label for="endereco">ENDEREÇO :</label><input type="text" name="endereco" id="endereco" value="<?php echo htmlspecialchars($aluno['endereco']) ?>;" required />
                        </div>
                        <div class="cadastro_aluno">
                            <label for="curso">CURSO :</label><select name="curso" id="curso">
                                <option value="Administração <?php if (isset($aluno['curso']) && $aluno['curso'] == 'Administração') echo 'selected'; ?> ">Administração</option>
                                <option value="Ciências Contábeis" <?php if (isset($aluno['curso']) && $aluno['curso'] == 'Ciências Contábeis') echo 'selected'; ?>>Ciências Contábeis</option>
                                <option value="Direito" <?php if (isset($aluno['curso']) && $aluno['curso'] == 'Direito') echo 'selected';   ?>>Direito</option>
                                <option value="Engenharia de Software"<?php if (isset($aluno['curso']) && $aluno['curso'] == 'Engenharia de Software') echo 'selected';?>>Engenharia de Software</option>
                                <option value="Pedagogia" <?php if (isset($aluno['curso']) && $aluno['curso'] == 'Pedagogia') echo 'selected';?>>Pedagogia</option>
                            </select>
                        </div>
                        <div class="cadastro_aluno">
                            <label for="ano">ANO :</label>
                            <select name="ano" id="ano">
                                <option value="1" <?php if(isset($aluno['ano']) && $aluno['ano'] == '1') echo 'selected';?>>1° Ano</option>
                                <option value="2" <?php if(isset($aluno['ano']) && $aluno['ano'] == '2') echo 'selected';?>>2° Ano</option>
                                <option value="3" <?php if(isset($aluno['ano']) && $aluno['ano'] == '3') echo 'selected';?>>3° Ano</option>
                                <option value="4" <?php if(isset($aluno['ano']) && $aluno['ano'] == '4') echo 'selected';?>>4° Ano</option>
                                <option value="5" <?php if(isset($aluno['ano']) && $aluno['ano'] == '5') echo 'selected';?>>5° Ano</option>
                            </select>
                        </div>
                        <input id="btn" type="submit" value="Cadastrar Aluno" />
                    </form>
                <?php endif; ?>
            </section>
        </div>
    </main>
    <footer class="rodape">
        <h3>Copyright © 2025 Núcleo de Apoio a Carreira - FATEB</h3>
    </footer>
</body>

</html>