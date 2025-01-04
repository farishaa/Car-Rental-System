<?php
include 'home.php';

$backup_dir = 'backups/';
$files = glob($backup_dir . '*.sql'); 
usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a); 
});

//import database
if (count($files) > 0) {
    $latest_backup = $files[0];  
    $command = "\"C:\\xampp\\mysql\\bin\\mysql\" -h $servername -u $username --password=$password $dbname < \"$latest_backup\"";


$output = null;
$return_var = null;
exec($command, $output, $return_var);

if ($return_var === 0) {
    echo "Database restored successfully from: $latest_backup";
} else {
    echo "Error restoring database.";
}
} else {
echo "No backup files found.";
}

?>
