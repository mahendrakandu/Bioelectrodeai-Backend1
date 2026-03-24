<?php
$bg_colors = [];
$text_colors = [];
$card_bg_colors = [];

$files = glob('../app/src/main/res/layout/*.xml');
foreach ($files as $file) {
    $content = file_get_contents($file);
    
    preg_match_all('/android:background="([^"]+)"/', $content, $matches);
    foreach ($matches[1] as $match) {
        if (strpos($match, '@color/app_background') !== 0 && strpos($match, '@color/card_background') !== 0 && strpos($match, 'gradient') === false && strpos($match, '?attr') === false && strpos($match, '@drawable') === false && strpos($match, '@android:color/transparent') === false && strpos($match, 'bg_border') === false) {
            if (!isset($bg_colors[$match])) $bg_colors[$match] = 0;
            $bg_colors[$match]++;
        }
    }
            
    preg_match_all('/app:cardBackgroundColor="([^"]+)"/', $content, $matches);
    foreach ($matches[1] as $match) {
        if (strpos($match, 'card_background') === false) {
            if (!isset($card_bg_colors[$match])) $card_bg_colors[$match] = 0;
            $card_bg_colors[$match]++;
        }
    }
            
    preg_match_all('/android:textColor="([^"]+)"/', $content, $matches);
    foreach ($matches[1] as $match) {
        $lower = strtolower($match);
        if (strpos($match, 'text_') === false && strpos($match, '@android:color/white') === false && $lower !== '#ffffff') {
            if (!isset($text_colors[$match])) $text_colors[$match] = 0;
            $text_colors[$match]++;
        }
    }
}

arsort($bg_colors);
arsort($card_bg_colors);
arsort($text_colors);

echo "Suspicious Backgrounds:\n"; print_r(array_slice($bg_colors, 0, 20));
echo "Suspicious Card Backgrounds:\n"; print_r(array_slice($card_bg_colors, 0, 20));
echo "Suspicious Texts:\n"; print_r(array_slice($text_colors, 0, 20));
?>
