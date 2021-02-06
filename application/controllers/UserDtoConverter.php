<?php

class UserDtoConverter {

	public function buildUserDTO($userInfo) {
		return array(
			'name'   => $userInfo->getName(),
			'email'      => $userInfo->getEmail(),
			'password'   => $userInfo->getPassword(),
			//'created_at' => date('Y-m-j H:i:s'),
			'active' => 1
		);
	}
}
?>
