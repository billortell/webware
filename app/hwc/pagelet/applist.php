<?php

$this->headtitle = "Web Creator";

?>

<table width="100%" class="table_list" cellspacing="0">
<thead>
  <tr>
    <th><b>NAME</b></th>
    <th><b>APP ID</b></th>
    <th><b>VERSION</b></th>
    <th><b>RELEASE</b></th>
    <th><b>TYPE</b></th>
    <th></th>
  </tr>
</thead>

<?php
$patt = SYS_ROOT.'app/*';
$def  = array(
  'id'    => '',
  'name'  => '',
  'type'  => '0',
  'version' => '1.0.0',
  'release' => '1',
);

foreach (glob($patt, GLOB_ONLYDIR) as $st) {

  $appid = trim(strrchr($st, '/'), '/');
  
  if (in_array($appid, array('hwc', 'hww', 'Zend'))) {
    continue;
  }

  if (file_exists($st."/info.php")) {
    $val = require $st."/info.php";
  } else {
    
    continue;
    
    $val = array(
      'name'  => $appid,
      'appid'  => $appid,
    );
  }
  
  $val = array_merge($def, $val);
?>
<tr>
  <td><b><a href="javascript:hwc_app('<?=$val['appid']?>')"><?=$val['name']?></a></b></td>
  <td><?=$val['appid']?></td>
  <td><?=$val['version']?></td>
  <td><?=$val['release']?></td>
  <td><?=$val['type']?></td>
  <td>
    <a href="javascript:hwc_appcreate('<?=$val['appid']?>')">Setting</a>
  </td>
</tr>
<?php } ?>
</table>


