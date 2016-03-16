<?php
/**
 * 進行管理
 *
*/
class Progress_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
		Login_model::check();	// ログインチェック
	}

	/**
	* リスト表示
	*/
	public static function action_list()
	{
		$param = self::get_param(array(
			'acw_url'
			,'search_category_name'
			,'search_series_name'
			,'search_item_no'
			));
		if (self::get_validate_result() == false) {
			return ACWView::OK; // 何も返さない
		}

		$med_db = new Media_model();
		$result = $med_db->get_info($param['head_id']);
		if (count($result) != 1) {
			return ACWView::OK; // 何も返さない
		}
		
		$db = new Progress_model();
		$medsec = $db->get_media_section($param['head_id']);
		return ACWView::template('progress.html',
			ACWArray::filter($param, array
				(
				'search_category_name'
				,'search_series_name'
				,'search_item_no'
				),
				array(
				'list' => $db->get_series_list($param, $medsec)
				,'medsec' => $medsec
				,'info' => $result[0]
				,'head_id' => $param['head_id']
				)
			));
	}

	/**
	* 承認
	*/
	public static function action_approval()
	{
		$param = self::get_param(array('series_id', 'approval'));
		if (self::get_validate_result() == false) {
			return ACWView::OK; // 何も返さない
		}
		
		$db = new Progress_model();
		$db->update_series_approval($param);
		return ACWView::json(array('status'=>'OK'));
	}

	/**
	* ページ、サイズ、メモ、チェック
	*/
	public static function action_updatemseries()
	{
		$param = self::get_param(array(
			'series_id'
			,'page'
			,'size'
			,'memo'
			,'secchk'
			));
		if (self::get_validate_result() == false) {
			return ACWView::json(array(
				'error' => ACWError::get_list()
				));
		}

		$db = new Progress_model();
		$db->update_series_text($param);
		return ACWView::json(array('status'=>'OK'));
	}
	
	/**
	* 版一覧
	*/
	public static function action_verlist()
	{
		$param = self::get_param(array(
			'media_series_id'
			));

		$db = new Progress_model();
		return ACWView::template('progress/verlist.html',
				array(
				'list' => $db->get_ver_list($param)
				,'media_series_id' => $param['media_series_id']
			));
	}

	/**
	* 項目リスト
	*/
	public static function action_sectionlist()
	{
		$param = self::get_param(array(
			'head_id'
			));

		$db = new Progress_model();
		return ACWView::template('progress/add_section.html',
				array(
				'list' => $db->get_media_section($param['head_id'])
				,'head_id' => $param['head_id']
			));
	}

	/**
	* 版更新
	*/
	public static function action_updatever()
	{
		$param = self::get_param(array(
			'media_series_id'
			,'ver_id'
			));
		if (self::get_validate_result() == false) {
			return ACWView::json(array(
				'error' => ACWError::get_list()
				));
		}

		$db = new Progress_model();
		$db->update_series_ver($param);
		return ACWView::json(array('status'=>'OK'));
	}

	/**
	* 媒体チェック
	*/
	public static function action_checkmedia()
	{
		$param = self::get_param(array(
			'ms_id'
			));

		$db = new Progress_model();
		$verinfo = $db->get_media_series($param['ms_id']);
		$verinfo = $verinfo[0];
		$section = $db->get_media_section($verinfo['t_media_head_id']);

		ACWLog::write_file('MEDIACHECK', var_export($section, true));
		ACWLog::write_file('MEDIACHECK', var_export($verinfo, true));

		$sfile = new SeriesFile_lib($verinfo['t_series_head_id'], $verinfo['t_series_mei_id']);
		$check_result = $sfile->check_media_xml($section);
		ACWLog::write_file('MEDIACHECK', var_export($check_result, true));
		// チェック結果を書く
		$db->update_series_check($param['ms_id'], $check_result);

		return ACWView::json(
			array(
				'status' => 'OK'
				,'ms_id' => $param['ms_id']
				,'check' => $check_result
			));
	}

    //Add Start - Trung VNIT - 2014/08/21
    public static function action_checkmaxlenght() {
        $param = self::get_param(array('search_category_name','search_series_name','search_item_no', 'page', 'size', 'memo'));
        $validate = new Validate_lib();
        $result['error'] = array();
        if(strlen($param['search_category_name']) > 0){
            $rs = $validate->check_max_lenght_item_string('進行管理', 'カテゴリ表示名', $param['search_category_name'], 'search_category_name');
            if($rs == FALSE){
                $result['error'] = ACWError::get_list();
            }
        }
        if(strlen($param['search_series_name']) > 0){
            $rs = $validate->check_max_lenght_item_string('進行管理', '商品名', $param['search_series_name'], 'search_series_name');
            if($rs == FALSE){
                $result['error'] = ACWError::get_list();
            }
        }
        if(strlen($param['search_item_no']) > 0){
            $rs = $validate->check_max_lenght_item_string('進行管理', '品番', $param['search_item_no'], 'search_item_no');
            if($rs == FALSE){
                $result['error'] = ACWError::get_list();
            }
        }
        if(strlen($param['page']) > 0){
            $rs = $validate->check_max_lenght_item_string('進行管理', 'ページ', $param['page'], 'page');
            if($rs == FALSE){
                $result['error'] = ACWError::get_list();
            }
        }
        if(strlen($param['size']) > 0){
            $rs = $validate->check_max_lenght_item_string('進行管理', 'サイズ', $param['size'], 'size');
            if($rs == FALSE){
                $result['error'] = ACWError::get_list();
            }
        }
        if(strlen($param['memo']) > 0){
            $rs = $validate->check_max_lenght_item_string('進行管理', 'メモ', $param['memo'], 'memo');
            if($rs == FALSE){
                $result['error'] = ACWError::get_list();
            }
        }
        return ACWView::json($result);    
    }
    //Add End - Trung VNIT - 2014/08/21

	/**
	 * 入力チェック
	 */
	public static function validate($action, &$param)
	{
		switch ($action) {
		case 'list':
            //Add Start - Trung VNIT - 2014/08/27
            if($param['search_category_name'] != ''){
                $s_category_name = $param['search_category_name'];
                $param['s_category_name'] = strtolower($s_category_name);
            }
            if($param['search_series_name'] != ''){
                $s_series_name = $param['search_series_name'];
                $param['s_series_name'] = strtolower($s_series_name);
            }
            if($param['search_item_no'] != ''){
                $s_item_no = $param['search_item_no'];
                $param['s_item_no'] = strtolower($s_item_no);
            }
            //Add End - Trung VNIT - 2014/08/27
			if (count($param['acw_url']) != 1) {
				return false;
			}

			$param['head_id'] = $param['acw_url'][0];
			unset($param['acw_url']);
			break;
		case 'settext':
			return self::_validate_text($param);  
		}
		return true;
	}

	/**
	 * 更新時のチェック
	 */
	private static function _validate_text(&$param)
	{
		// 制限チェック
		$limit = new Limit_common_model();
		$limit->select_category('媒体シリーズ');

		$vl = new Validate_lib();

		// ページ
		$vl->type_int('page', 'ページ', $param['page'], false, 999999999, 1);
		// サイズ
		$vl->type_int('size', 'サイズ', $param['size'], false, 999999999, 1);
		// メモ
		$vl->type_str('memo', 'メモ', $param['memo'], false, $limit->get('メモ_最大'));

		if (ACWError::count() > 0) {
			return false;
		}
		return true;
	}

	/**
	* シリーズ承認更新
	*/
	public function update_series_approval($param)
	{
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['m_user_id'];

		// 媒体シリーズ
		$this->execute("
			UPDATE t_media_series SET
				approval_flg = :approval
				,add_user_id = :user_id
				,add_datetime = now()
			WHERE
				t_media_series_id = :series_id
			", $param);
		$this->commit();
	}

	/**
	* シリーズの文字情報更新
	*/
	public function update_series_text($param)
	{
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['m_user_id'];

		if (isset($param['secchk']) && is_array($param['secchk'])) {
			$param['secchk'] = implode(',', $param['secchk']);
		} else {
			$param['secchk'] = null;
		}

		//  媒体シリーズ
		$this->execute("
			UPDATE t_media_series SET
				page = :page
				,size = :size
				,memo = :memo
				,section_result = :secchk
				,add_user_id = :user_id
				,add_datetime = now()
			WHERE
				t_media_series_id = :series_id
			", $param);
		$this->commit();
	}

	/**
	* シリーズの版更新
	*/
	public function update_series_ver($param)
	{
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['m_user_id'];

		//  媒体シリーズ
		$this->execute("
			UPDATE t_media_series SET
				series_ver_id = :ver_id
				,add_user_id = :user_id
				,add_datetime = now()
			WHERE
				t_media_series_id = :media_series_id
			", $param);
		$this->commit();
	}

	/**
	* シリーズのチェック結果更新
	*/
	public function update_series_check($ms_id, $check_result)
	{
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');

		// 結果を文字列へ
		$result_text = '';
		foreach ($check_result as $cr) {
			if ($result_text != '') {
				$result_text .= ',';
			}

			$result_text .= $cr['id'] . '=' . $cr['data'];
		}

		//  媒体シリーズ
		$this->execute("
			UPDATE t_media_series SET
				section_result = :result_text
				,upd_user_id = :user_id
				,upd_datetime = now()
			WHERE
				t_media_series_id = :ms_id
			", array(
				'ms_id' => $ms_id
				,'user_id' => $login_info['m_user_id']
				,'result_text' => $result_text
			));
		$this->commit();
	}

	/**
	 * 媒体情報
	 */
	public function get_media_info($head_id)
	{
		$sql = "
			SELECT
				 mhead.t_media_head_id
				,mhead.m_lang_id
				,mhead.media_name
				,mhead.note
				,mlang.lang_name
				,media_type_name
				,mhead.media_type_no
			FROM
				t_media_head mhead
			INNER JOIN
				m_lang mlang
					ON mlang.m_lang_id = mhead.m_lang_id AND mlang.del_flg = 0
			INNER JOIN
				m_media_type mmtype
					ON mmtype.media_type_no = mhead.media_type_no AND mmtype.m_lang_id = mhead.m_lang_id AND mmtype.del_flg = 0
			WHERE
				mhead.del_flg = 0
				AND mhead.t_media_head_id = :head_id

		";
		return $this->query($sql, array('head_id' => $head_id));
	}

	/**
	 * 媒体のシリーズ情報
	 */
	public function get_media_series($ms_id)
	{
		$sql = "
			SELECT
				medmei.t_series_mei_id
				,shead.t_series_head_id
				,msrs.t_media_head_id
			FROM
				t_media_series msrs
			INNER JOIN
				t_series_ver medver
				ON medver.t_series_ver_id = msrs.series_ver_id AND medver.del_flg = 0
			INNER JOIN
				t_series_mei medmei
				ON medmei.t_series_mei_id = medver.t_series_mei_id AND medmei.del_flg = 0
			INNER JOIN
				t_series_lang slang
				ON slang.t_series_lang_id = medver.t_series_lang_id AND slang.del_flg = 0
			INNER JOIN
				t_series_head shead
				ON shead.t_series_head_id = slang.t_series_head_id AND shead.del_flg = 0
			WHERE
				msrs.t_media_series_id = :ms_id
		";
		return $this->query($sql, array('ms_id' => $ms_id));
	}

	/**
	 * 版選択用版リスト
	 */
	public function get_ver_list($param)
	{
		$sql = "
		SELECT
			sver.t_series_ver_id
			,sver.major_ver
			,sver.minor_ver
			,smei.series_name
		FROM
			t_series_ver sver
		INNER JOIN
			t_series_mei smei
			ON
			smei.t_series_mei_id = sver.t_series_mei_id AND smei.del_flg = 0
		INNER JOIN
			t_series_lang slang
			ON
			slang.t_series_lang_id = sver.t_series_lang_id AND slang.del_flg = 0
		INNER JOIN
			t_series_ver medver
			ON
			medver.t_series_lang_id = slang.t_series_lang_id AND medver.del_flg = 0
		INNER JOIN
			t_media_series msrs
			ON
			msrs.series_ver_id = medver.t_series_ver_id
		WHERE
			msrs.t_media_series_id = :media_series_id
			AND msrs.series_ver_id <> sver.t_series_ver_id
		ORDER BY
			major_ver DESC
			,minor_ver DESC
			";
		return $this->query($sql, $param);
	}

	/**
	 * 媒体管理項目リスト
	 */
	public function get_media_section($head_id)
	{
		$sql = "
		SELECT
			medsec.t_media_section_id
			,msec.section_name
			,medsec.section_name AS sec_nm
			,medsec.m_section_id
			,user_flg
			,disable_ctg
		FROM
			m_section msec
		INNER JOIN
			t_media_section medsec
			ON medsec.m_section_id = msec.m_section_id
		WHERE
			medsec.t_media_head_id = :head_id
		ORDER BY
			msec.disp_seq
			";
		return $this->query($sql, array('head_id' => $head_id));
	}

	/**
	 * 進行管理シリーズリスト
	 */
	public function get_series_list($param, $medsec)
	{
			$sql = "
			SELECT
				msrs.t_media_series_id
				,mctg.ctg_name
				,medmei.series_name
				,nm.kbn_name
				,msrs.page
				,msrs.size
				,msrs.memo
				,msrs.section_result
				,msrs.approval_flg
				,medver.t_series_ver_id AS med_ver_id
				,medver.major_ver AS med_major_ver
				,medver.minor_ver AS med_minor_ver
				,medmei.t_series_mei_id
				,newver.t_series_ver_id AS new_ver_id
				,newver.major_ver AS new_major_ver
				,newver.minor_ver AS new_minor_ver
				,shead.t_ctg_head_id
			FROM
				t_media_series msrs
			INNER JOIN
				t_media_ctg mctg
				ON mctg.t_media_ctg_id = msrs.t_media_ctg_id AND mctg.del_flg = 0
			INNER JOIN
				t_series_ver medver
				ON medver.t_series_ver_id = msrs.series_ver_id AND medver.del_flg = 0
			INNER JOIN
				t_series_mei medmei
				ON medmei.t_series_mei_id = medver.t_series_mei_id AND medmei.del_flg = 0
			INNER JOIN
				t_series_lang slang
				ON slang.t_series_lang_id = medver.t_series_lang_id AND slang.del_flg = 0
			INNER JOIN
				t_series_head shead
				ON shead.t_series_head_id = slang.t_series_head_id AND shead.del_flg = 0
			INNER JOIN
				t_series_ver newver
				ON newver.t_series_ver_id = slang.t_series_ver_id AND newver.del_flg = 0
			INNER JOIN
				m_name nm ON medver.approval_status = nm.kbn_cd AND nm.ctg_cd = 'A0001' AND nm.del_flg = 0
			WHERE
				msrs.t_media_head_id = :head_id
			";
		$search_param = array('head_id' => $param['head_id']);
		$where = "";
                
        //Edit Start - Trung VNIT - 2014/08/27
		if (isset($param['s_category_name'])) {
			// 媒体名
			$search_param['category_name'] = '%' . SQL_lib::escape_like($param['s_category_name'])  . '%';
			$where .= ' AND lower(mctg.ctg_name) like lower(:category_name)';//Edit - LIXD-360 - TrungVNIT - 2015/26/11
		}
		if (isset($param['s_series_name'])) {
			// 媒体名
			$search_param['series_name'] = '%' . SQL_lib::escape_like(strtolower($param['s_series_name']))  . '%';
			$where .= ' AND lower(medmei.series_name) like lower(:series_name)';//Edit - LIXD-360 - TrungVNIT - 2015/26/11
		}
        //Edit End - Trung VNIT - 2014/08/27
		if (isset($param['s_item_no'])) {//Edit - Trung VNIT - 2014/08/27
			// 品番
			$search_param['item_no'] = '%' . SQL_lib::escape_like($param['s_item_no'])  . '%';//Edit - Trung VNIT - 2014/08/27
			$where .= " AND medmei.t_series_ver_id IN
						(
						SELECT 
                                                    th_lang.t_series_ver_id 
                                                FROM 
                                                    t_item_info as info 
                                                INNER JOIN 
                                                    t_series_head AS th ON th.series_id = info.series_item_no
                                                INNER JOIN 
                                                    t_series_lang AS th_lang ON th_lang.t_series_head_id = th.t_series_head_id
                                                WHERE LOWER(info.item_item_no) 
                                                LIKE LOWER(:item_no) AND info.series_item_no = shead.series_id 
						)";
		}
		$order = "
				ORDER BY
				msrs.t_media_ctg_id
				,msrs.t_media_series_id
				";
		$result =  $this->query($sql . $where . $order, $search_param);

		// チェック結果を変換
		foreach ($result as &$r) {
			$r['section_result'] = $this->_check_row_section($r['t_ctg_head_id'], $r['section_result'], $medsec);
		}

		return $result;
	}

	/*
	 * 管理項目をチェックする
	 */
	private function _check_row_section($cate_id, $section_result, $medsec)
	{
		$sec_arr = array();
		if (is_null($section_result) == false && $section_result != '') {
			$sr = explode(',', $section_result);
			foreach ($sr as $sec) {
				$eq = explode('=', $sec);
				$sec_arr['s' .  $eq[0]] = $eq[1];
			}
		}
		$result = array();
		$cate_id = '[' . $cate_id . ']';	// 検索用
		/*
		 * チェックする管理項目数分ループ
		 */
		$chk = 0;
		foreach ($medsec as $msec) {
			$id = $msec['m_section_id'];
			if (strpos($msec['disable_ctg'], $cate_id) === false) {
				// 見つからなかった
				if (isset($sec_arr['s' . $id])) {
					// 設定があった 1であるはず
					$chk = $sec_arr['s' . $id];
				}
			} else {
				// 無効カテゴリの中に見つかった
				$chk = '-';
			}

			$result[] = array(
				'id' => $id
				,'val' => $chk
				);
		}

		return $result;
//<%if isset($srs.section_result[$msec.m_section_id])%><%if $srs.section_result[$msec.m_section_id] == 1%>checked="checked"<%/if%><%/if%>
			// 配列に置き換え
	}
}
/* ファイルの終わり */