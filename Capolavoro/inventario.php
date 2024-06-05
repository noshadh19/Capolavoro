<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require "shared/connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nome']) && isset($_POST['quantita']) && !isset($_POST['id'])) {
        // Aggiungere un nuovo elemento
        $nome = $_POST['nome'];
        $quantita = $_POST['quantita'];

        $stmt = $dbh->prepare("INSERT INTO inventario (nome, quantita) VALUES (:nome, :quantita)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':quantita', $quantita);
        $stmt->execute();
    } elseif (isset($_POST['delete_id'])) {
        // Eliminare un elemento
        $delete_id = $_POST['delete_id'];

        $stmt = $dbh->prepare("DELETE FROM inventario WHERE id = :id");
        $stmt->bindParam(':id', $delete_id);
        $stmt->execute();
    } elseif (isset($_POST['id']) && isset($_POST['update_quantita'])) {
        // Aggiornare la quantità di un elemento
        $update_id = $_POST['id'];
        $update_quantita = $_POST['update_quantita'];

        $stmt = $dbh->prepare("UPDATE inventario SET quantita = :quantita WHERE id = :id");
        $stmt->bindParam(':quantita', $update_quantita);
        $stmt->bindParam(':id', $update_id);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventario</title>
    <meta charset="UTF-8"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function confirmDelete(form) {
            if (confirm("Sei sicuro di voler rimuovere questo elemento?")) {
                form.submit();
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Inventario</h1>
        <form method="post" class="mb-4">
            <div class="form-group">
                <label for="nome">Nome: </label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="quantita">Quantità: </label>
                <input type="number" class="form-control" id="quantita" name="quantita" required>
            </div>
            <button type="submit" class="btn btn-primary">Aggiungi all'inventario</button>
        </form>

        <h2>Inventario:</h2>
        <ul class="list-group">
            <?php
            $stmt = $dbh->query("SELECT * FROM inventario");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($rows) === 0) {
                echo "<div class='alert alert-warning' role='alert'>Inventario vuoto.</div>";
            } else {
                foreach ($rows as $row) {
                    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                    echo "<div>
                            {$row['nome']} - Quantità: {$row['quantita']}
                          </div>";
                    echo "<div class='d-flex'>
                            <form method='post' class='mr-2' onsubmit='event.preventDefault(); confirmDelete(this);'>
                                <input type='hidden' name='delete_id' value='{$row['id']}'>
                                <button type='submit' class='btn btn-danger btn-sm'>Rimuovi</button>
                            </form>
                            <form method='post'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <button type='submit' class='btn btn-primary btn-sm'>Aggiorna</button>
								<input type='number' name='update_quantita' value='{$row['quantita']}' class='form-control form-control-sm mr-2' style='width: 70px;'>
                            </form>
                          </div>";
                    echo "</li>";
                }
            }
            ?>
        </ul>
    </div>
</body>
</html>
