<?php
$count = 0;
$files = glob('../app/src/main/res/drawable/*.xml');
foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);
    
    $new_content = preg_replace('/<solid\s+android:color="#FFFFFF"\s*\/>/', '<solid android:color="@color/card_background" />', $content);
    
    if ($content !== $new_content) {
        file_put_contents($file, $new_content);
        $count++;
    }
}
            
echo "Updated $count drawable files.\n";
?>
