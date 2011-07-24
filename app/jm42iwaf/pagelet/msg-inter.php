<?php

if (!isset($msg) && isset($this->msg)) {
    $msg = $this->msg;
}

if (isset($msg)) {
    switch ($msg['type']) {
        case 'error':
            $msg_bg = '#ffebe8';
            $msg_bc= '#cc0000';
            break;
        default :
            $msg_bg = '#f0f8ff';
            $msg_bc= '#c6d9e9';
   }
?>

<style>
.msgitem {
    margin: 5px 0px 5px 0px; 
    background-color: <?php echo $msg_bg; ?>; 
    border: <?php echo $msg_bc; ?> 2px solid;
}
.msgitembody {font-weight: bold;}
</style>

<div class="wapper">
<table class="msgitem" width="100%" border="0" cellspacing="0" cellpadding="2px">
    <tr>
      	<td align="center" width="30px">
      	    <img src="/_default/img/<?=$msg['type']?>-small.png" border="0" />
      	</td>
      	<td class="msgitembody">
      	    <?=$msg['body']?>
      	    <ul>
      	        <?php foreach ($msg['links'] as $link): ?> 
      	        <li>&#8250; <a href="<?=$link['url']?>"><?=$link['title']?></a></li>
      	        <?php endforeach; ?>
      	    </ul>
      	</td>
    </tr>
</table>
</div>

<?php } ?>
