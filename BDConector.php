<?php
require_once("Utilidades.php");
// Inicia e retorna uma conexão com o banco de dados
function BDConnect(){
    return new mysqli("localhost", "id19569475_timeupadmin", "jsW*zpzX]=NY02~4", "id19569475_timeupbd");
}


// Verifica se a Foto com o ID especificado existe no banco de dados
function BDFotoExiste($ID){
    $connection = BDConnect();
    $queryRes = $connection->query("SELECT * FROM Foto_ID WHERE ID = '$ID'");
    BDDisconnect($connection);
    return ($queryRes->num_rows > 0);
}
// Insere uma Foto no banco de dados
function BDRegistrarFoto($Nome, $Caminho, $Diretorio){
    $connection = BDConnect();

    if(!file_exists($Diretorio))
        mkdir($Diretorio, 0755, true);

    if(!move_uploaded_file($Caminho, $Diretorio.$Nome)){
        JSAlert("Erro ao fazer upload da Foto");
        return 0;
    }
    
    $insertQueryRes = $connection->query("INSERT INTO Foto (Nome) VALUES ('$Nome')");
    if(!$insertQueryRes){
        JSAlert("Erro ao inserir Foto no banco de dados");
        BDDisconnect($connection);
        return 0;
    }
    
    $selectQueryRes = $connection->query("SELECT * FROM Foto WHERE Nome = '$Nome'")->fetch_assoc();
    BDDisconnect($connection);
    return $selectQueryRes["ID"];
}
// Recupera o nome de uma Foto com o ID especificado
function BDRecuperarFoto($ID){
    $connection = BDConnect();
    $queryRes = $connection->query("SELECT * FROM Foto WHERE ID = '$ID'")->fetch_assoc();
    BDDisconnect($connection);
    return $queryRes["Nome"];
}
// Atualiza o nome de uma Foto com o ID especificado
function BDAtualizarFoto($ID, $Nome, $Caminho, $Diretorio){
    $connection = BDConnect();
    if(!rename($Diretorio.BDRecuperarFoto($ID), $Diretorio.$Nome)){
        JSAlert("Erro ao atualizar Foto_ID de perfil");
        BDDisconnect($connection);
        return false;
    }
    move_uploaded_file($Caminho, $Diretorio.$Nome);
    $queryRes = $connection->query("UPDATE Foto SET Nome = $Nome WHERE ID = '$ID'");
    BDDisconnect($connection);
    return $queryRes;
}
// Deleta a Foto com o ID especificado
function BDDeletarFoto($ID){
    $connection = BDConnect();
    $queryRes = $connection->query("DELETE FROM Foto WHERE ID = '$ID'");
    BDDisconnect($connection);
    return $queryRes;
}


// Verifica se o usuário portador do CPF especificado já está cadastrado e retorna verdadeiro ou falso
function BDClienteExiste($CPF){
    $CPF = DesformatarCPF($CPF);
    $connection = BDConnect();
    $queryRes = $connection->query("SELECT * FROM Cliente WHERE CPF = '$CPF'");
    BDDisconnect($connection);
    return ($queryRes->num_rows > 0);
}
// Insere os dados de um usuário no banco de dados
function BDRegistrarCliente($dadosCliente){
    $dadosCliente->CPF = DesformatarCPF($dadosCliente->CPF);
    $dadosCliente->Telefone = DesformatarTelefone($dadosCliente->Telefone);
    $connection = BDConnect();
    JSAlert($dadosCliente->Foto_ID);
    $queryRes = $connection->query("INSERT INTO Cliente (Nome, Foto_ID, Data_Nascimento, CPF, Telefone, Email, Senha, Rua, Numero) VALUES ('$dadosCliente->Foto_ID', '$dadosCliente->Nome', '$dadosCliente->Data_Nascimento', '$dadosCliente->CPF', '$dadosCliente->Telefone', '$dadosCliente->Email', '$dadosCliente->Senha', '$dadosCliente->Rua', '$dadosCliente->Numero')");
    BDDisconnect($connection);
    return $queryRes;
}
// Recupera os dados de um usuário com o nome CPF
function BDRecuperarCliente($CPF){
    $CPF = DesformatarCPF($CPF);
    $connection = BDConnect();
    $queryRes = $connection->query("SELECT * FROM Cliente WHERE CPF = '$CPF'")->fetch_assoc();
    $dadosCliente = new ObjCliente();
    $dadosCliente->ID = $queryRes["ID"];
    $dadosCliente->Foto_ID = $queryRes["Foto_ID"];
    $dadosCliente->Nome = $queryRes["Nome"];
    $dadosCliente->Data_Nascimento = $queryRes["Data_Nascimento"];
    $dadosCliente->CPF = FormatarCPF($queryRes["CPF"]);
    $dadosCliente->Telefone = FormatarTelefone($queryRes["Telefone"]);
    $dadosCliente->Email = $queryRes["Email"];
    $dadosCliente->Senha = $queryRes["Senha"];
    $dadosCliente->Rua = $queryRes["Rua"];
    $dadosCliente->Numero = $queryRes["Numero"];
    BDDisconnect($connection);
    return $dadosCliente;
}
// Atualiza os dados do usuário especificado no banco de dados
function BDAtualizarCliente($CPF, $dado, $valor){
    $CPF = DesformatarCPF($CPF);
    if($dado == DadosCliente::CPF)
        $valor = DesformatarCPF($valor);
    else if($dado == DadosCliente::Telefone)
        $valor = DesformatarTelefone($valor);
    $connection = BDConnect();
    $queryRes = $connection->query("UPDATE Cliente SET $dado = '$valor' WHERE CPF = '$CPF'");
    BDDisconnect($connection);
    return $queryRes;
}
// Deleta o usuário especificado do banco de dados
function BDDeletarCliente($CPF){
    $CPF = DesformatarCPF($CPF);
    $connection = BDConnect();
    $queryRes = $connection->query("DELETE FROM Cliente WHERE CPF = '$CPF'");
    return $queryRes;
}


// Verifica se o vendedor portador do CNPJ especificado já está cadastrado e retorna verdadeiro ou falso
function BDVendedorExiste($CNPJ){
    $CNPJ = DesformatarCNPJ($CNPJ);
    $connection = BDConnect();
    $queryRes = $connection->query("SELECT * FROM Vendedor WHERE CNPJ = '$CNPJ'");
    BDDisconnect($connection);
    return ($queryRes->num_rows > 0);
}
// Insere os dados de um vendedor no banco de dados
function BDRegistrarVendedor($dadosVendedor){
    $dadosVendedor->CNPJ = DesformatarCNPJ($dadosVendedor->CNPJ);
    $connection = BDConnect();
    $queryRes = $connection->query("INSERT INTO Vendedor (Foto_ID, Nome, CNPJ, Email, Senha, Rua, Numero) VALUES ('$dadosVendedor->Foto_ID', '$dadosVendedor->Nome', '$dadosVendedor->CNPJ', '$dadosVendedor->Email', '$dadosVendedor->Senha', '$dadosVendedor->Rua', '$dadosVendedor->Numero')");
    BDDisconnect($connection);
    return $queryRes;
}
// Recupera os dados de um vendedor com o nome CNPJ
function BDRecuperarVendedor($CNPJ){
    $CNPJ = DesformatarCNPJ($CNPJ);
    $connection = BDConnect();
    $queryRes = $connection->query("SELECT * FROM Vendedor WHERE CNPJ = '$CNPJ'")->fetch_assoc();
    $dadosVendedor = new ObjVendedor();
    $dadosVendedor->ID = $queryRes["ID"];
    $dadosVendedor->Foto_ID = $queryRes["Foto_ID"];
    $dadosVendedor->Nome = $queryRes["Nome"];
    $dadosVendedor->CNPJ = FormatarCNPJ($queryRes["CNPJ"]);
    $dadosVendedor->Email = $queryRes["Email"];
    $dadosVendedor->Senha = $queryRes["Senha"];
    $dadosVendedor->Rua = $queryRes["Rua"];
    $dadosVendedor->Numero = $queryRes["Numero"];
    BDDisconnect($connection);
    return $dadosVendedor;
}
// Recupera os dados de um vendedor com o ID
function BDRecuperarVendedorID($ID){
    $connection = BDConnect();
    $queryRes = $connection->query("SELECT * FROM Vendedor WHERE ID = '$ID'")->fetch_assoc();
    $dadosVendedor = new ObjVendedor();
    $dadosVendedor->ID = $queryRes["ID"];
    $dadosVendedor->Foto_ID = $queryRes["Foto_ID"];
    $dadosVendedor->Nome = $queryRes["Nome"];
    $dadosVendedor->CNPJ = FormatarCNPJ($queryRes["CNPJ"]);
    $dadosVendedor->Email = $queryRes["Email"];
    $dadosVendedor->Senha = $queryRes["Senha"];
    $dadosVendedor->Rua = $queryRes["Rua"];
    $dadosVendedor->Numero = $queryRes["Numero"];
    BDDisconnect($connection);
    return $dadosVendedor;
}
// Atualiza os dados do vendedor especificado no banco de dados
function BDAtualizarVendedor($CNPJ, $dado, $valor){
    $CNPJ = DesformatarCNPJ($CNPJ);
    if($dado == DadosVendedor::CNPJ)
        $valor = DesformatarCNPJ($valor);
    $connection = BDConnect();
    $queryRes = $connection->query("UPDATE Vendedor SET $dado = '$valor' WHERE CNPJ = '$CNPJ'");
    BDDisconnect($connection);
    return $queryRes;
}
// Deleta o vendedor especificado do banco de dados
function BDDeletarVendedor($CNPJ){
    $CNPJ = DesformatarCNPJ($CNPJ);
    $connection = BDConnect();
    $queryRes = $connection->query("DELETE FROM Vendedor WHERE CNPJ = '$CNPJ'");
    return $queryRes;
}


// Verifica se o produto com o Nome especificado já está cadastrado e retorna verdadeiro ou falso
function BDProdutoExiste($Nome){
    $connection = BDConnect();
    $queryRes = $connection->query("SELECT * FROM Produto WHERE Nome = '$Nome'");
    BDDisconnect($connection);
    return ($queryRes->num_rows > 0);
} 
// Registra um produto no banco de dados
function BDRegistrarProduto($dadosProduto){
    $connection = BDConnect();
    $queryRes = $connection->query("INSERT INTO Produto (Foto_ID, Nome, Valor, Categoria, Vendedor_ID) VALUES ('$dadosProduto->Foto_ID', '$dadosProduto->Nome', '$dadosProduto->Valor', '$dadosProduto->Categoria', '$dadosProduto->Vendedor_ID')");
    BDDisconnect($connection);
    return $queryRes;
}
// Recupera os dados de um produto com o ID
function BDRecuperarProduto($ID){
    $connection = BDConnect();
    $queryRes = $connection->query("SELECT * FROM Produto WHERE ID = '$ID'")->fetch_assoc();
    $dadosProduto = new ObjProduto();
    $dadosProduto->ID = $queryRes["ID"];
    $dadosProduto->Foto_ID = $queryRes["Foto_ID"];
    $dadosProduto->Nome = $queryRes["Nome"];
    $dadosProduto->Valor = $queryRes["Valor"];
    $dadosProduto->Categoria = $queryRes["Categoria"];
    $dadosProduto->Vendedor_ID = $queryRes["Vendedor_ID"];
    BDDisconnect($connection);
    return $dadosProduto;
}
// Atualiza os dados do produto com o ID especificado no banco de dados
function BDAtualizarProduto($ID, $dado, $valor){
    $connection = BDConnect();
    $queryRes = $connection->query("UPDATE Produto SET $dado = '$valor' WHERE ID = '$ID'");
    BDDisconnect($connection);
    return $queryRes;
}
// Deleta o produto especificado do banco de dados
function BDDeletarProduto($ID){
    $connection = BDConnect();
    $queryRes = $connection->query("DELETE FROM Produto WHERE ID = '$ID'");
    BDDisconnect($connection);
    return $queryRes;
}
function BDListarProdutos(){
    $connection = BDConnect();
    $queryRes = $connection->query("SELECT * FROM Produto");
    BDDisconnect($connection);
    $list = [];
    
    $i = 0;
    while($produto = $queryRes->fetch_assoc()){
        $list[$i] = new ObjProduto();
        $list[$i]->Foto_ID = $produto["Foto_ID"];
        $list[$i]->Nome = $produto["Nome"];
        $list[$i]->Valor = $produto["Valor"];
        $list[$i]->Categoria = $produto["Categoria"];
        $list[$i]->Vendedor_ID = $produto["Vendedor_ID"];
        $i++;
    }
    return $list;
}


// Termina a conexão com o banco de dados
function BDDisconnect($connection){
    $connection->close();
}
?>