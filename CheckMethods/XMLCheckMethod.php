<?php

use SUpdateServer\Internal\CheckMethod;

class XMLCheckMethod extends CheckMethod {

   public function getName() {
       return "xml-check-method";
   }

   public function createFileInfos($file) {
       return '<Key>'.$file.':'.md5_file(SUpdateServer::FILES_DIRECTORY . "/" . $file).'</Key>';
   }

}
