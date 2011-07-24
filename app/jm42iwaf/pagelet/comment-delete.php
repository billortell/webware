<?php

$msg = '';

if (!isset($hdata_instance)) {
    return;
}

if (!isset($this->reqs->id)) {
   return;
}
    
hdata_entry::setInstance($hdata_instance);
    
$links[] = array('url' => $_GET['url'], 'title' => 'Back'); // TODO XSS

try {

    $set = array('id' => $this->reqs->id, 'status' => 0);
    
    hdata_entry::updateEntry($set);

    $msg = w_msg::simple('success', 'Success', $links);
            
} catch (Exception $e) {
        
    $msg = w_msg::simple('error', $e->simpleMessage(), $links);

}
    
print $msg;

