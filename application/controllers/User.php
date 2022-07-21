<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once('/app/vendor/chriskacerguis/codeigniter-restserver/src/RestController.php');
include_once('/app/vendor/chriskacerguis/codeigniter-restserver/src/Format.php');

use chriskacerguis\RestServer\RestController;

class User extends RestController {

    function __construct()
    {
        parent::__construct();
        $this->load->model('Users_model');
        $this->load->model('Chat_model');
    }

    public function index_get()
    {
        $this->response( ['server'=>'Codeigniter is running...'], 200 );
    }

    //Endpoint register user
    public function create_post()
    {

        $response = array(
            "success"=>false,
            "message"=>"",
            "data"=>array()
        );
        $_POST = json_decode(file_get_contents("php://input"), true);
        //validate json data
        $this->form_validation->set_rules('name', 'Nombre', 'trim|min_length[4]|required');
        //validamos que no exista otro email igual por que es unique field en mysql
        $this->form_validation->set_rules('email', 'Correo', 'trim|required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Contraseña', 'trim|min_length[4]|required');

        if ($this->form_validation->run() == false) {
            $errors = $this->form_validation->error_array();
            $response['message'] = array('errors'=>$errors);
            $this->set_response($response, RestController::HTTP_BAD_REQUEST);
        }else{
            $request = $this->security->xss_clean($this->input->raw_input_stream);
            $output = $this->Users_model->add($request);
            $response['success'] = true;
            $response['message'] = 'created user';
            $response['data'] = $output;
            $this->set_response($response, RestController::HTTP_OK);
        }

    }

    //endpoint chatbot
    public function chat_post(){
        $response = array(
            "success"=>false,
            "message"=>"",
            "data"=>array()
        );
        //Only users auntenticados pueden iniciar un chat:)
        $headers = $this->input->request_headers();
        $auth = AUTHORIZATION::tokencheck($headers);
        if(!$auth['success']){ $this->set_response($auth, RestController::HTTP_UNAUTHORIZED); return; }

        $_POST = json_decode(file_get_contents("php://input"), true);
        //validamos que existan [question,quick_reply]
        $this->form_validation->set_rules('question', 'Pregunta', 'trim|min_length[4]|required');
        $this->form_validation->set_rules('quick_reply', 'Quick Reply', 'trim|required');
        if ($this->form_validation->run() == false) {
            $errors = $this->form_validation->error_array();
            $response['message'] = array('errors'=>$errors);
            $this->set_response($response, RestController::HTTP_BAD_REQUEST);
        }else{

            $question = $this->input->post('question');
            $quick_reply = $this->input->post('quick_reply');
            //if is quick_reply then query = [complete question]
            if($quick_reply == false){
                $query = preg_split('/\s+/', $question);
                $strQuery = implode('|', $query);
            }else{
                //Esto provocara 100% match en tabla questions
                $strQuery = $question;
                $query = array($question);
            }

            //Procedimiento para traer el row con mas coincidencias
            $questions = $this->Chat_model->getQuestions($strQuery);
            //if get results
            if(!empty($questions)){
                $count = array();
                foreach ($questions as $k => $val) {
                    $match = 0;
                    foreach ($query as $qv) {
                        if (strpos($val->question, $qv) !== FALSE) {
                            $match++;
                        }
                    }
                    //Guardamos como key el id de la pregunta
                    $count[$val->id] = $match;
                }
                //Ordenamos de forma decendente y traemos el primer key
                arsort($count);
                $idQuestion = array_key_first($count);
                $responses = $this->Chat_model->getResponses($idQuestion);
                if(!empty($responses)){
                    //var_dump($responses); die();
                    $response['success'] = true;
                    $response['message'] = 'get response';
                    $response['data'] = array('messages'=>$responses);
                    $this->set_response($response, RestController::HTTP_OK);
                    return;
                }
            }
            $response['success'] = true;
            $response['message'] = 'not found matches';
            $response['data'] = array('messages'=>array('response'=>'no entiendo la pregunta'));
            $this->set_response($response, RestController::HTTP_OK);
            return;
        }


    }

}








