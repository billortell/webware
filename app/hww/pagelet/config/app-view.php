<?php

$msg = '';

if (!strlen($this->reqs->appid)) {
    die('404');
}

$cf = array(
  'appid'   => $this->reqs->appid,
  'name'    => $this->reqs->appid,
  'summary' => '',
  'version' => '1.0.0',
  'release' => '1',
  'depends' => '',
  'props'   => '',
  'boot'    => '',
);

$title = 'App Setup';

$f = SYS_ROOT."app/{$this->reqs->appid}/info.php";
$f = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $f);
  
if (file_exists($f)) {
    $t = require $f;
    $cf = array_merge($cf, $t);
}


echo $msg;
?>

<fieldset class="editlet">
<legend class="titletab"><?=$title?> : <?=$cf['name']?></legend>
<form id="common_form" action="/hww/config-app-setup/" method="post">
  <input type="hidden" name="appid" value="<?=$cf['appid']?>" />
  <table class="box" width="100%" border="0" cellpadding="0" cellspacing="10" >   
    <tr>
      <td width="100px">Name</td>
      <td><strong><?=$cf['name']?></strong></td>
    </tr>
    <tr>
      <td>App ID</td>
      <td><?=$cf['appid']?></td>
    </tr>
    <tr>
      <td>Version</td>
      <td><?=$cf['version']?>-<?=$cf['release']?></td>
    </tr>
    <tr>
      <td valign="top">Summary</td>
      <td><textarea id="summary" name="summary" rows="6" style="width:90%;"><?=$cf['summary']?></textarea></td>
    </tr>
    <tr>
      <td></td>
      <td>
      <?php
      $cs = require SYS_ROOT."conf/sites.php";
      
      if ((isset($cf['boot']) && strlen($cf['boot']) && !isset($cs['app'][$cf['appid']]))) {
         echo '<input type="submit" name="submit" value="Install" class="input_button" />';
      }
      ?>      
      </td>
    </tr>
  </table>
</form>
</fieldset>
<script>

$("#common_form").submit(function(event) {

  /* stop form from submitting normally */
  event.preventDefault(); 
  
  var dataString = $(this).serialize();

  $.ajax({ 
    type: "POST",
    url: $(this).attr('action'),
    data: dataString,
    success: function(data) {
      alert(data);
    }
  });
});
</script>
