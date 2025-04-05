<?php
error_reporting(0);
ini_set('display_errors', 0);

$host = "XXXXXXXXXXXXXXXX";
$user = "XXXXXXXXXXXXXXXX";
$password = "XXXXXXXXXXXXXXXX";
$dbname = "XXXXXXXXXXXXXXXX_db_registro";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Erro de conexão com o banco de dados."]));
}
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die(json_encode(["status" => "error", "message" => "Requisição inválida."]));
}
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $id = intval($_POST['id']); 

    $sql = "DELETE FROM tabela WHERE id = ?";
    $stmt = $conn->prepare($sql);

     if ($stmt) {
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Item deletado com sucesso!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Erro ao deletar item."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Erro ao preparar a query."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Ação inválida ou ID não fornecido."]);
}


$conn->close();
?>
