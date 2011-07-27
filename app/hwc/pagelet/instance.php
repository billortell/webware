<table class="table_list">
<tr>
<td>INSTANCE</td>
<td>APPID</td>
<td>NAME</td>
<td>OPERATIONS</td>
</tr>

<?php
$patt = SYS_ROOT.'conf/'.SITE_NAME.'/*';
    
$arr = array();
foreach (glob($patt, GLOB_ONLYDIR) as $st) {

    if (file_exists($st."/global.php")) {
        $cfg = require $st."/global.php";
        
        $oper = '';
        if (file_exists(SYS_ROOT."app/{$cfg['appid']}/permission.php")) {
            $oper .= "<a href=\"/hwc/instance-permission?instance={$cfg['instance']}\">Permissions</a>";
        }
        echo "<tr>";
        echo "<td>{$cfg['instance']}</td>";
        echo "<td>{$cfg['appid']}</td>";
        echo "<td>{$cfg['name']}</td>";
        echo "<td>{$oper}
            <a href=\"#\">Configure</a>
            </td>";
        echo "</tr>";
    }
}

?>
</table>

<?php

    //debugAction();

    function debugAction()
    {
        try {

            $conf = Hooto_Config_Array::get('global');

            $db = Zend_Db::factory($conf['database']['adapter'],
                    $conf['database']['params']);
            Zend_Db_Table_Abstract::setDefaultAdapter($db);
                    
            if (isset($conf['database2'])) {
                $dbsrc = Zend_Db::factory($conf['database2']['adapter'],
                    $conf['database2']['params']);
                //Zend_Db_Table_Abstract::setDefaultAdapter($dbsrc);
                
            } else {
                throw new Exception('Can not connect to db-server');
            }

        } catch (Exception $e) {
            $e->getMessage();
        }
        
        $_pin_dbentry= Core_Dao::factory(array('name' => 'datax_entry'));
        $_pin_dbcomment= Core_Dao::factory(array('name' => 'datax_comment'));
        $_pin_dbtermuser= Core_Dao::factory(array('name' => 'term_data'));
        $db = $_pin_dbentry->getAdapter();

        $ua = array();

        $rs = $dbsrc->query("SELECT a.*,u.username from kit_node_tree a,kit_user u WHERE a.userid = u.userid ORDER BY treeid LIMIT 99999")->fetchAll();
        $counter = 0;
        foreach ($rs as $val) {
        
            $str = @mb_convert_encoding($val['name'], 'UTF-8', mb_detect_encoding($val['name'], "auto", TRUE));
            
            $uid = uname2uid($val['username']);
            
            if ($val['module'] == 'blog') {
                $taxon = 1;
            } else if ($val['module'] == 'doc') {
                $taxon = 3;
            } else if ($val['module'] == 'link') {
                $taxon = 4;
            }
            
            //$hash = substr(md5(strtolower(trim($str))), 0, 16);
            $set = array(
                'id' => $val['treeid'],
                'gid' => $uid,
                'pid' => $val['parentid'],
                //'hash' => $hash,
                'name' => $str,
                //'created' => $val['created'],
                //'updated' => $val['modified'],
                'weight' => $val['ordering'],
                'taxon' => $taxon
            );
           
            
            try {
                // PIN
                $_pin_dbtermuser->insert($set);
            } catch (Exception $e) {
                $counter++;
            }
        }
        echo ",cat:$counter";
        
        $rs = $dbsrc->query("SELECT a.*,u.username from kit_node a,kit_user u WHERE a.userid = u.userid ORDER BY a.nodeid ASC LIMIT 99999")->fetchAll();

        $counter = 0;
        foreach ($rs as $val) {
        
            $uid = uname2uid($val['username']);
            
            if ($val['module'] == 'blog') {
                $instance = 10;
                $instance_app = 'blog';
            } else if ($val['module'] == 'doc') {
                $instance = 11;
                $instance_app = 'doc';
            } else if ($val['module'] == 'link') {
                $instance = 12;
                $instance_app = 'link';
            }
            
            
            hdata_entry::setInstance($instance);
            
            $entry = new Hooto_Object();
            $entry->id = $val['nodeid'];
            $entry->uid = $uid;
            $entry->uname = $val['username'];
            $entry->category = $val['treeid'];
            $entry->instance = $instance;
            $entry->status = $val['status'];
            $entry->title = $val['title'];
            $entry->tag = $val['terms'];
            $entry->created = $val['created'];
            $entry->updated = $val['modified'];
            $entry->weight = $val['ordering'];
            $entry->stat_access = $val['count_access'];
            $entry->comment = $val['allow_comment'];
            
            $ua[$uid][$instance_app] = true;
            
            // PIN                
            try {
                // PIN
                hdata_entry::replaceEntry($entry);
            } catch (Exception $e) {
                echo $e->getMessage()."\n <br />";
                $counter++;
            }
        }
        echo ",entry:$counter";
    
        $rs = $dbsrc->query("SELECT * from kit_node_revision ORDER BY nodeid LIMIT 99999")->fetchAll();
        $counter = 0;
        foreach ($rs as $val) {
        
            $str = @mb_convert_encoding($val['body'], 'UTF-8', mb_detect_encoding($val['body'], "auto", TRUE));
            if ($str === FALSE || $str === NULL || $str == "") {
                $counter++;
            }
            if (strlen(trim($str)) < 10) {
                $counter++;
            }
            
            if (strlen($val['summary']) > 2) {
                $val['summary_auto'] = 0;
            } else {
                $val['summary_auto'] = 1;
            }
            
            $set = array(
                'summary' => $val['summary'],
                'summary_auto' => $val['summary_auto'],
                'content' => $str
            );

            try {
                // PIN
                $_pin_dbentry->update($set, array('id' => $val['nodeid']));
            } catch (Exception $e) {
                echo $e->getMessage()."\n <br />";
                $counter++;
            }
        }
        echo ",entry-content:$counter";
        
        
        $user_map = array();
        $rs = $dbsrc->query("SELECT * from kit_user ORDER BY userid LIMIT 99999")->fetchAll();
        foreach ($rs as $val) {
            $des = str_split($val['username']);            
            $path = './data/user/v5/'.$des['0'].'/'.$des['1'].'/'.$des['2'].'/'.$val['username'];
            
            if (file_exists($path."/icon-normal.png")) {
                $des = str_split(uname2uid($val['username']));            
                $path2 = './data/user/'.$des['0'].'/'.$des['1'].'/'.$des['2'].'/'.uname2uid($val['username']);
                Core_Util_Directory::mkdir($path2);
                copy($path."/icon-normal.png", $path2."/w100.png");
                copy($path."/icon-small.png", $path2."/w40.png");
            }
            $user_map[$val['userid']] = uname2uid($val['username']);
        }
        
        $rs = $dbsrc->query("SELECT * from kit_node_comment ORDER BY comid LIMIT 99999")->fetchAll();
        $counter = 0;
        foreach ($rs as $val) {
        
            $str = @mb_convert_encoding($val['body'], 'UTF-8', mb_detect_encoding($val['body'], "auto", TRUE));
            if ($str === FALSE || $str === NULL || $str == "") {
                $counter++;
            }
            if (strlen(trim($str)) < 10) {
                $counter++;
            }
            
            $puid = isset($user_map[$val['userid']]) ? $user_map[$val['userid']] : '0';
            
            if ($val['module'] == 'blog') {
                $instance = 10;
            } else if ($val['module'] == 'doc') {
                $instance = 11;
            } else if ($val['module'] == 'link') {
                $instance = 12;
            }
            
            $set = array(
                'id' => $val['comid'],
                'pid' => $val['nodeid'],
                'pinstance' => $instance,
                'puid' => $puid,
                
                'content' => $str,
                
                'uid' => 0,
                'uname' => $val['username'],

                'instance' => 5,
                'status' => $val['status'],
                'created' => $val['created'],
                'updated' => $val['modified']
            );
            
            if ($val['userid_commit'] > 0) {
                if (isset($user_map[$val['userid_commit']])) {
                    $set['uid'] = $user_map[$val['userid_commit']];
                }
            }

            try {
                // PIN
                $_pin_dbcomment->insert($set);
            } catch (Exception $e) {
                echo $e->getMessage()."\n <br />";
                $counter++;
            }
        }
        echo ",comment:$counter";
      
        
        $_pin_dbuser = Core_Dao::factory(array('name' => 'user'));
        $_pin_dbuserp = Core_Dao::factory(array('name' => 'user_profile'));
        $rs = $dbsrc->query("SELECT * from kit_user ORDER BY userid LIMIT 99999")->fetchAll();
        foreach ($rs as $val) {
        
            $name = @mb_convert_encoding($val['name'], 'UTF-8', mb_detect_encoding($val['name'], "auto", TRUE));
            $content = @mb_convert_encoding($val['aboutme'], 'UTF-8', mb_detect_encoding($val['aboutme'], "auto", TRUE));
            $home_name = @mb_convert_encoding($val['home_name'], 'UTF-8', mb_detect_encoding($val['home_name'], "auto", TRUE));
            
            $uid = uname2uid($val['username']);
            
            $set = array('id' => $uid,
                'uname' => $val['username'],
                'pass' => $val['password'],
                'email' => $val['email'],
                'roles' => $val['roles'],
                'name' => $name,
                'created' => strtotime($val['created']),
                'updated' => strtotime($val['modified'])
            );
            // PIN
            $_pin_dbuser->insert($set);

            
            $set = array('id' => $uid,
                'gender' => $val['gender'],
                'birthday' => $val['birthday'],
                'name' => $name,
                'address' => $val['address'],
                'content' => $content,
                'uname' => $val['username'],
                'sitename' => $home_name,
                'created' => strtotime($val['created']),
                'updated' => strtotime($val['modified'])
            );
            // PIN
            $_pin_dbuserp->insert($set);
        }
        
        $_pin_dbhss_v1 = Core_Dao::factory(array('name' => 'hss_v1'));
        $rs = $dbsrc->query("SELECT * from kit_media ORDER BY mediaid LIMIT 99999")->fetchAll();
        foreach ($rs as $val) {

            $uid = isset($user_map[$val['userid']]) ? $user_map[$val['userid']] : '0';
            $set = array(
                'id'            => $val['mediaid'],
                'uid'           => $uid,
                'media_dir'     => $val['media_dir'],
                'media_name'    => $val['media_name'],
                'media_stored'  => $val['media_stored'],
                'media_ext'     => $val['media_ext'],
                'media_size'    => $val['media_size'],
                'media_mime'    => $val['media_mime'],
                'created'       => $val['created'],
                'updated'       => $val['modified'],
            );

            $ua[$uid]['media'] = true;
            
            // PIN
            $_pin_dbhss_v1->insert($set);
        }
        
        $_pin_dbuserapps = Core_Dao::factory(array('name' => 'user_apps'));
        $_pin_dbmenulink = Core_Dao::factory(array('name' => 'menu_link'));
        $rs = $dbsrc->query("SELECT * from kit_user_module ORDER BY id LIMIT 99999")->fetchAll();
        foreach ($rs as $val) {
            
            if (!isset($ua[$user_map[$val['userid']]][$val['submodule']])) {
                continue;
            }
            
            if ($val['submodule'] == 'doc') {
                $val['submodule'] = 'article';
                $val['name'] = 'Article';
            } else if ($val['submodule'] == 'media') {
                $val['submodule'] = 'hssui';
                $val['name'] = 'Storage';
            }
            
            $set = array(
                'status' => 1,
                'uid' => $user_map[$val['userid']],
                'instance' => $val['submodule'],
                'title' => $val['name'],
            );
            // PIN
            $_pin_dbuserapps->insert($set);

            
            $set = array(
                'pid' => 0,
                'type' => 4,
                'status' => 1,
                'uid' => $user_map[$val['userid']],
                'instance' => $val['submodule'],
                'title' => $val['name'],
            );
            
            if ($set['instance'] == 'link') {
                $set['type'] = 9;
            }
            
            // PIN
            $_pin_dbmenulink->insert($set);
        }
        
        
        
        return;

    }
?>
