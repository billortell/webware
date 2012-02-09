<?php
$msg = "";

$appid = $_GET['appid'];

$pat = SYS_ROOT.'app/'.$appid;

if (!file_exists($pat."/info.php")) {
    die();
}
$appinfo = require $pat."/info.php";
if (!file_exists($pat."/setup.php")) {
    die();
}
$appsetup = require $pat."/setup.php";

$f = SYS_ROOT."/config/hds.php";

if (is_readable($f))
    $arc = require $f;
else
    $arc = array();

if (!isset($arc['s']))
    $arc['s'] = array();

if (!isset($arc['i']))
    $arc['i'] = array();

$t = array(
    'adapter' => '',
    'host' => '',
    'port' => '',
    'user' => '',
    'pass' => '',
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $post = $_POST;
    
    try {
  
        $a = array();
        foreach ($post['name'] as $k => $v) {
        
            
        }
        $arc['s'] = $a;
        $str = var_export($arc, true);
        $str = preg_replace("/\=\>\ \n\s*array/", "=> array", $str);
        $as = "<?php\nreturn $str;\n";
        $fp = fopen($f, 'w');
        if (!fwrite($fp, "\xef\xbb\xbf{$as}"))
            throw new Exception("The file '$f' is not writable");
        fclose($fp);
        
        $msg = '<div class="message success"><b>Success @'.date("Y-m-d H:i:s").'</b> </div>';
  
    } catch (Exception $e) {
        $msg = '<div class="message error"><b>ERROR</b>'.$e->getMessage().' @'.date("Y-m-d H:i:s").'</div>';
    }

}

foreach ($arc['s'] as $k => $v) {
    $arc['s'][$k] = array_merge($t, $v);
    if ($arc['s'][$k]['pass'] != "")
        $arc['s'][$k]['pass'] = "******";
}

//print_r($arc);die();
?>
<fieldset class="editlet">
<legend class="titletab">Instance Setup</legend>
<?php
echo $msg;
function _setup_node($n, $pre = '')
{
    $str ='<table class="table_list" width="100%" border="0" cellpadding="0" cellspacing="0">';
    foreach ($n as $k => $v) {
        
        if (!isset($v['type']))
            continue;
        
        if ($v['type'] == 'text') {
            $str .= "<tr>";
            $str .= "<td><strong>{$v['title']}</strong></td>";
            $str .= "<td><input type=\"text\" name=\"{$k}key\" value=\"{$k}\" size=\"20\" /></td>";
            $str .= "<td><input type=\"text\" name=\"{$k}val\" value=\"{$v['default']}\" size=\"40\" /></td>";
            $str .= "</tr>";
        } else if ($v['type'] == 'node') {
            $str .= "<tr>";
            $str .= "<td><strong>{$v['title']}</strong><br /><b><a href=\"javascript:_row_append()\">Append New</a></b></td>";
            $str .= "<td colspan=\"2\">"._setup_node($v['items'])."</td>";
            $str .= "</tr>";
        }
    }
    $str .= '</table>';
    
    return $str;
}
?>
<form id="form_common" action="/hww/config-app-instance-setup/" method="post" >
    
    <?php 
    echo _setup_node($appsetup);
    ?>
    
    
    <br/>
    <input type="submit" name="submit" value="Save" class="input_button" />
</form>
</fieldset>
<script>

$("#form_common").submit(function(event) {

    event.preventDefault();
    $.ajax({ 
        type: "POST",
        url: $("#form_common").attr('action'),
        data: $(this).serialize(),
        success: function(data){
            $("#hww_config_layout_body").empty().append(data);
        }
    });
});

function _row_append() {
    entry = '<tr> \
      <td><input id="name[]" name="name[]" size="10" type="text" value=""/></td> \
      <td> \
        <select name="adapter[]"> \
          <option value="mysql">MySQL</option> \
          <option value="redis">Redis</option> \
        </select> \
      </td> \
      <td><input name="host[]" size="20" type="text" value="127.0.0.1"/></td> \
      <td><input name="port[]" size="5" type="text" value="3306"/></td> \
      <td><input name="user[]" size="10" type="text" value=""/></td> \
      <td><input name="pass[]" size="20" type="text" value=""/></td> \
      <td></td> \
      </tr>';
    $("#field_list").append(entry);
}
function _row_del(id) {
    $("#row"+id).remove();
}

</script>
