<?php
// Função para enviar email de confirmação
function enviarEmailConfirmacao($email, $codigo_verificacao) {
    $assunto = "Confirmação de Registro";
    $mensagem = "Seu código de verificação é: $codigo_verificacao";
    $headers = "From: diogogomes8076@gmail.com";

    if (mail($email, $assunto, $mensagem, $headers)) {
        return true; // Email enviado com sucesso
    } else {
        return false; // Erro ao enviar o email
    }
}

// Verifique se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extrair os dados do formulário
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $nif = $_POST["nif"];
    $data_inicio = $_POST["data_inicio"];
    $lente = $_POST["tipo_lentes"]; // Considerando que "tipo_lentes" se refere a "lente" na base de dados
    $embalagem = $_POST["tipo_embalagem"];

    // Gerar um código de verificação aleatório
    $codigo_verificacao = rand(100000, 999999);

    // Conecte-se ao banco de dados - Substitua os valores conforme suas configurações
    $conn = new mysqli("localhost", "root", "", "teste");

    // Verifique a conexão
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    // Preparar e executar a declaração de inserção na tabela de verificações
    $stmt = $conn->prepare("INSERT INTO verificacoes (email, codigo_verificacao, data_criacao) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $email, $codigo_verificacao);
    $stmt->execute();

    // Verificar se a inserção foi bem-sucedida
    if ($stmt->affected_rows > 0) {
        // Enviar email de confirmação
        if (enviarEmailConfirmacao($email, $codigo_verificacao)) {
            echo "<script>alert('Um email de confirmação foi enviado para $email. Por favor, verifique sua caixa de entrada.');</script>";
        } else {
            echo "<script>alert('Erro ao enviar o email de confirmação. Por favor, tente novamente mais tarde.');</script>";
        }
    } else {
        echo "<script>alert('Erro ao inserir registro na tabela de verificações: " . $stmt->error . "');</script>";
    }

    // Fechar a declaração e a conexão
    $stmt->close();
    $conn->close();

    // Saia do script após o envio do email de confirmação
    exit;
}
?>
