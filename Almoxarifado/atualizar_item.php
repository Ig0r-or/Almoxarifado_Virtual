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

$conn->begin_transaction();

try {
    if ($campo === "quantidade") {
        $valor = intval($valor);
        
        // 1. Obtemos a quantidade atual e o total de retiradas nos meses
        $sql_select = "SELECT quantidade, 
                      (janeiro + fevereiro + marco + abril + maio + junho +
                       julho + agosto + setembro + outubro + novembro + dezembro) as total_retiradas 
                       FROM tabela WHERE id = ?";
        $stmt = $conn->prepare($sql_select);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Item não encontrado");
        }
        
        $row = $result->fetch_assoc();
        $quantidade_atual = $row['quantidade'];
        $total_retiradas = $row['total_retiradas'];
        
        // 2. Calcula o novo saldo
        $novo_saldo = $valor - $total_retiradas;
        
        // 3. Atualiza apenas a quantidade e o saldo (não altera os meses)
        $sql = "UPDATE tabela SET 
                quantidade = ?, 
                saldo = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $valor, $novo_saldo, $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Erro ao atualizar quantidade: " . $conn->error);
        }
        
    } else {
        // Atualiza apenas o nome (comportamento original)
        $sql = "UPDATE tabela SET nome = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $valor, $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Erro ao atualizar nome: " . $conn->error);
        }
    }
    $conn->commit();
    echo json_encode(["status" => "success"]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
?>