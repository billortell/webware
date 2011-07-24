
<table class="table_list">
<tr>
<td>APPID</td>
<td>NAME</td>
<td>VERSION</td>
<td>DESCRIPTION</td>
<td>OPERATIONS</td>
</tr>

<?php
$patt = SYS_ROOT.'app/*';
$arr = array();
foreach (glob($patt, GLOB_ONLYDIR) as $st) {

    if (file_exists($st."/package.info.php")) {
        $cfg = require $st."/package.info.php";
        
        echo "<tr>";
        echo "<td>{$cfg['id']}</td>";
        echo "<td>{$cfg['name']}</td>";
        echo "<td>{$cfg['version']}</td>";
        echo "<td>{$cfg['summary']}</td>";
        echo "<td></td>";
        echo "</tr>";
    }
}
?>
</table>

