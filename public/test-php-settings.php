<?php
// Temporary PHP settings test file
// DELETE THIS FILE AFTER TESTING

header('Content-Type: text/plain');
echo "PHP Settings Test\n";
echo "=================\n\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "max_input_time: " . ini_get('max_input_time') . "\n";
echo "\nIf you see 800M for upload and post sizes, the settings are active!\n";
