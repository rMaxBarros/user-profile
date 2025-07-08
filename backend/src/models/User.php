<?php

class User {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nome;     
    public $idade;    
    public $bio;
    public $rua;      
    public $numero;   
    public $bairro;   
    public $cidade;   
    public $url_foto; 

    // Construtor com $db para conexão com o banco de dados
    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para ler um único usuário
    public function readOne() {
        // Selecionando todos os campos da tabela 'usuarios'
        $query = "SELECT
                    id, nome, idade, bio, rua, numero, bairro, cidade, url_foto
                  FROM
                    " . $this->table_name . "
                  WHERE
                    id = ?
                  LIMIT
                    0,1";

        // Preparando a query
        $stmt = $this->conn->prepare($query);

        // Vinculando o ID que será pesquisado
        $stmt->bindParam(1, $this->id);

        // Executando a query
        $stmt->execute();

        // Pegando a linha do resultado
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Atribuindo os valores da linha às propriedades do objeto
        if ($row) {
            $this->id = $row['id'];
            $this->nome = $row['nome'];
            $this->idade = $row['idade'];
            $this->bio = $row['bio'];
            $this->rua = $row['rua'];
            $this->numero = $row['numero'];
            $this->bairro = $row['bairro'];
            $this->cidade = $row['cidade'];
            $this->url_foto = $row['url_foto'];
            return true; // Usuário encontrado
        }
        return false; // Usuário não encontrado
    }

    // Método para atualizar um usuário
    public function update() {
        // Query de atualização
        $query = "UPDATE
                    " . $this->table_name . "
                  SET
                    nome = :nome,
                    idade = :idade,
                    bio = :bio,
                    rua = :rua,
                    numero = :numero,
                    bairro = :bairro,
                    cidade = :cidade,
                    url_foto = :url_foto
                  WHERE
                    id = :id";

        // Preparando a query
        $stmt = $this->conn->prepare($query);

        // Limpando os dados (remove tags HTML, espaços em branco...)
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->idade = htmlspecialchars(strip_tags($this->idade));
        $this->bio = htmlspecialchars(strip_tags($this->bio));
        $this->rua = htmlspecialchars(strip_tags($this->rua));
        $this->numero = htmlspecialchars(strip_tags($this->numero));
        $this->bairro = htmlspecialchars(strip_tags($this->bairro));
        $this->cidade = htmlspecialchars(strip_tags($this->cidade));
        $this->url_foto = $this->url_foto ? htmlspecialchars(strip_tags($this->url_foto)) : null;
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vinculando os parâmetros
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':idade', $this->idade);
        $stmt->bindParam(':bio', $this->bio);
        $stmt->bindParam(':rua', $this->rua);
        $stmt->bindParam(':numero', $this->numero);
        $stmt->bindParam(':bairro', $this->bairro);
        $stmt->bindParam(':cidade', $this->cidade);
        $stmt->bindParam(':url_foto', $this->url_foto);
        $stmt->bindParam(':id', $this->id);
        if ($this->url_foto === null) {
            $stmt->bindValue(':url_foto', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':url_foto', $this->url_foto);
        }
        $stmt->bindParam(':id', $this->id); 
          // Executando a query
          if ($stmt->execute()) {
              return true;
          }
          return false;
    }

    // Método para criar um usuário (usaremos para inserir o primeiro usuário se não existir)
    public function create() {
        // Query de criação
        $query = "INSERT INTO
                    " . $this->table_name . "
                  SET
                    nome = :nome,
                    idade = :idade,
                    bio = :bio,
                    rua = :rua,
                    numero = :numero,
                    bairro = :bairro,
                    cidade = :cidade,
                    url_foto = :url_foto";

        // Preparando a query
        $stmt = $this->conn->prepare($query);

        // Limpando os dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->idade = htmlspecialchars(strip_tags($this->idade));
        $this->bio = htmlspecialchars(strip_tags($this->bio));
        $this->rua = htmlspecialchars(strip_tags($this->rua));
        $this->numero = htmlspecialchars(strip_tags($this->numero));
        $this->bairro = htmlspecialchars(strip_tags($this->bairro));
        $this->cidade = htmlspecialchars(strip_tags($this->cidade));
        $this->url_foto = htmlspecialchars(strip_tags($this->url_foto));

        // Vinculando os parâmetros
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':idade', $this->idade);
        $stmt->bindParam(':bio', $this->bio);
        $stmt->bindParam(':rua', $this->rua);
        $stmt->bindParam(':numero', $this->numero);
        $stmt->bindParam(':bairro', $this->bairro);
        $stmt->bindParam(':cidade', $this->cidade);
        $stmt->bindParam(':url_foto', $this->url_foto);

        // Executando a query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Método para listar todos os usuários
    public function readAll() {
        $query = "SELECT
                    id, nome, idade, bio, rua, numero, bairro, cidade, url_foto
                  FROM
                    " . $this->table_name . "
                  ORDER BY
                    nome ASC";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        return $stmt;
    }

    // Método para buscar usuários
    public function search($keywords){
        // Query de busca - busca por nome, bairro, cidade ou ID
        $query = "SELECT
                    id, nome, idade, bio, rua, numero, bairro, cidade, url_foto
                  FROM
                    " . $this->table_name . "
                  WHERE
                    nome LIKE ? OR bairro LIKE ? OR cidade LIKE ? OR id LIKE ?
                  ORDER BY
                    nome ASC";

        $stmt = $this->conn->prepare($query);

        // Limpando as palavras-chave da busca
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%"; // Adiciona wildcards para busca parcial

        // Vinculando os parâmetros
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->bindParam(4, $keywords); // Busca por ID também

        $stmt->execute();

        return $stmt;
    }
}
?>