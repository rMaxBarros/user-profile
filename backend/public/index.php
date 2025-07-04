<?php
// Definir o cabeçalho para permitir requisições de origens diferentes (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Lida com requisições OPTIONS (preflight requests) que navegadores fazem antes de POST/PUT/DELETE
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inclui os arquivos necessários
include_once '../src/config/Database.php';
include_once '../src/models/User.php';

// Cria uma instância do banco de dados
$database = new Database();
$db = $database->getConnection();

// Cria uma instância do objeto Usuário
$user = new User($db);

// Obtém o método da requisição HTTP e a URL
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Pega a parte relevante da URL após o alias.
// Ex: http://localhost/sync-360-api/usuario
//     $request_uri[0] seria "sync-360-api"
//     $request_uri[1] seria "usuario"
// Precisamos de $request_uri[1] para identificar a rota.
$api_base = 'sync-360-api'; // Alias do Apache configurado no httpd-vhosts.conf
$route_index = array_search($api_base, $request_uri);
$route_segment = ($route_index !== false && isset($request_uri[$route_index + 1])) ? $request_uri[$route_index + 1] : '';
$action_segment = ($route_index !== false && isset($request_uri[$route_index + 2])) ? $request_uri[$route_index + 2] : '';


switch ($method) {
    case 'GET':
        if ($route_segment === 'usuario') {
            // Rota GET /usuario (buscar um único usuário)
            $user->id = isset($_GET['id']) ? $_GET['id'] : 1; // Permite buscar por ID na URL, senão ID 1

            if ($user->readOne()) {
                $user_arr = array(
                    "id" => $user->id,
                    "nome" => $user->nome,
                    "idade" => $user->idade,
                    "bio" => $user->bio,
                    "rua" => $user->rua,
                    "numero" => $user->numero,
                    "bairro" => $user->bairro,
                    "cidade" => $user->cidade,
                    "url_foto" => $user->url_foto
                );
                http_response_code(200);
                echo json_encode($user_arr);
            } else {
                // Padrão para a primeira vez que a aplicação é usada
                $user->nome = "Novo Usuário";
                $user->idade = 0;
                $user->bio = "Escreva sua biografia aqui.";
                $user->rua = "Rua Exemplo";
                $user->numero = "123";
                $user->bairro = "Bairro Teste";
                $user->cidade = "Cidade Fictícia";
                $user->url_foto = "https://via.placeholder.com/200"; // URL de foto padrão

                if ($user->create()) {
                    http_response_code(201); // Criado
                    echo json_encode(array("message" => "Primeiro usuário criado com sucesso.", "id" => $db->lastInsertId()));
                } else {
                    http_response_code(503); // Não foi possível criar o usuário
                    echo json_encode(array("message" => "Não foi possível criar o primeiro usuário."));
                }
            }
        } elseif ($route_segment === 'usuarios') {
            if ($action_segment === 'search' && isset($_GET['keywords'])) {
                // Rota GET /usuarios/search?keywords=termo
                $keywords = $_GET['keywords'];
                $stmt = $user->search($keywords);
                $num = $stmt->rowCount();

                if($num > 0) {
                    $users_arr = array();
                    $users_arr["records"] = array();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        extract($row);
                        $user_item = array(
                            "id" => $id,
                            "nome" => $nome,
                            "idade" => $idade,
                            "bio" => $bio,
                            "rua" => $rua,
                            "numero" => $numero,
                            "bairro" => $bairro,
                            "cidade" => $cidade,
                            "url_foto" => $url_foto
                        );
                        array_push($users_arr["records"], $user_item);
                    }
                    http_response_code(200);
                    echo json_encode($users_arr);
                } else {
                    http_response_code(404);
                    echo json_encode(array("message" => "Nenhum usuário encontrado."));
                }
            } else {
                // Rota GET /usuarios (listar todos os usuários)
                $stmt = $user->readAll();
                $num = $stmt->rowCount();

                if($num > 0) {
                    $users_arr = array();
                    $users_arr["records"] = array();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        extract($row);
                        $user_item = array(
                            "id" => $id,
                            "nome" => $nome,
                            "idade" => $idade,
                            "bio" => $bio,
                            "rua" => $rua,
                            "numero" => $numero,
                            "bairro" => $bairro,
                            "cidade" => $cidade,
                            "url_foto" => $url_foto
                        );
                        array_push($users_arr["records"], $user_item);
                    }
                    http_response_code(200);
                    echo json_encode($users_arr);
                } else {
                    http_response_code(404);
                    echo json_encode(array("message" => "Nenhum usuário encontrado."));
                }
            }
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Rota GET não encontrada."));
        }
        break;

    case 'POST':
        if ($route_segment === 'usuario') {
            // Rota POST /usuario (atualiza os dados do usuário com ID 1)
            $data = json_decode(file_get_contents("php://input"));

            // Verifica se os dados necessários foram enviados
            if (
                !empty($data->id) &&
                !empty($data->nome) &&
                isset($data->idade) && // Idade pode ser 0
                !empty($data->bio) &&
                !empty($data->rua) &&
                !empty($data->numero) &&
                !empty($data->bairro) &&
                !empty($data->cidade) &&
                !empty($data->url_foto)
            ) {
                // Atribuindo os valores recebidos das propriedades do objeto usuário
                $user->id = $data->id;
                $user->nome = $data->nome;
                $user->idade = $data->idade;
                $user->bio = $data->bio;
                $user->rua = $data->rua;
                $user->numero = $data->numero;
                $user->bairro = $data->bairro;
                $user->cidade = $data->cidade;
                $user->url_foto = $data->url_foto;

                // Atualizar o usuário
                if ($user->update()) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Usuário atualizado com sucesso."));
                } else {
                    http_response_code(503);
                    echo json_encode(array("message" => "Não foi possível atualizar o usuário."));
                }
            } else {
                http_response_code(400); // Bad Request
                echo json_encode(array("message" => "Não foi possível atualizar o usuário. Dados incompletos."));
            }
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Rota POST não encontrada."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido."));
        break;
}
?>