Daemon log:

<?php
$display = file('transcode_daemon.log');
$count = count($display);
echo '<pre>';
for($i=$count - 20; $i<$count; $i++){
  echo $display[$i];
}
echo '</pre>';
?>

Transcode Log:

<?php
$display = file('transcode.log');
$count = count($display);
echo '<pre>';
for($i=$count - 20; $i<$count; $i++){
  echo $display[$i];
}
echo '</pre>';
?>

