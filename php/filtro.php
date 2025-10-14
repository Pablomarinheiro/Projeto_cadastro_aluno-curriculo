<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="../css/front.css" />
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
    <link
        rel="stylesheet"
        type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <title>Pesquisa</title>
</head>

<body>
    <header>
        <nav class="navegacao">
            <a href="../index.html" class="logo"><i class="fa-solid fa-house"></i> NAE </a>
            <div class="links">
                <a href="../html/cadastro_aluno.html">Cadastro</a>
                <a href="filtro.php">Pesquisa</a>
            </div>
        </nav>
    </header>
    <main>
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <td>Curso</td>
                        <td>Ano</td>
                        <td>Nome</td>
                        <td>E-mail</td>
                        <td>Telefone</td>
                        <td>Editar</td>
                        <td>Excluir</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once "conn.php";

                    $sql = "SELECT curso, ano, nome, email, telefone, id_aluno 
                            FROM alunos
                            ORDER BY curso, nome ASC";

                    $resultado = $conn->query($sql);

                    $alunos = [];
                    if ($resultado->num_rows > 0) {
                        $alunos = $resultado->fetch_all(MYSQLI_ASSOC);
                    }
                    $conn->close();

                    if (!empty($alunos)) {
                        foreach ($alunos as $aluno) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($aluno["curso"]) . "</td>";
                            echo "<td>" . htmlspecialchars($aluno["ano"])  . "</td>";
                            echo "<td>" . htmlspecialchars($aluno["nome"])  . "</td>";
                            echo "<td>" . htmlspecialchars($aluno["email"])  . "</td>";
                            echo "<td>" . htmlspecialchars($aluno["telefone"])  . "</td>";
                            echo "<td><a href='editar.php?id=" . $aluno["id_aluno"] . "'>Editar</a></td>";
                            echo "<td><a href='excluir.php?id=" . $aluno["id_aluno"] . "'>Excluir</a></td>";
                            echo "<tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'> Nenhum aluno cadastrado ainda.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    <footer>
        <div class="rodape">
            <h3>Copyright © 2025 Núcleo de Apoio a Carreira - FATEB</h3>
        </div>
    </footer>
</body>

</html>