<?php
/**
 * ユーザ管理
 *
*/
class Dwg_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
		Login_model::check();	// ログインチェック
	}
	
	public static function validate($action, &$param)
	{		
		return true;
	}

	/**
	* 初期表示
	*/
	public static function action_blank()
	{	 
		$usr = new  User_model();
		
		$ttql_list=$usr->get_user_bylevel(array('trungtam_quanly'=>1));
		return ACWView::template('dwg.html',array(
                                    'data_rows' =>array()
                                    ,'search_file_name'=>''
                                    ,'search_banve_name'=>''
                                    ,'ttql_list'=>$ttql_list
                                    ,'dwg_status'=>''
                                    )
                                    );
	}
	public static function action_index()
	{	
        $param = self::get_param(array(
			'search_file_name',
			'search_banve_name',
            'dwg_status'
		));
        $db= new Dwg_model();
     
        $login_info = ACWSession::get('user_info');
        $data = $db->get_file_rows($param);
		return ACWView::template('dwg.html',array(
                                    'data_rows' =>$data
                                    ,'search_file_name'=>$param['search_file_name']
                                    ,'search_banve_name'=>$param['search_banve_name']
                                    ,'dwg_status'=>$param['dwg_status']
                                    ,'user_id'=>$login_info['user_id']
                                    ));
	}
	public static function action_search()
	{	
        $param = self::get_param(array(
			'search_file_name',
			'search_banve_name',
            'dwg_status'
		));
        $db= new Dwg_model();
     
        $login_info = ACWSession::get('user_info');
        $data = $db->get_file_rows($param);
		return ACWView::template('dwg/list.html',array(
                                    'data_rows' =>$data,
                                    'user_id'=>$login_info['user_id']
                                    ));
	}
	public static function action_update(){
		 $param = self::get_param(array(			
			'note',
            'list_req',
            'usr_ttql'
		));
        $db= new Dwg_model();
        $list_id = array();
        $list_name = array();
        $result['status'] ='OK';
        foreach($param['list_req'] as $file){	
        	$exp = explode('|',$file);	
        	$list_id[] = $exp[0];
        	$list_name[] = $exp[1];
		}
		
		if($db->insert_dwg($param,$list_id)){
			if(!$db->send_mail($param['note'],$list_name,$param['usr_ttql'])){
				$result['status'] ='NG';
			}
		}else{
			$result['status'] ='NG';
		}
		return ACWView::json($result);
        
	}
	public static function action_updstatus(){
		 $param = self::get_param(array(
			'dwg_id',
			'dwg_status',           
		));
		$result['status'] ='OK';
		$db= new Dwg_model();
		$info = $db->update_status($param['dwg_id'],$param['dwg_status']);
		
		$db->send_mail_appr($info['file_name'],$param['dwg_status'],$info['email']);
		
		//$result['status'] ='NG';
		
		return ACWView::json($result);
	}
	public function insert_dwg($param,$list_file){
		$sql ="insert into dwg(	file_id,user_req_id,user_app_id,req_date,status,note)
		value(:file_id,:user_req_id,:user_app_id,now(),1,:note)";
		$login_info = ACWSession::get('user_info');
		$par_sql['user_req_id'] = $login_info['user_id'];
		$par_sql['user_app_id'] = $param['usr_ttql'];
		$par_sql['note'] = $param['note'];
		foreach($list_file as $file){			
			$par_sql['file_id'] = $file;
			$this->execute($sql,$par_sql);
		}
		return TRUE;
	}
	public function update_status($dwg_id,$status){
		$sql="update dwg set status = :status, app_date=now() where dwg_id=:dwg_id";
		$this->execute($sql,array('status'=> $status,'dwg_id'=>$dwg_id	));
		$sql ="select mu.email,f.file_name from dwg 
				inner join file f on f.file_id = dwg.file_id
				inner JOIN m_user mu on mu.user_id = dwg.user_req_id
				where dwg.dwg_id = :dwg_id";
		$res = $this->query($sql,array('dwg_id'=>$dwg_id));
		if(count($res) > 0){
			return $res[0];
		}
		return array();
	}
	public function get_file_rows($param,$file_type ='')
	{
        $login_info = ACWSession::get('user_info');
        $filter = "'pdf','rar'";
        if(strlen($file_type)>0){
			$filter =$file_type;
		}
		$dwg ="left join dwg on dwg.file_id = t.file_id ";
		if (isset($param['dwg_status']) && !empty($param['dwg_status'])) {			
			$dwg ="inner join dwg on dwg.file_id = t.file_id and dwg.status=" . $param['dwg_status'];
		}
		if($login_info['trungtam_quanly'] =='1'){
			$dwg .=" and (dwg.user_app_id = " .$login_info['user_id'] ." or dwg.user_req_id = " .$login_info['user_id'].")" ; 
		}else{
			$dwg .=" and dwg.user_req_id = " .$login_info['user_id'] ; 
		}
        //lay tat ca folder ma user co quyen,co quyen o folder cha thi co quyen o tat ca folder con
        $this->query("select f_get_allfolder_child(:user_id) ",array('user_id'=>$login_info['user_id']));
		$sql = "SELECT	DISTINCT			 
				t.file_id ,
            	t.file_name,
            	t.file_type,
                t.don_id,
                t.`status` trangthai,
                u.user_name_disp user_name,							
							d.tieude,d.don_no,
							(case 
				  when d.loaidon = 0 then 'Tạo mới'
				  when d.loaidon = 1 then 'Cập nhật'
				  end)  loaidon,
          DATE_FORMAT(dwg.req_date,'%d/%m/%Y %H:%i:%s') add_datetime,     
          dwg.status as dwg_status,
          dwg.user_req_id,dwg.user_app_id,
          dwg.note,
          IFNULL(dwg.dwg_id,0) dwg_id,
          bv.banve_name
			FROM	file t
				 inner join (select a.file_name,max(a.file_id) file_id from file a where del_flg=0 group by a.file_name) mx
									on mx.file_id = t.file_id 
				inner join don d on d.don_id = t.don_id and d.del_flg = 0
				inner join don_folder df on df.don_id = t.don_id
				inner JOIN (SELECT DISTINCT * from temp_folder) tmp on tmp.folder_id = df.folder_id
       
				$dwg 
				left join m_user u on dwg.user_req_id = u.user_id
				left join banve bv on t.file_name like CONCAT(bv.kho_giay,bv.banve_no,'%') and bv.del_flg = 0 
            where /*t.`status`=3
                and*/ t.file_type = 'dwg'
                and t.del_flg = 0
		";
		$sql_param = array();
		if (isset($param['search_file_name']) && !empty($param['search_file_name'])) {
			$sql_param['file_name'] =  '%' . SQL_lib::escape_like($param['search_file_name']) . '%';
			$sql .= " and lower(t.file_name) like lower(:file_name) ";
        }
        if (isset($param['search_tieude']) && !empty($param['search_tieude'])) {
			$sql_param['tieude'] = '%' . SQL_lib::escape_like($param['search_file_name']) . '%';
			$sql .= " and lower(d.tieude) like lower(:tieude) ";
		}
		if (isset($param['search_banve_name']) && !empty($param['search_banve_name'])) {
			$sql_param['banve_name'] = '%' . SQL_lib::escape_like($param['search_banve_name']) . '%';
			$sql .= " and lower(bv.banve_name) like lower(:banve_name) ";
		}
		
		 
		$sql .= "
			ORDER BY
				t.file_name
		";		
        //var_dump($sql);die;
		return $this->query($sql, $sql_param);
	}
	public function send_mail($note,$file_list,$user_app)
    {
        $str = "";
        foreach($file_list as $item){
			$str .="<p>".$item."</p>";
		}
		$login_info = ACWSession::get('user_info');
		$user_req = $login_info['user_id'];
		$date = date('d/m/Y H:i:s');
        $body_tmp = '
                <p>
                Ngày：　'.$date.'
                </p>
                <p>
                Người gửi：　'.$login_info['user_name_disp'].'
                </p>
                <p>
                --------------------------------
                </p>
                <p>Lý do: '.nl2br($note).'</p>
                <p>
                Danh sách file cần duyệt :
                </p>
                '.$str.'                                
                <p>
                Link web：　'.ACW_BASE_URL.'dwg?dwg_status=1
                </p>
                <p>
                --------------------------------
                </p>
			    ';
          
			$replacements['BODY'] = $body_tmp;
			$dont_send = 0;
			$mail_subject = '';
			$list_id[] = $user_req;
			$list_id[] =  $user_app;
            $list_mail = $this->get_email_add($list_id);
            $mail_to = array();            
            $mail_cc = array();
           
			$mail_subject = '[DWG]Yêu cầu duyệt download file Dwg ('.$date.')';
			$replacements['HEADER'] = '<h3 style="font-weight: bold;">Yêu cầu duyệt download file Dwg</h3>';
        	$mail_to[]['mail_address']=$list_mail[$user_app];
            $mail_cc[] =$list_mail[$user_req];
           
			        
			$email = new Mail_lib();
			
			if(count($mail_to) > 0 && $dont_send == 0) {
			    $email->AddListAddress($mail_to);
                foreach($mail_cc as $cc){
                    $email->addCC($cc);
                }
			    $email->Subject = $mail_subject;                
			    $email->loadbody('template_mail.html');
			    $email->replaceBody($replacements);
			    $result = $email->send();
               // ACWLog::debug_var('---don----','result: '.$result);
               return $result;
			}
			return FALSE;
    }
    public function send_mail_appr($file_name,$status,$email_to)
    {
        
		$date = date('d/m/Y H:i:s');
       
			$mail_subject = '';
			/*$list_id[] = $user_id_to;			
            $list_mail = $this->get_email_add($list_id);
            $mail_to = array(); */          
         
            if($status ==3){
				$mail_subject = '[Duyệt DWG]Đã duyệt yêu cầu duyệt download file Dwg ('.$date.')';
				$replacements['HEADER'] = '<h3 style="font-weight: bold;">Đã duyệt yêu cầu duyệt download file Dwg</h3>';
				$content = "<p>File được duyệt : </p>";
			}elseif($status ==2){
				$mail_subject = '[Không Duyệt DWG]Không duyệt yêu cầu duyệt download file Dwg ('.$date.')';
				$replacements['HEADER'] = '<h3 style="font-weight: bold;">Không duyệt yêu cầu duyệt download file Dwg</h3>';
				$content = "<p>File không được duyệt : </p>";
			}
			
			$body_tmp = '
                <p>
                Ngày：　'.$date.'
                </p>               
                <p>
                --------------------------------
                </p>             
                <p>File được duyệt : </p>
                '.$file_name.'                                
                <p>
                Link web：　'.ACW_BASE_URL.'dwg?search_file_name='.$file_name.'
                </p>
                <p>
                --------------------------------
                </p>
			    ';
          
			$replacements['BODY'] = $body_tmp;
        	$mail_to[]['mail_address']=$email_to;
        	        
			$email = new Mail_lib();
			
			if(count($mail_to) > 0 ) {
			    $email->AddListAddress($mail_to);               
			    $email->Subject = $mail_subject;                
			    $email->loadbody('template_mail.html');
			    $email->replaceBody($replacements);
			    $result = $email->send();
               
               return $result;
			}
			return FALSE;
    }
    public function get_email_add($user_list){
    	$ids = implode(',',$user_list);
		$sql = "select user_id,email from m_user
				where user_id in ($ids)";
		$res = $this->query($sql);
		$result=array();
		foreach($res as $item){
			$result[$item['user_id']] = $item['email'];
		}
		return $result;
	}
	
}
/* ファイルの終わり */