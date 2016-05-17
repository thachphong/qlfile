<?php

class Message_model extends ACWModel
{
	
	public static function init()
	{
		Login_model::check();	
	}
	public function get_all($screen){
		
		$sql = "select * from message_lang whre screen=:screen";
		return $this->query($sql,array('screen'=>$screen));		
	}
	
	
}
