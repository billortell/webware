<?php

header('Content-type: text/plain');

$msg = '';

$appid = hwl_string::rand(8,2);
$item = array(
  'appid' => $appid,
  'name'  => $appid,
  'summary' => '',
  'version' => '1.0.0',
  'depends' => array(),
);
if (strlen($this->reqs->appid)) {
  $item['appid'] = $this->reqs->appid;
}

$f = SYS_ROOT."/app/{$item['appid']}/package.info.php";
$f = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $f);


$do = 'new';
$title = 'Create Application';
if (file_exists($f)) {
  $title = 'Edit Application'; 
  $do = 'edit';
  
  $t = require $f;
  //print_r($t);
  foreach ($item as $k => $v) {
    if (isset($t[$k])) {
      $item[$k] = $t[$k];
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'
  || $_SERVER['REQUEST_METHOD'] == 'PUT') {
  
  foreach ($item as $k => $v) {
    if (isset($_POST[$k])) {
      $item[$k] = $_POST[$k];
    }
  }
  //echo var_export($item ,true);
  $as = "<?php\nreturn ". var_export($item, true) .";\n";
  
  Hooto_Util_Directory::mkfiledir($f, 0777);
  //echo $f;
  $fp = fopen($f, 'w');
  fwrite($fp, $as);
  fclose($fp);
  
  $msg = "<div>OK</div>";
}

echo $msg;
?>

<fieldset class="editlet">
<legend class="titletab"><?=$title?></legend>
<form id="hwc_appcreate_form" action="/hwc/appcreate/" method="post" >
  <table class="box" width="100%" border="0" cellpadding="0" cellspacing="10" >
    <tr>
      <td width="140px" align="right" >APP ID</td>
      <td ><input id="appid" name="appid" size="30" type="text" value="<?=$item['appid']?>" /></td>
    </tr>
    <tr>
      <td align="right" >NAME</td>
      <td ><input id="name" name="name" size="30" type="text" value="<?=$item['name']?>" /></td>
    </tr>
    <tr>
      <td align="right" >VERSION</td>
      <td ><input id="version" name="version" size="30" type="text" value="<?=$item['version']?>" /></td>
    </tr>
    <tr>
      <td align="right" valign="top">SUMMARY</td>
      <td ><textarea id="summary" name="summary" rows="6" style="width:500px;"><?=$item['summary']?></textarea></td>
    </tr>
    <tr>
      <td></td>
      <td ><input type="submit" name="submit" value="Submit" class="input_button" /></td>
    </tr>
  </table>
</form>
</fieldset>
<script>

$("#hwc_appcreate_form").submit(function(event) {

  /* stop form from submitting normally */
  event.preventDefault(); 
  
  var dataString = $(this).serialize();
  //alert($(this).attr('action'));
  $.ajax({ 
    type: "POST",
    url: $(this).attr('action'),
    data: dataString,
    success: function(data){
      //alert(data);
      $("#hwc_layout_body_content").empty().append(data);
      window.scrollTo(0,0);
    }
  });
});
</script>
