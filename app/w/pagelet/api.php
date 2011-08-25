<?php
$patt = SYS_ROOT.'app/*';
    
$arr = array();
foreach (glob($patt, GLOB_ONLYDIR) as $st) {

    if (file_exists($st."/info.php")) {
        $cfg = require $st."/info.php";
        
        echo "<h2>{$cfg['name']}</h2> {$cfg['summary']}";
        echo "<ul>";
        echo "<li>ID: {$cfg['appid']}</li>";
        echo "<li>Version: {$cfg['version']}</li>";
        echo "</ul>";
    }
}


