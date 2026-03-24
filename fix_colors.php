<?php
$replacements = [
    '"#F5F5F5"' => '"@color/app_background"',
    '"#1A1A1A"' => '"@color/text_header"',
    '"#333333"' => '"@color/text_primary"',
    '"#888888"' => '"@color/text_secondary"'
];

$count = 0;
$files = glob('../app/src/main/res/layout/*.xml');
foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);
    
    $new_content = strtr($content, $replacements);
        
    if ($content !== $new_content) {
        file_put_contents($file, $new_content);
        $count++;
    }
}
            
echo "Updated $count layout files.\n";
?>
