<table class="table_list">
<tr>
<td>INSTANCE</td>
<td>APPID</td>
<td>NAME</td>
<td>OPERATIONS</td>
</tr>

<?php
$patt = SYS_ROOT.'conf/'.SITE_NAME.'/*';
    
$arr = array();
foreach (glob($patt, GLOB_ONLYDIR) as $st) {

    if (file_exists($st."/global.php")) {
        $cfg = require $st."/global.php";
        
        $oper = '';
        if (file_exists(SYS_ROOT."app/{$cfg['appid']}/permission.php")) {
            $oper .= "<a href=\"/hww/instance-permission?instance={$cfg['instance']}\">Permissions</a>";
        }
        echo "<tr>";
        echo "<td>{$cfg['instance']}</td>";
        echo "<td>{$cfg['appid']}</td>";
        echo "<td>{$cfg['name']}</td>";
        echo "<td>{$oper}
            <a href=\"#\">Configure</a>
            </td>";
        echo "</tr>";
    }
}

?>
</table>

