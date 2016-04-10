<?php
require_once ACW_VENDOR_DIR . '/imgpdf/fpdf.php';
require_once ACW_VENDOR_DIR . '/imgpdf/fpdi.php';

class ImgToPdf_lib
{
    protected $_logo="";
    public function set_logo($logo_file){
        $this->_logo = $logo_file;
    }
    public function addimg($img_file,$pdf_file){
        $pdf =new FPDI();
        $pdf->AddPage();
        $pagecount = $pdf->setSourceFile($pdf_file);
        $tppl = $pdf->importPage(1);
        $size2 = $pdf->useTemplate($tppl,NULL,NULL,0,0,TRUE); //TRUE set theo kich thuoc ban dau
        $height = $pdf->GetPageHeight()- (10+IMG_CON_DAU_HEIGHT);
        $width	= $pdf->GetPageWidth()-(5+IMG_CON_DAU_WIDTH);
        $pdf->Image($img_file,$width,$height,IMG_CON_DAU_WIDTH,IMG_CON_DAU_HEIGHT);//x,y,width,height
        $pdf->Output($pdf_file, 'F');
    }
}
