<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

/*
 * Notes:
 * 1. This project contains .htaccess file for linux and apache server.
 *
 * 2. Watch 'encryption_key' in application\config\config.php
 * 
 * 3. Configuration 'jwt_key' in application\config\jwt.php
 *
 * 4. This endpoint use RestServer library
 */

class Auth extends RestController {


    public function login_post() {

        $user = $this->post('email');
        $pswd = $this->post('password');
        $response = $this->validateUser($user,$pswd);
        //verificacion de usuario y password 
        
        if ($response['success'] == true){
            $tokenData = array();
            $tokenData['header'] = array("typ" => "JWT", "alg" => "HS256");
            //var_dump($response['data'][0]->id); die();
            $tokenData['payload'] = array(
                'uid' => $response['data'][0]->id,
                'sub' => $user,
                'exp' => time() + (7 * 24 * 60 * 60),//una semana
                'iat' => time(),
                'jti' => rand(5000, 999999)
            );

            $response['token'] = AUTHORIZATION::generateToken($tokenData);
            $this->set_response($response, RestController::HTTP_OK);
        }else{
             $this->set_response($response, RestController::HTTP_UNAUTHORIZED);
        } 
        
    }

    public function validateUser($user,$pswd){
        $data = array(
            "success"=>false,
            "message"=>"",
            "data"=>array()
        );

        $this->load->model('Users_model');
        $output = $this->Users_model->getUser($user);
        //var_dump($output);
        //die();
        if(!empty($output)){
            //Si exite el usuario validamos el password codificado en sha1
            if($output[0]->{'password'} == sha1($pswd)){
                unset($output[0]->{'password'});
                $data['success'] = true;
                $data['message'] = 'user authenticate';
                $data['data'] = $output;
            }else{
                $data['message'] = 'incorrect password';
            }
            return $data;
        }
        $data['message'] = 'user not found';
        return $data;
    }
    
    //this methos id for test token 
    public function tokencheck_post() {
        $headers = $this->input->request_headers();
        $response = array(
            "success" => false,
            "message" => "Unauthoised",
            "data" => array()
        );        
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            try{
                $decodedToken = AUTHORIZATION::validateToken($token);
                $response['success'] = true;
                $response['message'] = 'user authenticate';
                $response['data'] = $decodedToken->payload;
                $this->set_response($response, RestController::HTTP_OK);
                return;
                //return $response;

            }catch(Exception $e) {
                $response['message'] = 'invalid token';
                $this->set_response($response, RestController::HTTP_UNAUTHORIZED);
                return;
                //return $response;
            }
        }
        //return $response;
        $this->set_response($response, RestController::HTTP_UNAUTHORIZED);
        return;
    }

}
