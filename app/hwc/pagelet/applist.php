<?php

$this->headtitle = "Web Creator";

?>
<table width="100%" class="table_list" cellspacing="0">
<thead>
  <tr>
    <th><b>ID</b></th>
    <th><b>NAME</b></th>
    <th><b>VERSION</b></th>
    <th><b>TYPE</b></th>
    <th></th>
  </tr>
</thead>
<?php

$patt = SYS_ROOT.'app/*';
$def  = array(
    'id'    => '',
    'name'  => '',
    'version' => '1.0.0',
    'type'    => '1',
);

foreach (glob($patt, GLOB_ONLYDIR) as $st) {

    $appid = trim(strrchr($st, '/'), '/');
    
    if (in_array($appid, array('hwc', 'hwl', 'hww', 'Zend'))) {
      continue;
    }

    if (file_exists($st."/package.info.php")) {
        $val = require $st."/package.info.php";
    } else {
            
        $val = array(
            'name'  => $appid,
            'id'    => $appid,
            'type'  => 0,
        );
    }
    
    $val = array_merge($def, $val);
?>
<tr>
    <td><b><?=$val['id']?></b></td>
    <td><b><?=$val['name']?></b></td>
    <td><?=$val['version']?></td>
    <td><?=$val['type']?></td>
    <td>
        <a href="javascript:hwc_appcreate('<?=$val['id']?>')">Setting</a>
        <a href="javascript:hwc_app('<?=$val['id']?>')">Edit</a>
    </td>
</tr>
<?php } ?>
</table>


