<?php 

header('Content-Type: text/html; charset=utf-8');

/*Função 


function exibir() {
	echo "Olá!";
}

exibir();

*/


/* Parâmentros de funções


//Var criada
function exibir($palavra1, $palavra2) {
	echo $palavra1 . ' ' . $palavra2 ;
}

//Var recebe input quando invocada
exibir('Hello ppl!', 'from earth!');


*/

/* Utilizando as funções com var

function exibir() {
	return 'Olá mundo!';
}

$retorno = exibir();

echo $retorno;

*/

/* Utilizando as funções com var

function exibir($parametro) {
	return $parametro;
}

echo exibir('whatever');

*/


/* FUnções e parametros
function comparar($num) {

	if ($num > 10) {
		return $num . ' é maior que 10';
	}
	if ($num < 10) {
		return $num . ' é menor que 10';
	}
		return 'o número é 10';
	
}

echo comparar(50);


*/

/*

class Usuario{
	public $id;
	public $nome;
	public $email;
}

$usuario = new Usuario();

$usuario->id = 1;
$usuario->nome = 'Marvin';
$usuario->email = 'marvin@coracaodeoutro.com';

echo $usuario->id. '<br>';
echo $usuario->nome. '<br>';
echo $usuario->email. '<br>';

*/

/*

class Usuario{
	protected $id;
	protected $nome;
	protected $email;

	public function setId($id)
	{
		$this->id = $id;
	}
	public function getId(){
		return $this->$id;
	}

	public function setName($nome)
	{
		$this->nome = $nome;
	}
	public function getName() {
		return $this->$nome;
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}
	public function getEmail(){
		return $this->$email;
	}
}


$usuario = new Usuario();

$usuario->setId(5);
$usuario->setName('I dont care');
$usuario->setEmail('tantofaz@hotmail.com');

var_dump($usuario);



class Admin extends Usuario
{
	public $senha;

	public function setSenha($senha) {
		//Criptografar o item utiliza md5
		$this->senha = md5($senha);
	}
	public function getSenha() {
		return $this->senha;
	}
}

$admin = new Admin();

var_dump($admin);

$usuario = new Usuario();

var_dump($usuario);

*/


//PHALCON 2 A PARTIR DAQUI
//PHALCON 2 A PARTIR DAQUI
//PHALCON 2 A PARTIR DAQUI
//PHALCON 2 A PARTIR DAQUI
//PHALCON 2 A PARTIR DAQUI
//PHALCON 2 A PARTIR DAQUI
//PHALCON 2 A PARTIR DAQUI
//PHALCON 2 A PARTIR DAQUI
//PHALCON 2 A PARTIR DAQUI
//PHALCON 2 A PARTIR DAQUI
//PHALCON 2 A PARTIR DAQUI




/*

$app = new Phalcon\Mvc\Micro();

$app->get('/diga/ola/{nome}', function ($nome)
{
	echo json_encode(array($nome, "uma", "informação", "importante"));
}
);

$app->notFound(function() use ($app) {
	$app->response->setStatusCOde(404, "Not Found")->sendHeaders();
	echo 'Me desculpe, mas parece que a varredura não encontrou a página que você procurava :( !!!';

});

$app->handle();

*/

header("Acess-Control-Allow-Origin: *");
header("Acess-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

$di = new \Phalcon\DI\FactoryDefault();

$di->set('db', function(){
    return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => "mariadb",
        "username" => "root",
        "password" => "123456",
        "dbname" => "operand_iscool"
    ));
});

$app = new \Phalcon\Mvc\Micro($di);

//Retrieves all bank accounts
$app->get('/v1/bankaccounts', function() use ($app) {

    $sql = "SELECT id,name,balance FROM bank_account ORDER BY name";
    $result = $app->db->query($sql);
    $result->setFetchMode(Phalcon\Db::FETCH_OBJ);
    $data = array();
    while ($bankAccount = $result->fetch()) {
        $data[] = array(
            'id' => $bankAccount->id,
            'name' => $bankAccount->name,
            'balance' => $bankAccount->balance,
        );
    }

    $response = new Phalcon\Http\Response();

    if ($data == false) {
        $response->setStatusCode(404, "Not Found");
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
    } else {
        $response->setJsonContent(array(
            'status' => 'FOUND',
            'data' => $data
        ));
    }

    return $response;

});

//Adds a new bank account
$app->post('/v1/bankaccounts', function() use ($app) {

    $bankAccount = $app->request->getPost();

    if (!bankAccount) {
    	$bankAccount = (array) $app->request->getJsonRawBody();
    }

    $response = new Phalcon\Http\Response();

    try {
        $result = $app->db->insert("bank_account",
            array($bankAccount['name'], $bankAccount['balance']),
            array("name", "balance")
        );

        $response->setStatusCode(201, "Created");
        $bankAccount['id'] = $app->db->lastInsertId();
        $response->setJsonContent(array('status' => 'OK', 'data' => $bankAccount));

    } catch (Exception $e) {
        $response->setStatusCode(409, "Conflict");
        $errors[] = $e->getMessage();
        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }

    return $response;

});

$app->options('/v1/bankaccounts', function() use ($app) {
	$app->response->setHeader('Acess-Control-Allow-Origin', '*');
});

//Updates bank account based on primary key
$app->put('/v1/bankaccounts/{id:[0-9]+}', function($id) use ($app) {

    $bankAccount = $app->request->getPut();
    $response = new Phalcon\Http\Response();

    try {
        $result = $app->db->update("bank_account",
            array("name", "balance"),
            array($bankAccount['name'], $bankAccount['balance']),
            "id = $id"
        );

        $response->setJsonContent(array('status' => 'OK'));

    } catch (Exception $e) {
        $response->setStatusCode(409, "Conflict");
        $errors[] = $e->getMessage();
        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }

    return $response;

});

//Deletes bank account based on primary key
$app->delete('/v1/bankaccounts/{id:[0-9]+}', function($id) use ($app) {
    $response = new Phalcon\Http\Response();

    try {
        $result = $app->db->delete("bank_account",
            "id = $id"
        );

        $response->setJsonContent(array('status' => 'OK'));

    } catch (Exception $e) {
        $response->setStatusCode(409, "Conflict");
        $errors[] = $e->getMessage();
        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }

    return $response;
});

$app->get('/v1/bankaccounts/search/{id:[0-9]+}', function($id) use ($app) {
    
    $sql = "SELECT id,name,balance FROM bank_account WHERE id = ?";

    $result = $app->db->query($sql, array($id));
    $result->setFetchMode(Phalcon\Db::FETCH_OBJ);

    $data  = array();
    $bankAccount = $result->fetch();
    $response = new Phalcon\Http\Response();

    if ($bankAccount == false) {
        $response->setStatusCode(404, 'Not Found');
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
    } else {
        $sqlOperations = "SELECT id, operation, bank_account_id, date, value FROM bank_account_operations WHERE bank_account_id = ". $id. " ORDER BY date";
        $resultOperations = $app->db->query($sqlOperations);
        $resultOperations->setFetchMode(Phalcon\Db::FETCH_OBJ);
        $bankAccountOperations = $resultOperations->fetchAll();

        $response->setJsonContent(array(
            'id' => $bankAccount->id,
            'name' => $bankAccount->name,
            'balance' => $bankAccount->balance,
            'operations' => $bankAccountOperations,

            ));
        
        return $response;
    }
});

$app->post('/v1/bankaccounts/deposit', function () use ($app){
    $depositInfo = $app->request->getPost();
    if (!$depositInfo) {
        $depositInfo = (array)$app->request->getJsonRawBody();
    }

    $response = new Phalcon\Http\Response();

    try {
            $result = $app->db->insert("bank_account_operations",
                array("deposit",$depositInfo['bank_account_id'], $depositInfo['value'],date('Y-m-d H:i:s')),
                array("operation", "bank_account_id", "value", "date")
            );

            //atualizar saldo da conta
            $sqlUpdate = "UPDATE bank_account set balance = (SELECT SUM(value) as balance FROM bank_account_operations WHERE bank_account_id = ?) WHERE id=?";
            $app->db->query($sqlUpdate, array($depositInfo['bank_account_id'], $depositInfo['bank_account_id']));

            $response->setStatusCode(201, "Created");
            $response->setJsonContent(array('status' => 'OK'));

        } catch (Exception $e) {
            $response->setStatusCode(409, "Conflict");
            $errors[] = $e->getMessage();
            $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
        }

        return $response;

});

$app->post('/v1/bankaccounts/saque', function () use ($app){
    $saqueInfo = $app->request->getPost();
    if (!$saqueInfo) {
        $saqueInfo = (array)$app->request->getJsonRawBody();
    }

    $response = new Phalcon\Http\Response();

        try {
            $result = $app->db->insert("bank_account_operations",
                array("saque",$saqueInfo['bank_account_id'], $saqueInfo['value']*-1,date('Y-m-d H:i:s')),
                array("operation", "bank_account_id", "value", "date")
            );

            //atualizar saldo da conta
            $sqlUpdate = "UPDATE bank_account set balance = (SELECT SUM(value) as balance FROM bank_account_operations WHERE bank_account_id = ?) WHERE id=?";
            $app->db->query($sqlUpdate, array($saqueInfo['bank_account_id'], $saqueInfo['bank_account_id']));

            $response->setStatusCode(201, "Created");
            $response->setJsonContent(array('status' => 'OK'));

        } catch (Exception $e) {
            $response->setStatusCode(409, "Conflict");
            $errors[] = $e->getMessage();
            $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
        }

        return $response;

});


$app->get('/', function() use ($app){
	echo "Operand is cool";
});

$app->notFound(function() use ($app) {
	$app->response->setStatusCode(404, "Not Found")->sendHeaders();
	echo include 'a.php'; ;

});


$app->handle();


