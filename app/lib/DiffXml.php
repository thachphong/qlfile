<?php

/**
 * XML差分抽出
 *
 */
class DiffXml_lib
{
	const debug_file_name = 'DIFF_XML';

	const XPATH_SERIES = '/XML/SeriesHead/SeriesHeadSection/SectionInfo[SectionId ="replace_str"]';
	const XPATH_CHILD_SERIES = '/XML/SeriesHead/SeriesHeadSection/SectionInfo/ChildSection/SectionInfo[SectionId ="replace_str"]';  //Add LIXD-11 TinVNIT - 9/25/2015
	const XPATH_FREE = '/XML/Free/FreeSection[FreeItemId ="replace_str"]';
	const XPATH_RELA = '/XML/RelationInfo';
	
	const XPATH_SERIES_DATA = '/XML/SeriesHead/SeriesHeadSection/SectionInfo/Data[@DataId="%s"]';
	const XPATH_CHILD_SERIES_DATA = '/XML/SeriesHead/SeriesHeadSection/SectionInfo/ChildSection/SectionInfo/Data[@DataId="%s"]';  //Add LIXD-11 TinVNIT - 9/25/2015
	
	const XPATH_FREE_DATA = '/XML/Free/FreeSection/Data[@DataId="%s"]';
	const XPATH_RELA_DATA = '/XML/RelationInfo/Data[@DataId="%s"]';

	const MOD_FLG_NONE = '1';
	const MOD_FLG_YES = '2';
	const MOD_FLG_DEL = '3';

	protected $TAG_SERIES = array('tag_main'=>'SectionInfo' ,'tag_id'=>'SectionId', 'tag_name'=>'SectionName', 'tag_per'=>'SeriesHeadSection');
	protected $TAG_CHILD_SERIES = array('tag_main'=>'SectionInfo' ,'tag_id'=>'SectionId', 'tag_name'=>'SectionName', 'tag_per'=>'SeriesHeadSection'); //Add LIXD-11 TinVNIT - 9/25/2015
	protected $TAG_FREE = array('tag_main'=>'FreeSection' ,'tag_id'=>'FreeItemId', 'tag_name'=>'SectionName', 'tag_per'=>'Free');
	protected $TAG_RELA = array('tag_main'=>'RelationInfo' ,'tag_id'=>'', 'tag_name'=>'', 'tag_per'=>'XML');

	protected $doc;
	protected $xpath;

	protected $old_doc;
	protected $old_xpath;

	protected $RELATION_CHK_TAG = array('0'=>'OrgFileName', '1'=>'Textdata', '2'=>'Note');

	/**
	 * 差分XMLを作成
	 */
	public function create_diff_xml($old_xml_path, $new_xml_path, $dif_xml_path)
	{
		ACWLog::debug_var(self::debug_file_name, '旧XMLパス：' . $old_xml_path);
		ACWLog::debug_var(self::debug_file_name, '新XMLパス：' . $new_xml_path);
		ACWLog::debug_var(self::debug_file_name, '差XMLパス：' . $dif_xml_path);
		
		// 各XMLの存在チェック&オープン
        $file = new File_lib();
        if ($file->FileExists($old_xml_path) == false) {
            return false;
        }
        
		$old_ctrl = new ReadXml_lib();
		if ($old_ctrl->open($old_xml_path) == false) {
			return false;
		}
        
        if ($file->FileExists($new_xml_path) == false) {
            return false;
        }

		$new_ctrl = new ReadXml_lib();
		if ($new_ctrl->open($new_xml_path) == false) {
			return false;
		}

		if (is_null($dif_xml_path)) {
			return false;
		}

		// 差分用XML
		$this->doc = new DOMDocument('1.0', 'UTF-8');
		$this->doc->preserveWhiteSpace = false;
		$this->doc->formatOutput = true;
		$this->doc->load($new_xml_path);
		$this->xpath = new DOMXPath($this->doc);

		$this->old_doc = new DOMDocument('1.0', 'UTF-8');
		$this->old_doc->preserveWhiteSpace = false;
		$this->old_doc->formatOutput = true;
		$this->old_doc->load($old_xml_path);
		$this->old_xpath = new DOMXPath($this->old_doc);
		
		// SectionInfoタグ一覧を取得
		$old_section_list = self::get_section_data($old_ctrl);	// Edit - miyazaki U_SYS - 2014/11/12
		$new_section_list = self::get_section_data($new_ctrl);	// Edit - miyazaki U_SYS - 2014/11/12

		// SeriesHead
		$ser_result = self::main($old_section_list, $new_section_list, self::XPATH_SERIES, $this->TAG_SERIES, false, self::XPATH_SERIES_DATA, $old_ctrl, $new_ctrl); //Edit LIXD-11 TinVNIT - 9/25/2015

		// FreeSectionタグ一覧を取得
		$old_free_list = self::get_free_section_data($old_ctrl);
		$new_free_list = self::get_free_section_data($new_ctrl);

		// 新にFreeが無く、旧にFreeがある場合作成する
		if ((empty($old_free_list) == false) && (empty($new_free_list))) {
			$element = $this->doc->createElement($this->TAG_FREE['tag_per']);
			$xml = $this->doc->getElementsByTagName('XML');
			$xml->item(0)->appendChild($element);
		}

		// Free
		$free_result = self::main($old_free_list, $new_free_list, self::XPATH_FREE, $this->TAG_FREE, true, self::XPATH_FREE_DATA);
		/*　3/27の対応で削除しない
		if ($free_result == false) {
			// 変更がなかった場合、Freeタグの子要素を削除して出力する
			$free = $this->doc->getElementsByTagName($this->TAG_FREE['tag_per']);
			$parnet = $free->item(0);

			for ($i = $parnet->childNodes->length - 1; $i >= 0; $i--) {
				$child = $parnet->childNodes->item($i);
				$parnet->removeChild($child);
			}
		}
		 */

		// RelationInfoタグ一覧を取得
		$old_relation_list = self::get_relation_section_data($old_ctrl);
		$new_relation_list = self::get_relation_section_data($new_ctrl);
		
		// RelationInfo
		$rela_result = self::main($old_relation_list, $new_relation_list, self::XPATH_RELA, $this->TAG_RELA, true, self::XPATH_RELA_DATA);
		
		// 特定のタグを削除
		// Add - miyazaki U_SYS - 2014/11/12
		self::delete_ex_tag();

		// 保存
		$this->doc->save($dif_xml_path);

		// 終了
		$old_ctrl->close();
		$new_ctrl->close();

		unset($this->xpath);
		unset($this->doc);

        if (  $ser_result == false
           && $free_result == false
           && $rela_result == false) {
            return 0;
        } else {
            return 1;
        }
	}

	/**
	 * 処理本体
	 */
	private function main($old_list, $new_list, $x_path, $tag_map, $free_flg, $x_path_data, $old_ctrl = NULL, $new_ctrl = NULL) //Edit LIXD-11 TinVNIT - 9/25/2015
	{
		// 削除要素の親を取得
		$headsection = $this->doc->getElementsByTagName($tag_map['tag_per']);
		$parnet = $headsection->item(0);
		
		$rtn = false;
		if (isset($parnet)) {
			$exists_keys = array();
										
			// 新XMLにあるsectionタグのIDを元に変更、追加要素を探す
			foreach ($new_list as $key => $new_section) {
				ACWLog::write_file(self::debug_file_name, 'target : ' . $key);

				// 新XMLにあるタグのIDを入れる
				$exists_keys[$key] = '1';
						
				$diff_list = array();

				if ($tag_map['tag_id'] == '') {
					$section_path = $x_path;
				} else {
					$section_path = str_replace('replace_str', $new_section[$tag_map['tag_id']], $x_path);
				}

				$section_node = $this->xpath->query($section_path)->item(0);
				//Add start LIXD-11 TinVNIT - 9/25/2015
				if(isset($new_section['ChildSection']['SectionInfo']))
				{
					if(isset($old_ctrl) && isset($new_ctrl))
					{
						$old_child_section_list = self::get_child_section_data($old_ctrl, $section_path);	// Edit - miyazaki U_SYS - 2014/11/12
						$new_child_section_list = self::get_child_section_data($new_ctrl, $section_path);	// Edit - miyazaki U_SYS - 2014/11/12
						
						$rtn_child = self::main($old_child_section_list, $new_child_section_list, self::XPATH_CHILD_SERIES, $this->TAG_CHILD_SERIES, false, self::XPATH_CHILD_SERIES_DATA);
						if($rtn === FALSE)
						{
							$rtn = $rtn_child;
						}
					}
				}
				//Add end LIXD-11 TinVNIT - 9/25/2015
				if (empty($new_section['Data'])) {
					// 新のDataタグが存在しない　→　旧のデータがあれば削除パターンで追加
					if (array_key_exists($key, $old_list)) {
						// 旧のDataタグ一覧ループ
						foreach($old_list[$key]['Data'] as $old_data) {
							$old_data_id = $old_data['@attributes']['DataId'];
							$diff_list[$old_data_id] = '0';
						}
					}
				} else {
					// 新のDataタグ一覧ループ
					foreach ($new_section['Data'] as $new_data) {
						$new_data_id = $new_data['@attributes']['DataId'];						
						// 対象
						$data_path = sprintf($x_path_data, $new_data_id);
						$data_node = $this->xpath->query($data_path)->item(0);						
						// 変更区分
						$mod_flg = $this->doc->createElement('ModFlg');

						if (is_null($this->xpath->query($data_path . '/ModFlg')->item(0))) {
							// 変更区分を持っていない → 新規かもしれないのでModFlgを2（変更あり）で登録
							$mod_flg->nodeValue = self::MOD_FLG_YES;
						}

						// 両方にあればDataId同士で比較
						if (array_key_exists($key, $old_list)) {

							// 旧のDataタグ一覧ループ
							foreach($old_list[$key]['Data'] as $old_data) {
								$old_data_id = $old_data['@attributes']['DataId'];								
								if (array_key_exists($old_data_id, $diff_list) === false) {
									$diff_list[$old_data_id] = '0';
								}

								// 旧と新のDataIdを比較
								if (strcmp($new_data_id, $old_data_id) == 0) {
									// 旧と新のDataを比較
									//$result = self::search_diff_tag($old_data, $new_data);
									$result = self::compare_data_list($old_data, $new_data, $data_node);

									if ($result == false) {
										// 変更がなかった→ModFlgを1（変更なし）で登録
										$mod_flg->nodeValue = self::MOD_FLG_NONE;
										$data_node->appendChild($mod_flg);

										/*　3/27の対応で削除しない
										// 変更がなかった→Dataタグを新から削除
										$section_node->removeChild($data_node);
										*/
									} else {
										$rtn = true;
										$data_node->appendChild($mod_flg);
									}

									$diff_list[$old_data_id] = '1';
									//break;
								}
							}

							if (is_null($this->xpath->query($data_path . '/ModFlg')->item(0))) {
								// 新規のデータ部→すべてのタグのModFlgを2（変更あり）で登録
								$result = self::compare_data_list(array(), $new_data, $data_node);
								// 変更区分を持っていない → 新規ModFlgを2（変更あり）で登録
								$rtn = true;
								$data_node->appendChild($mod_flg);
							}

						} else {
							// 新規のデータ部→すべてのタグのModFlgを2（変更あり）で登録
							$result = self::compare_data_list(array(), $new_data, $data_node);
							// 旧になければ新規
							$rtn = true;
							$data_node->appendChild($mod_flg);
						}
					}
				}

				// 新XMLで削除されたもの
				foreach ($diff_list as $d_key => $diff) {
					if ($diff == '0') {
						$data_path = sprintf($x_path_data, $d_key);
						$data_node = $this->old_xpath->query($data_path)->item(0);
						$this->import_del_data_tag($data_node);

						$new_node = $this->doc->importNode($data_node, true);
						$section_node->appendChild($new_node);

						$rtn = true;
					}
				}

				// 処理後Dataタグが無い場合の処理
				$after_data = $section_node->getElementsByTagName('Data');
				if ($after_data->length <= 0) {
					// Dataタグがない場合は親を削除する
					$parnet->removeChild($section_node);
				} else {
					// SectionNameにModFlgを付加
					if (array_key_exists($key, $old_list) === true) {
						// 両方に同じ項目がある
						if (empty($old_list[$key]['Data']) == true) {
							// 旧側にDataタグがない→前回の組版では削られた項目
							//edit start LIXD-335 Phong VNIT -20151112
							if(empty($new_section['Data'])== true){
								//edit start LIXD-315 Phong VNIT-20151117
								if (strcmp($old_list[$key]['SectionName'].$old_list[$key]['disp_name'], $new_section['SectionName'].$new_section['disp_name']) == 0) {
								self::add_mod_flg('SectionName', self::MOD_FLG_NONE, $section_node);	
								}else{
									self::add_mod_flg('SectionName', self::MOD_FLG_YES, $section_node);									
									$rtn = true;
								}
								//edit end LIXD-315 Phong VNIT-20151117
							}else{
								self::add_mod_flg('SectionName', self::MOD_FLG_YES, $section_node); 
								$rtn = true;	// 差分あり
							}//edit end LIXD-335 Phong VNIT -20151112
						} else {
							// 両方Dataタグがあれば比較
							// Add start NBKD-46 Phong VNIT 20141209
							/*$old_pre_name ='';
							$old_suf_name ='';
							$new_pre_name ='';
							$new_suf_name ='';							
							if( isset($old_list[$key]['PreName'])) {
								$old_pre_name = $old_list[$key]['PreName'];								
							}
							if( isset($old_list[$key]['SufName'])) {
								$old_suf_name = $old_list[$key]['SufName'];
							}
							if( isset($new_section['PreName'])) {
								$new_pre_name = $new_section['PreName'];
							}
							if( isset($new_section['SufName'])) {
								$new_suf_name = $new_section['SufName'];
							}
							if (strcmp($old_suf_name.$old_list[$key]['SectionName'].$old_pre_name, $new_suf_name.$new_section['SectionName'].$new_pre_name) == 0) {remove LIXD-118 Tin-VNIT-20150825*/
							if (strcmp($old_list[$key]['SectionName'].$old_list[$key]['disp_name'], $new_section['SectionName'].$new_section['disp_name']) == 0) { //add Edit-118 Phong-VNIT-20150831
							//if (strcmp($old_list[$key]['SectionName'], $new_section['SectionName']) == 0) {
							// Add end NBKD-46 Phong VNIT 20141209							
								self::add_mod_flg('SectionName', self::MOD_FLG_NONE, $section_node);
							} else {								
								self::add_mod_flg('SectionName', self::MOD_FLG_YES, $section_node);
								$rtn = true;	// 差分あり
							}
						}
					} else {
						// 旧側にないため、変更あり
						self::add_mod_flg('SectionName', self::MOD_FLG_YES, $section_node);
						$rtn = true;	// 差分あり
					}
				}						
			}

			// 新XMLに存在しないIDを持つ旧XMLのsectionの一覧を取得
			$del_list = array_diff_key($old_list, $exists_keys);

			if (isset($del_list) == true) {
				// 削除されたIDを追加
				foreach ($del_list as $del_section) {

					// 削除されたXMLを取得し、関連付けする
					if ($tag_map['tag_id'] == '') {
						$path = $x_path;
					} else {
						$path = str_replace('replace_str', $del_section[$tag_map['tag_id']], $x_path);
					}
					
                    $node = $this->old_xpath->query($path)->item(0);
                    // 不要な子要素を削除
					$data_flg = false;
                    for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {
                        $child = $node->childNodes->item($i);

                        if (strcmp($child->nodeName, 'Data') == 0) {
                            $this->import_del_data_tag($child);
							$data_flg = true;
                        } else if (strcmp($child->nodeName, 'SectionId') == 0) {
                            ; //残す
                        } else if (strcmp($child->nodeName, 'SectionName') == 0) {
                            ; //残す
						} else if (strcmp($child->nodeName, 'FreeItemId') == 0) {
							; // 残す
						} else if (strcmp($child->nodeName, 'ChildSection') == 0) {//add LIXD-293 Phong VNIT-20151104
							;
                        } else {
                            // Dataタグ以外は消す
                            $node->removeChild($child);
                        }
                    }
                    //add start LIXD-293 Phong VNIT-20151104
                    $is_parent = FALSE; //add LIXD-315 Phong VNIT-20151112
                    if(isset($del_section['ChildSection']['SectionInfo'])){
                    	$is_parent = TRUE; //add LIXD-315 Phong VNIT-20151112
                    	if(isset($del_section['ChildSection']['SectionInfo']['SectionId']))
                    	{
                    		$sec_child = $del_section['ChildSection']['SectionInfo'];
							$child_path = '/XML/SeriesHead/SeriesHeadSection/SectionInfo/ChildSection/SectionInfo[SectionId="'.$sec_child['SectionId'].'"]';
							$child_node = $this->old_xpath->query($child_path)->item(0);
                    
							$data_flg_child = false;
		                    for ($i = $child_node->childNodes->length - 1; $i >= 0; $i--) {
		                        $item_child = $child_node->childNodes->item($i);
		                        if (strcmp($item_child->nodeName, 'Data') == 0) {
		                            $this->import_del_data_tag($item_child);
									$data_flg = true;
									$data_flg_child = false;
		                        } else if (strcmp($item_child->nodeName, 'SectionId') == 0) {
		                            ; //残す
		                        } else if (strcmp($item_child->nodeName, 'SectionName') == 0) {
		                            ; //残す
								} else if (strcmp($item_child->nodeName, 'FreeItemId') == 0) {
									; // 残す
		                        } else {
		                            // Dataタグ以外は消す
		                            $child_node->removeChild($item_child);
		                        }		                                              
		                    }
		                    if ($data_flg_child) {
								$new_node_child = $this->doc->importNode($child_node, true);
								// 削除されたセクションのSectionNameのModFlg
								self::add_mod_flg('SectionName', self::MOD_FLG_NONE, $new_node_child);
								$node->appendChild($new_node_child);
							}  
						}
						else
						{
							foreach($del_section['ChildSection']['SectionInfo'] as $sec_child){
								$child_path = '/XML/SeriesHead/SeriesHeadSection/SectionInfo/ChildSection/SectionInfo[SectionId="'.$sec_child['SectionId'].'"]';
								$child_node = $this->old_xpath->query($child_path)->item(0);
	                    
								$data_flg_child = false;
			                    for ($i = $child_node->childNodes->length - 1; $i >= 0; $i--) {
			                        $item_child = $child_node->childNodes->item($i);
			                        if (strcmp($item_child->nodeName, 'Data') == 0) {
			                            $this->import_del_data_tag($item_child);
										$data_flg = true;
										$data_flg_child = false;
			                        } else if (strcmp($item_child->nodeName, 'SectionId') == 0) {
			                            ; //残す
			                        } else if (strcmp($item_child->nodeName, 'SectionName') == 0) {
			                            ; //残す
									} else if (strcmp($item_child->nodeName, 'FreeItemId') == 0) {
										; // 残す
			                        } else {
			                            // Dataタグ以外は消す
			                            $child_node->removeChild($item_child);
			                        }		                                              
			                    }
			                    if ($data_flg_child) {
									$new_node_child = $this->doc->importNode($child_node, true);
									// 削除されたセクションのSectionNameのModFlg
									self::add_mod_flg('SectionName', self::MOD_FLG_NONE, $new_node_child);
									$node->appendChild($new_node_child);
								}  
							}
						}
					}
					//add end LIXD-293 Phong VNIT-20151104
                    // Dataタグが１つ以上存在している場合のみ追加
					if ($data_flg) {
						$new_node = $this->doc->importNode($node, true);		
						
						// 削除されたセクションのSectionNameのModFlg
						//edit start LIXD-315 Phong VNIT-20151112
						if($is_parent){
							self::add_mod_flg('SectionName', self::MOD_FLG_DEL, $new_node);	
						}else{
							self::add_mod_flg('SectionName', self::MOD_FLG_NONE, $new_node);
						}//edit end LIXD-315 Phong VNIT-20151112
						
						$parnet->appendChild($new_node);
						
						$rtn = true;
					}
				}
			}
		}

		return $rtn;
	}
    
    private function import_del_data_tag(&$child)
    {
        // Dataタグの子要素を消す
        for ($j = $child->childNodes->length - 1; $j >= 0; $j--) {
            $data_child = $child->childNodes->item($j);
            if (strcmp($data_child->nodeName, 'DataId') == 0) {
				// データIDは残す
				continue;
			} else if (strcmp($data_child->nodeName, 'DataKbn') == 0) {
				// データ区分は残す
                continue;
            } else {
				// 上記以外を削除
				$child->removeChild($data_child);
			}
        }      
		// 削除された→ModFlgを3（削除）で登録
		$mod_flg = $this->old_doc->createElement('ModFlg', self::MOD_FLG_DEL);	
		$child->appendChild($mod_flg);								
    }

	/**
	 * 処理本体 (Relation用)
	 */
	private function main_relation()
	{
		// 削除・追加用
		$head = $this->doc->getElementsByTagName($this->TAG_RELA['tag_per']);
		$parnet = $head->item(0);

		// 両方の要素を取得
		$old_xml = $this->old_xpath->query(self::XPATH_RELA);
		$new_xml = $this->xpath->query(self::XPATH_RELA);

		if ($new_xml->length < $old_xml->length) {
			$loop_count = $new_xml->length;
		} else {
			$loop_count = $old_xml->length;
		}

		// 両方にある → dataタグ内の特定要素で比較
		// → すべてのデータタグが同じならば削除
		// → 一つでも違っているならば残す

		for ($i = 0; $i < $loop_count; $i++) {

			$old_data_list = $old_xml->item($i)->getElementsByTagName('Data');
			$new_data_list = $new_xml->item($i)->getElementsByTagName('Data');

			if ($old_data_list->length != $new_data_list->length) {
				// Dataタグの数が違っている時点で変更あり
				continue;
			}

			$diff = false;

			for ($j = 0; $j < $old_data_list->length; $j++) {

				$old_data = $old_data_list->item($j);
				$new_data = $new_data_list->item($j);

				foreach ($this->RELATION_CHK_TAG as $tag) {

					$old_node_value = $old_data->getElementsByTagName($tag)->item(0)->nodeValue;
					$new_node_value = $new_data->getElementsByTagName($tag)->item(0)->nodeValue;

					// 特定のタグの値が変更されているか
					if (strcmp($old_node_value, $new_node_value) != 0) {
						$diff = true;
						break;
					}
				}

				if ($diff) {
					break;
				}
			}

			if ($diff == false) {
				$parnet->removeChild($new_xml->item($i));
			}
		}
        
        // 差分があったかチェック
        $relation = $this->doc->getElementsByTagName($this->TAG_RELA['tag_main']);
        if ($relation->length <= 0) {
            // 差分なし
            return false;
        } else {
            return true;
        }
	}

	/**
	 * ModFlg属性を付加
	 */
	private function add_mod_flg($name, $flg, &$data_node) 
	{
		$target = $data_node->getElementsByTagName($name);
		if ($target->length > 0) {
			// 属性を追加
			$target->item(0)->setAttributeNode(new DOMAttr('ModFlg', $flg));			
		}
	}
	//add start LIXD-11 Phong VNIT-20150831
	private function dataTag_add_mod_flg($flg, &$data_node) 
	{		
		$data_node->setAttributeNode(new DOMAttr('ModFlg', $flg));			
	}//add end LIXD-11 Phong VNIT-20150831
	/**
	 * 旧XMLと新XMLの要素同士を比較する
	 */
	private function compare_data_list($old_tag, $new_tag, &$data_node)
	{
		// 全体の差分結果
		$diff_flg = false;
		
		if (empty($old_tag) == false) {
			// 旧データが存在するときのみ
			if (($new_tag['DataKbn'] == AKAGANE_SECTION_KBN_EXCEL) || ($new_tag['DataKbn'] == AKAGANE_SECTION_KBN_HEADER_EXCEL)) {
				// エクセル、ヘッダーフォーマットエクセル

				// ファイルがアップロードされていない場合、
				// ExcelDataは作られないので、ダミーを用意
				// Edit start - miyazaki U_SYS - 2014/11/18
				if (isset($old_tag['ExcelData']) == false) {
					$old_tag['ExcelData'] = '';
				} else {
					// ExcelDataがあるが、空⇒配列になっているため変換
					if (is_array($old_tag['ExcelData']) == true) {
						$old_tag['ExcelData'] = '';
					}
				}
				if (isset($new_tag['ExcelData']) == false) {
					$new_tag['ExcelData'] = '';
				} else {
					// ExcelDataがあるが、空⇒配列になっているため変換
					if (is_array($new_tag['ExcelData']) == true) {
						$new_tag['ExcelData'] = '';
					}
				}
				// Edit end - miyazaki U_SYS - 2014/11/18

				// 比較
				if (strcmp($old_tag['ExcelData'], $new_tag['ExcelData']) == 0) {
					$flg = self::MOD_FLG_NONE;
				} else {
					$flg = self::MOD_FLG_YES;
					$diff_flg = true;
				}

				// ModFlgを追加
				$this->add_mod_flg('ExcelData', $flg, $data_node);
				$this->add_mod_flg('FileName', $flg, $data_node);
				$this->add_mod_flg('OrgFileName', $flg, $data_node);

				// 共通の比較から外す
				unset($old_tag['ExcelData']);
				unset($old_tag['FileName']);
				unset($old_tag['OrgFileName']);
				unset($new_tag['ExcelData']);
				unset($new_tag['FileName']);
				unset($new_tag['OrgFileName']);

				// ヘッダーフォーマットエクセルの場合、ヘッダーの項目も同様に操作
				if ($new_tag['DataKbn'] == AKAGANE_SECTION_KBN_HEADER_EXCEL) {
					$this->add_mod_flg('HeaderFileName', $flg, $data_node);
					$this->add_mod_flg('OrgHeaderFileName', $flg, $data_node);

					unset($old_tag['HeaderFileName']);
					unset($old_tag['OrgHeaderFileName']);
					unset($new_tag['HeaderFileName']);
					unset($new_tag['OrgHeaderFileName']);
				}

			} else if ($new_tag['DataKbn'] == AKAGANE_SECTION_KBN_IMAGE || $new_tag['DataKbn'] == AKAGANE_SECTION_KBN_IMAGE_MASTER) { //Add LIXD-9 hungtn VNIT 20150626
				// 画像

				// 比較
				if (strcmp($old_tag['FileName'], $new_tag['FileName']) == 0) {
					$flg = self::MOD_FLG_NONE;
				} else {
					$flg = self::MOD_FLG_YES;
					$diff_flg = true;
				}

				// ModFlgを追加
				$this->add_mod_flg('FileName', $flg, $data_node);
				$this->add_mod_flg('OrgFileName', $flg, $data_node);

				// 共通の比較から外す
				unset($old_tag['FileName']);
				unset($old_tag['OrgFileName']);
				unset($new_tag['FileName']);
				unset($new_tag['OrgFileName']);

			}

			// チェックボックス
			$old_str = json_encode($old_tag['CheckBox']['SelectedValue']);
			$new_str = json_encode($new_tag['CheckBox']['SelectedValue']);

			// 比較
			if (strcmp($old_str, $new_str) == 0) {
				$flg = self::MOD_FLG_NONE;
			} else {
				$flg = self::MOD_FLG_YES;
				$diff_flg = true;
			}

			// ModFlgを追加
			$this->add_mod_flg('CheckBox', $flg, $data_node);

			// 共通の比較から外す
			unset($old_tag['CheckBox']);
			unset($new_tag['CheckBox']);
			
			// 表の幅
			if (($new_tag['DataKbn'] != AKAGANE_SECTION_KBN_EXCEL) && ($new_tag['DataKbn'] != AKAGANE_SECTION_KBN_HEADER_EXCEL)) {
				$this->add_mod_flg('TableType', self::MOD_FLG_NONE, $data_node);
				unset($old_tag['TableType']);
				unset($new_tag['TableType']);
			}
			//add start LIXD-11 Phong VNIT 20150831
			/*if($old_tag['@attributes']['DataIndent'] !== $new_tag['@attributes']['DataIndent']){
				self::dataTag_add_mod_flg( self::MOD_FLG_YES, $data_node);
				$diff_flg = true;
			}else{
				self::dataTag_add_mod_flg( self::MOD_FLG_NONE, $data_node);
			}
			unset($old_tag['@attributes']['DataIndent']);
			unset($new_tag['@attributes']['DataIndent']);*/
			//add end LIXD-11 Phong VNIT 20150831
		}
		
		// その他の項目の比較
		foreach ($new_tag as $key => $value) {
			// ModFlgを付けない項目
			//esit start  LIXD-11 Phong VNIT 20150831
			if ((strcmp($key, '@attributes') == 0)
			 || (strcmp($key, 'DataId') == 0)
			 || (strcmp($key, 'DataKbn') == 0)
			 || (strcmp($key, 'ModFlg') == 0)
			) {
				continue;
			}			
			/*if((strcmp($key, '@attributes') == 0)){
				if(isset($value['DataIndent']))
					self::dataTag_add_mod_flg( self::MOD_FLG_YES, $data_node);
				continue;
			}*/
			//edit end LIXD-11 Phong VNIT 20150831
			if (array_key_exists($key, $old_tag) === true) {
				// 比較
				if (strcmp($value, $old_tag[$key]) == 0) {
					$flg = self::MOD_FLG_NONE;
				} else {
					$flg = self::MOD_FLG_YES;
					$diff_flg = true;
				}
			} else {
				// 同じ項目がない
				$flg = self::MOD_FLG_YES;
				$diff_flg = true;				
			}
			
			// ModFlgを追加
			$this->add_mod_flg($key, $flg, $data_node);
		}
		
		return $diff_flg;
	}

	/**
	 * 旧XMLと新XMLの要素同士を比較する
	 */
	private function search_diff_tag($old_tag_list, $new_tag_list)
	{
		// データタイプがエクセルの場合は、ファイル名を比較しない
		if (($new_tag_list['DataKbn'] == AKAGANE_SECTION_KBN_EXCEL) || ($new_tag_list['DataKbn'] == AKAGANE_SECTION_KBN_HEADER_EXCEL)) {
			$new_tag_list['OrgFileName'] = null;
			$new_tag_list['FileName'] = null;
			$new_tag_list['OrgHeaderFileName'] = null;
			$new_tag_list['HeaderFileName'] = null;
		}
		if (($old_tag_list['DataKbn'] == AKAGANE_SECTION_KBN_EXCEL) || ($old_tag_list['DataKbn'] == AKAGANE_SECTION_KBN_HEADER_EXCEL)) {
			$old_tag_list['OrgFileName'] = null;
			$old_tag_list['FileName'] = null;
			$old_tag_list['OrgHeaderFileName'] = null;
			$old_tag_list['HeaderFileName'] = null;
		}	
		
		$old = json_encode($old_tag_list);
		$new = json_encode($new_tag_list);

		if (strcmp($old, $new) == 0) {
			// 変更なし
			return false;
		} else {
			// 変更有り
			return true;
		}

		/*
		$result = false;
		foreach ($new_tag_list as $key => $new_tag) {
			// 取得したkey値があるか調べる
			if (array_key_exists($key, $old_tag_list)) {
				// 旧XMLのタグを取得
				$old_tag = $old_tag_list[$key];

				// 配列か文字列が調べる
				if (is_array($new_tag)) {
					// ループ中の要素は配列
					ACWLog::write_file(self::debug_file_name, 'Array : ' . $key);
var_dump($new_tag);
var_dump($old_tag);

					// 配列が違っている場合→変更されたと判断
					$array = array_diff($new_tag, $old_tag);
					if (is_array($array) == false) {
						$result = true;
						break;
					}

					// 配列が同じならば掘り下げる
					if (self::search_diff_tag($old_tag, $new_tag) == true) {
						$result = true;
						break;
					}
				} else {
					// ループ中の要素は文字列
					ACWLog::write_file(self::debug_file_name, 'String : ' . $key);

					// 文字列が異なっている場合→変更されたと判断
					if (strcmp($new_tag, $old_tag) != 0) {
						$result = true;
						break;
					}
				}
			} else {
				// 新XMLから取得したkey値が旧XMLにない→変更されたと判断
				$result = true;
				break;
			}
		}
		*/

		return $result;
	}
	
	/**
	 * ReadXml_libのget_section_dataの拡張
	 * DataShareListを削除する
	 * Add - miyazaki U_SYS - 2014/11/12
	 */
	public function get_section_data($ctrl)
	{
		$list = $ctrl->get_section_data();
		
		$result = $list;
		
		foreach ($list as $s_key => $section) {
			foreach ($section['Data'] as $d_key => $data) {
				foreach ($data as $t_key => $tag) {
					if (strcmp($t_key, 'DataShareList') == 0) {
						unset($result[$s_key]['Data'][$d_key][$t_key]);
						break;
					}
				}
			}
		}

		return $result;
	}
	//Add start LIXD-11 TinVNIT - 9/25/2015
	public function get_child_section_data($ctrl, $section_path)
	{
		$list = $ctrl->get_child_section_data($section_path);
		$result = $list;
		foreach ($list as $s_key => $section) {
			foreach ($section['Data'] as $d_key => $data) {
				foreach ($data as $t_key => $tag) {
					if (strcmp($t_key, 'DataShareList') == 0) {
						unset($result[$s_key]['Data'][$d_key][$t_key]);
						break;
					}
				}
			}
		}
		return $result;
	}
    //Add end LIXD-11 TinVNIT - 9/25/2015
	/**
	 * ReadXml_libのget_free_section_dataの拡張
	 * 配列のkeyをget_section_dataと同様のものにする
	 */
	public function get_free_section_data($ctrl)
	{
		$list = $ctrl->get_free_section_data();

		$result = array();

		foreach ($list as $data) {
			if (array_key_exists('FreeItemId', $data) == true) {
				$result['free_' . $data['FreeItemId']] = $data;
			}
		}

		return $result;
	}
	
	/**
	 * ReadXml_libのget_section_dataを関連情報用に変更
	 */
	public function get_relation_section_data($ctrl)
	{
		$sec_arr = $ctrl->get_part('/XML/RelationInfo');
		
		$index = 0;
		$result = array();
		foreach ($sec_arr as $sec_xml) {
			$key = 'relation_' . $index;
			$ctrl->format_section($sec_xml);
			$result[$key] = $sec_xml;
			
			$index++;
		}
		ACWLog::debug_var('READRELATIONXML_FORMAT', $result);

		return $result;
	}
	
	/**
	 * 特定のタグを消す
	 * Add - miyazaki U_SYS - 2014/11/12
	 */
	public function delete_ex_tag()
	{
		$res_list = $this->xpath->query('//DataShareList');
		foreach ($res_list as $res) {
			$node = $res->parentNode;
			$node->removeChild($res);
		}
	}	
}