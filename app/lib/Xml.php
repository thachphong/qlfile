<?php
/**
 * XML操作ライブラリ
*/
class Xml_lib
{
	/**
	 * XMLから配列変換
	 * @param string $filename 読込み元のファイルフルパス
	*/
	public function to_array($filename, $get_disp_name = false)// edit LIXD-118 Phong VNIT-20150826
	{
		try {
			$xml = simplexml_load_file($filename);
			$xml_lib = new Xml_lib();
            /*$xml = $xml_lib->append_pre_suf_xml($xml, $get_pre_suf);//remove LIXD-118 Tin-VNIT-20150825*/
            $xml = $xml_lib->append_disp_name_xml($xml,$get_disp_name); // add LIXD-118 Phong VNIT-20150826
            $return = json_decode(json_encode($xml), true);
            //Add Start NBKD-73 hungtn VNIT 2014/12/10
            /*if($get_pre_suf === true) {
                foreach ($return['SeriesHead']['SeriesHeadSection']['SectionInfo'] as &$item) {
                    if(isset($item['PreName'])) {
                        if(count($item['PreName']) == 0) {
                            $item['PreName'] = "";
                        }
                    }
                    if(isset($item['SufName'])) {
                        if(count($item['SufName']) == 0) {
                            $item['SufName'] = "";
                        }
                    }
                }
            }//remove LIXD-118 Tin-VNIT-20150825*/
            //Add End NBKD-73 hungtn VNIT 2014/12/10
            return $return;
		} catch (Exception $e) {
			ACWLog::write_file('XMLERROR', $e->getMessage());
		}
		return null;
	}
        
        //Add Start - NBKD-37,79 - Trung VNIT - 2014/11/06
        public function to_array_filter($filename, $section_filter, $get_disp_name = false)// edit LIXD-118 Phong VNIT-20150826
	{
		try {
			$xml = simplexml_load_file($filename);
			$itemexp_db = new ItemExport_model();								//add LIXD-11 Phong VNIT-20151021
            $section_filter = $itemexp_db->add_parent_section($section_filter);	//add LIXD-11 Phong VNIT-20151021
			$xml_lib = new Xml_lib();
            /*$xml = $xml_lib->append_pre_suf_xml($xml, $get_pre_suf);//remove LIXD-118 Tin-VNIT-20150825*/
			$xml = $xml_lib->append_disp_name_xml($xml,$get_disp_name); // add LIXD-118 Phong VNIT-20150826
			$xml_data = json_decode(json_encode($xml), true);
                        $arr_section_info = $xml_data['SeriesHead']['SeriesHeadSection']['SectionInfo'];
                        for($i=0;$i<count($arr_section_info);$i++){
                            $unset = 'NG';
                            for($z=0;$z<count($section_filter);$z++){
                                if($arr_section_info[$i]['SectionId'] == $section_filter[$z]['t_ctg_section_id']){
                                    $unset = 'OK';
                                }
                            }
                            if($unset == 'NG'){
                                unset($xml_data['SeriesHead']['SeriesHeadSection']['SectionInfo'][$i]);
                            }
                        }
                        //Add Start - NBKD-857 - TrungVNIT - 2014/12/16
                        if(isset($xml_data['Free'])){
                            unset($xml_data['Free']);
                        }
                        //Add End - NBKD-857 - TrungVNIT - 2014/12/16
                        return $xml_data;
                        
		} catch (Exception $e) {
			ACWLog::write_file('XML_FILTER_ERROR', $e->getMessage());
		}
		return null;
	}
        //Add End - NBKD-37,79 - Trung VNIT - 2014/11/06
        
        // Add Start NBKD-73 hungtn VNIT 2014/12/10
        /*private function append_pre_suf_xml($xml, $get_pre_suf){
            if($get_pre_suf === true){
                $count_section_info = count($xml->SeriesHead->SeriesHeadSection->SectionInfo);
                for($i=0;$i<$count_section_info;$i++)
                {
                    if(isset($xml->SeriesHead->SeriesHeadSection->SectionInfo[$i]->SectionName->attributes()->pre))
                    {
                        $pre = $xml->SeriesHead->SeriesHeadSection->SectionInfo[$i]->SectionName->attributes()->pre;
                        $xml->SeriesHead->SeriesHeadSection->SectionInfo[$i]->PreName = $pre;
                    }
                    if(isset($xml->SeriesHead->SeriesHeadSection->SectionInfo[$i]->SectionName->attributes()->suf))
                    {
                        $suf = $xml->SeriesHead->SeriesHeadSection->SectionInfo[$i]->SectionName->attributes()->suf;
                        $xml->SeriesHead->SeriesHeadSection->SectionInfo[$i]->SufName = $suf;
                    }
                }
            }
            return $xml;
        }//remove LIXD-118 Tin-VNIT-20150825*/
        // Add End NBKD-73 hungtn VNIT 2014/12/10
		// Add Start LIXD-118 VNIT 20150826
        public function append_disp_name_xml($xml, $get_disp_name){
            if($get_disp_name === true){
                $count_section_info = count($xml->SeriesHead->SeriesHeadSection->SectionInfo);
                for($i=0;$i<$count_section_info;$i++)
                {
                    if(isset($xml->SeriesHead->SeriesHeadSection->SectionInfo[$i]->SectionName->attributes()->disp))
                    {
                        $disp_name = $xml->SeriesHead->SeriesHeadSection->SectionInfo[$i]->SectionName->attributes()->disp;
                        $xml->SeriesHead->SeriesHeadSection->SectionInfo[$i]->disp_name = $disp_name;
						//Add start LIXD-11 TinVNIT - 9/25/2015
                        if(isset($xml->SeriesHead->SeriesHeadSection->SectionInfo[$i]->ChildSection->SectionInfo))
                        {
							$count_child_section_info = count($xml->SeriesHead->SeriesHeadSection->SectionInfo[$i]->ChildSection->SectionInfo);
							for($j=0;$j<$count_child_section_info;$j++)
							{
								$disp_name_child = $xml->SeriesHead->SeriesHeadSection->SectionInfo[$i]->ChildSection->SectionInfo[$j]->SectionName->attributes()->disp;
                        		$xml->SeriesHead->SeriesHeadSection->SectionInfo[$i]->ChildSection->SectionInfo[$j]->disp_name = $disp_name_child;
							}
						}
						//Add end LIXD-11 TinVNIT - 9/25/2015
                    }
                }
                
                //Add Start - LIXD-294 - TrungVNIT - 2015/05/11
                $count_free_section = count($xml->Free->FreeSection);
                for ($i = 0; $i < $count_free_section; $i++) {
                    if (isset($xml->Free->FreeSection[$i]->SectionName) && isset($xml->Free->FreeSection[$i]->SectionName->attributes()->disp)) {
                        $disp_name_free = $xml->Free->FreeSection[$i]->SectionName->attributes()->disp;
                        $xml->Free->FreeSection[$i]->disp_name = $disp_name_free;
                    }
                }
                //Add End - LIXD-294 - TrungVNIT - 2015/05/11
            }
            return $xml;
        }
        //add end LIXD-118 Phong-VNIT-20150826
        private $_xml = null;

	/**
	 * 連想配列でXML保存
	 * @param array $arr 連想配列
	 * @param string $filename ファイル名
	 */
	public function save($arr, $filename)
	{
		$this->_xml = new DOMDocument('1.0', 'utf-8');
		$element = $this->_xml->createElement('XML');
		$this->_xml->appendChild($element);
		
		$this->append_from_array($element, $arr);
		
		$this->_xml->formatOutput = true;
		file_put_contents($filename, $this->_xml->saveXml());
	}

	/**
	 * 配列の内容をエレメントに詰め込んで返却
	 * @param DOMElement $element
	 * @param array $arr 配列
	 */
	private function append_from_array(&$element, $arr) {

		if ($this->is_hash_lite($arr)) {
			foreach ($arr as $k => $v) {
				if (strcmp($k, '@attributes') == 0) {
					foreach ($v as $a_key => $attr) {
						// 属性付加
						$element->setAttributeNode(new DOMAttr($a_key, $attr));
					}
				} else {
					$node = $this->_xml->createElement($k);
					$element->appendChild($node);		
					
					if (is_array($v)) {
						$this->append_from_array($node, $v);
					} else {
						$text = $this->_xml->createTextNode($v);
						$node->appendChild($text);
					}
				}
			}
		} else {
			$is_first = true;
			foreach ($arr as $v) {
				$node = $element;
				if ($is_first) {
					$is_first = false;
				} else {
					$node = $element->cloneNode();
					$element->parentNode->appendChild($node);
				}
				if (is_array($v)) {
					$this->append_from_array($node, $v);
				} else {
					$text = $this->_xml->createTextNode($v);
					$node->appendChild($text);
				}
			}
		}
	}

	/**
	 * 連想配列かチェック
	 * @param bool $array true: 連想配列、false: 普通の配列
	 */
	private function is_hash_lite(&$array) {
		reset($array);
		list($k) = each($array);
		return $k !== 0;
	}
	
	//Add Start NBKD-46 hungtn VNIT 2014/12/09
	/**
	 * function save_series_fomat
	 * @param array $arr 連想配列
	 * @param string $filename ファイル名
	 */
	public function save_series_format($arr, $filename)
	{
	    $this->_xml = new DOMDocument('1.0', 'utf-8');
	    $element = $this->_xml->createElement('XML');
	    $this->_xml->appendChild($element);
	
	    $this->append_from_array_format($element, $arr);
	
	    $this->_xml->formatOutput = true;
// Add Start NBKD-73 hungtn VNIT 2014/12/10
	    $this->_xml->preserveWhiteSpace = false;
	    /*if($pre = $this->_xml->getElementsByTagName('PreName')) {
	        foreach ($pre as $pre_child) {
	            $pre_child->parentNode->removeChild($pre_child);
	        }
	    }
	    if($suf = $this->_xml->getElementsByTagName('SufName')) {
	        foreach ($suf as $suf_child) {
	            $suf_child->parentNode->removeChild($suf_child);
	        }
	    }//remove LIXD-118 Tin-VNIT-20150825*/
// Add End NBKD-73 hungtn VNIT 2014/12/10
	    file_put_contents($filename, $this->_xml->saveXml());
	}
	
	private function append_from_array_format(&$element, &$arr, $disp_name = null,  $status = false) {//edit LIXD-118 Phong VNIT 20150831
	
	    if($status == true) {
	        $element->setAttributeNode(new DOMAttr('disp', $disp_name));//edit LIXD-118 Phong VNIT 20150831
	        //$element->setAttributeNode(new DOMAttr('suf', $suf));
	    } else 
	    if ($this->is_hash_lite($arr)) {
	        foreach ($arr as $k => $v) {

	            if (strcmp($k, '@attributes') == 0) {
	                foreach ($v as $a_key => $attr) {
	                    // 属性付加
	                    $element->setAttributeNode(new DOMAttr($a_key, $attr));
	                }
	            } else {
                    if($k == 'SectionName') {
                        $node = $this->_xml->createElement($k);
                        $text = $this->_xml->createTextNode($v);
                        $node->appendChild($text);
                        $element->appendChild($node);
                        $status_tmp = 0;
						//add start LIXD-118 Phong VNIT 20150831
                        if(isset($arr['disp_name'])) {
                            if(!empty($arr['disp_name'])) {
                                $disp_name = $arr['disp_name'];
                            }
                            $status_tmp = 1;
                            unset($arr['disp_name']);
                        }//add end LIXD-118 Phong VNIT 20150831
                        /*if(isset($arr['PreName'])) {
                            if(!empty($arr['PreName'])) {
                                $pre = $arr['PreName'];
                            }
                            $status_tmp = 1;
                            unset($arr['PreName']);
                        }

                        if(isset($arr['SufName'])) {
                            if(!empty($arr['SufName'])) {
                                $suf = $arr['SufName'];
                            }
                            $status_tmp = 1;
                            unset($arr['SufName']);
                        }//remove LIXD-118 Tin-VNIT-20150825*/
                        
                        if($status_tmp == 1) {
                            $this->append_from_array_format($node, $v, $disp_name,  true);//edit LIXD-118 Phong VNIT 20150831
                        }

                    } else {
                        $node = $this->_xml->createElement($k);
                        $element->appendChild($node);
                        
                        if (is_array($v)) {
                            $this->append_from_array_format($node, $v);
                        } else {
                            $text = $this->_xml->createTextNode($v);
                            $node->appendChild($text);
                        }
                    }

	            }
	        }
	    } else {
	        $is_first = true;
	        foreach ($arr as $v) {
	            $node = $element;
	            if ($is_first) {
	                $is_first = false;
	            } else {
	                $node = $element->cloneNode();
	                $element->parentNode->appendChild($node);
	            }
	            if (is_array($v)) {
	                $this->append_from_array_format($node, $v);
	            } else {
	                $text = $this->_xml->createTextNode($v);
	                $node->appendChild($text);
	            }
	        }
	    }
	}
	//Add End NBKD-46 hungtn VNIT 2014/12/09
}
/* ファイルの終わり */