<?php 
$session = user_session::getInstance();
if ($session->uid == "0") {
    print w_msg::simple('error', 'Access Denied');
    return;
}

$fsid = (int)$this->reqs->id;
$page = (int)$this->reqs->page;
if ($page < 1) {
    $page = 1;
}
$limitcount = 10;

$pageurl = '/hssui/editor-plugin/';

$_media = Hooto_Data_Sql::getTable('hss_v1');

$query = $_media->select()
    ->where('uid = ?', $session->uid)
    ->order('created', 'desc')
    ->limit($limitcount, ($page - 1) * $limitcount);
$items = $_media->query($query);

$cfg = Hooto_Config_Array::get('hssui/global');
$cfg = new Hooto_Object($cfg['v1']);

foreach ($items as $key => $val) {

    $items[$key]['url'] = '/media/image/view/?id='.$val['id'];
    $items[$key]['iconsrc'] = '/media/image/view/?id='. $val['id'] .'&style=thumb';

    $srcweb = '/media/image/view/?id='. $val['id'];
    $src = $cfg->uploadDir .'/'. $val['media_dir'] .'/'. $val['media_stored'];
    
    $items[$key]['media_mime_display'] = strtoupper(substr(strrchr($val['media_mime'], '/'), 1));
            
    // 1024x1024
    if ($items[$key]['media_size'] > 1048576) {
        $items[$key]['media_size_formated'] = round($val['media_size'] / 1048576, 2).'MB';
    } else if ($val['media_size'] > 1024) {
        $items[$key]['media_size_formated'] = ceil($val['media_size'] / 1024).'KB';
    } else {
        $items[$key]['media_size_formated'] = $val['media_size'].'B';
    }
            
    // Thumbnail
    $im = preg_replace('/(.jpg|.png|.gif)/', '-thumb.jpg', $src);
    $items[$key]['ims'] = array();
    
    if (file_exists($im)) {
        $ims = getimagesize($im);
        $items[$key]['ims']['thumb'] = array(
            'w' => $ims[0], 'h' => $ims[1], 
            'name' => 'Thumbnail',
            'value' => $srcweb.'&style=thumb'
        );
    }
    
    // Medium
    $im = preg_replace('/(.jpg|.png|.gif)/', '-medium.jpg', $src);
    if (file_exists($im)) {
        $ims = getimagesize($im);
        $items[$key]['ims']['medium'] = array(
            'w' => $ims[0], 'h' => $ims[1], 
            'name' => 'Medium',
            'value' => $srcweb.'&style=medium'
        );
    }
    
    // Large
    $im = preg_replace('/(.jpg|.png|.gif)/', '-large.jpg', $src);
    if (file_exists($im)) {
        $ims = getimagesize($im);
        $items[$key]['ims']['large'] = array(
            'w' => $ims[0], 'h' => $ims[1], 
            'name' => 'Large',
            'value' => $srcweb.'&style=large'
        );
    }
    
    // Full size
    if (file_exists($src)) {
        $ims = getimagesize($src);
        $items[$key]['ims']['full'] = array(
            'w' => $ims[0], 'h' => $ims[1], 
            'name' => 'Full',
            'value' => $srcweb.'&style=full'
        );
    }
    
    $items[$key]['srcweb'] = $srcweb.'&style=full';
    
}


$query = $query->select("count(id) as count")->reset(array('limit', 'order'));

$feed = $_media->query($query);
$count = 0;
if (isset($feed[0]) && isset($feed[0]['count'])) {
    $count = $feed[0]['count'];
}
$pager = hwl_pager::get($page, $count, $limitcount);


$urlpager = $this->siteurl('/editor-plugin?', $this->reqs->ins);

      
?>

<script>

function mediaShow(id) {
    document.getElementById('media-'+ id).className = 'displaynone';
    document.getElementById('media-button-'+ id).className = 'displaynone';
    document.getElementById('media-show-'+ id).className = '';
    document.getElementById('media-show-button-'+ id).className = '';
}

function mediaHide(id) {
    document.getElementById('media-'+ id).className = '';
    document.getElementById('media-button-'+ id).className = '';
    document.getElementById('media-show-'+ id).className = 'displaynone';
    document.getElementById('media-show-button-'+ id).className = 'displaynone';
}

function mediaInsert(id) {

    var text = null;
    src = document.getElementById('media_image_srcweb_'+ id).value;
    obj = document.getElementsByName('media_image_size_'+ id);
    obj_align = document.getElementsByName('media_image_align_'+ id);
    align_style = 'alignnone';
    
    for (i = 0; i < obj.length; i++) { 
        if (obj[i].checked) {
            for (j = 0; j < obj_align.length; j++) { 
                if (obj_align[j].checked) {
                    align_style = obj_align[j].value;
                }    
            }    

            if (align_style != 'alignnone') {
                text = '<a href="'+ src +'" target="_blank"><img class="'+align_style+'" src="'+ obj[i].value +'" /\></a>';
            } else {
                text = '<a href="'+ src +'" target="_blank"><img src="'+ obj[i].value +'" /\></a>';
            }
        }
    }
    
    if (text == null) {
        alert('Please select the size');
    } else {
        mediaInsertParent(text);
    }
}

function mediaInsertParent(text) {

    if (window.opener) {
        window.opener.mediaplugin.insert(text);
    } else {
        alert("window.opener has closed");
    }
    window.close();
}
</script>

<div class="editorPluginBody">
<table class="tblist" width="100%" border="0" cellpadding="0" cellspacing="0" >
	<tr>
		<td align="left"><b>Name</b></td>
		<td align="right"></td>
	</tr>
    <?php 
    $even = 'Even';
    foreach ($items as $item) {
    ?> 
	<tr class="draggAble<?php echo $even = ($even == 'Even') ? 'Odd' : 'Even';?>">
		<td align="left">
	        <div id="media-<?=$item['id']?>">
	            <a href="javascript:;" onclick="mediaShow(<?=$item['id']?>)"><img src="<?=$item['iconsrc']?>" width="40px" height="40px" /> <?=$item['media_name']?></a>
	        </div>
	        <div id="media-show-<?=$item['id']?>" class="displaynone">
	            <div class="editorPluginArea">
	                <div class="editorPluginAreaL">
	                    <img src="<?=$item['iconsrc']?>" />
	                </div>
	                <div class="editorPluginAreaR">
	                    <h3><?=$item['media_name']?></h3>
	                    <div><?=$item['media_mime']?> (<?=$item['media_size_formated']?>)</div><br />
	                    <div><?=$item['created']?></div><br />
	                    <h3>Alignment</h3>
	                    <ul class="editorPluginAlign">
	                        <li>
	                            <input type="radio" name="media_image_align_<?=$item['id']?>" value="alignnone" checked="checked" /> 
	                            <img src="/_hssui/img/align-none.png" border="0" /> <b>None</b>
	                        </li>
	                        <li>
	                            <input type="radio" name="media_image_align_<?=$item['id']?>" value="alignleft" /> 
	                            <img src="/_hssui/img/align-left.png" border="0" /> <b>Left</b>
	                        </li>
	                        <li>
	                            <input type="radio" name="media_image_align_<?=$item['id']?>" value="aligncenter" /> 
	                            <img src="/_hssui/img/align-center.png" border="0" /> <b>Center</b>
	                        </li>
	                        <li>
	                            <input type="radio" name="media_image_align_<?=$item['id']?>" value="alignright" /> 
	                            <img src="/_hssui/img/align-right.png" border="0" /> <b>Right</b>
	                        </li>
	                    </ul>
	                    
	                    <div class="clear_both" />
	                    <?php if (count($item['ims']) > 0) { ?>
	                    <h3>Size</h3>
	                    <ul class="editorPluginSize">
	                        <?php foreach ($item['ims'] as $key => $value) { ?>
	                        <li>
	                            <input type="radio" name="media_image_size_<?=$item['id']?>" value="<?=$value['value']?>" /> 
	                            <span><b><?=$value['name']?></b> <br />(<?=$value['w']?>x<?=$value['h']?>)</span>
	                        </li>
	                        <?php } ?>
	                    </ul>
	                    <input type="hidden" id="media_image_srcweb_<?=$item['id']?>" value="<?=$item['srcweb']?>" />
	                    <input type="button" onclick="mediaInsert(<?=$item['id']?>)" class="input_button" value="Insert media" />
	                    <?php } ?>
	                </div>
	            </div>
	        </div>
		</td>
		<td align="right">
		    <div id="media-button-<?=$item['id']?>">
		        <a href="javascript:;" onclick="mediaShow(<?=$item['id']?>)" class="aButtonText">Show</a>
		    </div>
		    <div id="media-show-button-<?=$item['id']?>" class="displaynone">
		        <a href="javascript:;" onclick="mediaHide(<?=$item['id']?>)" class="aButtonText">Hide</a>
		    </div>
		</td>
	</tr>
	<?php } ?> 
</table>


<ul class="pager">
    <li class="info">Items <?=$pager['itemFrom']?> - <?=$pager['itemTo']?> </li>
    
    <?php if (isset($pager['first'])) { ?>
    <li><a href="<?=$urlpager?>&page=<?=$pager['first']?>">First</a></li>
    <?php } ?>
    
    <?php if (isset($pager['previous'])) { ?>
    <li><a href="<?=$urlpager?>&page=<?=$pager['previous']?>">Previous</a></li>
    <?php } ?>
    
    <?php foreach ($pager['list'] as $page): ?>
    <li><a href="<?=$urlpager?>&page=<?=$page['page']?>" <?php if ($page['isCurrent']) {echo 'class="current"';}?>><?=$page['page']?></a></li>
    <?php endforeach; ?>
    
    <?php if (isset($pager['next'])) { ?>
    <li><a href="<?=$urlpager?>&page=<?=$pager['next']?>">Next</a></li>
    <?php } ?>
    
    <?php if (isset($pager['last'])) { ?>
    <li><a href="<?=$urlpager?>&page=<?=$pager['last']?>">Last</a></li>
    <?php } ?>
</ul>

</div>

<?php

if ($this->id > 0) {
    echo '<script>mediaShow("'.$this->id.'")</script>';
}

?>
