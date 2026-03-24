<?php
$files = glob('../app/src/main/res/layout/*.xml');
$count = 0;

foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);
    
    $new_content = preg_replace('/android:background="#(FAFAFA|F5F7FA|F0F2F5|F8F9FA|F9FAFB|F5F6FA|EDE7F6|E0E0E0)"/i', 'android:background="@color/app_background"', $content);
    $new_content = preg_replace('/app:cardBackgroundColor="(@android:color\/white|#FFFFFF|#FAFAFA|#F0F4FC|#F4F8FE|#F2FBF8)"/i', 'app:cardBackgroundColor="@color/card_background"', $new_content);
    $new_content = preg_replace('/android:textColor="#(1A237E)"/i', 'android:textColor="@color/text_header"', $new_content);
    $new_content = preg_replace('/android:textColor="#(000000|212121|424242|333333|1A1A2E|1A1A1A)"/i', 'android:textColor="@color/text_primary"', $new_content);
    $new_content = preg_replace('/android:textColor="#(757575|666666|888888|999999|555555|4A4A4A|777777|5D4037)"/i', 'android:textColor="@color/text_secondary"', $new_content);

    if ($content !== $new_content) {
        file_put_contents($file, $new_content);
        $count++;
    }
}

echo "Updated $count layout files.\n";
?>
