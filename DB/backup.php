<?php
include 'home.php';

$backup_file = 'backups/' . $dbname . '_backup_' . date('Y-m-d_H-i-s') . '.sql';

$command = "\"C:\\xampp\\mysql\\bin\\mysqldump\" -h $servername -u $username --password=$password $dbname > \"$backup_file\"";

$output = null;
$return_var = null;
exec($command, $output, $return_var);

if ($return_var === 0) {
    echo "Database backup created successfully: $backup_file";
} else {
    echo "Error creating database backup.";
}
?>