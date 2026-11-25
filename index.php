<?php
// Configura o cabeçalho para retornar JSON
header("Content-Type: application/json; charset=UTF-8");

// Nosso "banco de dados" simples em memória (Array)
$alunos = [];
$idAtual = 1;

// Pega o método HTTP (POST, GET, PUT, DELETE)
$metodo = $_SERVER['REQUEST_METHOD'];

// Pega a URL e separa as partes para encontrar o ID
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$partes = explode('/', trim($uri, '/'));
$id = isset($partes[1]) ? (int)$partes[1] : null;

// Lê os dados enviados no corpo da requisição (JSON)
$corpo = json_decode(file_get_contents("php://input"), true);

// Roteamento simples
switch ($metodo) {
   // POST - Cadastrar
   case 'POST':
       $novoAluno = [
           "id" => $idAtual,
           "nome" => $corpo['nome'] ?? null,
           "unidadeCurricular" => $corpo['unidadeCurricular'] ?? null,
           "aprovado" => $corpo['aprovado'] ?? null
       ];
       // Simula o push no array
       $alunos[$idAtual] = $novoAluno;
       $idAtual++;
       http_response_code(201); // Criado
       echo json_encode($novoAluno);
       break;
   // GET - Consultar por ID
   case 'GET':
       if (isset($alunos[$id])) {
           http_response_code(200); // Sucesso
           echo json_encode($alunos[$id]);
       } else {
           http_response_code(404); // Não encontrado
           echo json_encode(["mensagem" => "Estudante não encontrado."]);
       }
       break;
   // PUT - Atualizar apenas o campo aprovado
   case 'PUT':
       if (isset($alunos[$id])) {
           // Altera APENAS o campo aprovado
           $alunos[$id]['aprovado'] = $corpo['aprovado'] ?? null;
           http_response_code(200);
           echo json_encode($alunos[$id]);
       } else {
           http_response_code(404);
           echo json_encode(["mensagem" => "Estudante não encontrado."]);
       }
       break;
   // DELETE - Remover
   case 'DELETE':
       if (isset($alunos[$id])) {
           unset($alunos[$id]); // Remove o aluno do array
           http_response_code(200);
           echo json_encode(["mensagem" => "Registro removido com sucesso."]);
       } else {
           http_response_code(404);
           echo json_encode(["mensagem" => "Estudante não encontrado."]);
       }
       break;
   default:
       http_response_code(405);
       echo json_encode(["mensagem" => "Método não permitido."]);
       break;
}
?>