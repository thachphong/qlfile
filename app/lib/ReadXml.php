<?php
/**
 * XML読み込みライブラリ
*/
class ReadXml_lib
{
	protected $xml;	/* オープンしたXML */

	/**
	 * XMLオープン
	*/
	public function open($xml_path)
	{
		try {
			//edit start LIXD-376 Phong VNIT 20151219
			$xml_str = $this->delete_exuber_tag($xml_path);
			$this->xml = simplexml_load_string($xml_str);
			//$this->xml = simplexml_load_file($xml_path);
			//edit end LIXD-376 Phong VNIT 20151219
		} catch (Exception $e) {
			ACWLog::write_file('XMLERROR', $e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * XMLから配列変換
	*/
	public function close()
	{
		unset($this->xml);
		$this->xml = null;
	}

	/**
	 * 連想配列でXMLの一部を読み込み
	 */
	public function get_part($path)
	{

	    //Add Start NBKD-41 hungtn VNIT 2014/12/27
	    if(!is_object($this->xml))
	    {
	        $tmp_id = ACWSession::get("t_import_history_id");
	        if(!empty($tmp_id)) {
	            ACWLog::write_file('PATCH_IMPORT_ITEM_ERROR_LOG', 'error get by tag name 128: history_id: '.$tmp_id);
	        }

	        return -1;
	    }
	    //Add Start NBKD-41 hungtn VNIT 2014/12/27
	     
		$el_result = $this->xml->xpath($path);
		$result = array();
		foreach ($el_result as $elr) {
			$result[] = $this->_to_array($elr);
		}

		return $result;
	}

	/**
	 * 項目（フリーではない）を取ってくる
	 * 新データ型対応シンプル版
	 */
	public function get_section_data()
	{
		$sec_arr = $this->get_part('/XML/SeriesHead/SeriesHeadSection/SectionInfo');

		$result = array();
		foreach ($sec_arr as $sec_xml) {
			$key = 'section_' . $sec_xml['SectionId'];
			$this->format_section($sec_xml);
			//Add Start LIXD-11 MINH_VNIT 2015/09/28
			if(isset($sec_xml['ChildSection']) == TRUE){
				$child_section_info = $sec_xml['ChildSection']['SectionInfo'];
				if(isset($child_section_info['SectionName']) == FALSE)
				{
					foreach ($child_section_info as $key_index => $sec_child_xml) {
						$this->format_child_xml_data($sec_child_xml);
						$sec_xml['ChildSection']['SectionInfo'][$key_index]  = $sec_child_xml;
					}
				}
				else
				{
					$this->format_child_xml_data($child_section_info);
					$sec_xml['ChildSection']['SectionInfo']  = $child_section_info;
				}
			}
			//Add End LIXD-11 MINH_VNIT 2015/09/28
			
			// 表示順序の取得
			$info = $this->get_part('/XML/SeriesHead/SeriesHeadSection/SectionInfo/SectionId[text()="' . $sec_xml['SectionId'] . '"]');
			if ((isset($info[0]['@attributes']) == true) && (isset($info[0]['@attributes']['DispSeq'])) == true) {
				$sec_xml['DispSeq'] = $info[0]['@attributes']['DispSeq'];
			} else {
				$sec_xml['DispSeq'] = null;
			}
			
			//Add Start - NBKD-46 - Tin VNIT - 2014/1/12
			$info_name = $this->get_part('/XML/SeriesHead/SeriesHeadSection/SectionInfo/SectionName[text()="' . $sec_xml['SectionName'] . '"]');
			
			//add start LIXD-118 Phong VNIT-20150825
			if ((isset($info_name[0]['@attributes']) == true) && (isset($info_name[0]['@attributes']['disp'])) == true) {
				$sec_xml['disp_name'] = $info_name[0]['@attributes']['disp'];			
			}
			//add end LIXD-118 Phong VNIT-20150825
			/*if ((isset($info_name[0]['@attributes']) == true) && (isset($info_name[0]['@attributes']['pre'])) == true) {
				$sec_xml['PreName'] = $info_name[0]['@attributes']['pre'];
			} else {
				$sec_xml['PreName'] = null;
			}
			if ((isset($info_name[0]['@attributes']) == true) && (isset($info_name[0]['@attributes']['suf'])) == true) {
				$sec_xml['SufName'] = $info_name[0]['@attributes']['suf'];
			} else {
				$sec_xml['SufName'] = null;
			}//remove LIXD-118 Tin-VNIT-20150825 */ 
			//Add End - NBKD-46 - Tin VNIT - 2014/1/12
			if(count($sec_xml['Data']) > 0){
				for($i=0;$i<count($sec_xml['Data']);$i++){
					if(isset($sec_xml['Data'][$i]['DataShareList']['DataShareFlg'][0])){
						$sec_xml['Data'][$i]['DataShareList']['ShareENG'] = $sec_xml['Data'][$i]['DataShareList']['DataShareFlg'][0];
					}
					if(isset($sec_xml['Data'][$i]['DataShareList']['DataShareFlg'][1])){
						$sec_xml['Data'][$i]['DataShareList']['ShareCN'] = $sec_xml['Data'][$i]['DataShareList']['DataShareFlg'][1];
					}
				}
			}
			//Add Start - NBKD-85 - Trung VNIT - 2014/10/30
/*			if(isset($sec_xml['Data'][0]['DataShareList']['DataShareFlg'][0])){
				$sec_xml['Data'][0]['DataShareList']['ShareENG'] = $sec_xml['Data'][0]['DataShareList']['DataShareFlg'][0];
			}else{
				//$sec_xml['Data'][0]['DataShareList']['ShareENG'] = '0';
			}
			if(isset($sec_xml['Data'][0]['DataShareList']['DataShareFlg'][1])){
				$sec_xml['Data'][0]['DataShareList']['ShareCN'] = $sec_xml['Data'][0]['DataShareList']['DataShareFlg'][1];
			}else{
				//$sec_xml['Data'][0]['DataShareList']['ShareCN'] = '0';
			}*/
			//Add End - NBKD-85 - Trung VNIT - 2014/10/30

			$result[$key] = $sec_xml;
		}
		ACWLog::debug_var('READXML_FORMAT', $result);

		return $result;
	}
	//Add start LIXD-11 TinVNIT - 9/25/2015
	public function get_child_section_data($section_path)
	{
		//$sec_arr = $this->get_part('/XML/SeriesHead/SeriesHeadSection/SectionInfo');
		$section_path = $section_path . '/ChildSection/SectionInfo';
		$sec_arr = $this->get_part($section_path);

		$result = array();
		foreach ($sec_arr as $sec_xml) {
			$key = 'section_' . $sec_xml['SectionId'];
			$this->format_section($sec_xml);
			
			// 表示順序の取得
			//$info = $this->get_part('/XML/SeriesHead/SeriesHeadSection/SectionInfo/SectionId[text()="' . $sec_xml['SectionId'] . '"]');
			$info = $this->get_part($section_path . 'SectionId[text()="' . $sec_xml['SectionId'] . '"]');
			if ((isset($info[0]['@attributes']) == true) && (isset($info[0]['@attributes']['DispSeq'])) == true) {
				$sec_xml['DispSeq'] = $info[0]['@attributes']['DispSeq'];
			} else {
				$sec_xml['DispSeq'] = null;
			}
			
			//Add Start - NBKD-46 - Tin VNIT - 2014/1/12
			//$info_name = $this->get_part('/XML/SeriesHead/SeriesHeadSection/SectionInfo/SectionName[text()="' . $sec_xml['SectionName'] . '"]');
			$info_name = $this->get_part($section_path.'/SectionName[text()="' . $sec_xml['SectionName'] . '"]');
			
			//add start LIXD-118 Phong VNIT-20150825
			if ((isset($info_name[0]['@attributes']) == true) && (isset($info_name[0]['@attributes']['disp'])) == true) {
				$sec_xml['disp_name'] = $info_name[0]['@attributes']['disp'];			
			}
			//add end LIXD-118 Phong VNIT-20150825
			/*if ((isset($info_name[0]['@attributes']) == true) && (isset($info_name[0]['@attributes']['pre'])) == true) {
				$sec_xml['PreName'] = $info_name[0]['@attributes']['pre'];
			} else {
				$sec_xml['PreName'] = null;
			}
			if ((isset($info_name[0]['@attributes']) == true) && (isset($info_name[0]['@attributes']['suf'])) == true) {
				$sec_xml['SufName'] = $info_name[0]['@attributes']['suf'];
			} else {
				$sec_xml['SufName'] = null;
			}//remove LIXD-118 Tin-VNIT-20150825 */ 
			//Add End - NBKD-46 - Tin VNIT - 2014/1/12
			if(count($sec_xml['Data']) > 0){
				for($i=0;$i<count($sec_xml['Data']);$i++){
					if(isset($sec_xml['Data'][$i]['DataShareList']['DataShareFlg'][0])){
						$sec_xml['Data'][$i]['DataShareList']['ShareENG'] = $sec_xml['Data'][$i]['DataShareList']['DataShareFlg'][0];
					}
					if(isset($sec_xml['Data'][$i]['DataShareList']['DataShareFlg'][1])){
						$sec_xml['Data'][$i]['DataShareList']['ShareCN'] = $sec_xml['Data'][$i]['DataShareList']['DataShareFlg'][1];
					}
				}
			}
			//Add Start - NBKD-85 - Trung VNIT - 2014/10/30
/*			if(isset($sec_xml['Data'][0]['DataShareList']['DataShareFlg'][0])){
				$sec_xml['Data'][0]['DataShareList']['ShareENG'] = $sec_xml['Data'][0]['DataShareList']['DataShareFlg'][0];
			}else{
				//$sec_xml['Data'][0]['DataShareList']['ShareENG'] = '0';
			}
			if(isset($sec_xml['Data'][0]['DataShareList']['DataShareFlg'][1])){
				$sec_xml['Data'][0]['DataShareList']['ShareCN'] = $sec_xml['Data'][0]['DataShareList']['DataShareFlg'][1];
			}else{
				//$sec_xml['Data'][0]['DataShareList']['ShareCN'] = '0';
			}*/
			//Add End - NBKD-85 - Trung VNIT - 2014/10/30

			$result[$key] = $sec_xml;
		}
		ACWLog::debug_var('READXML_FORMAT', $result);

		return $result;
	}
	//Add end LIXD-11 TinVNIT - 9/25/2015
	//Add Start LIXD-11 MINH_VNIT 2015/09/28	
	public function format_child_xml_data(&$sec_xml)
	{
			$key = 'section_' . $sec_xml['SectionId'];
			$this->format_section($sec_xml);
			
			// 表示順序の取得
			$info = $this->get_part('/XML/SeriesHead/SeriesHeadSection/SectionInfo/ChildSection/SectionInfo/SectionId[text()="' . $sec_xml['SectionId'] . '"]');
			if ((isset($info[0]['@attributes']) == true) && (isset($info[0]['@attributes']['DispSeq'])) == true) {
				$sec_xml['DispSeq'] = $info[0]['@attributes']['DispSeq'];
			} else {
				$sec_xml['DispSeq'] = null;
			}
			
			//Add Start - NBKD-46 - Tin VNIT - 2014/1/12
			$info_name = $this->get_part('/XML/SeriesHead/SeriesHeadSection/SectionInfo/ChildSection/SectionInfo/SectionName[text()="' . $sec_xml['SectionName'] . '"]');
			
			//add start LIXD-118 Phong VNIT-20150825
			if ((isset($info_name[0]['@attributes']) == true) && (isset($info_name[0]['@attributes']['disp'])) == true) {
				$sec_xml['disp_name'] = $info_name[0]['@attributes']['disp'];			
			}
			
			if(count($sec_xml['Data']) > 0){
				for($i=0;$i<count($sec_xml['Data']);$i++){
					if(isset($sec_xml['Data'][$i]['DataShareList']['DataShareFlg'][0])){
						$sec_xml['Data'][$i]['DataShareList']['ShareENG'] = $sec_xml['Data'][$i]['DataShareList']['DataShareFlg'][0];
					}
					if(isset($sec_xml['Data'][$i]['DataShareList']['DataShareFlg'][1])){
						$sec_xml['Data'][$i]['DataShareList']['ShareCN'] = $sec_xml['Data'][$i]['DataShareList']['DataShareFlg'][1];
					}
				}
			}
	}
	//Add End LIXD-11 MINH_VNIT 2015/09/28
	
	//Add Start NBKD-85 Minh Vnit 2014/11/14
	public function check_section_datashare($t_series_head_id, $m_lang, $yoyaku_flg = null)
	{
		if (is_null($yoyaku_flg) == false) {
			$Series_model = new YoyakuSeries_model();
		} else {
			$Series_model = new Series_model();
		}
		$sec_arr = $this->get_part('/XML/SeriesHead/SeriesHeadSection/SectionInfo');

		foreach ($sec_arr as $sec_xml) {
			
			if(isset($sec_xml['Data']) &&  count($sec_xml['Data']) > 0){
				if(isset($sec_xml['Data']['DataShareList']['DataShareFlg'][0]) && $m_lang == 2){
					$value = $sec_xml['Data']['DataShareList']['DataShareFlg'][0];
					if($value == 1){
						$t_ctg_section_id = $sec_xml["SectionId"];
						if($Series_model->get_item_section_lock($t_series_head_id, $m_lang, $t_ctg_section_id)== TRUE){
							return TRUE;
						}						
					}
					
				}
				if(isset($sec_xml['Data']['DataShareList']['DataShareFlg'][1]) && $m_lang == 3){
					$value = $sec_xml['Data']['DataShareList']['DataShareFlg'][0];
					if($value == 1){
						$t_ctg_section_id = $sec_xml["SectionId"];
						if($Series_model->get_item_section_lock($t_series_head_id, $m_lang, $t_ctg_section_id)== TRUE){
							return TRUE;
						}
					}
				}
			}
		}

		return FALSE;
	}
	//Add End NBKD-85 Minh Vnit 2014/11/14
	
	//Add Start - NBKD-928-SearchAdvance - MinhVNIT - 2015/05/08
	public function read_xml_icon_name($t_series_head_id, $icon_value, $section_name)
	{
		$sec_arr = $this->get_part('/XML/SeriesHead/SeriesHeadSection/SectionInfo');

		foreach ($sec_arr as $sec_xml) {
			if($sec_xml['SectionName'] ==  $section_name 
				&& isset($sec_xml['Data']) 
				&&  count($sec_xml['Data']) > 0
				){
				if(isset($sec_xml['Data']['CheckBox']['SelectedValue']) 
					&& $sec_xml['Data']['DataKbn'] == 5
					){
					$SelectedValue = $sec_xml['Data']['CheckBox']['SelectedValue'];
					if(is_array($SelectedValue))
					{
						if(in_array($icon_value, $SelectedValue)){
							return TRUE;						
						}
						else{
							return FALSE;
						}
					}
					else
					{
						if($SelectedValue === $icon_value){
							return TRUE;						
						}
						else{
							return FALSE;
						}
					}	
				}
			}
		}

		return FALSE;
	}
	//Add End - NBKD-928-SearchAdvance - MinhVNIT - 2015/05/08
	
	/**
	 * フリー項目を取ってくる
	 * 新データ型対応シンプル版
	 */
	public function get_free_section_data()
	{
		$sec_arr = $this->get_part('/XML/Free/FreeSection');
		// Add start NBKD-41 hungtn VNIT 2014/12/27
		if($sec_arr == -1) {
		    return -1;
		}
		// Add end NBKD-41 hungtn VNIT 2014/12/27
		foreach ($sec_arr as &$sec_xml) {
			//Add Start LIXD-294 MINHVNIT 2015/11/05
			if(isset($sec_xml['SectionName']) == TRUE){
				$info_name = $this->get_part('/XML/Free/FreeSection/SectionName[text()="' . $sec_xml['SectionName'] . '"]');
			
				$sec_xml['disp_name'] = $sec_xml['SectionName'];
				if ((isset($info_name[0]['@attributes']) == true) 
				&& (isset($info_name[0]['@attributes']['disp'])) == true
				&& $info_name[0]['@attributes']['disp'] != ""
				) {
					$sec_xml['disp_name'] = $info_name[0]['@attributes']['disp'];			
				}
			}
			//Add End LIXD-294 MINHVNIT 2015/11/05
			$this->format_section($sec_xml);
            
            //Add Start - LIXD-294 - TrungVNIT - 2015/05/11
            $info_name_free = $this->get_part('/XML/Free/FreeSection/SectionName[text()="' . $sec_xml['SectionName'] . '"]');
            if ((isset($info_name_free[0]['@attributes']) == true) && (isset($info_name_free[0]['@attributes']['disp'])) == true) {
				$sec_xml['disp_name'] = $info_name_free[0]['@attributes']['disp'];			
			}
            //Add End - LIXD-294 - TrungVNIT - 2015/05/11
		}
		ACWLog::debug_var('READFREEXML_FORMAT', $sec_arr);

		return $sec_arr;
	}


	/**
	 * XML読み込み形の統一
	 */
	public function format_section(&$sec_xml)
	{
		$this->covert_text($sec_xml['SectionName']);
		// 複数可能
		$this->convert_multi($sec_xml['Data']);
		foreach ($sec_xml['Data'] as &$xml_data) {
			$this->covert_text($xml_data['DataKbn']);
			$this->covert_text($xml_data['Textdata']);
			$this->covert_text($xml_data['OrgFileName']);
			$this->covert_text($xml_data['FileName']);
			$this->covert_text($xml_data['OrgHeaderFileName']);
			$this->covert_text($xml_data['HeaderFileName']);
			$this->covert_text($xml_data['Title']);
			$this->covert_text($xml_data['SubTitle']);//Add - LIXD-376 - TrungVNIT - 2015/08/12
			$this->covert_text($xml_data['Note']);
			$this->covert_text($xml_data['Caption']); //Add LIXD-80 TinVNIT 8/12/2015
			$this->covert_text($xml_data['Unit']);
			$this->covert_text($xml_data['CommonSpecId']);
//			$this->covert_text($xml_data['Alt']);
//			$this->covert_text($xml_data['LinkURL']);
//			$this->covert_text($xml_data['Target']);
			$this->force_multi($xml_data, 'CheckBox', 'SelectedValue');
            
            $this->covert_text($xml_data['DataId']);
            $this->covert_text($xml_data['TableType']);
            $this->covert_text($xml_data['TablePage']);
		}
		$this->force_multi($sec_xml, 'MediaType', 'SelectedValue');
	}

	/**
	 * 項目（フリーではない）の一部を取ってくる
	 */
	public function get_section($read_section)
	{
		$result = $this->get_section_data();
		$key = 'section_' . $read_section;
		return (isset($result[$key])) ? $result[$key] : array();
	}

	/*
	 * セクションをチェック
	 */
	public function check_all_section($sec_arr)
	{
		$result = array();
		foreach ($sec_arr as $sec_xml) {
			$result[] = $this->check_section_data($sec_xml['SectionId'], $sec_xml['Data']);
		}

		return $result;
	}

	/*
	 * 単一セクションをチェック
	 */
	public function check_section_data($sec_id, $mdata)
	{
		$data_ok = 1;
		foreach ($mdata as $data) {
			$kbn = $data['DataKbn'];
			if ($kbn == AKAGANE_SECTION_KBN_TEXT || $kbn == AKAGANE_SECTION_KBN_TEXTAREA || $kbn == AKAGANE_SECTION_KBN_CHECKBOX) {
				// 単一行テキスト、複数行テキスト
				if (isset($data['Textdata']) == false) {
					$data_ok = 0;
					break;
				}
			} else if ($kbn == AKAGANE_SECTION_KBN_IMAGE || $kbn == AKAGANE_SECTION_KBN_EXCEL || AKAGANE_SECTION_KBN_HEADER_EXCEL || AKAGANE_SECTION_KBN_IMAGE_MASTER) { // Add Start LIXD-9 hungtn VNIT 20150622
				// EXCELと画像
				if (isset($data['FileName']) == false) {
					$data_ok = 0;
					break;
				}
			}
		}

		return array(
			'id' => $sec_id
			,'data' => $data_ok
		);
	}

	/*
	 * テキストの場合の変換
	 */
	public function covert_text(&$text)
	{
		if (is_array($text)) {	// なぜか空だと配列になる
			$text = null;
		}
	}

	/*
	 * 複数可の場合の強制変換
	 */
	public function force_multi(&$parent, $arr_key, $key)
	{
		if (isset($parent[$arr_key]) == false) {
			$parent[$arr_key]  = array();
		}
		if (isset($parent[$arr_key][$key]) == false) {
			$parent[$arr_key][$key] = array();
			return;
		}

		$this->convert_multi($parent[$arr_key][$key]);
	}

	/*
	 * 複数可の場合の変換
	 */
	public function convert_multi(&$info)
	{
		if (isset($info) == false) {
			$info = array();
			return;
		}

		if (is_array($info) && isset($info[0])) {
			return;
		}

		$newinfo = array();
		$newinfo[] = $info;
		$info = $newinfo;
	}

	/**
	 * 変換
	 */
	private function _to_array($el)
	{
		return json_decode(json_encode($el), true);
	}
	//add start LIXD-376 Phong VNIT 20151219
	private function delete_exuber_tag($file_path)
	{	
		$xml_str = file_get_contents($file_path);
		$doc = new DOMDocument('1.0', 'UTF-8');
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$doc->loadXML($xml_str);
		$xpath = new DOMXPath($doc);
			
		$alt_list = $xpath->query('//Alt');
		foreach ($alt_list as $res) {
			$node = $res->parentNode;
			$node->removeChild($res);
		}
		$link_list = $xpath->query('//LinkURL');
		foreach ($link_list as $res) {
			$node = $res->parentNode;
			$node->removeChild($res);
		}
		$target_list = $xpath->query('//Target');
		foreach ($target_list as $res) {
			$node = $res->parentNode;
			$node->removeChild($res);
		}		
		
		$res_xml = $doc->saveXML();
		
		unset($xpath);
		unset($doc);
		return $res_xml;
	}//add end LIXD-376 Phong VNIT 20151219
}
/* ファイルの終わり */