<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "1. Config include<br>";
require_once '/Applications/XAMPP/xamppfiles/htdocs/just_aj/includes/config.php';

echo "2. DB include<br>";
require_once '/Applications/XAMPP/xamppfiles/htdocs/just_aj/includes/db.php';

echo "3. Functions include<br>";
require_once '/Applications/XAMPP/xamppfiles/htdocs/just_aj/includes/functions.php';

echo "4. Header include<br>";
require_once '/Applications/XAMPP/xamppfiles/htdocs/just_aj/includes/header.php';

echo "All includes successful!<br>";
?>
