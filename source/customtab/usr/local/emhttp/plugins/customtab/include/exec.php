<?PHP

function read_json_file($file) {
  $json = json_decode(@file_get_contents($file),true);
  if ( ! $json ) {
    $json = array();
  }
  return $json;
}

function create_tab_settings($index,$settings,$addFlag = false) {
  if ( ! $settings['tabURL'] && ! $settings['page'] && ! $addFlag ) {
    return;
  }
  $pageFiles = json_decode(file_get_contents("/tmp/customtab/pagefiles.json"),true);
  $o = "<strong><font size='2'>Custom Tab Settings #$index</font></strong>&nbsp;&nbsp;<img style='cursor:pointer;' src='/plugins/customtab/images/delete.png' width='30px' onclick='deleteTab($index);'><br>";
  $o .= "<dd>";
  $o .= "<dt>Custom URL or Built-In Page:</dt>";
  $o .= "<dl><select id='selectPage$index' class='narrow setting' onchange='enablePage(&quot;$index&quot;)';>";
/*   if ( $settings['selectPage'] == "page" ) {
    $selectPage = 'selected'; $URLoptions = "disabled";
  } else {
    $selectURL = 'selected'; $pageoptions = "disabled";
  } */
	switch ($settings['selectPage']) {
		case 'page':
			$selectPage = 'selected';
			$URLoptions = 'disabled';
			break;
		case 'url': 
			$selectURL = 'selected';
			$pageoptions = 'disabled';
			break;
		case 'bookmark':
			$selectBookmark = 'selected';
			$pageoptions = 'disabled';
			$bookmarkoptions = 'disabled';
			break;
		case 'tab':
			$selectTab = 'selected';
			$pageoptions = 'disabled';
			$bookmarkoptions = 'disabled';
	}
  $o .= "<option value='url' $selectURL>URL</option>";
  $o .= "<option value='page' $selectPage>Built-In Page</option>";
	$o .= "<option value='bookmark' $selectBookmark>URL (Open Same Tab)</option>";
	$o .= "<option value='tab' $selectTab>URL (Open New Tab) * popups MUST be enabled</option>";
  $o .= "<select>";
  $o .= "</dl>";
  $o .= "<dt>Tab Name:</dt>";
  $o .= "<dl><input type='text' id='customtabname$index' class='narrow setting' maxlength='20' value='{$settings['name']}' placeholder='Custom$index'></dl>";
  $o .= "<dt>Full Tab Name:</dt>";
  $o .= "<dl><input type='text' id='fullname$index' class='narrow setting' maxlength='80' value='{$settings['fullname']}'></dl>";
  $o .= "<dt>URL:</dt>";
  $o .= "<dl><input type='text' id='url$index' class='setting url$index' $URLoptions value='{$settings['tabURL']}'></dl>";
  $o .= "<dt>Built-In Page File:</dt>";
  $o .= "<dl>";
  $o .= "<select id='page$index' class='setting page$index' $pageoptions>";
  $o .= "<option value=''>Select a page file</option>";
  foreach ($pageFiles as $page) {
    $o .= "<option value='{$page['CustomTabSource']}'>{$page['Title']} {$page['CustomTabSource']}</option>";
  }
  $o .= "</select></dl>";  
  $o .= "<dt>Width:</dt>";
  $o .= "<dl><input type='number' id='width$index' class='narrow setting url$index bookmark$index' $URLoptions $bookmarkoptions value='{$settings['width']}' placeholder='1280'></dl>";
  $o .= "<dt>Height:</dt>";
  $o .= "<dl><input type='number' id='height$index' class='narrow setting url$index bookmark$index' $URLoptions $bookmarkoptions value='{$settings['height']}' placeholder='500'></dl>";
  $o .= "<dt>Azure / Gray Icon: (See <a href='http://fontawesome.io/cheatsheet/' target='_blank'>HERE</a>)</dt>";
  $o .= "<dl><input type='text' id='fontawesome$index' class='narrow setting' value='{$settings['fontawesome']}' placeholder='f111'></dl>";
	$o .= "<dt>Tab Position (see <a href='https://lime-technology.com/forums/topic/57109-plugin-custom-tab/'>HERE</a>)</dt>";
	$o .= "<dl><input type='number' id='position$index' class='narrow setting' value='{$settings['position']}' placeholder='Automatic'></dl>";
  $o .= "</dd>";
  $o .= "<hr>";
  $o .= "<script>$('#page$index').val('{$settings['page']}');</script>";

  
  return $o;
}

function make_tabs($settings,$flag = false) {
  $pageFiles = json_decode(file_get_contents("/tmp/customtab/pagefiles.json"),true);
  $index = 0;
  foreach ($settings as $tab) {
    $set = $flag ? $tab : tabArray($tab);
    $o .= create_tab_settings($index,$set);
    $index++;
  }
  return $o;
}

function tabArray($tab) {
  $set['name'] = $tab[0];
  $set['fullname'] = $tab[1];
  $set['tabURL'] = $tab[2];
  $set['width'] = $tab[3];
  $set['height'] = $tab[4];
  $set['fontawesome'] = $tab[5];
  $set['selectPage'] = $tab[6];
  $set['page'] = $tab[7];
	$set['position'] = $tab[8];
  
  return $set;
}
function disableAddTab() {
  return "<script>$('#newtab').prop('disabled',true);</script>";
}
function enableAddTab() {
  return "<script>$('#newtab').prop('disabled',false);</script>";
}

switch ($_POST['action']) {
  case 'get_tabs_init':
    exec("find /usr/local/emhttp/plugins -name '*.page'",$pageFiles);
    foreach ($pageFiles as $page) {
      $file = explode("\n",trim(file_get_contents($page)));
      unset($pageINI);
      foreach ($file as $line) {
        if ( (trim($line) == "---") || (trim($line) == "----") ) {
          break;
        }
        $pageINI .= "$line\n";
      }
      $pageVars = parse_ini_string($pageINI);
      if ( $pageVars['Type'] == "menu" ) {
        continue;
      }
      $pageVars['CustomTabSource'] = $page;
      $allVars[] = $pageVars;
    }
    exec("mkdir -p /tmp/customtab");
    file_put_contents("/tmp/customtab/pagefiles.json",json_encode($allVars,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    $o = make_tabs(read_json_file("/boot/config/plugins/customtab/customtab.json"),true);
    echo $o;
    break;
  case 'add_tab':
    $settings = json_decode($_POST['settings']);
    $index = 0;
    foreach ($settings as $tab) {
      $set = tabArray($tab);
      if ( ! $set['tabURL'] && ! $set['page'] ) {
        continue;
      }
      $o .= create_tab_settings($index,$set);
      $index++;
    }
    $o .= create_tab_settings($index,array(),true);
    $o .= disableAddTab();
    echo $o;
    break;
  case 'show_tabs':
    $settings = json_decode($_POST['settings']);
    
    $o = make_tabs($settings);
    $o .= enableAddTab();
    if ( ! $o ) { $o = " "; }
    echo $o;
    break;
  case 'apply':
    $settings = json_decode($_POST['settings']);
    foreach ($settings as $set) {
      $tmp = tabArray($set);
      if ( ! $tmp['tabURL'] && ! $tmp['page']) {
        continue;
      }
      $newSettings[] = $tmp;
    }
    file_put_contents("/boot/config/plugins/customtab/customtab.json",json_encode($newSettings,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    exec("/usr/local/emhttp/plugins/customtab/scripts/createTab.php");
    echo "ok";
    break;
}

?>