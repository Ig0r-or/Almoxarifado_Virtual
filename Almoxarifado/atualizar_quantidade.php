<?php
header('Content-Type: application/json');

$host = "XXXXXXXXXXXXXXXX";
$user = "XXXXXXXXXXXXXXXX";
$password = "XXXXXXXXXXXXXXXX";
$dbname = "XXXXXXXXXXXXXXXX_db_registro";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Erro de conexão com o banco de dados."]));
}

// Recebe os dados
$id = $_POST['id'] ?? null;
$quantidade = $_POST['quantidade'] ?? null;
$mes = $_POST['mes'] ?? null;
$action = $_POST['action'] ?? null;

if (!$id || !$quantidade || !$mes || !$action) {
    echo json_encode(["status" => "error", "message" => "Dados incompletos."]);
    exit;
}
// Mapeia o número do mês para o nome da coluna
$meses = [
    1 => 'janeiro',
    2 => 'fevereiro',
    3 => 'marco',
    4 => 'abril',
    5 => 'maio',
    6 => 'junho',
    7 => 'julho',
    8 => 'agosto',
    9 => 'setembro',
    10 => 'outubro',
    11 => 'novembro',
    12 => 'dezembro'
];

if (!isset($meses[$mes])) {
    echo json_encode(["status" => "error", "message" => "Mês inválido."]);
    exit;
}

$coluna_mes = $meses[$mes];

// Primeiro verifica o saldo atual
$sql = "SELECT saldo FROM tabela WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Item não encontrado."]);
    exit;
}

$row = $result->fetch_assoc();
$saldoAtual = $row['saldo'];

// Atualiza o saldo conforme a ação
if ($action === "retirar") {
    if ($quantidade > $saldoAtual) {
        echo json_encode(["status" => "error", "message" => "Quantidade insuficiente em estoque."]);
        exit;
    }
    
    $novoSaldo = $saldoAtual - $quantidade;
    
    // Atualiza o banco de dados - agora atualizando o saldo
    $sql = "UPDATE tabela SET saldo = ?, $coluna_mes = $coluna_mes + ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $novoSaldo, $quantidade, $id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Retirada registrada com sucesso! Saldo atualizado."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erro ao atualizar o saldo."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Ação inválida."]);
}

$conn->close();
?>