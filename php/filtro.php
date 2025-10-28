<?php
require_once 'conn.php';

$filtro_nome = $_GET['filtro_aluno'] ?? '';
$filtro_nome_sql = "%" . $filtro_nome . "%";

$filtro_curso = $_GET['curso'] ?? '';
$filtro_curso_sql = $filtro_curso;
if (empty($filtro_curso)) {
    $filtro_curso_sql = "%";
}

$filtro_ano = $_GET['ano'] ?? '';
$filtro_ano_sql = $filtro_ano;
if (empty($filtro_ano)) {
    $filtro_ano_sql = "%";
}

$sql = "SELECT curso, ano, nome, email, telefone, id_aluno 
        FROM alunos 
        WHERE curso LIKE ?
          AND ano LIKE ?
          AND nome LIKE ?
        ORDER BY curso, ano, nome ASC";

$stmt_filtro = $conn->prepare($sql);
$stmt_filtro->bind_param("sss", $filtro_curso_sql, $filtro_ano_sql, $filtro_nome_sql);
$stmt_filtro->execute();

$resultado = $stmt_filtro->get_result();

$alunos = [];
if ($resultado->num_rows > 0) {
    $alunos = $resultado->fetch_all(MYSQLI_ASSOC);
}

$stmt_filtro->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="../css/front.css" />
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon" />
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
            <form action="filtro.php" method="get">
                <div class="filtro_aluno">
                    <label for="filtro_aluno">Nome : </label>
                    <input type="text" name="filtro_aluno" id="filtro_aluno" placeholder="Busca por aluno..." value="<?php echo htmlspecialchars($filtro_nome); ?>">
                    <label for="curso">Curso : </label>
                    <select name="curso" id="curso">
                        <option value="">Todos</option>
                        <option value="Administração" <?php if ($filtro_curso == 'Administração') echo 'selected'; ?>>Administração</option>
                        <option value="Ciências Contábeis" <?php if ($filtro_curso == 'Ciências Contábeis') echo 'selected'; ?>>Ciências Contábeis</option>
                        <option value="Direito" <?php if ($filtro_curso == 'Direito') echo 'selected'; ?>>Direito</option>
                        <option value="Engenharia de Software" <?php if ($filtro_curso == 'Engenharia de Software') echo 'selected'; ?>>Engenharia de Software</option>
                        <option value="Pedagogia" <?php if ($filtro_curso == 'Pedagogia') echo 'selected'; ?>>Pedagogia</option>
                    </select>
                    <label for="ano">Ano : </label>
                    <select name="ano" id="ano">
                        <option value="">Todos</option>
                        <option value="1" <?php if ($filtro_ano == '1') echo 'selected'; ?>>1° Ano</option>
                        <option value="2" <?php if ($filtro_ano == '2') echo 'selected'; ?>>2° Ano</option>
                        <option value="3" <?php if ($filtro_ano == '3') echo 'selected'; ?>>3° Ano</option>
                        <option value="4" <?php if ($filtro_ano == '4') echo 'selected'; ?>>4° Ano</option>
                        <option value="5" <?php if ($filtro_ano == '5') echo 'selected'; ?>>5° Ano</option>
                    </select>
                    <button type="submit" class="btn_filtro"><i class="fa-solid fa-search"></i></button>
                </div>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Ano</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Editar</th>
                        <th>Excluir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($alunos)) {
                        foreach ($alunos as $aluno) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($aluno["curso"]) . "</td>";
                            echo "<td>" . htmlspecialchars($aluno["ano"]) . "°"  . "</td>";
                            echo "<td>" . htmlspecialchars($aluno["nome"])  . "</td>";
                            echo "<td>" . htmlspecialchars($aluno["email"])  . "</td>";
                            echo "<td>" . htmlspecialchars($aluno["telefone"])  . "</td>";
                            echo "<td><a href='editar.php?id=" .   $aluno["id_aluno"] . "' target='_blank'>Editar</a></td>";
                            echo "<td><a href='excluir.php?id=" .  $aluno["id_aluno"] . "' target='_blank'>Excluir</a></td>";
                            echo "<tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'> Nenhum aluno encontrado.</td></tr>";
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