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

$id = intval($this->reqs->id);

$_term = Hooto_Data_Sql::getTable('term_data');


try {
    
    $where = array('taxon' => 1, 'gid' => $uid);
    $ret = hdata_taxonomy::fetchTerms($where);
    $feed = array();

    foreach ($ret as $key => $val) {
    
        $feed[$key] = $val;
        
        if (in_array($id, $val['_paths'])) {
            throw new Exception ('#10, Can only delete the spatial term');
        }
    }
    
    $query  = hdata_entry::select()
        ->where('uid = ?', $uid)
        ->where('category = ?', $id)
        ->limit(1);
    
    $feed = hdata_entry::query($query);
    
    if (isset($feed[0]['id'])) {
        throw new Exception ('#11, Can only delete the spatial term');
    }
    
    $term = $_term->fetch($id);
            
    if (isset($term['id'])) {
            
        if ($term['gid'] != $session->uid) {
            throw new Exception('Access Denied');
        }
            
        $_term->delete($id);
    }
    
    $msg = w_msg::simple('success', 'Success');
} catch (Exception $e) {
    $msg = w_msg::simple('error', $e->getMessage());
}

echo $msg;
?>

<a href="<?=$this->reqs->urlins?>/term-category/">Go Back</a>

