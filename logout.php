<?php
require_once __DIR__ . '/config/conexion.php';
$_SESSION = [];
session_regenerate_id(true);
flash('success', 'You have logged out successfully.');
redirect('index.php');
