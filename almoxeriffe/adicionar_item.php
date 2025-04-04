<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "XXXXXXXXXXXXXX";
$user = "XXXXXXXXXXXXXX";
$password = "XXXXXXXXXXXXX";
$dbname = "XXXXXXXXXXXXXX_db_registro";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Erro de conexão: " . $conn->connect_error]));
}
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die(json_encode(["status" => "error", "message" => "Método não permitido."]));
}

$sql = "INSERT INTO tabela (nome, quantidade, janeiro, fevereiro, marco, abril, maio, junho, julho, agosto, setembro, outubro, novembro, dezembro, saldo) 
        VALUES ('Novo Item', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)";

if ($conn->query($sql)) {
    echo json_encode(["status" => "success", "message" => "Item adicionado!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erro SQL: " . $conn->error]);
}

$conn->close();
?>