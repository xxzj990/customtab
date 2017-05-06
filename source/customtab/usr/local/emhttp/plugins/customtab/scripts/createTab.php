#!/usr/bin/php
<?PHP
$cfg = parse_ini_file("/boot/config/plugins/customtab/customtab.cfg");
print_r($cfg);
$name = $cfg['name'];
$fullname = $cfg['fullname'];
$tabURL = $cfg['tabURL'];
$width = $cfg['width'];
$height = $cfg['height'];

$width = $width ? $width : "1280";
$height = $height ? $height : "500";
exec("rm -rf /usr/local/emhttp/plugins/customtabtemp");
$mainPage = "Menu='Tasks'\nName='$name'\nType='xmenu'\nTabs='true'\n";
$page = "Menu='$name'\nTitle='$fullname'\n---\n<iframe src='$tabURL' height='$height' width='$width'></iframe>\n";
exec("mkdir -p /usr/local/emhttp/plugins/customtabtemp");
file_put_contents("/usr/local/emhttp/plugins/customtabtemp/$name.page",$mainPage);
file_put_contents("/usr/local/emhttp/plugins/customtabtemp/SquidCustomCreatedTabPlugin.page",$page);
?>