<?php

Hooto_Web_View::headStylesheet('/_user/css/sign.css');

$message = null;
if (isset($_REQUEST['cburl']) && strlen($_REQUEST['cburl']) > 10) {
    $goback = "<div><a href=\"{$_REQUEST['cburl']}\">Go Back</a></div>";
} else {
    $goback = "<div><a href=\"/\">Go Back</a></div>";
}
        
try {
        
    user_sign::out();
    
    $message = w_msg::simple('success', 'Success'.$goback);
            
} catch (Exception $e) {
    $message = w_msg::simple('error', $e->getMessage().$goback);
}

print $message;
?>
