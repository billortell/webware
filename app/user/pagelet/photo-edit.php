<?php
$this->headtitle = "Change photo";

Hooto_Web_View::headStylesheet('/_user/css/manage.css');

$msg = null;

$session = user_session::getInstance();

if ($session->uid == "0") {
    print w_msg::simple('error', 'Access Denied');
    return;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $vars = get_object_vars($this->reqs);

    $_user = Hooto_Data_Sql::getTable('user');
    $_image = new Core_Util_Image();
    
    $status = true;
    
    $file_tmp  = $_FILES['attachment']['tmp_name'];
    $file_name = $_FILES['attachment']['name'];
    $file_size = $_FILES['attachment']['size'];
    $file_mime = $_FILES['attachment']['type'];
    
    $file_ext  = substr(strrchr(strtolower($file_name), '.'), 1);
    
    if (! in_array($file_ext, array('png', 'jpg', 'jpeg', 'gif'))) {
    
        $msg = w_msg::simple('error', 'You must upload a JPG, GIF, or PNG file');
        
    } else if (is_uploaded_file($file_tmp)) {
        
        $des = str_split($session->uid);
        $des_dir = SYS_ROOT.'/data/user/'.$des['0'].'/'.$des['1'].'/'.$des['2'];
        $des_dir.= '/'.$session->uid;
        
        Core_Util_Directory::mkdir($des_dir);
    
        $file_size_stored = @filesize($file_tmp);
    
        if ($file_size_stored > 1000000) {
            @unlink($file_tmp);
            $max_size = 1000000 / 1000;
            $msg = w_msg::simple('error', "File size must less than $max_size Kb");
            $status =  false;
        } elseif ($file_size_stored != $file_size) {
            @unlink($file_tmp);
            $msg = w_msg::simple('error', 'Unknown error');
            $status =  false;
        }
        
        if ($status && $imginfo = @getimagesize($file_tmp)) {
            if (!$imginfo[2]) {
                @unlink($file_tmp);
                $msg = w_msg::simple('error', 'Invalid image');
                $status =  false;
            }
        }
    
        $_image->resampimagejpg(100, 100, $file_tmp, $des_dir.'/w100.png', true);
        $_image->resampimagejpg(40, 40, $file_tmp, $des_dir.'/w40.png', false);
    }
    
    if ($msg === null) {
        $msg = w_msg::simple('success', 'Success');
    }
}
$time = $_SERVER['REQUEST_TIME'];



$des = str_split($session->uid);            
$photo_path = '/data/user/'.$des['0'].'/'.$des['1'].'/'.$des['2'].'/'.$session->uid;
    
if (!file_exists(SYS_ROOT.$photo_path."/w100.png")) {
    $photo_path = '/data/user';
}

echo "<div><a href=\"/user/manage/\">Go Back</a></div><div class=\"clearhr\"></div>";
echo $msg;
?>

<fieldset class="editlet">
<legend class="titletab">Change photo</legend>


<table border="0" cellpadding="0" cellspacing="0" > 
  <tr> 
    <td valign="bottom"><img src="<?php echo $photo_path?>/w100.png?.t=<?php echo $time?>" /> <b>Normal size</b></td> 
    <td width="30px"></td> 
    <td valign="bottom"><img src="<?php echo $photo_path?>/w40.png?.t=<?php echo $time?>" /> <b>Small size</b></td> 
  </tr> 
</table>

<div class="clearhr"></div>

<p>You can upload a JPG, GIF, or PNG file. (Maximum size of 500KB)</p>
<p>Do not upload photos containing children, pets, cartoons, celebrities, nudity, artwork or copyrighted images.</p>

<div class="clearhr"></div>

<form id="changephoto" name="changephoto" enctype="multipart/form-data" action="/user/photo-edit/" method="post">
  <table border="0" cellpadding="0" cellspacing="0" >
    <tr>
      <td width="160px"><b>Select picture</b></td>
      <td><input id="attachment" name="attachment" size="40" type="file" style="width:500px" /></td>
    </tr>
    <tr>
      <td></td>
      <td><input type="submit" value="Upload" class="input_button" />
    </tr>
  </table>
</form>

</fieldset>


