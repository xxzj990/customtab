#!/usr/bin/php
<?PHP
function create_ini_file($settings,$mode=false) {
  if ( $mode ) {
    $keys = array_keys($settings);

    foreach ($keys as $key) {
      $iniFile .= "[$key]\r\n";
      $entryKeys = array_keys($settings[$key]);
      foreach ($entryKeys as $entry) {
        $iniFile .= $entry.'="'.$settings[$key][$entry].'"'."\r\n";
      }
    }
  } else {
    $entryKeys = array_keys($settings);
    foreach ($entryKeys as $entry) {
      $iniFile .= $entry.'="'.$settings[$entry].'"'."\r\n";
    }
  }
  return $iniFile;
}

###############################################################################

exec("rm -rf /usr/local/emhttp/plugins/customtabtemp");

if ( ! is_file("/boot/config/plugins/customtab/customtab.json") ) {
  return;
}
$config = json_decode(file_get_contents("/boot/config/plugins/customtab/customtab.json"),true);
$index = 0;
$location = 91;
foreach ($config as $cfg) {
  if ( $cfg['selectPage'] == 'page' ) {
    if ( ! $cfg['page'] ) {
      continue;
    }
    if ( ! file_exists($cfg['page']) ) {
      continue;
    }
    $name = ucfirst($cfg['name']);
    $fullname = $cfg['fullname'];
    $pageFile = $cfg['page'];
    $fontawesome = $cfg['fontawesome'];
	$pageLocation = $cfg['position'] ?: "9$location";
    if ( ! $name ) {
      $name = "Custom$index";
    }
    $name = str_replace("'","",$name);
    $name = str_replace('"',"",$name);
    $name = str_replace(" ","",$name);
    if (! preg_match('/^[a-z]/i', $name)) {
      $name = "A$name";
    }
    $fontawesome = $fontawesome ? $fontawesome : "f111";
    $fullPageFile = explode("\n",trim(file_get_contents($pageFile)));
    unset($pageINI);
    unset($code);
    $codeFlag = false;
    foreach ($fullPageFile as $line) {
      if ( (trim($line) == "---") || (trim($line) == "----") ) {
        $codeFlag = true;
      }
      if ( $codeFlag ) {
        $code .= "$line\n";
      } else {
        $pageINI .= "$line\n";
      }
    }
    $pageVars = parse_ini_string($pageINI);
     
    $pageVars['CustomTabSource'] = $page;
    $pageVars['Menu'] = "Tasks:$pageLocation";
      
    $mainPage = create_ini_file($pageVars)."---\n";
     
    exec("mkdir -p /usr/local/emhttp/plugins/customtabtemp");
    $mainFileName = "$name";
    $dupeTest = exec("find /usr/local/emhttp/plugins -name '$name.page'");
    if ( $dupeTest ) {
      $mainFileName = "{$name}_";
    }
    $mainPage = "Menu='Tasks:$pageLocation'\nName='{$name}'\nType='xmenu'\nTabs='true'\nCode='$fontawesome'\n";
    $page = "Menu='$mainFileName'\nTitle='$fullname'\n---\n$code";
    exec("mkdir -p /usr/local/emhttp/plugins/customtabtemp");
    file_put_contents("/usr/local/emhttp/plugins/customtabtemp/$mainFileName.page",$mainPage);
    file_put_contents("/usr/local/emhttp/plugins/customtabtemp/$name".mt_rand().".page",$page);
    $index++;
    $location++;
  } elseif ($cfg['selectPage'] == 'url') {
    $name = ucfirst($cfg['name']);
    $fullname = $cfg['fullname'];
    $tabURL = $cfg['tabURL'];
    $width = $cfg['width'];
    $height = $cfg['height'];
    $fontawesome = $cfg['fontawesome'];
	$pageLocation = $cfg['position'] ?: "9$location";
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
    $fontawesome = $fontawesome ? $fontawesome : "f111";
    $dupeTest = exec("find /usr/local/emhttp/plugins -name '$name.page'");
    if ( $dupeTest ) {
      $name = "{$name}_";
    }
    $mainPage = "Menu='Tasks:$pageLocation'\nName='$name'\nType='xmenu'\nTabs='true'\nCode='$fontawesome'\n";
    $page = "Menu='$name'\nTitle='$fullname'\n---\n<iframe src='$tabURL' height='$height' width='$width' onload='this.contentWindow.focus();'></iframe>\n";
    exec("mkdir -p /usr/local/emhttp/plugins/customtabtemp");
    file_put_contents("/usr/local/emhttp/plugins/customtabtemp/$name.page",$mainPage);
    file_put_contents("/usr/local/emhttp/plugins/customtabtemp/$name".mt_rand().".page",$page);
    $index++;
    $location++;
  } elseif ($cfg['selectPage'] == 'bookmark' || $cfg['selectPage'] == 'tab') {
	  $pageLocation = $cfg['position'] ?: "9$location";
		$name = ucfirst($cfg['name']);
    $fullname = $cfg['fullname'];
    $tabURL = $cfg['tabURL'];
    $width = $cfg['width'];
    $height = $cfg['height'];
    $fontawesome = $cfg['fontawesome'];
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
    $fontawesome = $fontawesome ? $fontawesome : "f111";
    $dupeTest = exec("find /usr/local/emhttp/plugins -name '$name.page'");
    if ( $dupeTest ) {
      $name = "{$name}_";
    }
    $mainPage = "Menu='Tasks:$pageLocation'\nName='$name'\nType='xmenu'\nTabs='true'\nCode='$fontawesome'\n";
		if ($cfg['selectPage'] == 'bookmark') {
			$page = "Menu='$name'\nTitle='$fullname'\n---\n<script>window.location = '$tabURL';</script>";
		} else {
			$page = "Menu='$name'\nTitle='$fullname'\n---\n<script>var newWin = window.open('$tabURL','_blank');if (!newWin || newWin.closed || typeof newWin.closed=='undefined'){alert('Pop Up Blocked.  You must allow popups for this feature of Custom Tab to work');}history.back();</script>";
		}
    exec("mkdir -p /usr/local/emhttp/plugins/customtabtemp");
    file_put_contents("/usr/local/emhttp/plugins/customtabtemp/$name.page",$mainPage);
    file_put_contents("/usr/local/emhttp/plugins/customtabtemp/$name".mt_rand().".page",$page);
    $index++;
    $location++;		
  }
}
?>