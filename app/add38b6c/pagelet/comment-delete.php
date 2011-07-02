<?php

if (!isset($hdata_instance)) {
    return;
}

if (!isset($this->reqs->id)) {
   return;
}
    
hdata_entry::setInstance($hdata_instance);
    
$links[] = array('url' => $_GET['url'], 'title' => 'Back');

try {

    $set = array('id' => $this->reqs->id, 'status' => 0);
    
    hdata_entry::updateEntry($set);

    $this->msg = w_msg::get('success', 'Success', $links);
            
} catch (Exception $e) {
        
    $this->msg = w_msg::get('error', $e->getMessage(), $links);

}
    
print $this->pagelet('msg-inter');

