<?php
/**
* ImportTags
* @author VNIT
* @since 2015
*/
class ImportTags_lib extends ACWModel{

    public function get_data_file($path, $file_lib) {
        
        $list_file = $file_lib->FileList($path);
        $tmp_result = array();
        if(count($list_file) > 0) {
            foreach($list_file as $file) {
                if(!empty($file)) {
                    $tmp_file_name = explode('_', $file);
                    if(is_numeric($tmp_file_name[0])) {
                        $tmp['FileName'] = $file;
                        $tmp['tags'] = $tmp_file_name[0];
                        $tmp['OrgFileName'] = substr($file, strlen($tmp['tags']) + 1);
                        $tmp_result[] = $tmp;
                    }
                }
            }
        }
        return $tmp_result;
    }
    
    public function upload_data_tags(&$tags, $src_path, $dst_path, $file_lib, $t_series_head_id, $t_series_mei_id, $section_id, $t_multi_data_id, $yoyaku_flg, $data_id) {//Edit LIXD-422 hungtn VNIT 20150111
        
        if(count($tags) > 0) {
            
            $model = new ImportTags_lib();
            foreach($tags as &$tag) {
                
                if(!empty($tag['FileName'])) {
                   $ext = pathinfo($tag['FileName'], PATHINFO_EXTENSION);
                   $tag['SectionId'] = $section_id;
                   if($ext != 'xlsx' && $ext != 'xls' ) {
                       $tag['FileName'] = $model->upload_img_series($tag['FileName'], $src_path, $dst_path, $file_lib);
                       $tag['Link'] = ImportTags_lib::create_image_link($tag, $t_series_head_id, $t_series_mei_id, $yoyaku_flg);
                   } else if($ext == 'xlsx' || $ext == 'xls' ){
                       $tag['FileName'] = $model->upload_excel_series($tag['FileName'], $src_path, $dst_path, $file_lib, $t_multi_data_id, $section_id, $tag['index'], $t_series_mei_id, $section_id, $data_id);//Edit LIXD-422 hungtn VNIT 20150111
                   }
                }
            }
        }
        
    }

    public function upload_img_series($file_name, $src_path, $dst_path, $file_lib) {
        
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $uid = uniqid('UID', true);
        $param['FileName']    = $uid . '.' . $ext;
        
        $dsr_full_path   = ''; // コピー先のフルパス
        $dsr_full_path_s = ''; // コピー先のフルパス、サムネ
        $dsr_full_path   = $dst_path . $param['FileName'];
        $dsr_full_path_s = $dst_path . $uid . '_s.' . $ext;
        $file_lib->CopyFile($src_path.$file_name, $dsr_full_path);
        
        $image = new Image_lib();
        $thumb_res = $image->thumb($dsr_full_path, $dsr_full_path_s, THUMBNAIL_IMAGE_WIDTH, THUMBNAIL_IMAGE_HEIGHT, THUMBNAIL_IMAGE_FORMATT);
        unset($image);
        if($thumb_res == FALSE) {
            ACWLog::debug_var('LIXD-10', 'thumb file error');
        }
        return  $param['FileName'];
    }
    
    public function upload_excel_series($file_name, $src_path, $dst_path, $file_lib, $t_multi_data_id, $section_id, $index, $t_series_mei_id, $section_id, $data_id) {//Edit LIXD-422 hungtn VNIT 20150111
        
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        // 一意のファイル名を設定
        
        $param['FileName']    = 'input_row_excel_' . $section_id .'_'. $t_multi_data_id .'_'. $index .'.'. $ext;
        if($file_lib->FolderExists($dst_path .'excel/') == FALSE) {
            $file_lib->CreateFolder($dst_path .'excel/');
        }
        $excel_file = $dst_path .'excel/'. $param['FileName']; //Edit Start LIXD-422 hungtn VNIT 20150111
        $file_lib->CopyFile($src_path.$file_name, $excel_file);
        // Excel ファイルから html 生成
        //$excel = new Excel_lib();
        //$excel->to_html( $dst_path . $param['FileName'], $dst_path . $uid . '.html' );
        
        // Extract excel file
        //extract_txt_file($excel_file,$mei_id,$section_id,$data_id,$org_excel_name)
        $itemcode = new Itemcode_model();
        $extract_resut = $itemcode->extract_txt_file($excel_file, $t_series_mei_id, $section_id, $data_id, $file_name);
        
        if($extract_resut !== FALSE) {
            if($itemcode->insert_item_code($extract_resut) == false) {
                ACWLog::debug_var("LIXD-422", "insert item code fail: ".$excel_file);
            }
        } else {
            ACWLog::debug_var("LIXD-422", "Extract txt fail: ".$excel_file);
        }
        //Edit End LIXD-422 hungtn VNIT 20150111
        return  $param['FileName'];
    }
    /**
    * replace_tags_import
    * @param integer $t_series_head_id
    * @param integer $t_series_mei_id
    * @param integer $m_lang_id
    * @param array $excel_change
    * @param text $src_path
    * @param text $dst_path
    * 
    * @return
    */
    public static function replace_tags_import($t_series_head_id, $t_series_mei_id, $m_lang_id, $excel_change, $src_path, $dst_path, $yoyaku_flg = '') {
        try {
            /**
            * 
            * @todo: 
            * check isset excel folder and then:
            * -> get tags from excel
            * -> get tags from excel folder
            *    -> megere tags
            *         ->upload data of tags
            *             -> update new data for excel html
            *             -> save data to database
            * 
            */
            
            $model = new ImportTags_lib();
            $excel_change = array_filter($excel_change);
            $file_lib = new File_lib();
            
            $seriesModel = new Series_model();

            foreach($excel_change as $excel_data) {
                if(!empty($excel_data['OrgFileName'])) {
                    //Add Start LIXD-393 hungtn VNIT 20151216
                    if(!is_numeric($excel_data['SectionId'])) {
                        $dst_path = $dst_path.$excel_data['SectionId']."/";
                        if($file_lib->FolderExists($dst_path) == FALSE) {
                            $file_lib->CreateFolder($dst_path);
                        }
                    }
                    //Add End LIXD-393 hungtn VNIT 20151216
                    $excel_path = '';
                    $excel_uploaded_path = '';
                    //if (is_numeric($excel_data['SectionId'])) {
                    $excel_path = $dst_path . $excel_data['FileName'];
                    $excel_uploaded_path = $dst_path . Itemfile_model::replace_name_uploaded($excel_data['FileName']);
                    /*} else {
                        $excel_path = $dst_path .$excel_data['SectionId'] . '/' . $excel_data['FileName'];
                        $excel_uploaded_path = $dst_path .$excel_data['SectionId'] . '/' . Itemfile_model::replace_name_uploaded($excel_data['FileName']);
                    }*/
                    
                    if ($file_lib->FileExists($excel_path) == FALSE) {
                        ACWLog::debug_var('LIXD-10', 'Excel file not exits: ' . $excel_path);
                        continue;
                    }
                    
                    //Add Start LIXD-422 hungtn VNIT 20150111
                    $itemcode = new Itemcode_model();
                    $extract_resut = $itemcode->extract_txt_file($excel_path, $t_series_mei_id, $excel_data['SectionId'], $excel_data['DataId'], $excel_data['OrgFileName']);
                    if($extract_resut !== FALSE) {
                        if($itemcode->insert_item_code($extract_resut) == false) {
                            ACWLog::debug_var("LIXD-422", "insert item code fail: ".$excel_path);
                        }
                    } else {
                        ACWLog::debug_var("LIXD-422", "Extract txt fail: ".$excel_path);
                    }
                    //Add End LIXD-422 hungtn VNIT 20150111
                    
                    $excel_path_dir_sheet1 = Itemfile_model::Get_Excel_NewName($excel_path); // eidt LIXD-287 Phong VNIT-20151105
                    $sheet1_flg = 0;
                    if($file_lib->FileExists($excel_path_dir_sheet1)) {
                        if ($file_lib->CopyFile($excel_path_dir_sheet1, $excel_uploaded_path) == FALSE) {
                            ACWLog::debug_var('LIXD-10', 'Cannot copy file Excel Uploaded: ' . $excel_path);
                        }
                        $sheet1_flg = 1;
                    } else {
                        if ($file_lib->CopyFile($excel_path, $excel_uploaded_path) == FALSE) {
                            ACWLog::debug_var('LIXD-10', 'Cannot copy file Excel Uploaded: ' . $excel_path);
                        }
                    }

                    $folder = pathinfo($excel_data['OrgFileName'], PATHINFO_FILENAME);
                    
                    if($file_lib->FolderExists($src_path.$folder)) {
						//Edit start LIXD-284 Tin VNIT 10/29/2015
	                    try {
				            $excel = new ImportExport_lib();
                            if($sheet1_flg == 1) {
                                if ($excel->load($excel_path_dir_sheet1) === false) {
    				                ACWLog::debug_var('LIXD-10', 'Can not open excel file: '.$excel_path_dir_sheet1);
    				                continue;//Edit LIXD-361 hungtn VNIT 20151119
    				            }
                            } else {
                                $excel_path_new =  Itemfile_model::SaveAsExcelFile($excel_path); //Add LIXD-287 hungtn VNIT 20151104
                                if ($excel->load($excel_path_new) === false) {
    				                ACWLog::debug_var('LIXD-10', 'Can not open excel file: '.$excel_path_new);
    				                continue;//Edit LIXD-361 hungtn VNIT 20151119
    				            }
                            }
                            
                            
				            
				        } catch (Exception $exc) {
				            ACWLog::debug_var('LIXD-10', $exc->getMessage());
				            continue;//Edit LIXD-361 hungtn VNIT 20151119
				        }
                        $tags_data = SeriesFile_lib::get_tags_excel($excel_path, NULL, $excel);
                        $excel->free();
						//Edit end LIXD-284 Tin VNIT 10/29/2015
                        $tags_data = $tags_data['tags'];
                        
                        //Add Start LIXD-334 hungtn VNIT 20151112
                        if(count($tags_data) == 0) {
                            continue;//Edit LIXD-361 hungtn VNIT 20151119
                        }
                        //Add Start LIXD-334 hungtn VNIT 20151112
                        //sort($tags_data, SORT_STRING);
                        //asort($tags_data, 2); // Add LIXD-10 hungtn VNIT 20150918
                        ImportTags_lib::tag_sort_advance($tags_data);// Add LIXD-10 hungtn VNIT 20150918
                        
                        $file_data = $model->get_data_file($src_path.$folder, $file_lib);
                        
                        $arr_mege_tag = array();
                        $i = 0;
                        $arr_index = array();
                        foreach($tags_data as $value) {
                            $i++;
                            $arr_tags = array();
                            foreach($file_data as $data) {
                                if(($i) == intval($data['tags'])) {
                                    $data['index'] = $data['tags'];
                                    $data['tags'] = $value;
                                    $arr_tags = $data;
                                    break;
                                }
                            }
                            
                            if(count($arr_tags) == 0) {
                                $arr_tags['tags'] = $value;
                            }
                            $arr_mege_tag[] = $arr_tags;
                            $arr_index[] = $value;
                        }#end foreach
                        
                        // Upload image, excel tags
                        $model->upload_data_tags($arr_mege_tag, $src_path.$folder.'/', $dst_path, $file_lib, $t_series_head_id, $t_series_mei_id, $excel_data['SectionId'], $excel_data['t_multi_data_id'], $yoyaku_flg, $excel_data['DataId']);//Edit LIXD-422 hungtn VNIT 20150111

                        //Update new data for excel html
                        if(count($arr_mege_tag) > 0) {

                            $arr_tmp_index = array();
                            foreach($arr_index as $key => $val) {
                                foreach($arr_mege_tag as $item) {
                                    if($val == $item['tags']) {
                                        $arr_tmp_index[] = $item;
                                        break;
                                    }
                                }
                            }
                            
                            if($sheet1_flg == 0) {
                                Itemfile_model::SaveAsExcelFile($excel_uploaded_path, TRUE); //Add LIXD-287 hungtn VNIT 20151104
                            }
                            
			    			//add start LIXD-287 Phong VNIT-20151105
                            $imp_execl = new ImportExport_lib();
                            $imp_execl->load($excel_uploaded_path);
                            //add end LIXD-287 Phong VNIT-20151105
                            $arr_mege_tag = $arr_tmp_index;
                            foreach($arr_mege_tag as $tags){
                                $ext = pathinfo($tags['FileName'], PATHINFO_EXTENSION);
                                if ($ext == 'xlsx' || $ext == 'xls') {
                                    $excel_child_path = $dst_path . 'excel/'.$tags['FileName'];
                                    if ($file_lib->FileExists($excel_child_path) == FALSE) {
                                        ACWLog::debug_var('LIXD-10', 'Excel file not exits: ' . $excel_child_path);
                                    } else {
                                        $excel_child_path =  Itemfile_model::SaveAsExcelFile($excel_child_path); //Add LIXD-287 hungtn VNIT 20151104
                                        $excel_add = new ImportExport_lib();//Edit Start LIXD-321 hungtn VNIT 20151111
                                        $excel_add->load($excel_child_path);
                                        excelreplace_model::join_excel($imp_execl, $excel_add,$tags['tags']);//edit LIXD-287 Phong VNIT-20151105  //Edit  LIXD-287 hungtn VNIT 20151106
                                        $excel_add->free();
                                        unset($excel_add); //Edit End LIXD-321 hungtn VNIT 20151111
                                    }
                                }

                            }#end foreach $arr_mege_tag
                            
                            excelreplace_model::replace_cell_child_special_to_empty($imp_execl); // Add LIXD-10 hungtn VNIT 20150930 //Edit  LIXD-287 hungtn VNIT 20151106

                            $html_uploaded = Itemfile_model::replace_name_uploaded($excel_data['FileName'], 'html');

                            //if (is_numeric($excel_data['SectionId'])) {//Edit LIXD-393 hungtn VNIT 20151216
                                $html_uploaded_path = $dst_path . $html_uploaded;
                            /*} else {
                                $html_uploaded_path = $dst_path . $excel_data['SectionId'] . '/' . $html_uploaded;
                            }*/

                            if ($imp_execl->to_html($excel_uploaded_path, $html_uploaded_path, TRUE) == false) {//edit LIXD-287 Phong VNIT-20151105 //Edit LIXD-332 hungtn VNIT 20151113
                                ACWLog::debug_var('LIXD-10', 'Cannot convert Excel to HTML: ' . $excel_uploaded_path);
                            }
                            
                            foreach($arr_mege_tag as $tags){
                                $ext = pathinfo($tags['FileName'], PATHINFO_EXTENSION);
                                if ($ext != 'xlsx' || $ext != 'xls') {
                                    HTMLDOMParser_lib::import_replace_image_html($html_uploaded_path, $tags);
                                }

                            }#end foreach $arr_mege_tag
                                
                            self::merge_tag_excel($imp_execl, $arr_mege_tag);//Add - LIXD-316 - TrungVNIT - 2015/05/11
                            SeriesFile_lib::restore_tags_child_excel($imp_execl);// edit LIXD-287 Phong VNIT-20151105
                            SeriesFile_lib::restore_tags_child_html($html_uploaded_path);
                            
                            $imp_execl->save($excel_uploaded_path);
                            $imp_execl->free();
                            
                            unset($imp_execl);
                            //Edit End  LIXD-287 hungtn VNIT 20151106
                            //Delete data old
                            $seriesModel->import_delete_tags($m_lang_id, $t_series_head_id, $excel_data['SectionId'], $t_series_mei_id, $excel_data['t_multi_data_id'], $dst_path, $yoyaku_flg);
                            
                            //save to dabase
                            $user_info = ACWSession::get('user_info');
                            $m_user_id = $user_info['m_user_id'];
                            foreach ($arr_mege_tag as $value) {
                                $data_insert = array(
                                    't_multi_data_id' => $excel_data['t_multi_data_id'],
                                    'm_lang_id' => $m_lang_id,
                                    't_series_head_id' => $t_series_head_id,
                                    't_series_mei_id' => $t_series_mei_id,
                                    't_ctg_section_id' => isset($excel_data['SectionId']) ? $excel_data['SectionId'] : '',
                                    'tag_name' => $value['tags'],
                                    'image_name' => isset($value['OrgFileName']) ? $value['OrgFileName'] : '',
                                    'image_tmp_file' => isset($value['FileName']) ? $value['FileName'] : '',
                                    'free_item_id' => isset($excel_data['SectionId']) ? $excel_data['SectionId'] : '',
                                    'add_user_id' => $m_user_id,
                                    'upd_user_id' => $m_user_id,
                                    't_select_multi_data_id' => 1,
                                );

                                $seriesModel->import_insert_tags($data_insert, $yoyaku_flg);
                            }
                        }#end if count $arr_mege_tag

                    }
                }
            }
            
        } catch (Exception $exc) {
            ACWLog::debug_var('LIXD-10', $exc->getMessage());
        }
    }
    
    //Add Start - LIXD-316 - TrungVNIT - 2015/05/11
    protected static function merge_tag_excel(&$excel, $arr_mege_tag) {//edit start LIXD-287 Phong VNIT-20151105 //Edit  LIXD-287 hungtn VNIT 20151106
        try {
/*        	if($excel== null){ //Edit  LIXD-287 hungtn VNIT 20151106
				$excel = new ImportExport_lib();	
			}//edit end LIXD-287 Phong VNIT-20151105            
            if ($excel->load($excel_uploaded_path) === false) {
                ACWLog::debug_var('LIXD-10', 'Can not open excel file: '.$excel_uploaded_path);
                return FALSE;
            }*/

            $total_column = $excel->get_total_column();
            $total_row = $excel->get_total_row();
            for ($i = 0; $i < $total_column; $i++) {
                $col = $i;
                for ($r = 0; $r < $total_row; $r++) {
                    $row = $r + 1;
                    foreach ($arr_mege_tag as $value) {
                        $tags = isset($value['tags']) ? $value['tags'] : '';
                        $file_name = isset($value['FileName']) ? $value['FileName'] : '';
                        if($tags != '' && $file_name != null){
                            $repl = '|||'.$file_name.'|||';
                            $value_no = trim($excel->get_value_no($col, $row));
                            if($value_no != '' && strpos($value_no, '|||') === false){
                                $cel_data = str_replace($tags, $repl, $value_no);
                                $excel->set_value_no($col, $row, $cel_data); 	
                            }
                        }
                    }
                }
            }
            //$excel->save($excel_uploaded_path); //Edit  LIXD-287 hungtn VNIT 20151106
            return TRUE;
        } catch (Exception $exc) {
            ACWLog::debug_var('LIXD-10', $exc);
            return FALSE;
        }
    }
    //Add End - LIXD-316 - TrungVNIT - 2015/05/11

    protected static function create_image_link($value, $t_series_head_id, $t_series_mei_id, $yoyaku_flg = '') {
        try {
            $section_id = $value['SectionId'];
            $is_free = FALSE;

            $filename = $value['FileName'];
            if ($yoyaku_flg == '') {
                //Edit Start LIXD-393 hungtn VNIT 20151216
                if(is_numeric($section_id)) {
                    $v = 'view';
                } else {
                    $v = 'viewfree';
                }
                
            } else {
                $v = 'yoyakuview';
            }
            
            if(is_numeric($section_id)) {
                $path = ACW_BASE_URL . 'itemfile/' . $v . '/' . $filename . '/' . $t_series_head_id . '/' . $t_series_mei_id . '/s';
            } else {
                //http://192.168.1.109/lixil_dev/itemfile/viewfree/56714159ce4dc/56714cee6381f831359274.png/1284/2993/s
                $path = ACW_BASE_URL . 'itemfile/' . $v . '/' .$section_id."/". $filename . '/' . $t_series_head_id . '/' . $t_series_mei_id . '/s';
            }
            
            //Edit End LIXD-393 hungtn VNIT 20151216
            return $path;
        } catch (Exception $exc) {
            ACWLog::debug_var('LIXD-10', $exc->getMessage());
        }
    }

    // Add Start LIXD-10 hungtn VNIT 20150914
    public function get_data_excelchange($data_new) {
        $tmp_data = array();
        if(count($data_new) > 0) {
            foreach($data_new as  $new) {
                
                $count_data = count($new['Data']);
                for ($i = 0; $i < $count_data; $i ++ ) {
                    $tmp_item = array();
                    if($new['Data'][$i]['DataKbn'] == AKAGANE_SECTION_KBN_EXCEL && $new['Data'][$i]['OrgFileName'] != '') {
                       $tmp_item['SectionId'] = $new['SectionId'];
                       $tmp_item['DataId'] = $new['Data'][$i]['DataId']; //Edit LIXD-422 hungtn VNIT 20150111
                       $tmp_item['OrgFileName'] = $new['Data'][$i]['OrgFileName'];
                       $tmp_item['FileName'] = $new['Data'][$i]['FileName'];
                       $tmp_item['t_multi_data_id'] = $i + 1;
                       $tmp_data[] = $tmp_item;
                    }
                } #end foreach
                
                //Add Start LIXD-11 hungtn VNIT 20151006
                if(isset($new['ChildSection'])) {
                    $xmlfnc = new ImportExport_lib();
                    $count_child = count($new['ChildSection']['SectionInfo']);
                    //$section = $new['ChildSection']['SectionInfo'];
                    if(isset($new['ChildSection']['SectionInfo']['SectionId']) == FALSE)
                    {
                        foreach ($new['ChildSection']['SectionInfo'] as $section) {
                            $datalist = $xmlfnc->xml_get_array($section,'Data');
                            foreach($datalist as $i=>$data_child_info)
                            {
                                $tmp_item = array();
                                if($data_child_info['DataKbn'] == AKAGANE_SECTION_KBN_EXCEL && $data_child_info['OrgFileName'] != '') {
                                   $tmp_item['SectionId'] = $section['SectionId'];
                                   $tmp_item['DataId'] = $data_child_info['DataId'];//Edit LIXD-422 hungtn VNIT 20150111
                                   $tmp_item['OrgFileName'] = $data_child_info['OrgFileName'];
                                   $tmp_item['FileName'] = $data_child_info['FileName'];
                                   $tmp_item['t_multi_data_id'] = $i + 1;
                                   $tmp_data[] = $tmp_item;
                                }
                            }
                        }
                    }
                    else
                    {
                        $section = $new['ChildSection']['SectionInfo'];
                        $datalist = $xmlfnc->xml_get_array($section,'Data');
                        foreach($datalist as $i=>$data_child_info)
                        {
                            $tmp_item = array();
                            if($data_child_info['DataKbn'] == AKAGANE_SECTION_KBN_EXCEL && $data_child_info['OrgFileName'] != '') {
                               $tmp_item['SectionId'] = $section['SectionId'];
                               $tmp_item['DataId'] = $data_child_info['DataId'];//Edit LIXD-422 hungtn VNIT 20150111
                               $tmp_item['OrgFileName'] = $data_child_info['OrgFileName'];
                               $tmp_item['FileName'] = $data_child_info['FileName'];
                               $tmp_item['t_multi_data_id'] = $i + 1;
                               $tmp_data[] = $tmp_item;
                            }
                        }
                    } 
                    
                }
                //Add End LIXD-11 hungtn VNIT 20151006
             }
        }
    
        return $tmp_data;
    }
    
    public function get_data_excelchange_free($data_new) {
        $tmp_data = array();
        if(count($data_new) > 0) {
            foreach($data_new as  $new) {
                $tmp_item = array();
                $count_data = count($new['Data']);
                for ($i = 0; $i < $count_data; $i ++ ) {
                    if($new['Data'][$i]['DataKbn'] == AKAGANE_SECTION_KBN_EXCEL && $new['Data'][$i]['OrgFileName'] != '') {
                       $tmp_item['SectionId'] = $new['FreeItemId'];
                       $tmp_item['DataId'] = $new['Data'][$i]['DataId'];//Edit LIXD-422 hungtn VNIT 20150111
                       $tmp_item['OrgFileName'] = $new['Data'][$i]['OrgFileName'];
                       $tmp_item['FileName'] = $new['Data'][$i]['FileName'];
                       $tmp_item['t_multi_data_id'] = $i + 1;
                       $tmp_data[] = $tmp_item;
                    }
                }#end for count data
             }
        }

        return $tmp_data;
    }
    
    //Edit Start LIXD-10 hungtn VNIT 20151026
    public static function tag_sort_advance(&$tags) {
        if(count($tags) > 0) {
            $arr_tmp = array();
            foreach($tags as $tag) {
                if(empty($tag)) {continue;}
                $tmp_text = explode("<", $tag);
                $tmp_text = explode(">", $tmp_text[1]);
                $arr_tmp[] = trim($tmp_text[0]);
            }
            
            asort($arr_tmp, 1);
            $arr_result = array();
            foreach($arr_tmp as $item) {
                foreach($tags as &$tag) {
                    if(empty($tag)) continue;
                    $tmp_text = explode("<", $tag);
                    $tmp_text = explode(">", $tmp_text[1]);
                    $tmp_text = trim($tmp_text[0]);
                    if($item == $tmp_text) {
                        $arr_result[] = $tag;
                        $tag = '';
                        break;
                    }
                }
            }
            
            $tags = $arr_result;
        }
    }
    //Edit End LIXD-10 hungtn VNIT 20151026
    // Add End LIXD-10 hungtn VNIT 20150914
}
