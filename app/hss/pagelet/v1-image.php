<?php

$cfg = Hooto_Config_Array::get('hssui/global');
$cfg = new Hooto_Object($cfg['v1']);

if (!isset($this->reqs->id) || $this->reqs->id == 0) {
    return false;
}

$_media = Hooto_Data_Sql::getTable('hss_v1');

$item = $_media->fetch($this->reqs->id);

if (!isset($item['id'])) {
    return false;
}

$source = $cfg->uploadDir.'/'.$item['media_dir'].'/'.$item['media_stored'];

if (!isset($this->reqs->style) 
    || !in_array($this->reqs->style, array('thumb', 'medium', 'large', 'full'))) {
    $this->reqs->style = 'thumb';
}

if ($this->reqs->style != 'full') {
    $dest = preg_replace('/(.jpg|.png|.gif)/', '-'.$this->reqs->style.'.jpg', $source);
} else {
    $dest = $source;
}

if (!file_exists($dest)) {
    return false;
}

$item['imagePath'] = $dest;

ob_end_clean();
$ims = getimagesize($item['imagePath']);

header("Cache-Control: private");
header("Pragma: cache");
header("Expires: " . gmdate("D, d M Y H:i:s",time()+31536000) . " GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s",strtotime($item['updated'])) . " GMT");
        
header("Content-type: ".$ims['mime']);

//$validator = new Zend_Validate_File_IsImage();
//if ($validator->isValid($item['imagePath'], array('type' => $ims['mime']))) {
    header("Content-Disposition: inline; filename=".$item['media_name']);
//} else {
//    header('Content-Disposition: attachment; filename='.$item['media_name']);
//}
header("Content-Length: ".filesize($item['imagePath']));    

$fp = fopen($item['imagePath'], "rb"); 

fpassthru($fp);
fclose($fp);
   
die();
 
