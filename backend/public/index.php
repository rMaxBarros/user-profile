<?php
// Definir o cabeçalho para permitir requisições de origens diferentes (CORS)
header("Access-Control-Allow-Origin: *");

// Definir o cabeçalho para permitir requisições de origens diferentes (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$DEFAULT_PROFILE_IMAGE = 'https://t3.ftcdn.net/jpg/05/16/27/58/360_F_516275801_f3Fsp17x6HQK0xQgDQEELoTuERO4SsWV.jpg';

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
                $user->url_foto = "https://via.placeholder.com/200";

                if ($user->create()) {
                    $user_arr = array(
                        "id" => $db->lastInsertId(), // Obtém o ID do último inserido
                        "nome" => $user->nome,
                        "idade" => $user->idade,
                        "bio" => $user->bio,
                        "rua" => $user->rua,
                        "numero" => $user->numero,
                        "bairro" => $user->bairro,
                        "cidade" => $user->cidade,
                        "url_foto" => $user->url_foto
                    );

                    http_response_code(201); // Criado
                    echo json_encode($user_arr); // Retorna o objeto completo do usuário
                } else {
                    http_response_code(503); // Não foi possível criar o usuário
                    echo json_encode(array("message" => "Não foi possível criar o usuário."));
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

    case 'PUT':
        if ($route_segment === 'usuario') {
             http_response_code(405); // Method Not Allowed
             echo json_encode(array("message" => "Método PUT não permitido para /usuario. Use PUT para atualizar."));
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Rota PUT não encontrada ou não suportada para este segmento."));
        }
        break;

    case 'POST':

        $raw_input = file_get_contents("php://input");

        if ($route_segment === 'usuario') {

            $data = (object)$_POST;

            $upload_dir = '../uploads/'; 
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true); // Cria o diretório se não existir
            }

            $profile_image_url = $_POST['url_foto_existente'] ?? null;

            if (isset($_POST['url_foto_existente']) && $_POST['url_foto_existente'] === '') {
                 $profile_image_url = null; // O usuário quer remover a foto
            }

            // Verifica se um arquivo de imagem foi enviado
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['profile_image'];

                // Validação básica do arquivo
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; // 5 MB

                if (!in_array($file['type'], $allowed_types)) {
                    http_response_code(400);
                    echo json_encode(array("message" => "Tipo de arquivo não permitido. Apenas JPG, PNG e GIF são aceitos."));
                    exit();
                }
                if ($file['size'] > $max_size) {
                    http_response_code(400);
                    echo json_encode(array("message" => "O arquivo é muito grande. Tamanho máximo é 5MB."));
                    exit();
                }

                // Gera um nome de arquivo único para evitar colisões
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $new_file_name = uniqid('profile_') . '.' . $extension;
                $target_file = $upload_dir . $new_file_name;

                // Move o arquivo temporário para o destino final
                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    // Guarda o caminho completo para salvar no banco de dados
                    $profile_image_url = 'http://localhost/projects/sync-360/backend/uploads/' . $new_file_name;

                } else {

                    http_response_code(500);
                    // echo json_encode(array("message" => "Não foi possível mover o arquivo de imagem enviado."));
                    // exit();
                    echo json_encode(array(
                    "message" => "DEBUG: Dados incompletos ou inválidos.",
                    "POST_data" => $_POST, // Conteúdo de $_POST
                    "FILES_data" => $_FILES, // Conteúdo de $_FILES
                    "debug_info" => "Verifique se todos os campos esperados estão em POST_data."
                ));
                exit();
                }
            }

            // Verifica se os dados necessários para atualização foram enviados
            if (
                !empty($data->id) &&
                !empty($data->nome) &&
                isset($data->idade) && (is_numeric($data->idade) || $data->idade === '') &&
                !empty($data->bio) &&
                !empty($data->rua) &&
                !empty($data->numero) &&
                !empty($data->bairro) &&
                !empty($data->cidade)
            ) {
                // Atribuindo os valores recebidos das propriedades do objeto usuário
                $user->id = $data->id;
                $user->nome = $data->nome;
                $user->idade = (int)$data->idade;
                $user->bio = $data->bio;
                $user->rua = $data->rua;
                $user->numero = $data->numero;
                $user->bairro = $data->bairro;
                $user->cidade = $data->cidade;
                $user->url_foto = $profile_image_url;

                // Tentar atualizar o usuário
                if ($user->update()) {
                    http_response_code(200); // OK
                    echo json_encode(array("message" => "Usuário atualizado com sucesso."));
                } else {
                    http_response_code(503); // Service Unavailable
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