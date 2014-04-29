<?php

namespace Model;

class User extends Base {

	protected $_table_name = "user";

	// Load currently logged in user, if any
	public function loadCurrent() {
		$f3 = \Base::instance();
		if($user_id = $f3->get("SESSION.user_id")) {
			$this->load(array("id = ? AND deleted_date IS NULL", $user_id));
			if($this->id) {
				$f3->set("user", $this->cast());
				$f3->set("user_obj", $this);
			}
		}
		return $this;
	}

	public function verify_password($password) {
		if($this->dry() || empty($this->password)) {
			return false;
		}
		$security = \Helper\Security::instance();
		return $security->bcrypt_verify($this->password, $password);
	}

	// Get path to user's avatar or gravatar
	public function avatar($size = 80) {
		$f3 = \Base::instance();
		if(!$this->get("id")) {
			return false;
		}
		if($this->get("avatar_filename") && is_file($f3->get("ROOT") . "/uploads/avatars/" . $this->get("avatar_filename"))) {
			return "/avatar/$size/" . $this->get("id") . ".png";
		}
		return gravatar($this->get("email"), $size);
	}

}

