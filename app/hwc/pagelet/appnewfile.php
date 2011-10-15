<?php

$msg = '';

$item = array(
  'name'  => '',
  'path' => '',
);

$path = $this->reqs->path;
$name = $this->reqs->name;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $f = SYS_ROOT."/app/{$path}/";
  
  if ($_REQUEST['m'] == 1) {

    if ($_FILES["attachment"]["error"] == UPLOAD_ERR_OK) {
      //echo 'UP';
      $tmp_name = $_FILES["attachment"]["tmp_name"];
      $f .= $_FILES["attachment"]["name"];
      $f = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $f);
      move_uploaded_file($tmp_name, $f);
    }
    
  } else {
    
    $f .= $name;
    $f = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $f);
  
    if (!file_exists($f)) {
      Hooto_Util_Directory::mkfiledir($f, 0777);
      $fp = fopen($f, 'w');
      //fwrite($fp, pack("CCC",0xef,0xbb,0xbf)); // utf8
      //fputs($fp,chr(0x01),1);
      fputs($fp,"\xef\xbb\xbf\n");
      //fwrite($fp, "");
      fclose($fp);
    }
  }
  $path = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), trim($path, '/'));
  $name = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), trim($name, '/'));

  echo "<div>Loading</div>
    <script>
    parent.window.opener._open_window_cb('{$path}', '{$name}');
    window.close();
    </script>";
  return;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-Strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>New File</title>
  <link rel="stylesheet" href="/_w/css/global.css" type="text/css" media="all" />

  <style>
  .editlet {
    text-align:left; margin:0px; padding:10px;
    border:#989898 2px solid;
    -moz-border-radius:3px;
    -khtml-border-radius:3px;
    -webkit-border-radius:3px;
    border-radius:3px;  
  }
  .editlet .titletab {padding:0 10px 0 10px; font-size:20px; font-weight:bold;}
  .editlet td {padding:0;}
  </style>
  
</head>
<body style="padding:10px;height:90%;">
<?php echo $msg;?>
<fieldset class="editlet">
<legend class="titletab">New Text File</legend>
<form id="hwc_appnewfile_form" action="/hwc/appnewfile?path=<?=$path?>&m=0" method="post" >
  <div><b>File Name</b> (e.g. file.php OR dir/file.php)</div>
  <div>
    <input id="name" name="name" size="30" type="text" value="" />
    <input id="path" name="path" type="hidden" value="<?=$path?>"/>
    <input type="submit" name="submit" value="Create" class="input_button" />
  </div>
</form>
</fieldset>

<br />

<fieldset class="editlet">
<legend class="titletab">Upload From Location</legend>
<div class="editorPluginBody">
    <b>Add media files from your computer, Choose files to upload</b>    
    <form id="hwc_appnewfileup_form" name="hwc_appnewfileup_form" enctype="multipart/form-data" action="/hwc/appnewfile?path=<?=$path?>&m=1" method="post">
        <input id="attachment" name="attachment" size="40" type="file" />
        <input id="path" name="path" type="hidden" value="<?=$path?>"/>
  	    <input class="input_button" type="submit" value="Upload" />
    </form>
</div>
</fieldset>

</body>
