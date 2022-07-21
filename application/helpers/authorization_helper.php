<?php

class AUTHORIZATION
{
    public static function validateTimestamp($token)
    {
        $CI =& get_instance();
        $token = self::validateToken($token);
        if ($token != false && (now() - $token->timestamp < ($CI->config->item('token_timeout') * 60))) {
            return $token;
        }
        return false;
    }

    public static function validateToken($token)
    {
        $CI =& get_instance();
        return JWT::decode($token, $CI->config->item('jwt_key'));
    }

    public static function generateToken($data)
    {
        $CI =& get_instance();
        return JWT::encode($data, $CI->config->item('jwt_key'));
    }


    public static function tokencheck($headers) {
        
        $response = array(
            "success" => false,
            "message" => "Unauthoised",
            "data" => array()
        );
        
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            try{
                $decodedToken = self::validateToken($token);
                $response['success'] = true;
                $response['message'] = 'user authenticate';
                $response['data'] = $decodedToken->payload;
                //$this->set_response($response, RestController::HTTP_OK);
                return $response;

            }catch(Exception $e) {
                $response['message'] = 'invalid token';
                //$this->set_response($response, RestController::HTTP_UNAUTHORIZED);
                return $response;
            }
        }
        return $response;
        //$this->set_response($response, RestController::HTTP_UNAUTHORIZED);
    }



}