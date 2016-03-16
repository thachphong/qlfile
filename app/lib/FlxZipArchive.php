<?php
// Add Start NBKD-765 hungtn VNIT 2014/11/20
class FlxZipArchive_lib extends ZipArchive {
    /** Add a Dir with Files and Subdirs to the archive;;;;; @param string $location Real Location;;;;  @param string $name Name in Archive;;; @author Nicolas Heimann;;;; @access private  **/

    public function addDir($location, $name) {
        $name = urldecode($name);
        // Add Start NBKD-765 hungtn VNIT 2014/11/27
         if(mb_detect_encoding($name) == 'UTF-8') {
             $name = mb_convert_encoding($name, 'sjis-win', 'UTF-8');
         }
         // Add End NBKD-765 hungtn VNIT 2014/11/27
        $this->addEmptyDir($name);

        $this->addDirDo($location, $name);
    } // EO addDir;

    /**  Add Files & Dirs to archive;;;; @param string $location Real Location;  @param string $name Name in Archive;;;;;; @author Nicolas Heimann
     * @access private   **/
    private function addDirDo($location, $name) {
        $name .= '/';
        $location .= '/';

        // Read all Files in Dir
        $dir = opendir ($location);
        while ($file = readdir($dir))
        {
            if ($file == '.' || $file == '..') continue;
            // Rekursiv, If dir: FlxZipArchive_lib::addDir(), else ::File();
            $do = (filetype( $location . $file) == 'dir') ? 'addDir' : 'addFile';
            // Add Start NBKD-765 hungtn VNIT 2014/11/27
            if(mb_detect_encoding($file) == 'UTF-8') {
                $file = mb_convert_encoding($file, 'sjis-win', 'UTF-8');
            }
            // Add End NBKD-765 hungtn VNIT 2014/11/27
            $this->$do($location . $file, $name . $file);
        }
    } // EO addDirDo();
}
// Add End NBKD-765 hungtn VNIT 2014/11/20