<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// use Slim\Factory\AppFactory;


require '../vendor/autoload.php';
require '../includes/DbOperations.php';

$app = new \Slim\App([
  'settings' =>[
      'displayErrorDetails' => true
  ]

]);


/***
 * CREATE USER
 */



$app->post('/createuser',function(Request $request, Response $response){
if(!haveEmptyParameters(array('email', 'password', 'name'), $response)){

    $request_data = $request->getParsedBody();

    $email = $request_data['email'];
    $password = $request_data['password'];
    $name = $request_data['name'];

    $hash_password = password_hash($password,PASSWORD_DEFAULT);



    $db = new DbOperations;

    // $result = $db->createUser($email,$hash_password, $name);
    $result = $db->createUser($email, $hash_password, $name);

    

    if($result == USER_CREATED){

        $message = array();
        $message['error'] = false;
        $message['message'] = 'User created Successfully';

        $response->write(json_encode($message));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(201);


    }else if($result == USER_FAILURE){

        $message = array();
        $message['error'] = true;
        $message['message'] = 'User failed, error occured';

        $response->write(json_encode($message));
       
        return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);

    }else if($result == USER_EXISTS){
        $message = array();
        $message['error'] = true;
        $message['message'] = 'User already exists';

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



/***
 * USER LOGIN
 */

$app->post('/userlogin', function(Request $request, Response $response){
    if(!haveEmptyParameters(array('email', 'password'), $response)){
        $request_data = $request->getParsedBody();

        $email = $request_data['email'];
        $password = $request_data['password'];

        $db = new DbOperations;

        $result = $db->userLogin($email, $password);
        if($result == USER_AUTHENTICATED){
            $user = $db-> getUserByEmail($email);
            
            $response_data = array();
            $response_data['error'] = false;
            $response_data['message'] = 'Login Successful';
            $response_data['user'] =$user;

            $response->write(json_encode($response_data));

            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);






        }else if($result == USER_NOT_FOUND){

            $response_data = array();
            $response_data['error'] = true;
            $response_data['message'] = 'User donot exists';
           

            $response->write(json_encode($response_data));

            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);

        }else if($result == USER_PASSWORD_DO_NOT_MATCH){

            $response_data = array();
            $response_data['error'] = true;
            $response_data['message'] = 'Password donot match';
         

            $response->write(json_encode($response_data));

            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
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
        if(!isset($request_params[$param]) || strlen($request_params[$param])<=0){
            $error = true;
            $error_params .=$param . ', ';

        }
    }

    if($error){
        $error_detail = array();
        $error_detail['error']=true;
        $error_detail['message'] = 'Required parameters:  ' . substr($error_params, 0, -1) . 'are missing or empty';

        $response->write(json_encode($error_detail));


    }

    return $error;


}

$app->run();