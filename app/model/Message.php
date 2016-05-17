<?php

class Message_model extends ACWModel
{
	
	public static function init()
	{
		Login_model::check();	
	}
	public function get_all($screen){
		$lang = ACWSession::get('lang');
		if(isset($lang)==FALSE){
			$lang = 1;
		}		
		$sql = "select * from message_lang where screen=:screen and lang_key = :lang_key";
		return $this->query($sql,array('screen'=>$screen,'lang_key' => $lang ));		
	}
	public static function get_message($creen){
		$db = new Message_model();
		$data = $db->get_all($creen);
		$res = array();
		foreach($data as $item){
			$res[$item['msg_no']] = $item['des'];
		}		
		return $res;		
	}
	
}
