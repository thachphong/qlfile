<?php

class User_model extends ACWModel
{	
	public static function init()
	{
		Login_model::check();	
	}
	
	public static function validate($action, &$param)
	{
		switch ($action) {
            case 'update':
				return self::_validate_update($param);
			case 'updatepass':
				return self::_validate_updatepass($param);
            case 'index':
                    if($param['search_user_name'] != ''){
                        $s_user_name = $param['search_user_name'];
                        $param['s_user_name'] = strtolower($s_user_name);
                    }
                break;  
        }
		return true;
	}
	
	public static function action_index()
	{
		$param = self::get_param(array(
			'search_user_name'
		));
		$model = new User_model();
		$rows = $model->get_user_rows($param);		
		return ACWView::template('user.html', array(
			'user_rows' => $rows,
			'lang_list'=>'',
			'search_user_name'=>$param['search_user_name']
		));
	}
	public static function action_password()
	{
		return ACWView::template('user/password.html', array(
		));
	}
	public static function action_updatepass()
	{
		$params = self::get_param(array(			
			'old_pass',
			'new_pass',
            'renew_pass'
		));
	    
		if (self::get_validate_result() === true) {
			$model = new User_model();
			$obj = $model->updatepass($params);
		}
		
		if (ACWError::count() <= 0) {
		    $result['status'] = 'OK';
		} else {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}

		return ACWView::json($result);
	}
	public static function action_edit()
	{
		$params = self::get_param(array(
			'm_user_id'
		));
		
		$m_user_id = null;
		if (isset($params['m_user_id'])) {
			$m_user_id = $params['m_user_id'];
		}
		$model = new User_model();
		$user_row = array();
		if ($m_user_id == null) {			
			$user_row['user_id'] = null;
			$user_row['user_name'] = null;
            $user_row['user_name_disp'] = null;
            $user_row['email'] = null;
			$user_row['donvi'] = null;
            $user_row['phong_ban'] = null;
            $user_row['to_nhom'] = null;
            $user_row['ip'] = null;
            $user_row['admin'] = null;
			$user_row['del_flg'] = null;
			$user_row['upload'] = null;
			$user_row['phanbo'] = null;
			$user_row['print'] = null;
			$user_row['kiemtra'] = null;
            $user_row['duyet'] = null;
            $user_row['trungtam_quanly'] = null;
            $user_row['admin'] = null;
            $user_row['list_mail'] = '';
			
		} else {
			$user_row = $model->get_user_row($m_user_id);			
		}		
		$donvi = $model->get_donvi();
        $phongban=addslashes(json_encode( $model->get_phongban()));
        $tonhom =addslashes(json_encode( $model->get_tonhom()));
        //var_dump($user_row);die;
		return ACWView::template('user/edit.html', array(
			'user_row' => $user_row	
            ,'don_vi'=>$donvi
            ,'phong_ban'=> $phongban	
            ,'to_nhom'=>$tonhom
		));
	}
	
	private static function _validate_update(&$param)
	{
	    /**
	     * 
	     * */
	    
		$validate = new Validate_lib();
		
		$param['user_id'] = $validate->trim_ext($param['user_id']);
		$param['user_name'] = $validate->trim_ext($param['user_name']);
       
        
		if ($validate->type_str('user_name', 'Tên đăng nhập', $param['user_name'], true) == false) {
			return false;
		}        
        if($param['user_name'] != ''){
            if(strlen($param['user_name']) > 50){
                ACWError::add('lelng', 'Tên đăng nhập không được quá 50 ký tự');
                return false;
            }
        }
		return true;
	}
	private static function _validate_updatepass(&$param)
	{	    
		$validate = new Validate_lib();
		
		$login = new Login_model();
		$login_info = ACWSession::get('user_info');
		$user = $login->check_login(array('passwd'=>$param['old_pass'],'user_id'=>$login_info['user_name']));
		if($user == null){
			ACWError::add('saipass',Message_model::get_msg('USER041')); //'Mật khẩu cũ không đúng !'
            return false;
		}
		return true;
	}
    public static function action_checkmaxlenght() {
        
        $result['error'] = array();        
        return ACWView::json($result);    
    }
   
	
	/**
	* 更新
	*/
	public static function action_update()
	{
		$params = self::get_param(array(			
			'user_id',
			'user_name',
            'user_name_disp',
            'email',
			'pass',			
				'don_vi'
				, 'phong_ban'
				, 'to_nhom'
				, 'ip'
				,'level'
                ,'del_flg'
                ,'lev_in'
                ,'lev_upload'
                ,'lev_phanbo'
                ,'lev_kiemtra'
                ,'lev_duyet'
                ,'lev_ttql'
                ,'lev_admin'
                ,'list_mail'
		));
	    $params['lev_in']=($params['lev_in']=='on') ?1:0;
        $params['lev_upload']=($params['lev_upload']=='on') ?1:0;
        $params['lev_phanbo']=($params['lev_phanbo']=='on' )?1:0;
        $params['lev_kiemtra']=($params['lev_kiemtra']=='on') ?1:0;
        $params['lev_duyet']=($params['lev_duyet']=='on') ?1:0;
        $params['lev_ttql']=($params['lev_ttql']=='on') ?1:0;
        $params['lev_admin']=($params['lev_admin']=='on') ?1:0;
		if (self::get_validate_result() === true) {
			$model = new User_model();
			$obj = $model->update($params);
		}
		
		if (ACWError::count() <= 0) {
		    $result['status'] = 'OK';
		} else {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}

		return ACWView::json($result);
	}
	
	
	public function get_user_rows($param)
	{
		$sql = "
			SELECT				 
				 usr.user_id
				, usr.user_name                
                , usr.user_name_disp
                ,email
				, usr.pass
				,dv.donvi_name as donvi
				, pb.phongban_name as  phong_ban
				, tn.tonhom_name as to_nhom
				, usr.ip
				, usr.level
                ,usr.del_flg
                ,usr.list_mail
			FROM
				m_user usr
				left join phong_ban pb on usr.PHONG_BAN = pb.phongban_id
				left join to_nhom tn on usr.TO_NHOM = tn.tonhom_id	
				left join don_vi dv on usr.DONVI =dv.donvi_id
			
		";
		
		if (isset($param['s_user_name'])) {
			$sql_param = array(
					'user_name' =>  '%' . SQL_lib::escape_like($param['s_user_name']) . '%'
				);
			$sql .= " WHERE lower(usr.user_name) like lower(:user_name) ";
		} else {
			$sql_param = array();
		}
		
		$sql .= "
			ORDER BY
				usr.user_id
		";
		//var_dump($sql);die;
		return $this->query($sql, $sql_param);
	}
		
	public function update($params)
	{
		$this->begin_transaction();
		$sql_lev= "select l.level_id from level l where l.print = :lev_in
                    and l.upload = :lev_upload
                    and l.phanbo = :lev_phanbo
                    and l.kiemtra= :lev_kiemtra
                    and l.duyet =:lev_duyet
                    and l.trungtam_quanly = :lev_ttql
                    and l.admin =:lev_admin ";
        $pam_lev = ACWArray::filter($params, array('lev_in',
        'lev_upload','lev_phanbo','lev_kiemtra','lev_duyet','lev_ttql','lev_admin'   ));
        $result = $this->query($sql_lev,$pam_lev);
        if(count($result) == 0){
            $sql_lev = "INSERT INTO level(print,upload,phanbo,kiemtra,duyet,trungtam_quanly,admin)
            values(:lev_in,:lev_upload,:lev_phanbo,:lev_kiemtra,:lev_duyet, :lev_ttql,:lev_admin )";
            $this->execute($sql_lev,$pam_lev);
            $result = $this->query("SELECT LAST_INSERT_ID() AS level_id");	
        }   	
        $level_id = $result[0]['level_id'];
        
		$login_info = ACWSession::get('user_info');
		
		$pass_md5 = null;
		if ($params['pass'] != null) {
			$pass_md5 = md5(AKAGANE_SALT . $params['pass']);
		}
		$sql_params = array(
			'user_id' => $params['user_id'],
			'user_name' => $params['user_name'],
            'user_name_disp' => $params['user_name_disp'],
            'email'=>$params['email'],
			'pass' => $pass_md5,
			'donvi' => $params['don_vi'],
			'phong_ban' => $params['phong_ban'],
			'to_nhom' => $params['to_nhom'],
			'level' => $level_id,
			'ip' => $params['ip'],
            'del_flg' => $params['del_flg'],
            'list_mail' => $params['list_mail'],
            'upd_user_id'=>$login_info['user_id']
		);		
		if ($params['user_id'] == null) {		
			
			$res = $this->get_user_id_count($params);
			if ($res['cnt'] > 0) {
				ACWError::add('user_name', 'Tên đăng nhập đã có, vui lòng sử dụng tên khác');
				return;
			}
		
			$sql = "
				INSERT INTO
					m_user
				(
					  user_id
					, user_name
					, pass
					, user_name_disp
					, donvi
					, phong_ban
					, to_nhom
					, level
                    , ip
                    ,email
                    , del_flg
					, add_user_id
					, add_datetime
					, upd_user_id
					, upd_datetime
					,list_mail
				) VALUES (
					  :user_id 
					, :user_name 
					, :pass 
					, :user_name_disp 
					, :donvi 
					, :phong_ban 
					, :to_nhom 
					, :level
                    , :ip
                    , :email
                    , :del_flg
					, :add_user_id 
					, NOW() 
					, :upd_user_id 
					, NOW() 
					, :list_mail
				);
			";
			$sql_params['add_user_id'] = $login_info['user_id'];
		} else {							
			
			$sql = "
				UPDATE
					m_user
				SET
					  user_id = :user_id
					, user_name = :user_name
					, pass = COALESCE(:pass, pass)
					, user_name_disp = :user_name_disp
					, donvi = :donvi 
					, phong_ban = :phong_ban 
					, to_nhom = :to_nhom 
                    ,level =:level
                    , ip =:ip
                    ,email=:email
					, del_flg = COALESCE(:del_flg, 0)
					, upd_user_id = :upd_user_id
					, upd_datetime = NOW()
					, list_mail = :list_mail
				WHERE
					user_id = :user_id
			";
			$sql_params['user_id'] = $params['user_id'];
			$sql_params['del_flg'] = $params['del_flg'];
		}
		$this->execute($sql, $sql_params);		
		
		$this->commit();
		
		return true;
	}
	public function updatepass($params)
	{
		$this->begin_transaction();
		$sql = "
				UPDATE
					m_user
				SET					
					 pass = :pass
					, upd_user_id = :user_id
					, upd_datetime = NOW()					
				WHERE
					user_id = :user_id
			";
		$login_info = ACWSession::get('user_info');
			$pass_md5 = md5(AKAGANE_SALT . $params['new_pass']);
			$sql_params['user_id'] = $login_info['user_id'];
			$sql_params['pass'] = $pass_md5;
		
		$this->execute($sql, $sql_params);		
		
		$this->commit();
		
		return true;
	}
	
	/**
	 * ユーザ取得
	 * @param integer $m_user_id ユーザID
	 * @return array
	 */
	public function get_user_row($m_user_id)
	{
		$rows = $this->query("
				SELECT
					usr.*,
                    l.print,
                    l.upload,
                    l.phanbo,
                    l.kiemtra,
                    l.duyet,
                    l.trungtam_quanly,
                    l.admin
				FROM
					m_user usr				
                    left join level l on l.level_id = usr.level
                WHERE
					usr.user_id = :m_user_id
			", array("m_user_id" => $m_user_id)
		);
		
		return $rows[0];
	}
    public function check_only_upload($m_user_id){
        $data_row = $this->get_user_row($m_user_id);
        if($data_row['upload'] > 0 && $data_row['kiemtra']=='0'&& $data_row['duyet']=='0'&& $data_row['trungtam_quanly']=='0'&& $data_row['phanbo']=='0'&& $data_row['admin']=='0'){
            return TRUE;
        }
        return FALSE;
    }
	public function get_user_bylevel($level)
	{
		$sql = " SELECT		usr.*
				FROM	m_user usr				
	          left join level l on l.level_id = usr.level					
	          WHERE	usr.del_flg=0 ";
		if(isset($level['kiemtra'])&& $level['kiemtra']==1){
			$sql .= " AND l.kiemtra = 1";
		}			
		if(isset($level['duyet'])&& $level['duyet']==1){
			$sql .= " AND l.duyet = 1";
		}
		if(isset($level['trungtam_quanly'])&& $level['trungtam_quanly']==1){
			$sql .= " AND l.trungtam_quanly = 1";
		}
		return $this->query($sql);
	}
	public function get_donvi()
	{
		$sql = "select * FROM don_vi where del_flg= 0 ";
		return $this->query($sql);
	}
    public function get_phongban()
	{
		$sql = "select * FROM phong_ban where del_flg= 0 ";
		return $this->query($sql);
	}   
    public function get_tonhom()
	{
		$sql = "select * FROM to_nhom where del_flg= 0 ";
		return $this->query($sql);
	}	
	private function get_user_id_count($param)
	{
		$sql = "
			SELECT
				COUNT(*) AS cnt
			FROM
				m_user
			WHERE
				user_name = :user_name
		";
		
		$filter = ACWArray::filter($param, array('user_name'));
		$rows = $this->query($sql, $filter);
		return $rows[0];
	}
	public static function action_redirect(){
		$user_info = ACWSession::get('user_info');
		
		if($user_info['upload'] > 0 || $user_info['kiemtra'] > 0 || $user_info['duyet'] > 0 || $user_info['trungtam_quanly'] > 0){
            return ACWView::redirect(ACW_BASE_URL . 'don');
        }else if($user_info['print'] > 0){
            return ACWView::redirect(ACW_BASE_URL . 'file/blank');
        }else if($user_info['phanbo'] > 0){  // cap phat
            return ACWView::redirect(ACW_BASE_URL . 'file/capphat');
        }else if($user_info['admin'] > 0){  
            return ACWView::redirect(ACW_BASE_URL . 'file/donvi');
        }
        return ACWView::redirect(ACW_BASE_URL . 'don');
        
    }
	
}
/* ファイルの終わり */