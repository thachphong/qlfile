<?php
/**
 * capphat
 *
*/
class Print_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
		Login_model::check();	// ログインチェック
	}
	
	public static function action_index()
	{
		$param = self::get_param(array(
			's_donvi'
            ,'s_phongban'
            ,'s_tonhom'
            ,'tu_ngay'
            ,'den_ngay'
            ,'file_name'
            ,'loai'
		));
		$model = new Print_model();
		$rows = $model->get_print_rows($param);		
        $usr = new User_model();
		return ACWView::template('lichsuin.html', array(
			'data_rows' => $rows			
			,'search_data'=>$param
            ,'donvi_list' =>$usr->get_donvi()
            ,'phongban_list' =>$usr->get_phongban()
            ,'tonhom_list' =>$usr->get_tonhom()
		));
	}
    public function get_print_rows($param)
	{
		$sql = "
			select d.donvi_name,
            p.phongban_name,
            n.tonhom_name,
			f.file_name,";
        $group ="" ;
        if (isset($param['loai']) && $param['loai']=="1" ) {			
			$sql .= " DATE_FORMAT(t.ngay_in,'%d/%m/%Y') add_datetime,
						sum(t.soluong) soluong";
            $group =" group by d.donvi_name,
                        p.phongban_name,
                        n.tonhom_name,
			            f.file_name,DATE_FORMAT(t.ngay_in,'%d/%m/%Y')";
		}else{
            $sql .= " DATE_FORMAT(t.ngay_in,'%d/%m/%Y %H:%i:%s') add_datetime  ,
			        t.soluong";
        }  
        $sql .="
            from lichsu_in t
                INNER JOIN file f on f.file_name = t.file_name and f.status=3
                INNER JOIN m_user  u on u.user_name = t.user_name
                INNER JOIN don_vi d on d.donvi_id = u.donvi
                INNER JOIN phong_ban p on p.phongban_id= u.phong_ban
                INNER JOIN to_nhom n on n.tonhom_id = u.to_nhom
            where 1= 1
		";
		$sql_param = array();
		if (isset($param['s_donvi']) ) {
			$sql_param['donvi'] =$param['s_donvi'];
			$sql .= " and d.donvi_id = :donvi ";
		}
        if (isset($param['s_phongban']) ) {
			$sql_param['phongban'] =$param['s_phongban'];
			$sql .= " and p.phongban_id = :phongban ";
		}
        if (isset($param['s_tonhom']) ) {
			$sql_param['tonhom'] =$param['s_tonhom'];
			$sql .= " and n.tonhom_id = :tonhom ";
		}
		if (isset($param['file_name']) && empty($param['file_name'])==FALSE) {
			$sql_param['file_name'] = '%'.SQL_lib::escape_like($param['file_name']).'%';
			$sql .= " and lower(f.file_name) like lower(:file_name) ";
		}
        
        $sql_param['tu_ngay'] ='00/00/0000';        
        if (isset($param['tu_ngay']) && empty($param['tu_ngay'])==FALSE) {
			$sql_param['tu_ngay'] = $param['tu_ngay'];
		}
        if (isset($param['den_ngay']) && empty($param['den_ngay'])==FALSE) {
			$sql_param['den_ngay'] = $param['den_ngay'];
            $sql .= " and t.ngay_in between STR_TO_DATE(:tu_ngay,'%d/%m/%Y %H:%i') and STR_TO_DATE(:den_ngay,'%d/%m/%Y %H:%i')";
		}else{
            $sql .= " and t.ngay_in between STR_TO_DATE(:tu_ngay,'%d/%m/%Y %H:%i') and SYSDATE()";
        }
        
		$sql .=$group. "
			ORDER BY   t.lichsu_in_id
		";		
		return $this->query($sql, $sql_param);
	}
	
	
	
}
/* ファイルの終わり */