<?php
header('Content-Type: application/json');

$host = "XXXXXXXXXXXXXXXX";
$user = "XXXXXXXXXXXXXXXX";
$password = "XXXXXXXXXXXXXXXX";
$dbname = "XXXXXXXXXXXXXXXX_db_registro";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Erro de conexão com o banco de dados."]));
}

$sql = "SELECT 
            h.*, 
            t.nome as item_nome,
            CASE 
                WHEN h.data_retirada = '0000-00-00' THEN 'Data não registrada'
                WHEN h.data_retirada IS NULL THEN 'Data não informada'
                ELSE DATE_FORMAT(h.data_retirada, '%d/%m/%Y')
            END as data_formatada,
            h.data_retirada as data_original
        FROM historico_retiradas h
        LEFT JOIN tabela t ON h.item_id = t.id
        ORDER BY COALESCE(h.data_retirada, '9999-12-31') DESC, h.id DESC
        LIMIT 10";

$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['data_formatada'] = $row['data_formatada'] ?? 'Data inválida';
        $row['mes_retirada'] = $row['mes_retirada'] ?? 'Mês não registrado';
        $data[] = $row;
    }
}

echo json_encode($data);
$conn->close();
?>