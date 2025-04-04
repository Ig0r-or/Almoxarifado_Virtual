<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "XXXXXXXXXXXXXX";
$user = "XXXXXXXXXXXXXX";
$password = "XXXXXXXXXXXXX";
$dbname = "XXXXXXXXXXXXXX_db_registro";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => $conn->connect_error]));
}
$input = json_decode(file_get_contents('php://input'), true); 
$id = intval($_POST['id'] ?? 0);
$campo = $_POST['campo'] ?? '';
$valor = $_POST['valor'] ?? '';

file_put_contents('debug.txt', print_r($_POST, true));

$id = intval($_POST['id'] ?? 0);
$campo = $conn->real_escape_string($_POST['campo'] ?? '');
$valor = $conn->real_escape_string($_POST['valor'] ?? '');

if (!in_array($campo, ['nome', 'quantidade'])) {
    die(json_encode(["status" => "error", "message" => "Campo inválido"]));
}

if ($campo === "quantidade") {
    $valor = intval($valor);
    $sql = "UPDATE tabela SET quantidade = $valor WHERE id = $id";
} else {
    $sql = "UPDATE tabela SET nome = '$valor' WHERE id = $id";
}

if ($conn->query($sql)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$conn->close();
?>