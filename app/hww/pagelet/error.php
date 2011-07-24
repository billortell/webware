
<style type="text/css">
.system_error {
	border:1px solid #990000;
	padding:10px 20px;
	margin:10px;
	font: 13px/1.4em verdana;
	background: #fff;
}
code.source {
	white-space: pre;
	background: #fff;
	padding: 1em;
	display: block;
	margin: 1em 0;
	border: 1px solid #bedbeb;
}
.system_error .box {
	margin: 1em 0;
	background: #ebf2fa;
	padding: 10px;
	border: 1px solid #bedbeb;
}
.highlight_line {background: #ffc;}
</style>

<div class="system_error">
	<b style="color: #990000"><?php echo $title; ?></b>
	<p><?php echo $error; ?></p>
	<p><?php echo "File: $file, Line: $line"; ?></p>
</div>

