<?php

$this->headtitle = "Web Creator";

?>
<table width="100%" class="table_list" cellspacing="0">
<thead>
  <tr>   
    <th><b>ID</b></th>
    <th><b>NAME</b></th>
    <th><b>VERSION</b></th>
    <th></th>
  </tr>
</thead>
<?php

$patt = SYS_ROOT.'app/*';

foreach (glob($patt, GLOB_ONLYDIR) as $st) {
    
    if (!file_exists($st."/info.php")) {
        continue;
    }
    
    $val = require $st."/info.php";
    
?>
<tr>
    <td><b><?=$val['id']?></b></td>
    <td><b><?=$val['name']?></b></td>
    <td><?=$val['version']?></td>
    <td>
        <a href="javascript:node_edit('<?=$val['id']?>')">#</a>
    </td>
</tr>
<?php } ?>
</table>


