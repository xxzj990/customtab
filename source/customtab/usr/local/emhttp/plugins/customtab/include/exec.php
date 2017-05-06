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

file_put_contents("/boot/config/plugins/customtab/customtab.cfg",create_ini_file($_POST));
exec("/usr/local/emhttp/plugins/customtab/scripts/createTab.php");


echo "ok";
?>