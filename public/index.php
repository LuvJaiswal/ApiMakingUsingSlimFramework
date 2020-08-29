<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// use Slim\Factory\AppFactory;


require '../vendor/autoload.php';
require '../includes/DbOperations.php';

$app = new \Slim\App;


$app->post('/createuser',function(Request $request, Response $response){
if(!haveEmptyParameters(array('email', 'password', 'name'), $response)){

    $request_data = $request->getParsedBody();

    $email = $request_data['email'];
    $password = $request_data['password'];
    $name = $request_data['name'];

$hash_password = password_hash($password,PASSWORD_DEFAULT);

    $db = new DbOperations;

    $result = $db->createUser($email,$hash_password,$name);
    

    if($result == USER_CREATED){

        $message = array(); 
        $message['error'] = false; 
        $message['message'] = 'User created successfully';

        $response->write(json_encode($message));

        return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(201);

    }else if($result == USER_FAILURE){

        $message = array(); 
        $message['error'] = true; 
        $message['message'] = 'Some error occurred';

        $response->write(json_encode($message));

        return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(422);    

    }else if($result == USER_EXISTS){
        $message = array(); 
        $message['error'] = true; 
        $message['message'] = 'User Already Exists';

        $response->write(json_encode($message));

        return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(422);    
    }
}
return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(422);    
});

function haveEmptyParameters($required_params, $response){
    $error = false;
    $error_params = '';
    $request_params = $_REQUEST;

    foreach($required_params as $param){
        if(!isset($request_params[$param]) || strlen($request_params[$param]<=0)){
            $error = true;
            $error_params .= $param . ', ';

        }
    }

    if($error){
        $error_detail = array();
        $error_detail['error']= true;
        $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . 'are missing or empty';

        $response->write(json_encode($error_detail));


    }

    return $error;


}

$app->run();