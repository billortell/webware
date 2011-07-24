<?php

if (!isset($hdata_instance)) {
    return;
}

if (!isset($this->reqs->id)) {
   return;
}
$msg = '';
    
hdata_entry::setInstance($hdata_instance);
    
if (isset($_GET['url'])) {
    $links[] = array('url' => $_GET['url'], 'title' => 'Back'); // TODO XSS
} else {
    $links[] = array('url' => $this->siteurl('/', $this->reqs->ins), 'title' => 'Back');
}

try {
            
    hdata_entry::deleteEntry($this->reqs->id);

    $msg = w_msg::simple('success', 'Success', $links);
            
} catch (Exception $e) {
        
    $msg = w_msg::simple('error', $e->getMessage(), $links);

}
    
print $msg;

