<?php 

namespace Core\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class CommandRegistrationSuccess extends Event{

	const NAME = "user_bundle.command.registration_success";

	private $user;
	
	public function __construct($user){
		$this->user = $user;
	}

	public function getUser(){
		return $this->user;
	}
}