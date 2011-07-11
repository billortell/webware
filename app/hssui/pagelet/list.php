<?php 
$session = user_session::getInstance();
if ($session->uid == "0") {
    print w_msg::simple('error', 'Access Denied');
    return;
}
$msg = '';
$fsid = (int)$this->reqs->id;
$page = (int)$this->reqs->page;
if ($page < 1) {
    $page = 1;
}
$limitcount = 20;

$cfg = Hooto_Config_Array::get('hssui/global');
$cfg = new Hooto_Object($cfg['v1']);

$_media = Hooto_Data_Sql::getTable('hss1');

$apiv1 = new hssui_apiv1();


if (isset($this->reqs->delid)) {

    try {
        $item = $_media->fetch($this->reqs->delid);

        if (!isset($item['id'])) {
            throw new Exception("File Can Not be Found");
        }
        
        if ($item['uid'] !== $session->uid) {
            throw new Exception("Access Denied");
        }
        
        $src = $cfg->uploadDir .'/'. $item['media_dir'] .'/'. $item['media_stored'];
        
        if (!$apiv1->deleteFile($src)) {
            //throw new Exception("Error");
        }
        
        $_media->delete($item['id']);
                    
        $msg = w_msg::simple('success', 'Success');
        
    } catch (Exception $e) {
        $msg = w_msg::simple('error', $e->getMessage());
    }
}

$query = $_media->select()
    ->where('uid = ?', $session->uid)
    ->order('created', 'desc')
    ->limit($limitcount, ($page - 1) * $limitcount);
$items = $_media->query($query);


foreach ($items as $key => $val) {

    $items[$key]['created_formated'] = date("Y-m-d H:i:s", strtotime($val['created']));
    
    $items[$key]['src_thumb'] = '/media/image/view/?id='. $val['id'] .'&style=thumb';
    
    $items[$key]['media_mime_display'] = strtoupper(substr(strrchr($val['media_mime'], '/'), 1));
            
    // 1024x1024
    if ($items[$key]['media_size'] > 1048576) {
        $items[$key]['media_size_formated'] = round($val['media_size'] / 1048576, 2).'MB';
    } else if ($val['media_size'] > 1024) {
        $items[$key]['media_size_formated'] = ceil($val['media_size'] / 1024).'KB';
    } else {
        $items[$key]['media_size_formated'] = $val['media_size'].'B';
    }
    
    $src = $cfg->uploadDir .'/'. $val['media_dir'] .'/'. $val['media_stored'];
    $items[$key]['resizes'] = $apiv1->getImageResizes($src);
    
    $items[$key]['srcweb'] = '/media/image/view/?id='. $val['id'];
}

$query = $query->select("count(id) as count")->reset(array('limit', 'order'));

$feed = $_media->query($query);
$count = 0;
if (isset($feed[0]) && isset($feed[0]['count'])) {
    $count = $feed[0]['count'];
}
$pager = hwl_pager::get($page, $count, $limitcount);


$urlpager = $this->siteurl('/list?', $this->reqs->ins);

echo $msg;
?>

<div class="navindex_title">Manage Files</div>

<table class="tblist" width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
	    <td></td>
	    <td><b>Name</b></td>
        <td><b>Type</b></td>
		<td align="right"><b>Size</b></td>
		<td align="right"><b>Created</b></td>
		<td align="right"></td>
	</tr>
    <?php 
    $even = 'Even';
    foreach ($items as $item) {
    ?> 
	<tr class="draggAble<?php echo $even = ($even == 'Even') ? 'Odd' : 'Even';?>">
		<td>
		    <div id="media-image-<?=$item['id']?>">
		        <a href="javascript:mediaShow(<?=$item['id']?>)"><img src="<?=$item['src_thumb']?>" width="40px" height="40px" /></a>
		    </div>
		    <div id="media-image-show-<?=$item['id']?>" class="displaynone">
		        <a href="javascript:mediaHide(<?=$item['id']?>)"><img src="<?=$item['src_thumb']?>" /></a>
		    </div>
		</td>
		<td>
		    <b><?=$item['media_name']?></b>
            <div id="media-show-<?=$item['id']?>" class="displaynone">
                <div><?=$item['media_mime']?> (<?=$item['media_size_formated']?>)</div>
	            <div><?=$item['created']?></div>
                <?php if (isset($item['resizes']) && is_array($item['resizes']) && count($item['resizes']) > 0) { ?>
                <div>
                    <?php foreach ($item['resizes'] as $key => $val) { ?>
                    <span>
                        <b><?=$val['name']?></b>: <?=$val['width']?>x<?=$val['height']?> <a href="<?=$item['srcweb']?>&style=<?=$key?>" target="_blank">link</a> 
                    </span>
                    <?php } ?>
                <?php } ?>
                </div>
            </div>
		</td>
        <td><?=$item['media_mime_display']?></td>
        <td align="right"><?=$item['media_size_formated']?></td>
		<td align="right"><?=$item['created_formated']?></td>
		<td align="right">
		    <a href="/hssui/list?delid=<?=$item['id']?>" onclick="return confirm('Are you sure you want to delete?')">Delete</a>
		    <span id="media-button-<?=$item['id']?>">
		        <a href="javascript:;" onclick="mediaShow(<?=$item['id']?>)" class="aButtonText">Show</a>
		    </span>
		    <span id="media-show-button-<?=$item['id']?>" class="displaynone">
		        <a href="javascript:;" onclick="mediaHide(<?=$item['id']?>)" class="aButtonText">Hide</a>
		    </span>
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

<script>

function mediaShow(id) {
    document.getElementById('media-image-'+ id).className = 'displaynone';
    document.getElementById('media-button-'+ id).className = 'displaynone';
    document.getElementById('media-show-'+ id).className = '';
    document.getElementById('media-image-show-'+ id).className = '';
    document.getElementById('media-show-button-'+ id).className = '';
}

function mediaHide(id) {
    document.getElementById('media-image-'+ id).className = '';
    document.getElementById('media-button-'+ id).className = '';
    document.getElementById('media-show-'+ id).className = 'displaynone';
    document.getElementById('media-image-show-'+ id).className = 'displaynone';
    document.getElementById('media-show-button-'+ id).className = 'displaynone';
}

</script>

