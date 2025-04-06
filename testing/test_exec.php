<?php
$output = shell_exec('echo PHP shell_exec works as user: $(whoami)');
echo "<pre>$output</pre>";
?>