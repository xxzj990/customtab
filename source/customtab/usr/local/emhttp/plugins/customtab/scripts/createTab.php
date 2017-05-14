#!/usr/bin/php
<?PHP
exec("rm -rf /usr/local/emhttp/plugins/customtabtemp");

if ( ! is_file("/boot/config/plugins/customtab/customtab.json") ) {
  return;
}
$config = json_decode(file_get_contents("/boot/config/plugins/customtab/customtab.json"),true);
$index = 0;
$location = 91;
foreach ($config as $cfg) {  
  $name = ucfirst($cfg['name']);
  $fullname = $cfg['fullname'];
  $tabURL = $cfg['tabURL'];
  $width = $cfg['width'];
  $height = $cfg['height'];
  if ( ! $name ) {
    $name = "Custom$index";
  }
  $name = str_replace("'","",$name);
  $name = str_replace('"',"",$name);
  $name = str_replace(" ","",$name);
  if (! preg_match('/^[a-z]/i', $name)) {
    $name = "A$name";
  }
  $width = intval($width);
  $height = intval($height);

  $width = $width ? $width : "1280";
  $height = $height ? $height : "500";
  $mainPage = "Menu='Tasks:9$location'\nName='$name'\nType='xmenu'\nTabs='true'\n";
  $page = "Menu='$name'\nTitle='$fullname'\n---\n<iframe src='$tabURL' height='$height' width='$width'></iframe>\n";
  exec("mkdir -p /usr/local/emhttp/plugins/customtabtemp");
  file_put_contents("/usr/local/emhttp/plugins/customtabtemp/$name.page",$mainPage);
  file_put_contents("/usr/local/emhttp/plugins/customtabtemp/$name".mt_rand().".page",$page);
  $index++;
  $location++;
}
?>