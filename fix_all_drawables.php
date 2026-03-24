<?php
$files = glob('../app/src/main/res/drawable/*.xml');
$count = 0;

foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);
    
    $new_content = preg_replace('/<solid\s+android:color="(@android:color\/white|#FFFFFF|#ffffff|#FAFAFA|#F5F5F5|#F0F2F5)"\s*\/>/i', '<solid android:color="@color/card_background" />', $content);
    $new_content = preg_replace('/<solid\s+android:color="(#E0E0E0|#F8F9FA|#F9FAFB|#F5F6FA|#EDE7F6)"\s*\/>/i', '<solid android:color="@color/app_background" />', $new_content);

    if ($content !== $new_content) {
        file_put_contents($file, $new_content);
        $count++;
    }
}

echo "Updated $count drawable files.\n";
?>
