<?php
$msg = "";

$f = SYS_ROOT."/config/hds.php";
//$f = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $f);

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
        
            if ($post['pass'][$k] == "******" && isset($arc['s'][$v]['pass'])) {
                $post['pass'][$k] = $arc['s'][$v]['pass'];
            }
            
            $a[$v] = array(
                'adapter' => $post['adapter'][$k],
                'host'    => $post['host'][$k],
                'port'    => $post['port'][$k],
                'user'    => $post['user'][$k],
                'pass'    => $post['pass'][$k],
            );
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
<legend class="titletab">Data Connections Setting</legend>
<?php echo $msg;?>
<form id="form_common" action="/hww/config-dataconn/" method="post" >

  <table class="table_list" width="100%" border="0" cellpadding="0" cellspacing="0" >
    <thead>
    <tr>
      <th><b>Name</b></th>
      <th><b>Adapter</b></th>
      <th><b>Host</b></th>
      <th><b>Port</b></th>
      <th><b>User</b></th>
      <th><b>Password</b></th>
      <th></th>
    </tr>
    </thead>
    <tbody id="field_list">
    <?php foreach ($arc['s'] as $k => $v) { ?>
    <tr id="row<?php echo $k?>">
      <td><input id="name[]" name="name[]" size="10" type="text" value="<?=$k?>"/></td>
      <td>
        <select name="adapter[]">
          <option value="mysql" <?php if ($v['adapter'] == 'mysql') echo 'selected="selected"';?>>MySQL</option>
          <option value="redis" <?php if ($v['adapter'] == 'redis') echo 'selected="selected"';?>>Redis</option>
        </select>
      </td>
      <td><input id="host[]" name="host[]" size="20" type="text" value="<?=$v['host']?>"/></td>
      <td><input id="port[]" name="port[]" size="5" type="text" value="<?=$v['port']?>"/></td>
      <td><input id="user[]" name="user[]" size="10" type="text" value="<?=$v['user']?>"/></td>
      <td><input id="pass[]" name="pass[]" size="20" type="text" value="<?=$v['pass']?>"/></td>
      <td><a href="javascript:_row_del('<?php echo $k?>')" onclick="return confirm('Are you sure you want to delete?')">Delete</a></td>
    </tr>
    <?php } ?>
    </tbody>
  </table>
  <br/>
  <input type="submit" name="submit" value="Save" class="input_button" />
  &nbsp;&nbsp; <b><a href="javascript:_row_append()">Append New Connection</a></b>
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
