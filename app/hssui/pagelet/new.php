<?php 
$msg = '';
$session = user_session::getInstance();
if ($session->uid == "0") {
    print w_msg::simple('error', 'Access Denied');
    return;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    try {
    
		$media = new hssui_apiv1();
		$image = new Hooto_Util_Image();
        $_media = Hooto_Data_Sql::getTable('hss_v1');
        
        $cfg = Hooto_Config_Array::get('hssui/global');
        $cfg = new Hooto_Object($cfg['v1']);

        $url = '/hssui/list/';

        if (!$media->upload(array('uid' => $session->uid), $ret)) {
            throw new Exception($ret['errMsg']);
        }
        
        $image->resize($ret['stored'], 
            $cfg->image_thumb_width,
            $cfg->image_thumb_height,
            $cfg->image_thumb_crop,
            'thumb'
        );
        
        $image->resize($ret['stored'], 
            $cfg->image_medium_width,
            $cfg->image_medium_height,
            $cfg->image_medium_crop,
            'medium'
        );
        
        $image->resize($ret['stored'], 
            $cfg->image_large_width,
            $cfg->image_large_height,
            $cfg->image_large_crop,
            'large'
        );
        
        header("Location: /hssui/list?id={$ret['id']}");
        die();
        
    } catch (Exception $e) {
        $msg = w_msg::simple('error', $e->getMessage());
    }
}


echo $msg; 
?>

<div class="navindex_title">New File</div>

<div class="editorPluginBody">
    <b>Upload files from your computer, Choose files to upload</b>
    
    <br /><br />
    
    <form id="formMediaUpload" name="formMediaUpload" enctype="multipart/form-data" action="/hssui/new" method="post">
        <input id="attachment" name="attachment" size="40" type="file" />
  	    <input class="input_button" type="submit" value="Upload" />
    </form>
    
    <br /><br />
    
    <p>You can upload a JPG, GIF, or PNG file. (Maximum size of 500KB) Do not upload photos containing children, pets, cartoons, celebrities, nudity, artwork or copyrighted images.</p>
</div>

