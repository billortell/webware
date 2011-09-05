<?php
//print_r($this->reqs);

if (!isset($hdata_instance)) {
    return;
}
hdata_entry::setInstance($hdata_instance);

Hooto_Web_View::headStylesheet('/_w/css/cm.css');

$msg = null;

$session = user_session::getInstance();

if ($session->uid == "0") {
    print w_msg::simple('error', 'Access Denied');
    return;
}

$uid = uname2uid($this->reqs->uname);

if (!isset($this->reqs->id)) {
    print w_msg::simple('error', 'ID can not be null');
    return;
}

$_term = Hooto_Data_Sql::getTable('term_data');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $vars = get_object_vars($this->reqs);
    //print_r($vars);
    try {
        
        foreach ($vars['name'] as $key => $val) {
            
            if (strlen(trim($val)) == 0) {
                throw new Exception('Title can not be null');
            }
            
            $set = array('name' => $val);
            
            if (isset($vars['pid'][$key])) {
                $set['pid'] = intval($vars['pid'][$key]);
            } else {
                $set['pid'] = 0;
            }
            if (isset($vars['weight'][$key])) {
                $weight = intval($vars['weight'][$key]);
                $set['weight'] = $weight > 9999 ? 9999 : $weight;
            }
            
            $term = $_term->fetch($key);
            
            if (isset($term['id'])) {
            
                if ($term['gid'] != $session->uid) {
                    throw new Exception('Access Denied');
                }
            
                $_term->update($set, array('id' => $key));
            
            } else {
            
                $set['taxon'] = hdata_entry::$metadata['taxonomy']['category']['id'];
                $set['gid'] = $session->uid;
                //print_r($set);
                $_term->insert($set);
            }
        }
        
        $msg = w_msg::simple('success', 'Success');
        
    } catch (Exception $e) {
        $msg = w_msg::simple('error', $e->getMessage());
    }
}

$where = array('taxon' => hdata_entry::$metadata['taxonomy']['category']['id'], 'gid' => $uid);
$ret = hdata_taxonomy::fetchTerms($where);
$feed = array();
foreach ($ret as $key => $val) {
    $feed[$key] = $val;
}
//print_r($feed);
if (!isset($feed[$this->reqs->id])) {
    $entry = array(
        'id' => 0,
        'name' => '',
        'pid' => 0,
        'weight' => 10,
        '_level' => 0,
        '_paths' => array(),    
    );
} else {
    $entry = $feed[$this->reqs->id];
}

$feed0 = array_merge(array(array(
  'id' => 0,
  'name' => 'ROOT',
  '_level' => 0,
)), $feed);
//print_r($feed);
echo $msg;
?>
<fieldset class="contentbox">
<legend><?php echo ($this->reqs->id == 0) ? 'New Term' : 'Term Edit';?></legend>
<div class="clearhr"></div>
<form id="general_form" name="general_form" action="<?=$this->siteurl("/term-category-edit/")?>" method="post" >
<input name="id" type="hidden" value="<?=$entry['id']?>" />
<table class="table_edit" width="100%" cellspacing="0">

  <tr>
    <td width="140px"><b>Title</b></td>
    <td><input name="name[<?=$entry['id']?>]" type="text" size="20" value="<?=$entry['name']?>" /></td>
  </tr>

  <tr>
    <td><b>Relations</b></td>
    <td>
      <select id="pid[<?=$entry['id']?>]" name="pid[<?=$entry['id']?>]">
        <?php foreach ($feed0 as $val) { 
        if (($entry['id'] != 0 && $val['id'] == $entry['id']) 
            || in_array($entry['id'], $val['_paths'])) {
            continue;
        }
        ?> 
        <option value="<?=$val['id']?>" <?php if ($val['id'] == $entry['pid']) { echo 'selected'; } ?>><?php echo str_repeat('...', $val['_level']).$val['name']?></option>
        <?php } ?>
        </select>
     </td>
  </tr>

  <tr>
    <td width="140px"><b>Weight</b></td>
    <td><input name="weight[<?=$entry['id']?>]" type="text" size="5" value="<?=$entry['weight']?>" /></td>
  </tr>
  
  <tr>
    <td></td>
    <td>
      <input type="submit" name="submit" value="Save" />
      <a href="<?=$this->siteurl("/term-category/")?>">Go Back</a>
    </td>
  </tr>

</table>
</form>
</fieldset>
<div class="clearhr"></div>



