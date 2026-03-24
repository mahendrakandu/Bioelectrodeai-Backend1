<?php
$files = glob('../app/src/main/res/layout/*.xml');
$count = 0;

foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);
    
    $new_content = preg_replace('/android:background="#(FAFAFA|F5F7FA|F0F2F5|fafafa|f5f7fa|f0f2f5)"/', 'android:background="@color/app_background"', $content);
    $new_content = preg_replace('/android:background="(@android:color\/white|#FFFFFF|#ffffff)"/', 'android:background="@color/card_background"', $new_content);
    $new_content = preg_replace('/app:cardBackgroundColor="(@android:color\/white|#FFFFFF|#ffffff|#FAFAFA|#fafafa)"/', 'app:cardBackgroundColor="@color/card_background"', $new_content);
    $new_content = preg_replace('/android:textColor="#(000000|212121|424242|333333)"/i', 'android:textColor="@color/text_primary"', $new_content);
    $new_content = preg_replace('/android:textColor="#(757575|666666|888888|999999)"/i', 'android:textColor="@color/text_secondary"', $new_content);

    if ($content !== $new_content) {
        file_put_contents($file, $new_content);
        $count++;
    }
}

echo "Updated $count layout files.\n";
?>
