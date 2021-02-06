<?php

class UserInfo extends CI_Model {

	private $name;
	private $email;
	private $password;

	public function setName($name)
	{
		$this->name = $name;
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}

	public function setPassword($password)
	{
		$this->password = password_hash($password, PASSWORD_BCRYPT);
	}

	
	public function getName() 
	{
		return $this->name;
	}

	public function getEmail() 
	{
		return $this->email;
	}

	public function getPassword() 
	{
		return $this->password;
	}
	
}
?>
