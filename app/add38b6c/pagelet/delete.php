<?php

if (!isset($hdata_instance)) {
    return;
}

if (!isset($this->reqs->id)) {
   return;
}
    
hdata_entry::setInstance($hdata_instance);
    
if (isset($_GET['url'])) {
    $links[] = array('url' => $_GET['url'], 'title' => 'Back');
} else {
    $links[] = array('url' => $this->siteurl('/', $this->reqs->ins), 'title' => 'Back');
}

try {
            
    hdata_entry::deleteEntry($this->reqs->id);

    $this->msg = w_msg::get('success', 'Success', $links);
            
} catch (Exception $e) {
        
    $this->msg = w_msg::get('error', $e->getMessage(), $links);

}
    
print $this->pagelet('msg-inter');

