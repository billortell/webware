<?php

$this->headtitle = 'Edit permissions';

if (!isset($this->reqs->instance)) {
    return;// TODO
}
$instance = $this->reqs->instance;

if (!file_exists(SYS_ROOT."/conf/{$instance}/global.php")) {
    return;// TODO
}

$cfg = require SYS_ROOT."/conf/{$instance}/global.php";

$initperms = require SYS_ROOT."/app/{$cfg['appid']}/permission.php";

$_role = Hooto_Data_Sql::getTable('role');
$_roleperm = Hooto_Data_Sql::getTable('role_permission');


$q = $_role->select()->order('weight', 'desc')->limit(1000);
$roles = $_role->query($q);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $q = $_roleperm->select()->where('instance = ?', $instance)->limit(10000);
    $ret = $_roleperm->query($q);
    
    $roleperm = array();
    foreach ($ret as $val) {
        $roleperm[$val['rid'].$val['permission']] = $val['id'];
    }

    foreach ($this->reqs->r as $rid => $ps) {
        foreach ($ps as $perm => $tmp) {
            if (!isset($roleperm[$rid.$perm])) {
                $set = array('instance' => $instance,
                    'rid' => $rid,
                    'permission' => $perm);
                $_roleperm->insert($set);
            } else {
                unset($roleperm[$rid.$perm]);
            }
        }
    }
    
    foreach ($roleperm as $id) {
        $_roleperm->delete($id);
    }

}

$perm_all = $initperms['perms'];

$q = $_roleperm->select()->where('instance = ?', $instance)->limit(10000);
$ret = $_roleperm->query($q);

$defs = array();
if (count($ret) > 0) {

    foreach ($ret as $val) {
        $defs[$val['rid']][] = $val['permission'];
    }
} else {
    $defs = $initperms['defaults'];
}

?>
<form id="form-instance-permission" name="form-instance-permission" action="/hww/instance-permission" method="post">
<input id="instance" name="instance" type="hidden" value="<?=$instance?>" />

<table class="table_list">
<tr>
<td>PERMISSION</td>
<td>DESCRIPTION</td>
<?php 
foreach ($roles as $role) {
    echo "<td>{$role['name']}</td>";
}
?>
</tr>
<?php

foreach ($perm_all as $key => $val) {
        
    echo "\n<tr>";
    echo "<td>{$key}</td>";
    echo "<td>{$val['title']}</td>";
    foreach ($roles as $r) {
        $check = "";
        if (isset($defs[$r['id']])) {
            if ((is_array($defs[$r['id']]) && in_array($key, $defs[$r['id']]))
                || $defs[$r['id']] == 'all') {
                $check = "checked=\"checked\" ";
            }
        }
        echo "<td align=\"center\"><input type=\"checkbox\" id=\"r[{$r['id']}][{$key}]\" name=\"r[{$r['id']}][{$key}]\" value=\"1\" {$check} /></td>";
    } 
    echo "</tr>";
}
?>

</table>
<input class="input_button" type="submit" name="submit" value="Save">
</form>
