<?php

class Tonhom_model extends ACWModel
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
                case 'index':
                    if($param['search_tonhom_name'] != ''){
                        $s_tonhom_name = $param['search_tonhom_name'];
                        $param['s_tonhom_name'] = strtolower($s_tonhom_name);
                    }
                break;  
        }
		return true;
	}
	
	public static function action_index()
	{
		$param = self::get_param(array(
			'search_tonhom_name'
		));
		$model = new Tonhom_model();
		$rows = $model->get_tonhom_rows($param);		
		return ACWView::template('tonhom.html', array(
			'data_rows' => $rows,			
			'search_tonhom_name'=>$param['search_tonhom_name']
		));
	}
	
	public static function action_edit()
	{
		$params = self::get_param(array(
			'tonhom_id'
		));
		
		$tonhom_id = null;
		if (isset($params['tonhom_id'])) {
			$tonhom_id = $params['tonhom_id'];
		}
		$model = new Tonhom_model();
		$data_row = array();
		if ($tonhom_id == null) {			
			$data_row['tonhom_id'] = null;
			$data_row['tonhom_name'] = null;
            $data_row['phongban_id'] = null;
            $data_row['donvi_id'] = null;
			$data_row['del_flg'] = null;
		} else {
			$data_row = $model->get_tonhom_row($tonhom_id);			
		}		
		$donvi = $model->get_donvi();
        $phongban=addslashes(json_encode( $model->get_phongban()));
		return ACWView::template('tonhom/edit.html', array(
			'data_row' => $data_row	
            ,'don_vi'=>$donvi
            ,'phong_ban'=> $phongban	
		));
	}
	
	private static function _validate_update(&$param)
	{
		$validate = new Validate_lib();
		
		$param['tonhom_name'] = $validate->trim_ext($param['tonhom_name']);
        
		if (is_null($param['tonhom_name']) || strlen($param['tonhom_name'])== 0) {
			ACWError::add('tn', 'Bạn chưa nhập tên Tổ(nhóm) !');
			return false;
		}        
        if($param['tonhom_name'] != ''){
            if(strlen($param['tonhom_name']) > 100){
                ACWError::add('lelng', 'Tên Tổ(nhóm) không được quá 100 ký tự !');
                return false;
            }
        }
        if($param['donvi_id'] == ''){
        	ACWError::add('dv', 'Bạn chưa chọn đơn vị !');
            return false;
        }
        if($param['phongban_id'] == ''){
        	ACWError::add('pb', 'Bạn chưa chọn phòng ban !');
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
			'tonhom_id',
			'tonhom_name',
            'donvi_id',
            'phongban_id',
             'del_flg'
		));
		
		if (self::get_validate_result() === true) {
			$model = new Tonhom_model();
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
	
	
	public function get_tonhom_rows($param)
	{
		$sql = "
			SELECT				 
				 t.tonhom_id
				, t.tonhom_name                
                , pb.phongban_name
                ,dv.donvi_name
                ,t.del_flg
			FROM
				to_nhom t
				left join phong_ban pb on t.phongban_id = pb.phongban_id
				left join don_vi dv on t.donvi_id =dv.donvi_id
			
		";
		
		if (isset($param['s_tonhom_name'])) {
			$sql_param = array(
					'tonhom_name' =>  '%' . SQL_lib::escape_like($param['s_tonhom_name']) . '%'
				);
			$sql .= " WHERE lower(t.tonhom_name) like lower(:tonhom_name) ";
		} else {
			$sql_param = array();
		}
		
		$sql .= "
			ORDER BY
				t.tonhom_id
		";
		//var_dump($sql);die;
		return $this->query($sql, $sql_param);
	}
		
	public function update($params)
	{
		$this->begin_transaction();
		
		$login_info = ACWSession::get('user_info');
		
		$sql_params = array(
			'tonhom_id' => $params['tonhom_id'],
			'tonhom_name' => $params['tonhom_name'],
            'phongban_id' => $params['phongban_id'],
            'donvi_id'=>$params['donvi_id'],
            'del_flg' => $params['del_flg'],
            'upd_user_id'=>$login_info['user_id']
		);	
        $res = $this->get_tonhom_name_count($params);
			if ($res['cnt'] > 0) {
				ACWError::add('tonhom_name', 'Tên Tổ(nhóm) "'.$params['tonhom_name'].'" đã có, vui lòng sử dụng tên khác');
				return;
		}	
		if ($params['tonhom_id'] == null) {
		    
			$sql = "
				INSERT INTO
					to_nhom
				(
					  tonhom_id
					, tonhom_name
					, phongban_id
					, donvi_id					
                    , del_flg
					, add_user_id
					, add_datetime
					, upd_user_id
					, upd_datetime
				) VALUES (
					  :tonhom_id 
					, :tonhom_name 
					, :phongban_id 
					, :donvi_id
                    , :del_flg
					, :add_user_id 
					, NOW() 
					, :upd_user_id 
					, NOW() 
				);
			";
			$sql_params['add_user_id'] = $login_info['user_id'];
		} else {							
			
			$sql = "
				UPDATE
					to_nhom
				SET	  tonhom_id = :tonhom_id
					, tonhom_name = :tonhom_name
					, phongban_id = :phongban_id
					, donvi_id = :donvi_id 					
					, del_flg = COALESCE(:del_flg, 0)
					, upd_user_id = :upd_user_id
					, upd_datetime = NOW()
				WHERE
					tonhom_id = :tonhom_id
			";			
			$sql_params['del_flg'] = $params['del_flg'];
		}
		$this->execute($sql, $sql_params);		
		
		$this->commit();
		
		return true;
	}
	
	public function get_tonhom_row($tonhom_id)
	{
		$rows = $this->query("
				SELECT
					t.*
				FROM
					to_nhom t
				WHERE
					t.tonhom_id = :tonhom_id
			", array("tonhom_id" => $tonhom_id)
		);
		
		return $rows[0];
	}
	
	public function get_donvi()
	{
		$sql = "select * FROM don_vi ";
		return $this->query($sql);
	}
    public function get_phongban()
	{
		$sql = "select * FROM phong_ban ";
		return $this->query($sql);
	}   
    
	private function get_tonhom_name_count($param)
	{
		$sql = "
			SELECT
				COUNT(*) AS cnt
			FROM
				to_nhom
			WHERE
				tonhom_name = :tonhom_name
		";
        $param_sql['tonhom_name']= $param['tonhom_name'];
		if(isset($param['tonhom_id'])){
            $sql .= " and tonhom_id <> :tonhom_id";
            $param_sql['tonhom_id']= $param['tonhom_id'];
        }
		//$filter = ACWArray::filter($param, array('tonhom_name'));
		$rows = $this->query($sql, $param_sql);
		return $rows[0];
	}
	
	
}
/* ファイルの終わり */