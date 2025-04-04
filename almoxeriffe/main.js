        const formularioEntrada = document.getElementById('inscription-form');
        const historico = document.getElementById('historico-entrada');
        const mensagens = document.getElementById('mensagem');
        const itemsContainer = document.getElementById('items-container');
        const inscricoes = [];

        formularioEntrada.addEventListener('submit', function (event) {
            event.preventDefault();

//Condição de Registro de formulário
            const nome = formularioEntrada.querySelector('#nome').value;
            const email = formularioEntrada.querySelector('#email').value;
            const data = formularioEntrada.querySelector('#data').value;
            const descricao = formularioEntrada.querySelector('[name="descricao"]').value;

            if (nome.length < 4) {
                mensagens.textContent = 'O nome de usuário deve ter pelo menos 4 caracteres.';
                mensagens.style.color = 'red';
                return;
            }
            if (!email.endsWith('@ifsuldeminas.edu.br')) {
                mensagens.textContent = 'Por favor, insira um email válido';
                mensagens.style.color = 'red';
                formularioEntrada.querySelector('#email').style.border = '2px solid red';
                return;
            } else {
                mensagens.textContent = 'Formulário válido, documento enviado!';
                mensagens.style.color = 'green';
                formularioEntrada.querySelector('#email').classList.remove('email-erro');
            }

            const items = [...itemsContainer.querySelectorAll('.item')].map(item => {
                return {
                    categoria: item.querySelector('[name="categoria"]').value,
                    quantidade: item.querySelector('[name="quantidade"]').value,
                };
            });

            const inscricao = {
                nome,
                email,
                data,
                items,
                descricao,
            };

            inscricoes.push(inscricao);
            atualizarHistoricoEntrada();
            formularioEntrada.reset();
            itemsContainer.innerHTML = '';
            addItemEntrada();
        });
//Função pra adicionar Item
    function addItemEntrada() {
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('item');
        itemDiv.innerHTML = `
            <div class="form-group">
                <label for="categoria">Item</label>
                <select name="categoria">
                    <option value="Sacola Pequena">Sacola Pequena</option>
                    <option value="Sacola Grande">Sacola Grande</option>
                    <option value="Caneca">Caneca</option>
                     <option value="Pasta IF">Pasta IF</option> 
                        <option value="Garafinha squezee">Garafinha squezee</option> 
                        <option value="Caneta Madeira">Caneta Madeira</option>
                        <option value="Caneta Papel">Caneta Papel</option>
                        <option value="Caneta Metal">Caneta Metal</option>
                        <option value="Bloco Grande">Bloco Grande</option>
                        <option value="Bloco Pequeno">Bloco Pequeno/com caneta</option>
                        <option value="Chaveiro">Chaveiro</option>
                        <option value="Porta Crachá">Porta Crachá</option>
                        <option value="Camisa de Expedição">Camisa de Expedição</option>
                        <option value="Caderno Pronatec">Caderno Pronatec</option>
                        <option value="Microfone Boya">Microfone Boya</option>
                        <option value="Condemer Microphone by-un01">Condemer Microphone by-un01</option>
                        <option value="Para Filtro Water Dispenser">Para Filtro Water Dispenser</option>
                        <option value="Chapéu Expedição">Chapéu Expedição</option>
                        <option value="Mochila PROEX">Mochila PROEX</option>
                        <option value="Crachá IF sul">Crachá IF sul</option>
                        <option value="Panfleto Expedição">Panfleto Expedição</option>
                        <option value="Camisa IF Formação inicial">Camisa IF Formação inicial</option>
                        <option value="Colete Expedição">Colete Expedição</option>
                        <option value="Revista de extensão">Revista de extensão</option>
                        <option value="Pasta de notebook">Pasta de notebook</option>
                        <option value="Extenção com Carretel">Extenção com Carretel</option>
                        <option value="Extenção com 3 tomadas">Extenção com 3 tomadas</option>
                        <option value="Adaptador T">Adaptador T</option>
                        <option value="Garrafa Térmica">Garrafa Térmica</option> 
                        <option value="Canecas PREPARA">Canecas PREPARA</option>
                        <option value="Camisas PREPARA">Camisas PREPARA</option>
                        <option value="Sacolas ENCONTRO EXTENSÃO">Sacolas ENCONTRO EXTENSÃO</option>
                        <option value="Caderno PREPARA">Caderno PREPARA</option>
                        <option value="Bottom">Bottom</option>
                        <option value="Projector">Projector</option>
                        <option value="Rascunho IF">Rascunho IF</option>
                </select>
            </div>
            <div class="form-group">
                <label for="quantidade">Quantidade</label>
                <input type="number" name="quantidade" required>
            </div>
        `;
        itemsContainer.appendChild(itemDiv);
    }
    
    function atualizarHistoricoEntrada() {
        historico.innerHTML = '';
        inscricoes.forEach(inscricao => {
            const li = document.createElement('li');
            li.innerHTML = `<strong>Nome:</strong> ${inscricao.nome} <br>
                            <strong>Email:</strong> ${inscricao.email} <br>
                            <strong>Data:</strong> ${inscricao.data} <br>
                            <strong>Descrição:</strong> ${inscricao.descricao} <br>`;
            inscricao.items.forEach(item => {
                li.innerHTML += `
                    <strong>Item:</strong> ${item.categoria} <br>
                    <strong>Quantidade:</strong> ${item.quantidade} <br>
                `;
            });
            historico.appendChild(li);
        });
    }

    formularioEntrada.addEventListener('submit', function (event) {
        event.preventDefault();
        atualizarHistoricoEntrada();
    });
        
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------

//----------------------------------------------------------------------------------------------------------------------------------------------------------------------
//POST pro php
  function updateCell(element) {
    var newValue = element.innerText;
    var cellIndex = element.cellIndex;
    var rowIndex = element.parentNode.rowIndex;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update-cell.php", true); // Caminho para o arquivo PHP
    xhr.setRequestHeader("Content-Type", "text/html;charset=UTF-8");
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            console.log('Dados atualizados com sucesso!');
        }
    };
    xhr.send(JSON.stringify({
        row: rowIndex,
        cell: cellIndex,
        value: newValue
    }));
}
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//
document.getElementById("inscription-form").addEventListener("submit", function(event) {
            event.preventDefault(); // Evita o recarregamento da página

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "API_forms/adicionar_item.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log("Resposta do servidor:", xhr.responseText);
                     loadTableData();
                    alert("Novo item adicionado com sucesso!");
                } else {
                    console.error("Erro na requisição:", xhr.statusText);
                    alert("Erro ao adicionar item.");
                }
            };

            xhr.onerror = function() {
                console.error("Erro de conexão.");
                alert("Erro de conexão com o servidor.");
            };

            // Envia a requisição sem dados, pois queremos apenas adicionar um item vazio
            xhr.send();
        });