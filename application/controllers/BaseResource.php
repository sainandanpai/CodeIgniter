
<?php  
defined('BASEPATH') OR exit('No direct script access allowed');  
include 'UserDtoConverter.php';

class BaseResource extends CI_Controller {  
	private $userDtoConverter;

	public function __construct() {
		parent::__construct();
		$this->load->library(array('session'));
		$this->load->helper(array('url'));
		$this->load->model('UserInfo');
		$this->load->database();
		$this->load->helper(array('form', 'url'));
		$this->userDtoConverter = new UserDtoConverter(); 
	}
      
	/**
	 * Resolves the registration url and processes the requests
	 * 
	 * @access public
	 */
	public function register() {

		$this->load->helper('form');
		$this->load->library('form_validation');
		// load form helper and validation library
		$this->form_validation->set_rules('name', 'name', 'trim|required|alpha_numeric|min_length[4]|is_unique[users.name]', array('is_unique' => 'This username already exists. Please choose another one.'));
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
		$this->form_validation->set_rules('password_confirm', 'Confirm Password', 'trim|required|min_length[6]|matches[password]');

		if ($this->form_validation->run() === true) {
			
			$this->UserInfo->setName($this->input->post('name'));
			$this->UserInfo->setEmail($this->input->post('email'));
			$this->UserInfo->setPassword($this->input->post('password'));
			$userDto = new stdClass();
			$userDto = $this->userDtoConverter->buildUserDTO($this->UserInfo);
			$this->db->insert('users', $userDto);
			$this->db->trans_commit();
		} 

		$this->load->view('/register');

		
	}

	/**
	 * Resolves a get url and processes the login requests
	 * 
	 * @access public
	 */
	public function login() {

		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('user', 'Username', 'trim|required|alpha_numeric|min_length[4]|is_unique[users.username]', array('is_unique' => 'This username already exists. Please choose another one.'));
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');

		$username = $this->input->post('username');
		$password = $this->input->post('password');

		if ($this->validateUserLogin($username,$password) === true) {
			
			echo $this->retrieveUserId($username);

		} else {
			echo "Invalid account id and pass";
		}

		$this->load->view('/login');
	}


	public function uploadImage() {
		$config['upload_path'] = './images/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 2000;
        $config['max_width'] = 1500;
        $config['max_height'] = 1500;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());
            echo "error uploading image";
        } else {
            $imageData = $this->getImageDto($this->upload->data());
			$this->db->where('name', $this->retrieveUserId("nandan"));
			$isPersisted = $this->db->update('users', $imageData); //persist image
			$this->db->trans_commit();
			echo " image upload " . $isPersisted;
        }
		$error = array('error' => $this->upload->display_errors());
		
		$this->load->view('/imageupload',$error);
		
	}


	private function getImageDto($rawData) {
		return array('metadata' => base64_encode(json_encode($rawData)));
	}

	/**
	 * Resolves User login function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @param mixed $password
	 * @return bool true on success, false on failure
	 */
	private function validateUserLogin($username, $password) {
		
		$this->db->select('password');
		$this->db->from('users');
		$this->db->where('name', $username);
		$hash = $this->db->get()->row('password');
		
		return password_verify($password, $hash);
		
	}

	/**
	 * retrieves user id fom db.
	 * 
	 * @access public
	 * @param mixed $username
	 * @return int the user id
	 */
	private function retrieveUserId($username) {
		
		$this->db->select('name');
		$this->db->from('users');
		$this->db->where('name', $username);

		return $this->db->get()->row('name');
		
	}
	

	
}  
?>  
