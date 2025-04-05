<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "XXXXXXXXXXXXXXXX";
$user = "XXXXXXXXXXXXXXXX";
$password = "XXXXXXXXXXXXXXXX";
$dbname = "XXXXXXXXXXXXXXXX_db_registro";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Erro de conexão: ' . $conn->connect_error
    ]));
}

$input = json_decode(file_get_contents('php://input'), true);

$requiredFields = ['item_id', 'nome', 'quantidade'];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        echo json_encode([
            'status' => 'error',
            'message' => "O campo $field é obrigatório"
        ]);
        exit;
    }
}

$item_id = intval($input['item_id']);
$nome = $conn->real_escape_string(trim($input['nome']));
$email = isset($input['email']) ? $conn->real_escape_string(trim($input['email'])) : '';
$descricao = isset($input['descricao']) ? $conn->real_escape_string(trim($input['descricao'])) : '';
$quantidade = intval($input['quantidade']);

if (empty($input['data'])) {
    $data_retirada = date('Y-m-d');
} else {
    // Converte de DD/MM/AAAA para AAAA-MM-DD
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $input['data'], $matches)) {
        $data_retirada = "{$matches[3]}-{$matches[2]}-{$matches[1]}";
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Formato de data inválido. Use DD/MM/AAAA'
        ]);
        exit;
    }
}
$mes_retirada = isset($input['mes']) ? intval($input['mes']) : date('n');

if ($quantidade <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Quantidade deve ser maior que zero'
    ]);
    exit;
}

if ($mes_retirada < 1 || $mes_retirada > 12) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Mês inválido (deve ser entre 1 e 12)'
    ]);
    exit;
}

$sql = "INSERT INTO historico_retiradas (
    item_id,
    nome_solicitante,
    email_solicitante,
    descricao,
    quantidade,
    data_retirada,
    mes_retirada
) VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro ao preparar query: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param(
    "isssisi",
    $item_id,
    $nome,
    $email,
    $descricao,
    $quantidade,
    $data_retirada,
    $mes_retirada
);

if ($stmt->execute()) {
    $response = [
        'status' => 'success',
        'message' => 'Retirada registrada com sucesso',
        'data' => [
            'id' => $stmt->insert_id,
            'data_formatada' => date('d/m/Y', strtotime($data_retirada)),
            'mes' => $mes_retirada
        ]
    ];
} else {
    $response = [
        'status' => 'error',
        'message' => 'Erro ao registrar retirada: ' . $stmt->error
    ];
}
$stmt->close();
$conn->close();

echo json_encode($response);
?>