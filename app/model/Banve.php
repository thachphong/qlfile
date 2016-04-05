<?php
/**
 * カテゴリ登録
 *
 * TODO:親カテゴリによる排他制御
 *
*/
class Banve_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
		Login_model::check();	// ログインチェック
	}

	/**
	* カテゴリ登録
	*/
	public static function action_index()
	{
		$param = array(
			'section_type' =>null//=> json_encode(Section_common_model::get_type())
			,'common_spec' =>null// Section_common_model::get_icon_all()
			);
		return ACWView::template('banve.html', $param);
	}
	
	public static function action_new()
	{
		$param = self::get_param(array('acw_url'));	// 親IDを取る
		if (self::get_validate_result() == false) {
			return ACWView::OK;	// 何も返さない
		}		
		$db = new Banve_model();	
		$param['banve_no'] = "";	
		$param['banve_name'] = "";	
		$param['banve_child'] = NULL;
		$param['parent_level'] = 1;
		$pa_info=$db->get_banve_info($param['parent_id']);
		if(count($pa_info)>0){
			$param['parent_level'] = $pa_info['level'];
		}
		return ACWView::template('banve/edit.html', $param);
	}

	/**
	 * HTML取得（編集）
	 */
	public static function action_edit()
	{
		$param = self::get_param(array('acw_url'));	
		if (self::get_validate_result() == false) {
			return ACWView::OK;	
		}
		$db = new Category_model();
		$result = $db->get_banve_info($param['my_id']);
		$param['category_code'] = $result['folder_name'];
		$param['parent_id'] = $result['parent_folder_id'];		
		$param['folder_child'] = $db->get_child_folder($param['my_id']);
		return ACWView::template('banve/edit.html', $param);
	}

	/**
	 * 更新
	 */
	public static function action_update()
	{
		// 最初に決まっているもの
		$param = self::get_param(array(
			'banve_name'
			,'banve_no'
			, 'my_id'	
			, 'parent_id'
			, 'add_child'
			));
		
		$result = array('status' => 'OK');
		$result['param'] = $param;
		if (self::get_validate_result() === true) {			
			$db = new Banve_model();
			if ($db->check_banve_id($param) == true) { 
				$res = $db->get_child_banve($param['my_id']);    
				$list_child= array();
				foreach($res as $row){
					$list_child[] = $row['banve_name'];
				}           
				$add_new = array();
				if(isset($param['add_child'])){
					$add_child = $param['add_child'];
					foreach($add_child as $item){
						if(!in_array($item,$list_child))	
						{
							$add_new[] = $item ;							
						}
						$list_child[]= $item;
					}	
					
				}
				$param['add_child'] = $add_new;
				$db->update_banve($param);
			}
		}
		if (ACWError::count() > 0) {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}
		return ACWView::json($result);
	}
    /**
	 * カテゴリ削除
	 */
	public static function action_delete()
	{
		$param = self::get_param(array('acw_url'));	// 自分のIDを取る
		if (self::get_validate_result() == false)  {
			ACWError::add('acw_url', 'Tham số không hợp lệ');
		}
		$db = new Category_model();
		$db->delete_category($param['my_id']);
		if (ACWError::count() > 0) {
			// エラー
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		} else {
			// 正常
			$result['status'] = 'OK';
		}
		return ACWView::json($result);
	}
	
	/**
	* ツリー表示
	*/
	public static function action_tree()
	{
		$db = new Banve_model();
		$result = $db->get_banve_all();
		return ACWView::json($result);
	}

	/**
	* 並び替え
	*/
	public static function action_sort()
	{
		$result = array();
		$param = self::get_param(array(
			'my'
			,'parent'
			,'prev'
			));
		if (self::get_validate_result() == false) {
			ACWError::add('acw_url', 'パラメタが不正です。');
		}

		// 並び替え
		$db = new Category_model();
		$db->sort_category($param);
		if (ACWError::count() > 0) {
			// エラー
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		} else {
			// 正常
			$result['status'] = 'OK';
		}

		return ACWView::json($result);
	}
	
	public static function action_upload()
	{
		$param = self::get_param(array(
			't_ctg_section_id'
			,'upload_file'
		));	
		if (self::get_validate_result() == false) {
			$result['status'] = 'NG';
			$err = ACWError::get_list();
			$result['error'] = $err[0]['info'];
			return ACWView::json($result);
		}
		
		// アップロード成功
		$result['status'] = 'OK';
		$file_lib = new File_lib();

		// アップするファイル名 data/help/t_ctg_section_id/*******.***
		$dir_lib = new Path_lib(AKAGANE_HELP_STRAGE_PATH);
		if ($file_lib->FolderExists($dir_lib->get_full_path()) === false) {
			$file_lib->CreateFolder($dir_lib->get_full_path());
		}
		
		// フォルダの作成
		$dir_lib->combine($param['t_ctg_section_id']);
		if ($file_lib->FolderExists($dir_lib->get_full_path())) {
			$file_lib->DeleteFolder($dir_lib->get_full_path());
		}
		$file_lib->CreateFolder($dir_lib->get_full_path());
	
		// ファイル名をパスに追加
		$ext = $file_lib->GetExtensionName($param['upload_file']['name']);
		$dir_lib->combine(uniqid() . '.' . $ext);
		
		// ファイルをコピーする
		if ($file_lib->MoveFile($param['upload_file']['tmp_name'], $dir_lib->get_full_path()) === false) {
			// ファイル移動失敗
			$result['status'] = 'NG';
			$result['error'] = 'アップロードに失敗しました。';			
		}
		
		return ACWView::json($result);
	}
	
	public static function action_uploaddel()
	{
		$param = self::get_param(array(
			't_ctg_section_id'
		));
		if (self::get_validate_result() == false) {
			$result['status'] = 'NG';
			$err = ACWError::get_list();
			$result['error'] = $err[0]['info'];
			return ACWView::json($result);
		}
		
		$result['status'] = 'OK';
		$file_lib = new File_lib();
		
		// 削除するフォルダ名 data/help/t_ctg_section_id
		$dir_lib = new Path_lib(AKAGANE_HELP_STRAGE_PATH);
		$dir_lib->combine($param['t_ctg_section_id']);
		if ($file_lib->FolderExists($dir_lib->get_full_path())) {
			$file_lib->DeleteFolder($dir_lib->get_full_path());
		}
		
		return ACWView::json($result);
	}
    
	public static function validate($action, &$param)
	{
		switch ($action) {
		case 'new':
			if (count($param['acw_url']) != 1) {
				return false;
			}
			// 一個目は親ID
			$param = array(
				'parent_id' => $param['acw_url'][0]
				,'my_id' => ''
				);
			break;
		case 'edit':
			if (count($param['acw_url']) != 1) {
				return false;
			}
			// 一個目は自分のID
			$param = array(
				'parent_id' => ''
				,'my_id' => $param['acw_url'][0]
				);
			break;
		case 'delete':
			if (count($param['acw_url']) != 1) {
				return false;
			}
			// 一個目は自分のID
			$param = array(
				'my_id' => $param['acw_url'][0]
				);
			break;
		case 'sort':
			if (isset($param['my']) == false || isset($param['parent']) == false) {
				return false;
			}
			break;
		case 'update':
			return self::_validate_category($param);
		case 'upload':
			return self::_validate_upload($param);  
		}
		return true;
	}
    
	/**
	* カテゴリ入力チェック
	*/
	private static function _validate_category(&$param)
	{
		if ((isset($param['folder_name']) == false) || ($param['folder_name'] == '')) {
			ACWError::add('folder_name', 'Vui lòng nhập tên thư mục !');
			return false;
		}
		return true;
	}

	/**
	 * アップロード時のチェック
	 */
	private static function _validate_upload(&$param)
	{
		ACWLog::debug_var('UPLOAD_APPEND', $param);
		
		if ((isset($param['t_ctg_section_id']) == false) || ($param['t_ctg_section_id'] == '')) {
			ACWError::add('upload', '未登録の項目ではアップロードできません。');
			return false;
		}
		
		if (is_null($param['upload_file'])) {
			// NULL →　ファイルの容量制限オーバー
			ACWError::add('upload', 'アップロードに失敗しました。');
			return false;
		}
		
		if ($param['upload_file']['error'] != UPLOAD_ERR_OK) {
			// ファイルアップロードエラーあり
			if ($param['upload_file']['error'] == UPLOAD_ERR_NO_FILE) {
				// ファイル未選択
				ACWError::add('upload_file', 'アップロードするファイルが選択されていません。');
			} else {
				// その他
				ACWError::add('upload_file', 'アップロードに失敗しました。');
			}
			return false;
		}
		
		$exp = explode('/', $param['upload_file']['type']);
		if (strcmp($exp[0], 'image') != 0) {
			ACWError::add('upload_file', '画像ファイルを選択してください。');
			return false;
		}
		
		return true;
	}		
	//kiem tra trung ten trong chung folder cha
	public function check_banve_id($param)
	{
		$this->begin_transaction();

		$sel_param = ACWArray::filter($param, array('parent_id','banve_name'));
		$sql = "SELECT COUNT(*) cnt FROM banve WHERE parent_id=:parent_id and banve_name = :banve_name ";
		if (isset($param['my_id'])) {
			$sel_param['banve_id'] = $param['my_id'];
			$sql .= ' AND banve_id <> :banve_id';	
		}
		$result = $this->query($sql, $sel_param);
		$this->commit();
		if ($result[0]['cnt'] > 0) {
			ACWError::add('banve_name', 'Tên bản vẽ này đã có, vui lòng nhập tên khác !');
			return false;
		}
		if(isset($param['banve_no'])&& strlen($param['banve_no'])>0 ){
			if(isset($param['my_id'])===FALSE){
				$sel_param = ACWArray::filter($param, array('banve_no'));
				$sql = "SELECT COUNT(*) cnt FROM banve WHERE banve_no = :banve_no ";
				
				$result = $this->query($sql, $sel_param);
				$this->commit();
				if ($result[0]['cnt'] > 0) {
					ACWError::add('banve_no', 'Mã bản vẽ này đã có, vui lòng nhập tên khác !');
					return false;
				}	
			}				
		}
		return true;
	}
	
	public function update_banve($param)
	{
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];
		//$param['user_id_2'] = $login_info['user_id'];
		if(!isset($param['del_flg'])){
			$param['del_flg']='0';
		}
		$sql = "INSERT INTO banve
					(
					parent_id
					,banve_no
					,banve_name
					,level
					,del_flg
					,add_user_id
					,add_datetime
					,upd_user_id
					,upd_datetime
					)
				VALUES
					(
					:parent_id
					,:banve_no
					,:banve_name
					,:level
					,0
					,:user_id
					,now()
					,:user_id
					,now()
					)
				";
		
		$param_new['parent_id'] = $param['my_id'];
		$param_new['user_id'] = $login_info['user_id'];
		if (isset($param['my_id']) == false) {  //add new 
			$parent_info = $this->get_banve_info($param['parent_id']);
			$param['level']=1;
			if(count($parent_info)>0){
				$param['level']=$parent_info['level'];	
			}			
			$this->execute($sql, ACWArray::filter($param, array(
					'parent_id'
					,'banve_name'
					,'banve_no'
					,'level'
					,'user_id'
					//,'user_id_2'
					)));			
			$result = $this->query("SELECT LAST_INSERT_ID() AS banve_id");			
			$param_new['parent_id'] = $result[0]['banve_id'];			
		} else 
		{
			$param['ctg_head_id'] = $param['my_id'];			
			$this->execute("
				UPDATE folder
					SET
					folder_name = :folder_name
					,del_flg = :del_flg
					,upd_user_id = :user_id
					,upd_datetime = now()
				WHERE
					folder_id = :my_id
				", ACWArray::filter($param, array(
					'my_id'
					,'folder_name'
					,'user_id'
					,'del_flg'
					)));			
		}
		//insert folder con
		$parent_info = $this->get_banve_info($param_new['parent_id']);
		$param_new['level']=1;
		if(count($parent_info)>0){
			$param_new['level']=$parent_info['level'];	
		}
		if(isset($param['add_child'])){
			foreach($param['add_child'] as $row){
				if(isset($row) && !empty($row)){
					$param_new['folder_name'] = $row;
					$this->execute($sql,$param_new);	
				}				
			}
		}

		$this->commit();
	}
	
	public function delete_category($ctg_id)
	{
		$this->begin_transaction();

		// シリーズが存在するかどうか
		$series = $this->query("
			SELECT
				count(*) AS cnt
			FROM
				folder
			WHERE parent_folder_id = :folder_id and del_flg = 0
			", array(
				'folder_id' => $ctg_id
			));
		if ($series[0]['cnt'] > 0) {
			ACWError::add('folder_child', 'Tồn tại thư mục con, không thể xóa');
			return;
		}

		// カテゴリが下に存在するかどうか
		$series = $this->query("
			SELECT
				count(*) AS cnt
			FROM
				file
			WHERE folder_id = :folder_id and del_flg=0
				
			", array(
				'folder_id' => $ctg_id				
			));
		if ($series[0]['cnt'] > 0) {
			ACWError::add('file_error', 'Có file trong thư mục, không thể xóa');
			return;
		}
	
		$this->execute("UPDATE folder SET del_flg = 1 WHERE folder_id = :folder_id",
				array('folder_id' => $ctg_id));		

		$this->commit();
	}

	public function sort_category($param)
	{
		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['m_user_id'];

		$this->begin_transaction();
		/*
		 * category 並び替え
		 */
		
		$my_info = $this->query("
			SELECT
				oya_t_ctg_head_id
				,disp_seq
			FROM t_ctg_head
			WHERE t_ctg_head_id = :my AND del_flg = 0 -- Add NBKD-40,52 Minh Vnit 2014/10/30
			",
							ACWArray::filter($param, array('my')));
		if (count($my_info) != 1) {
			ACWError::add('my', 'カテゴリが削除されています。');
			return;
		}
		$my_i = $my_info[0];

		$param['delete_seq'] = $my_i['disp_seq'];
		$param['oya'] = $my_i['oya_t_ctg_head_id'];
		// 元のカテゴリで自分以上のものは下げる
		$this->execute("
			UPDATE t_ctg_head SET
				disp_seq = disp_seq - 1
				,upd_user_id = :user_id
				,upd_datetime = now()
			WHERE oya_t_ctg_head_id = :oya AND disp_seq > :delete_seq
			",
				ACWArray::filter($param, array('delete_seq', 'oya', 'user_id')));

		if (isset($param['prev']) == false) {
			// 先頭
			$param['my_seq'] = 1;
		} else {
			// 自分の前のカテゴリの番号
			$prev_info = $this->query("
				SELECT
					disp_seq
				FROM t_ctg_head
				WHERE t_ctg_head_id = :prev AND del_flg = 0 -- Add NBKD-40,52 Minh Vnit 2014/10/30
				",
								ACWArray::filter($param, array('prev')));

			if (count($prev_info) != 1) {
				ACWError::add('prev', 'カテゴリが削除されています。');
				return;
			}
			$param['my_seq'] = $prev_info[0]['disp_seq'] + 1;
		}

		// 今のカテゴリで自分以上のものを上げる
		$this->execute("
			UPDATE t_ctg_head SET
				disp_seq = disp_seq + 1
				,upd_user_id = :user_id
				,upd_datetime = now()
			WHERE oya_t_ctg_head_id = :parent AND disp_seq >= :my_seq AND t_ctg_head_id <> :my
			",
				ACWArray::filter($param, array('my', 'my_seq', 'parent', 'user_id')));

		// 自分を更新
		$this->execute("
			UPDATE t_ctg_head SET
				disp_seq = :my_seq
				,oya_t_ctg_head_id = :parent
				,upd_user_id = :user_id
				,upd_datetime = now()
			WHERE t_ctg_head_id = :my
			",
				ACWArray::filter($param, array('my', 'my_seq', 'parent', 'user_id')));
		$this->commit();
	}

	/**
	 * ツリーに表示するカテゴリ取得
	 */
	public function get_banve_all()
	{
		$sql = "SELECT
				  chd.banve_id AS id
				,chd.banve_no AS category_name
				,chd.parent_id AS parent_id
				,true AS is_folder
				,1 AS upd_sec
			FROM		banve chd
			WHERE	 chd.del_flg = 0
		";
		return $this->query($sql);
	}
	/**
	 * カテゴリ情報獲得
	 */
	public function get_banve_info($banve_id)
	{
		$r = $this->query("SELECT *	FROM banve f
				WHERE	 f.del_flg = 0
				and	f.banve_id = :banve_id 
			", array ('banve_id' => $banve_id));
		if(count($r) >0)
			return $r[0];
		else
			return null;
	}
	
	public function get_ctg_child($ctg_parent_id,&$all_ctg_child = NULL)
	{
		if($all_ctg_child == NULL){
			$all_ctg_child = array();
		}
		$obj_select = $this->query("
			SELECT
				t_ctg_head_id,oya_t_ctg_head_id
			FROM
				t_ctg_head
			WHERE
				oya_t_ctg_head_id = :ctg_parent_id AND del_flg = 0
			", array ('ctg_parent_id' => $ctg_parent_id));
			
		if(count($obj_select) > 0){
			foreach($obj_select as $key => $value){
				$all_ctg_child[] = $value;
				$this->get_ctg_child($value["t_ctg_head_id"],$all_ctg_child);
			}
			
		}
			
		return $all_ctg_child;
	}
	
	public function get_child_banve($folder_id)
	{
		$sql="select * from banve where parent_id = :banve_id and del_flg=0 order by banve_id";
		return $this->query($sql,array('banve_id'=>$folder_id));
	}
	
    public function get_ctg_head_id($ctg_id){
        $sql="select t_ctg_head_id from t_ctg_head where ctg_id =:ctg_id ";
        $res = $this->query($sql,array("ctg_id"=>$ctg_id));
        if(count($res)>0){
            return $res[0]['t_ctg_head_id'];
        }else{
            return 0;
        }
    }
}
/* ファイルの終わり */