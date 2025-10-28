<?php
require_once "conn.php";

$id_aluno = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_aluno = $_GET['id'];
}

$erro = "";
$sucesso = "";

if (isset($_POST['salvar_anotacao'])) {

    if (empty($_POST['id_aluno_hidden'])) {
        die("ERROR : ID do aluno não fornecido para anotação");
    }

    $id_aluno_anotacao = $_POST['id_aluno_hidden'];
    $conteudo = trim($_POST['conteudo_anotacao']);
    $tipo = $_POST['tipo_anotacao'];
    if (!empty($conteudo)) {
        $sql_inserir_anotacao = "INSERT INTO anotacoes (id_aluno, conteudo, tipo) VALUES (?, ?, ?)";
        $stmt_inserir = $conn->prepare($sql_inserir_anotacao);

        if ($stmt_inserir) {
            $stmt_inserir->bind_param("iss", $id_aluno_anotacao, $conteudo, $tipo);
            if ($stmt_inserir->execute()) {
                header("Location: editar.php?id=" . $id_aluno_anotacao . "&sucesso=1");
                exit();
            } else {
                $erro = "Erro ao salvar anotação: " . $stmt_inserir->error;
            }
            $stmt_inserir->close();
        } else {
            $erro = "Erro ao preparar SQL da anotação: " . $conn->error;
        }
    } else {
        $erro = "A anotação não pode estar vazia.";
    }

    $id_aluno = $id_aluno_anotacao;
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['id_aluno'])) {
        die("ERROR : ID do aluno não fornecido para edição");
    }

    $id_aluno_form = $_POST['id_aluno'];

    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $data_nascimento = trim($_POST['data_nascimento']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);
    $curso = trim($_POST['curso']);
    $ano = trim($_POST['ano']);

    $sql_aluno = "UPDATE alunos SET nome = ?, cpf = ?, data_nascimento = ?, email = ?, telefone = ?, endereco = ?, curso = ?, ano = ? 
                  WHERE id_aluno = ?";
    $stmt_aluno = $conn->prepare($sql_aluno);

    if ($stmt_aluno === false) {
        die("ERROR : Falha na preparação da atualização do aluno: " . $conn->error);
    }

    $stmt_aluno->bind_param("ssssssssi", $nome, $cpf, $data_nascimento, $email, $telefone, $endereco, $curso, $ano, $id_aluno_form);

    if ($stmt_aluno->execute()) {
        $sucesso = "Dados do aluno atualizados!";

        if (isset($_FILES['curriculo']) && $_FILES['curriculo']['error'] == 0) {

            $upload_dir = '../uploads/';
            $nome_original = basename($_FILES['curriculo']['name']);
            $caminho_temporario = $_FILES['curriculo']['tmp_name'];
            $nome_arquivo_novo = uniqid() . '_' . $nome_original;
            $caminho_destino = $upload_dir . $nome_arquivo_novo;

            if (move_uploaded_file($caminho_temporario, $caminho_destino)) {

                $sql_curriculo = "INSERT INTO curriculos (id_aluno, arquivo, nome_original) VALUES (?, ?, ?)";
                $stmt_curriculo = $conn->prepare($sql_curriculo);

                if ($stmt_curriculo) {
                    $stmt_curriculo->bind_param("iss", $id_aluno_form, $caminho_destino, $nome_original);
                    if ($stmt_curriculo->execute()) {
                        $sucesso .= " Currículo enviado com sucesso!";
                    } else {
                        $erro .= " Erro ao salvar currículo no banco: " . $stmt_curriculo->error;
                    }
                    $stmt_curriculo->close();
                } else {
                    $erro .= " Erro ao preparar SQL do currículo: " . $conn->error;
                }
            } else {
                $erro .= " Erro ao mover o arquivo de currículo. Verifique permissões da pasta 'uploads'.";
            }
        }

        header("Location: editar.php?id=" . $id_aluno_form . "&sucesso=2");
        exit();
    } else {
        $erro = "Erro ao autualizar os dados: " . $stmt_aluno->error;
    }
    $stmt_aluno->close();

    $id_aluno = $id_aluno_form;
}

$aluno = null;
$anotacoes = [];
$curriculos = [];

if ($id_aluno) {
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

    $sql_anotacoes = "SELECT * FROM anotacoes WHERE id_aluno = ? ORDER BY data_registro DESC";
    $stmt_anotacoes = $conn->prepare($sql_anotacoes);
    $stmt_anotacoes->bind_param("i", $id_aluno);
    $stmt_anotacoes->execute();
    $resultado_anotacoes = $stmt_anotacoes->get_result();
    if ($resultado_anotacoes->num_rows > 0) {
        $anotacoes = $resultado_anotacoes->fetch_all(MYSQLI_ASSOC);
    }
    $stmt_anotacoes->close();

    $sql_curriculos = "SELECT * FROM curriculos WHERE id_aluno = ? ORDER BY data_upload DESC";
    $stmt_curriculos = $conn->prepare($sql_curriculos);
    $stmt_curriculos->bind_param("i", $id_aluno);
    $stmt_curriculos->execute();
    $resultado_curriculos = $stmt_curriculos->get_result();
    if ($resultado_curriculos->num_rows > 0) {
        $curriculos = $resultado_curriculos->fetch_all(MYSQLI_ASSOC);
    }
    $stmt_curriculos->close();
} else {
    die("ID de aluno inválido ou não fornecido");
}

if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1) {
    $sucesso = "Anotação salva com sucesso!";
}
if (isset($_GET['sucesso']) && $_GET['sucesso'] == 2) {
    $sucesso = "Dados do aluno e/ou currículo salvos com sucesso!";
}

$conn->close();
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

                <?php if (isset($erro) && !empty($erro)): ?>
                    <p style="color: red; text-align: center; font-weight: bold; margin-bottom: 15px;"><?php echo htmlspecialchars($erro); ?></p>
                <?php endif; ?>
                <?php if (isset($sucesso) && !empty($sucesso)): ?>
                    <p style="color: green; text-align: center; font-weight: bold; margin-bottom: 15px;"><?php echo htmlspecialchars($sucesso); ?></p>
                <?php endif; ?>


                <h2>Editar Aluno</h2>
                <?php if ($aluno):  ?>
                    <form action="editar.php?id=<?php echo htmlspecialchars($aluno['id_aluno']); ?>" method="POST" enctype="multipart/form-data">
                        <div class="cadastro_aluno">
                            <input type="hidden" name="id_aluno" value="<?php echo htmlentities($aluno['id_aluno']) ?>">
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
                            <label for="email">EMAIL :</label><input type="email" name="email" id="email" value="<?php echo htmlspecialchars($aluno['email']); ?>" required />
                        </div>
                        <div class="cadastro_aluno">
                            <label for="telefone">TELEFONE :</label><input type="tel" name="telefone" id="telefone" value="<?php echo htmlspecialchars($aluno['telefone']); ?>" required />
                        </div>
                        <div class="cadastro_aluno">
                            <label for="endereco">ENDEREÇO :</label><input type="text" name="endereco" id="endereco" value="<?php echo htmlspecialchars($aluno['endereco']); ?>" required />
                        </div>
                        <div class="cadastro_aluno">
                            <label for="curso">CURSO :</label>
                            <select name="curso" id="curso">
                                <option value="Administração" <?php if (isset($aluno['curso']) && $aluno['curso'] == 'Administração') echo 'selected'; ?>>Administração</option>
                                <option value="Ciências Contábeis" <?php if (isset($aluno['curso']) && trim($aluno['curso']) == 'Ciências Contábeis') echo 'selected'; ?>>Ciências Contábeis</option>
                                <option value="Direito" <?php if (isset($aluno['curso']) && $aluno['curso'] == 'Direito') echo 'selected';   ?>>Direito</option>
                                <option value="Engenharia de Software" <?php if (isset($aluno['curso']) && $aluno['curso'] == 'Engenharia de Software') echo 'selected'; ?>>Engenharia de Software</option>
                                <option value="Pedagogia" <?php if (isset($aluno['curso']) && $aluno['curso'] == 'Pedagogia') echo 'selected'; ?>>Pedagogia</option>
                            </select>
                        </div>
                        <div class="cadastro_aluno">
                            <label for="ano">ANO :</label>
                            <select name="ano" id="ano">
                                <option value="1" <?php if (isset($aluno['ano']) && $aluno['ano'] == '1') echo 'selected'; ?>>1° Ano</option>
                                <option value="2" <?php if (isset($aluno['ano']) && $aluno['ano'] == '2') echo 'selected'; ?>>2° Ano</option>
                                <option value="3" <?php if (isset($aluno['ano']) && $aluno['ano'] == '3') echo 'selected'; ?>>3° Ano</option>
                                <option value="4" <?php if (isset($aluno['ano']) && $aluno['ano'] == '4') echo 'selected'; ?>>4° Ano</option>
                                <option value="5" <?php if (isset($aluno['ano']) && $aluno['ano'] == '5') echo 'selected'; ?>>5° Ano</option>
                            </select>
                        </div>

                        <div class="cadastro_aluno">
                            <label>ANEXAR NOVO CURRÍCULO :</label>
                            <label for="curriculo" class="label_arquivo">Clique para selecionar um arquivo (PDF, DOCX)</label>
                            <input type="file" name="curriculo" id="curriculo" accept=".pdf,.doc,.docx" style="display: none;">
                        </div>

                        <input id="btn" type="submit" value="Salvar Alterações do Aluno" />
                    </form>
                <?php endif; ?>


                <h4>ADICIONAR ANOTAÇÕES</h4>
                <form class="form_anotacoes" action="editar.php?id=<?php echo htmlspecialchars($aluno['id_aluno']); ?>" method="post">

                    <input type="hidden" name="id_aluno_hidden" id="id_aluno_hidden" value="<?php echo htmlspecialchars($aluno['id_aluno']); ?>">

                    <label for="conteudo_anotacao">NOVA ANOTAÇÃO</label>
                    <textarea name="conteudo_anotacao" id="conteudo_anotacao" placeholder="Deixe aqui a sua anotação sobre esse aluno" required></textarea>

                    <label for="tipo_anotacao">TIPO DA ANOTAÇÃO:</label>
                    <select name="tipo_anotacao" id="tipo_anotacao" required>
                        <option value="administrativa">Administrativa</option>
                        <option value="encaminhamento">Encaminhamento</option>
                    </select>
                    <input id="btn" name="salvar_anotacao" type="submit" value="Salvar Anotação">
                </form>
                <div class="curriculos-container">
                    <hr>
                    <h4>Currículos Anexados</h4>
                    <div class="lista-curriculos">
                        <?php if (!empty($curriculos)): ?>
                            <ul>
                                <?php foreach ($curriculos as $curriculo): ?>
                                    <li>
                                        <a href="<?php echo htmlspecialchars($curriculo['arquivo']); ?>" target="_blank">
                                            <?php echo htmlspecialchars($curriculo['nome_original']); ?>
                                        </a>
                                        <em>(Enviado em: <?php echo date('d/m/Y H:i', strtotime($curriculo['data_upload'])); ?>)</em>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Nenhum currículo anexado para este aluno.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="anotacoes-container">
                    <hr>
                    <h4>Histórico de Anotações</h4>
                    <div class="lista-anotacoes">
                        <?php if (!empty($anotacoes)): ?>
                            <?php foreach ($anotacoes as $anotacao): ?>
                                <div class="anotacao-item">
                                    <p><strong><?php echo htmlspecialchars(ucfirst($anotacao['tipo'])); ?></strong> - <em><?php echo date('d/m/Y H:i', strtotime($anotacao['data_registro'])); ?></em></p>
                                    <p><?php echo nl2br(htmlspecialchars($anotacao['conteudo'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Nenhuma anotação encontrada para este aluno.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <footer class="rodape">
        <h3>Copyright © 2025 Núcleo de Apoio a Carreira - FATEB</h3>
    </footer>

    <script>
        document.getElementById('curriculo').addEventListener('change', function() {
            var label = document.querySelector('label[for="curriculo"].label_arquivo');
            if (this.files && this.files.length > 0) {
                label.textContent = this.files[0].name;
            } else {
                label.textContent = 'Clique para selecionar um arquivo (PDF, DOCX)';
            }
        });
    </script>
</body>

</html>