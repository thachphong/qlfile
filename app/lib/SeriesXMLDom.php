<?php
/**
 * XML DOM操作ライブラリ出力用
*/
class SeriesXMLDom_lib
{
	protected $doc;
	protected $root;

	/**
	 * コンストラクタ
	 */
	public function __construct($file=null)
	{
		if (is_null($file)) {
			$this->doc = new DOMDocument('1.0', 'UTF-8');
			// XML
			$this->root = $this->_element($this->doc, 'XML');
		} else {
			// 新規
			$this->doc = new DOMDocument('1.0', 'UTF-8');
			$this->doc->preserveWhiteSpace = false;	// これを書かないと崩れる
			$this->doc->load($file);
			$this->root = $this->doc->documentElement;
		}
		// 出力はきれいに整形したいですね。
		$this->doc->formatOutput = true;
	}

	/*
	 * 要素を追加する
	 */
	protected function _element($elm, $name, $value=null)
	{
		$new_elm = $elm->appendChild($this->doc->createElement($name));
		if (is_null($value) == false) {
			$new_elm->appendChild($this->doc->createTextNode($value));
		}
		return $new_elm;
	}

	/*
	 * 作成する
	 */
	public function create($xml_path, $series_id, $lang, $series_name, $section_list, $mode_new = null)
	{
		/*
		 * Freeがあるか
		 */
		$Free = $this->root->getElementsByTagName('Free');
		if ($Free->length > 0) {
			// SeriesHeadは先頭へ
			$SeriesHead = $this->root->insertBefore($this->doc->createElement('SeriesHead'), $Free->item(0));
		} else {
			// 普通に追加
			$SeriesHead = $this->_element($this->root, 'SeriesHead');
		}

		/*
		 * 基本定義
		 */
		$SeriesHeadSection = $this->_element($SeriesHead, 'SeriesHeadSection');
		
		/*
		 * セクションループ
		 */
		foreach ($section_list as $sec) {
			// 定義データを取得
			$sec_def = $sec->convert();
			
			/*
			 * セクション基本
			 */
			// SectionInfo
			$SectionInfo = $this->_element($SeriesHeadSection, 'SectionInfo');
			// SectionId
			$sec_id = $this->_element($SectionInfo, 'SectionId', $sec_def['id']);
			// 表示順序
			if (is_null($sec_def['disp_seq']) == false) {
				$sec_id->setAttributeNode(new DOMAttr('DispSeq', $sec_def['disp_seq']));
			}
			// SectionName
			//Edit Start - NBKD-46 - Tin VNIT - 2014/1/12
			$sec_name = $this->_element($SectionInfo, 'SectionName', $sec_def['name']);
			$disp_name = (is_null($sec_def['disp_name']) == false) ? $sec_def['disp_name'] : $sec_def['name'];//add LIXD-118 Phong VNIT-20150825
			$sec_name->setAttributeNode(new DOMAttr('disp', $disp_name));									   //add LIXD-118 Phong VNIT-20150825	
				
			//Edit Start - NBKD-46 - TrungVNIT - 2014/12/03
			/*if($sec_def['section_type'] == 2){
				$pre_name = (is_null($sec_def['pre_name']) == false) ? $sec_def['pre_name'] : '';
				$sec_name->setAttributeNode(new DOMAttr('pre', $pre_name));
				$suf_name = (is_null($sec_def['suf_name']) == false) ? $sec_def['suf_name'] : '';
				$sec_name->setAttributeNode(new DOMAttr('suf', $suf_name));
			}//remove LIXD-118 Tin-VNIT-20150825*/
			//Edit End - NBKD-46 - TrungVNIT - 2014/12/03
			
			//Edit End- NBKD-46 - Tin VNIT - 2014/1/12
			// Data
			//Add Start - NBKD-85 - Trung VNIT - 2014/10/31
			if($mode_new == 'NEW'){
				for($i=0;$i<count($sec_def['info']);$i++){
					$sec_def['info'][$i]['share_flg_en'] = $sec_def['share_flg_en'];
					$sec_def['info'][$i]['share_flg_cn'] = $sec_def['share_flg_cn'];
				}
				$this->make_data_default($sec_def['info'], $sec_def['media'], $SectionInfo);	
			}else{
			//Add End - NBKD-85 - Trung VNIT - 2014/10/31
				$this->make_data($sec_def['info'], $sec_def['media'], $SectionInfo);
			}
			//add start LIXD-11 Phong VNIT-20150928
			if(count($sec_def['child_section']) > 0){
				$ChildSection = $this->_element($SectionInfo, 'ChildSection');
				foreach($sec->get_child_section() as $sec_child){
					$sec_def = $sec_child->convert();			
					// SectionInfo
					$ChildSecInfo = $this->_element($ChildSection, 'SectionInfo');
					// SectionId
					$sec_id = $this->_element($ChildSecInfo, 'SectionId', $sec_def['id']);
					// 表示順序
					if (is_null($sec_def['disp_seq']) == false) {
						$sec_id->setAttributeNode(new DOMAttr('DispSeq', $sec_def['disp_seq']));
					}					
					//Edit Start - NBKD-46 - Tin VNIT - 2014/1/12
					$sec_name = $this->_element($ChildSecInfo, 'SectionName', $sec_def['name']);
					$disp_name = (is_null($sec_def['disp_name']) == false) ? $sec_def['disp_name'] : $sec_def['name'];
					$sec_name->setAttributeNode(new DOMAttr('disp', $disp_name));									   					
					if($mode_new == 'NEW'){
						for($i=0;$i<count($sec_def['info']);$i++){
							$sec_def['info'][$i]['share_flg_en'] = $sec_def['share_flg_en'];
							$sec_def['info'][$i]['share_flg_cn'] = $sec_def['share_flg_cn'];
						}
						$this->make_data_default($sec_def['info'], $sec_def['media'], $ChildSecInfo);	
					}else{					
						$this->make_data($sec_def['info'], $sec_def['media'], $ChildSecInfo);
					}
				}
			}
			//add end LIXD-11 Phong VNIT-20150928
		}

		// SeriesId
		$this->_element($SeriesHead, 'SeriesId', $series_id);
		// Language
		$this->_element($SeriesHead, 'Language', $lang);
		// SeriesName
		$this->_element($SeriesHead, 'SeriesName', $series_name);

		// 保存
		$this->doc->save($xml_path);
	}
	
	//Add Start NBKD-85 Minh Vnit 2014/11/06
	public function create_new($xml_path, $series_id, $lang, $series_name, $sec_xml,$section_list, $isAssign = FALSE)
	{
		/*
		 * Freeがあるか
		 */
	    // Add start NBKD-41 hungtn VNIT 2014/12/27
	    if(!is_object($this->root)) {
	        $tmp_id = ACWSession::get("t_import_history_id");
	        if(!empty($tmp_id)) {
	            ACWLog::write_file('PATCH_IMPORT_ITEM_ERROR_LOG', 'error get by tag name 128: history_id: '.$tmp_id);
	        }
	        return -1;
	    }
	    // Add end NBKD-41 hungtn VNIT 2014/12/27
		$Free = $this->root->getElementsByTagName('Free');
		$SeriesHead = $this->root->getElementsByTagName('SeriesHead');
		if ($SeriesHead->length > 0) {
			// あったら消す
			$this->root->removeChild($SeriesHead->item(0));
		}
		if ($Free->length > 0) {
			// SeriesHeadは先頭へ
			$SeriesHead = $this->root->insertBefore($this->doc->createElement('SeriesHead'), $Free->item(0));
		} else {
			// 普通に追加
			$SeriesHead = $this->_element($this->root, 'SeriesHead');
		}

		/*
		 * 基本定義
		 */
		$SeriesHeadSection = $this->_element($SeriesHead, 'SeriesHeadSection');

		foreach ($section_list as $sec) {
			// 定義データを取得
			$sec->add_xml_data($sec_xml);
			$sec_def = $sec->convert();
			
			/*
			 * セクション基本
			 */
			// SectionInfo
			$SectionInfo = $this->_element($SeriesHeadSection, 'SectionInfo');
			// SectionId
			$sec_id = $this->_element($SectionInfo, 'SectionId', $sec_def['id']);
			// 表示順序
			if (is_null($sec_def['disp_seq']) == false) {
				$sec_id->setAttributeNode(new DOMAttr('DispSeq', $sec_def['disp_seq']));
			}
			// SectionName
			$sec_name = $this->_element($SectionInfo, 'SectionName', $sec_def['name']);
			$disp_name = (is_null($sec_def['disp_name']) == false) ? $sec_def['disp_name'] : $sec_def['name'];//add LIXD-118 Phong VNIT-20150825
			$sec_name->setAttributeNode(new DOMAttr('disp', $disp_name));									  //add LIXD-118 Phong VNIT-20150825	
			//Add Start- NBKD-46 - Tin VNIT - 2014/1/12			
			//Edit Start - NBKD-46 - TrungVNIT - 2014/12/03
			/*if($sec_def['section_type'] == 2){
				$pre_name = (is_null($sec_def['pre_name']) == false) ? $sec_def['pre_name'] : '';
				$sec_name->setAttributeNode(new DOMAttr('pre', $pre_name));
				$suf_name = (is_null($sec_def['suf_name']) == false) ? $sec_def['suf_name'] : '';
				$sec_name->setAttributeNode(new DOMAttr('suf', $suf_name));
			}//remove LIXD-118 Tin-VNIT-20150825*/
			//Edit End - NBKD-46 - TrungVNIT - 2014/12/03

			//Add End- NBKD-46 - Tin VNIT - 2014/1/12
			// Data
			$tmp_sec_name = $sec_def['name'];//Add NBKD-843 Minh Vnit 2015/01/19
			$this->make_data($sec_def['info'], $sec_def['media'], $SectionInfo, $isAssign, $series_id, $tmp_sec_name);
            
            //add start LIXD-11 Phong VNIT-20150928
			if(count($sec_def['child_section']) > 0){
				$ChildSection = $this->_element($SectionInfo, 'ChildSection');
				foreach($sec->get_child_section() as $sec_child){
					$sec_def = $sec_child->convert();			
					// SectionInfo
					$ChildSecInfo = $this->_element($ChildSection, 'SectionInfo');
					// SectionId
					$sec_id = $this->_element($ChildSecInfo, 'SectionId', $sec_def['id']);
					// 表示順序
					if (is_null($sec_def['disp_seq']) == false) {
						$sec_id->setAttributeNode(new DOMAttr('DispSeq', $sec_def['disp_seq']));
					}					
					//Edit Start - NBKD-46 - Tin VNIT - 2014/1/12
					$sec_name = $this->_element($ChildSecInfo, 'SectionName', $sec_def['name']);
					$disp_name = (is_null($sec_def['disp_name']) == false) ? $sec_def['disp_name'] : $sec_def['name'];
					$sec_name->setAttributeNode(new DOMAttr('disp', $disp_name));
					$tmp_sec_name = $sec_def['name'];//Add NBKD-843 Minh Vnit 2015/01/19
					$this->make_data($sec_def['info'], $sec_def['media'], $ChildSecInfo, $isAssign, $series_id, $tmp_sec_name);
				}
			}
			//add end LIXD-11 Phong VNIT-20150928
		}

		// SeriesId
		$this->_element($SeriesHead, 'SeriesId', $series_id);
		// Language
		$this->_element($SeriesHead, 'Language', $lang);
		// SeriesName
		$this->_element($SeriesHead, 'SeriesName', $series_name);
		
		// 保存
		$this->doc->save($xml_path);
	}
	//Add End NBKD-85 Minh Vnit 2014/11/06

	/*
	 * 更新する
	 */
	public function update($xml_path, $series_id, $lang, $series_name, $section_list)
	{
		/*
		 * SeriesHeadを消す（Freeは残す）
		 */
		$SeriesHead = $this->root->getElementsByTagName('SeriesHead');
		if ($SeriesHead->length > 0) {
			// あったら消す
			$this->root->removeChild($SeriesHead->item(0));
		}

		// 作成
		$this->create($xml_path, $series_id, $lang, $series_name, $section_list);
	}

    //Add Start - NBKD-1154 - TrungVNIT - 2015/06/15
    public function update_syu_free($xml_path, &$list_free_id = '') //Edit - LIXD-10 - TrungVNIT - 2015/07/04
	{
        $path = str_replace('series.xml', '', $xml_path);
		$Free = $this->root->getElementsByTagName('Free');
		$list_id = array();
        foreach ($Free as $xdata) {
            foreach ($xdata->getElementsByTagName('FreeItemId') as $value) {
               $fold = $value->nodeValue;
               $fnew = uniqid();
               $list_id[] = $fnew;
               $value->nodeValue = $fnew;
               $this->rename_free_folder($path, $fold, $fnew);
            }
        }
        //Add Start - LIXD-10 - TrungVNIT - 2015/07/04
        if($list_free_id != ''){
        	$list_key = array();
			foreach($list_free_id as $key => $val){
				$list_key[] = $key;
			}
			$list_free_id = array();
			for($i=0;$i<count($list_key);$i++){
				$list_free_id[$list_key[$i]] = $list_id[$i];
			}
		}
		//Add End - LIXD-10 - TrungVNIT - 2015/07/04
		$this->doc->save($xml_path);
	}
    
    protected function rename_free_folder($path, $fold, $fnew) {
        $path_old = $path . $fold;
        $path_new = $path . $fnew;
        $file = new File_lib();
        try {
            if($file->FolderExists($path_old)){
                rename($path_old, $path_new);
            }
            return TRUE;
        } catch (Exception $exc) {
            ACWLog::write_file('NBKD-1154', 'Cannot rename folder: '.$path_old);
        }
        return FALSE;
    }
    //Add End - NBKD-1154 - TrungVNIT - 2015/06/15
    
	/*
	 * フリー項目を更新する
	 */
	public function update_free($xml_path, $section_list)
	{
		/*
		 * Freeがあるか
		 */
		$Free = $this->root->getElementsByTagName('Free');
		if ($Free->length > 0) {
			// あったら消す
			$this->root->removeChild($Free->item(0));
		}
		$Free = $this->_element($this->root, 'Free');

		/*
		 * セクションループ
		 */
		foreach ($section_list as $sec_def) {
			/*
			 * セクション基本
			 */
			// SectionInfo
			$FreeSection = $this->_element($Free, 'FreeSection');
			// SectionId
			$this->_element($FreeSection, 'FreeItemId', $sec_def['id']);
			// SectionName
			$sec_name = $this->_element($FreeSection, 'SectionName', $sec_def['name']);
			//Add Start LIXD-294 MINHVNIT 2015/11/05			
			$disp_name = (is_null($sec_def['disp_name']) == false) ? $sec_def['disp_name'] : $sec_def['name'];
			$sec_name->setAttributeNode(new DOMAttr('disp', $disp_name));
			//Add End LIXD-294 MINHVNIT 2015/11/05			
			// Data
			$this->make_data($sec_def['info'], $sec_def['media'], $FreeSection);
		}

		// 保存
		$this->doc->save($xml_path);
	}
	
	//Add Start - NBKD-855 - Hungtn VNIT - 2014/12/16
	/*
	 * フリー項目を更新する
	 */
	public function update_free_import($xml_path, $section_list)
	{
	    /*
	     * Freeがあるか
	     */

	    // Add start NBKD-41 hungtn VNIT 2014/12/27
	    if(!is_object($this->root)) {
	        $tmp_id = ACWSession::get("t_import_history_id");
	        if(!empty($tmp_id)) {
	           ACWLog::write_file('PATCH_IMPORT_ITEM_ERROR_LOG', 'error get by tag name 256: history_id: '.$tmp_id);
	        }
	        return -1;
	    }
	    // Add start NBKD-41 hungtn VNIT 2014/12/27

	    $Free = $this->root->getElementsByTagName('Free');
	    if ($Free->length > 0) {
	        // あったら消す
	        $this->root->removeChild($Free->item(0));
	    }
	    $Free = $this->_element($this->root, 'Free');

	    /*
	     * セクションループ
	    */
	    foreach ($section_list as $sec_def) {
	        /*
	         * セクション基本
	         */
	        // SectionInfo
	        $FreeSection = $this->_element($Free, 'FreeSection');
	        // SectionId
	        $this->_element($FreeSection, 'FreeItemId', $sec_def['FreeItemId']);
	        // SectionName
	        $sec_name =  $this->_element($FreeSection, 'SectionName', $sec_def['SectionName']);
            //Add Start - LIXD-294 - TrungVNIT - 2015/05/11
            $disp_name = (is_null($sec_def['disp_name']) == false) ? $sec_def['disp_name'] : $sec_def['SectionName'];
			$sec_name->setAttributeNode(new DOMAttr('disp', $disp_name));
            //Add End - LIXD-294 - TrungVNIT - 2015/05/11
	        // Data
	        $this->make_data_import($sec_def['Data'], $sec_def['MediaType'], $FreeSection);
	    }

	    // 保存
	    $this->doc->save($xml_path);
	}
	
	public function make_data_import($sec_info, $media, $parent)
	{
	    foreach ($sec_info as $info) {
	        // 改行コード統一
	        foreach ($info as $key => $val) {
	            if (is_string($val)) {
	                $info[$key] = str_replace(array("\r\n", "\r"), "\n", $val);
	            }
	        }

	        $Data = $this->_element($parent, 'Data');

	        if (isset($info['DataId'])) {
	            $data_id = $info['DataId'];
	        } else {
	            $data_id = Sequence_common_model::nextval('xml_data_tag_id_seq');
	        }
	        $this->_element($Data, 'DataId', $data_id);
	        // 属性付加
	        $Data->setAttributeNode(new DOMAttr('DataId', $data_id));
			//$data_indent= isset($info['data_indent'])?$info['data_indent']:0;  //add LIXD-11 Phong VNIT -20150818
            //$Data->setAttributeNode(new DOMAttr('DataIndent', $data_indent));  //add LIXD-11 Phong VNIT -20150818
	        $this->_element($Data, 'DataKbn', $info['DataKbn']);
	        $this->_element($Data, 'Textdata', $info['Textdata']);
	        $this->_element($Data, 'OrgFileName', $info['OrgFileName']);
	        $this->_element($Data, 'FileName', $info['FileName']);
	        $this->_element($Data, 'OrgHeaderFileName', $info['OrgHeaderFileName']);
	        $this->_element($Data, 'HeaderFileName', $info['HeaderFileName']);
	        $this->_element($Data, 'Title', $info['Title']);
			$this->_element($Data, 'SubTitle', $info['SubTitle']);//Add - LIXD-376 - TrungVNIT - 2015/08/12
	        $this->_element($Data, 'Note', $info['Note']);
	        $this->_element($Data, 'Caption', $info['Caption']);//Add LIXD-80 TinVNIT 8/12/2015
	        $this->_element($Data, 'Unit', $info['Unit']);
	        $this->_element($Data, 'CommonSpecId', $info['CommonSpecId']);
	        // チェックボックス
	        $CheckBox = $this->_element($Data, 'CheckBox');

	        if (is_array($info['CheckBox'])) {
	            if(isset($info['CheckBox']['SelectedValue'])) {
	                foreach ($info['CheckBox']['SelectedValue'] as $opt) {
	                        $this->_element($CheckBox, 'SelectedValue', $opt);
	                }
	            }
	        }
//	        $this->_element($Data, 'Alt', $info['Alt']);
//	        $this->_element($Data, 'LinkURL', $info['LinkURL']);
//	        $this->_element($Data, 'Target', $info['Target']);
	        $this->_element($Data, 'TableType', $info['TableType']);
	        $this->_element($Data, 'TablePage', $info['TablePage']);

	    }

	    // MediaType
	    $MediaType = $this->_element($parent, 'MediaType');
	    if (is_array($media)) {
	        if(isset($media['SelectedValue'])) {
	            foreach ($media['SelectedValue'] as $med) {
	                    $this->_element($MediaType, 'SelectedValue', $med);
	            }
	        }

	    }
	}
	//Add End - NBKD-855 - hungtn VNIT - 2014/12/16
	
	//Add Start - NBKD-85 - Trung VNIT - 2014/10/28 
	public function make_data_default($sec_info, $media, $parent)
	{
		foreach ($sec_info as $info) {
			// 改行コード統一
			foreach ($info as $key => $val) {
				if (is_string($val)) {
					$info[$key] = str_replace(array("\r\n", "\r"), "\n", $val);
				}
			}

			$Data = $this->_element($parent, 'Data');
            $data_id = null; //Edit Start LIXD-9 hungtn VNIT 20150625
            if (isset($info['data_id'])) {
            	if(!empty($info['data_id'])) {
					$data_id = $info['data_id'];	
				} else {
					$data_id = Sequence_common_model::nextval('xml_data_tag_id_seq');
				}
                
            } else {
                $data_id = Sequence_common_model::nextval('xml_data_tag_id_seq');
            }//Edit End LIXD-9 hungtn VNIT 20150625
            $this->_element($Data, 'DataId', $data_id);
            // 属性付加
            $Data->setAttributeNode(new DOMAttr('DataId', $data_id));
            //$data_indent= isset($info['data_indent'])?$info['data_indent']:0;  //add LIXD-11 Phong VNIT -20150818
            //$Data->setAttributeNode(new DOMAttr('DataIndent',  $data_indent));  //add LIXD-11 Phong VNIT -20150818
            //add start LIXD-11 Phong VNIT -20150929
            if(isset($info['groupid'])&& !empty($info['groupid'])){
				$Data->setAttributeNode(new DOMAttr('GroupID',  $info['groupid'] ));  
			}
            //add end LIXD-11 Phong VNIT -20150929
            
			$this->_element($Data, 'DataKbn', $info['type']);
			$this->_element($Data, 'Textdata', $info['text_data']);
			$this->_element($Data, 'OrgFileName', $info['org_file_name']);
			$this->_element($Data, 'FileName', $info['file_name']);
			$this->_element($Data, 'OrgHeaderFileName', $info['org_header_file_name']);
			$this->_element($Data, 'HeaderFileName', $info['header_file_name']);
			$this->_element($Data, 'Title', $info['title']);
			$this->_element($Data, 'SubTitle', $info['sub_title']);//Add - LIXD-376 - TrungVNIT - 2015/08/12
			$this->_element($Data, 'Note', $info['note']);
			$this->_element($Data, 'Caption', $info['caption']); //Add LIXD-80 TinVNIT 8/12/2015
			$this->_element($Data, 'Unit', $info['unit']);
			$this->_element($Data, 'CommonSpecId', $info['common_spec_id']);
			// チェックボックス
			$CheckBox = $this->_element($Data, 'CheckBox');

			if (is_array($info['option'])) {
				foreach ($info['option'] as $opt) {
					if ($opt['selected'] == 1) {
						$this->_element($CheckBox, 'SelectedValue', $opt['val']);
					}
				}
			}
			//Remove - LIXD-376 - TrungVNIT - 2015/08/12
//			$this->_element($Data, 'Alt', $info['alt']);
//			$this->_element($Data, 'LinkURL', $info['link_url']);
//			$this->_element($Data, 'Target', $info['target']);
			
			$this->_element($Data, 'TableType', $info['table_type']);
			$this->_element($Data, 'TablePage', $info['table_page']);

			$data_share = $this->_element($Data, 'DataShareList');

			if($info['share_flg_en'] != 0 || $info['share_flg_cn'] != 0){
						$share_flg_en = $this->_element($data_share, 'DataShareFlg', $info['share_flg_en']);
						$share_flg_en->setAttributeNode(new DOMAttr('name', 'eng'));
						$share_flg_cn = $this->_element($data_share, 'DataShareFlg',  $info['share_flg_cn']);
						$share_flg_cn->setAttributeNode(new DOMAttr('name', 'cn'));	
				} 
			}
  
		// MediaType
		$MediaType = $this->_element($parent, 'MediaType');
		if (is_array($media)) {
			foreach ($media as $med) {
				if ($med['selected'] == 1) {
					$this->_element($MediaType, 'SelectedValue', $med['val']);
				}
			}
		}
	}
	//Add End - NBKD-85 - Trung VNIT - 2014/10/28 
	
	/*
	 * Data項目を作成する
	 */
	public function make_data($sec_info, $media, $parent, $isAssign = FALSE, $series_id = '', $tmp_sec_name ='')
	{
		foreach ($sec_info as $info) {
			// 改行コード統一
			foreach ($info as $key => $val) {
				if (is_string($val)) {
					$info[$key] = str_replace(array("\r\n", "\r"), "\n", $val);
				}
			}

			$Data = $this->_element($parent, 'Data');
            
            if (isset($info['data_id'])) {
            	if(!empty($info['data_id'])) { //Edit Start LIXD-9 hungtn VNIT 20150625
					$data_id = $info['data_id'];
				} else {
					$data_id = Sequence_common_model::nextval('xml_data_tag_id_seq');
				}

            } else {
                $data_id = Sequence_common_model::nextval('xml_data_tag_id_seq');
            }//Edit End LIXD-9 hungtn VNIT 20150625
            $this->_element($Data, 'DataId', $data_id);
            // 属性付加
            $Data->setAttributeNode(new DOMAttr('DataId', $data_id));
            //$data_indent= isset($info['data_indent'])?$info['data_indent']:0;  //add LIXD-11 Phong VNIT -20150818
            //$Data->setAttributeNode(new DOMAttr('DataIndent', $data_indent));  //add LIXD-11 Phong VNIT -20150818
            //add start LIXD-11 Phong VNIT -20150929
            if(isset($info['groupid'])&& !empty($info['groupid'])){
				$Data->setAttributeNode(new DOMAttr('GroupID',  $info['groupid'] ));  
			}
            //add end LIXD-11 Phong VNIT -20150929
			$this->_element($Data, 'DataKbn', $info['type']);
			//Add Start NBKD-843 Minh Vnit 2015/01/19
			if($isAssign == TRUE && $tmp_sec_name == 'シリーズ品番'){
				$this->_element($Data, 'Textdata', $series_id);
			}
			else{
				$this->_element($Data, 'Textdata', $info['text_data']);
			}
			//Add End NBKD-843 Minh Vnit 2015/01/19
			
			$this->_element($Data, 'OrgFileName', $info['org_file_name']);
			$this->_element($Data, 'FileName', $info['file_name']);
			$this->_element($Data, 'OrgHeaderFileName', $info['org_header_file_name']);
			$this->_element($Data, 'HeaderFileName', $info['header_file_name']);
			$this->_element($Data, 'Title', $info['title']);
			$this->_element($Data, 'SubTitle', $info['sub_title']);//Add - LIXD-376 - TrungVNIT - 2015/08/12
			$this->_element($Data, 'Note', $info['note']);
			$this->_element($Data, 'Caption', $info['caption']); //Add LIXD-80 TinVNIT 8/12/2015
			$this->_element($Data, 'Unit', $info['unit']);
			$this->_element($Data, 'CommonSpecId', $info['common_spec_id']);
			// チェックボックス
			$CheckBox = $this->_element($Data, 'CheckBox');

			if (is_array($info['option'])) {
				foreach ($info['option'] as $opt) {
					if ($opt['selected'] == 1) {
						$this->_element($CheckBox, 'SelectedValue', $opt['val']);
					}
				}
			}
			//Remove - LIXD-376 - TrungVNIT - 2015/08/12
//			$this->_element($Data, 'Alt', $info['alt']);
//			$this->_element($Data, 'LinkURL', $info['link_url']);
//			$this->_element($Data, 'Target', $info['target']);
            $this->_element($Data, 'TableType', $info['table_type']);
            $this->_element($Data, 'TablePage', $info['table_page']);
            
                //Add Start - NBKD-85 - Trung VNIT - 2014/10/28 
                $data_share = $this->_element($Data, 'DataShareList');
                if ($info['share_flg_en'] != null || $info['share_flg_cn'] != null) {
                    if (isset($info['share_flg_en']) && $info['share_flg_en'] == 1) {
                        $share_flg_en = $this->_element($data_share, 'DataShareFlg', 1);
                        $share_flg_en->setAttributeNode(new DOMAttr('name', 'eng'));
                    } else {
                        $share_flg_en = $this->_element($data_share, 'DataShareFlg', 0);
                        $share_flg_en->setAttributeNode(new DOMAttr('name', 'eng'));
                    }
                    if (isset($info['share_flg_cn']) && $info['share_flg_cn'] == 1) {
                        $share_flg_cn = $this->_element($data_share, 'DataShareFlg', 1);
                        $share_flg_cn->setAttributeNode(new DOMAttr('name', 'cn'));
                    } else {
                        $share_flg_cn = $this->_element($data_share, 'DataShareFlg', 0);
                        $share_flg_cn->setAttributeNode(new DOMAttr('name', 'cn'));
                    }
                }
                //Add End - NBKD-85 - Trung VNIT - 2014/10/28     
		}
  
		// MediaType
		$MediaType = $this->_element($parent, 'MediaType');
		if (is_array($media)) {
			foreach ($media as $med) {
				if ($med['selected'] == 1) {
					$this->_element($MediaType, 'SelectedValue', $med['val']);
				}
			}
		}
	}
}
/* ファイルの終わり */