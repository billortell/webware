<div>
  <table class="table_list" width="100%" border="0" cellpadding="0" cellspacing="0">
  
    <thead>
    <tr>
      <th width="200px"><b>Name</b></th>
      <th><b>Installed</b></th>
      <th><b>Latest</b></th>
      <th width="60%"><b>Description</b></th>
    </tr>
    </thead>
    
    <tbody id="field_list">
<?php
$patt = SYS_ROOT.'app/*';
$arr = array();
$cs = require SYS_ROOT."conf/sites.php";

foreach (glob($patt, GLOB_ONLYDIR) as $st) {

    if (file_exists($st."/info.php")) {
    
        $cfg = require $st."/info.php";
        
        echo "<tr>";
        echo "<td><a href=\"javascript:hww_config_app_view('{$cfg['appid']}')\">{$cfg['name']}</a></td>";
        if ($cfg['appid'] == 'hww' || !isset($cfg['boot']) ||
            (isset($cfg['boot']) && strlen($cfg['boot']) 
            && isset($cs['app'][$cfg['appid']]))) {
            echo "<td><font color='green'>{$cfg['version']}</font></td>";
        } else {
            echo "<td></td>";
        }
        
        echo "<td>{$cfg['version']}</td>";
        echo "<td>{$cfg['summary']}</td>";
        echo "</tr>";
    }
}
?>
    </tbody>
    
  </table>

</div>
<script>

</script>
