<?php
/**
 * Mail Class
 * @author VNIT
 * @since 20150411
 * @version 0.1
 * @todo : send mail smtp using phpmailer
 */

class Mail_lib extends PHPMailer_lib
{	
	// Construct function
	public function __construct($info = null) {
	    //Tell PHPMailer to use SMTP
	    $this->isSMTP();
	    
	    //Enable SMTP debugging
	    // 0 = off (for production use)
	    // 1 = client messages
	    // 2 = client and server messages
	    $this->SMTPDebug = 0;
	    //Whether to use SMTP authentication
	    $this->SMTPAuth = true;
	    $this->setFrom(AKAGANE_MAIL_ADMIN);
        $this->SMTPSecure="ssl"; // add phong
	    if(empty($info) == true) {
	        // default account
	        $this->Host = AKAGANE_MAIL_HOST;
	        $this->Username = AKAGANE_MAIL_USERNAME;
	        $this->Password = AKAGANE_MAIL_PASS;
	        $this->Port = AKAGANE_MAIL_PORT;
	    } else {      
	        // set up new account
	        if(isset($info['host']) && isset($info['username']) && isset($info['pass']) 
	            && !empty($info['host']) && !empty($info['username']) && !empty($info['pass'])) {
	                $this->Host = $info['host'];
	                $this->Username = $info['username'];
	                $this->Password = $info['pass'];
	                if(isset($info['port'])) {
	                    $this->Port = $info['port'];
	                }
	        }
	    }
	}

	public function loadBody($file) {
	    $base_url = ACW_ROOT_DIR . "/user/template/";
	    $file = $base_url . $file;
	    $this->msgHTML(file_get_contents($file), $base_url);
	}
	
	public function AddListAddress($list_mail) {
	    if(count($list_mail) > 0) {
	        $mail_arr = array();
	        foreach ($list_mail as $item) {
	            if($this->checkReplaceEmail($item['mail_address'], $mail_arr) == true) {
	                array_push($mail_arr, $item['mail_address']);
	                $this->addAddress($item['mail_address']);
	            }
	        }#end foreach
	    }
	}
	
	public function checkReplaceEmail($check, $maillist) {
	    if(count($maillist) > 0) {
	        foreach ($maillist as $item) {
	            if($item == $check) {
	                return false;
	            }
	        }
	    }
	    return true;
	}
	public function replaceBody($replacements) {
	    foreach($replacements as $find => $replace)
	    {
	        $this->Body = preg_replace('/\[\[' . preg_quote($find, '/') . '\]\]/', $replace, $this->Body);
	    }
	}
}
