<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$host = "XXXXXXXXXXXXXX";
$user = "XXXXXXXXXXXXXX";
$password = "XXXXXXXXXXXXX";
$dbname = "XXXXXXXXXXXXXX_db_registro";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Erro de conexão com o banco de dados."]));

// Verifica se os dados foram enviados corretamente
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Método inválido."]);
    exit;
}

// Verifica se os valores foram enviados
if (!isset($_POST['id']) || !isset($_POST['quantidade'])) {
    echo json_encode(["status" => "error", "message" => "Dados incompletos."]);
    exit;
}

$id = intval($_POST['id']);
$quantidadeRetirada = intval($_POST['quantidade']);

if ($id <= 0 || $quantidadeRetirada <= 0) {
    echo json_encode(["status" => "error", "message" => "Valores inválidos."]);
    exit;
}

// Buscar a quantidade atual do item
$stmt = $conn->prepare("SELECT quantidade FROM tabela WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo json_encode(["status" => "error", "message" => "Item não encontrado."]);
    exit;
}

if ($item['quantidade'] < $quantidadeRetirada) {
    echo json_encode(["status" => "error", "message" => "Estoque insuficiente."]);
    exit;
}

// Atualiza a quantidade no banco
$novaQuantidade = $item['quantidade'] - $quantidadeRetirada;
$stmt = $conn->prepare("UPDATE tabela SET quantidade = ? WHERE id = ?");
$stmt->bind_param("ii", $novaQuantidade, $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Quantidade retirada com sucesso!", "novaQuantidade" => $novaQuantidade]);
} else {
    echo json_encode(["status" => "error", "message" => "Erro ao atualizar o estoque."]);
}
?>
