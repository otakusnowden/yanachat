<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
	}

	public function dashboard_output($output = null)
	{
		$this->load->view('dashboard.php',(array)$output);
	}

	public function index()
	{
		$this->dashboard_output((object)array('output' => '' , 'js_files' => array() , 'css_files' => array()));
	}

	public function users()
	{
		try{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('users');
			$crud->set_subject('Users');

			$crud->callback_before_insert(function($post_array){
			    $post_array['password'] = sha1($post_array['password']);
			    return $post_array;
			});

			$crud->callback_before_update(function($post_array){
			    $post_array['password'] = sha1($post_array['password']);
			    return $post_array;
			});


			$output = $crud->render();

			$this->dashboard_output($output);

		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
	}

	public function questions()
	{
		try{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('questions');
			$crud->set_subject('Questions');
			//$crud->set_relation('id','responses','qid');
			$crud->columns('id','question');
			$output = $crud->render();

			$this->dashboard_output($output);

		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
	}

	public function responses()
	{
		try{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('responses');
			//$crud->setRelationNtoN('questions', 'responses', 'id', 'qid', 'id', 'responses');
			$crud->columns('id','qid','response');
			$crud->set_subject('Responses');
			//$crud->set_relation('qid','questions','id');
			$output = $crud->render();

			$this->dashboard_output($output);

		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
	}

}






