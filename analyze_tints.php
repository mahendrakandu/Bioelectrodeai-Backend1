<?php
$tint_colors = [];

$files = glob('../app/src/main/res/layout/*.xml');
foreach ($files as $file) {
    $content = file_get_contents($file);
    
    preg_match_all('/app:tint="([^"]+)"/', $content, $matches);
    foreach ($matches[1] as $match) {
        if (strpos($match, 'color') === false) {
            if (!isset($tint_colors[$match])) $tint_colors[$match] = 0;
            $tint_colors[$match]++;
        }
    }
            
    preg_match_all('/android:tint="([^"]+)"/', $content, $matches);
    foreach ($matches[1] as $match) {
        if (strpos($match, 'color') === false) {
            if (!isset($tint_colors[$match])) $tint_colors[$match] = 0;
            $tint_colors[$match]++;
        }
    }
}

arsort($tint_colors);
echo "Suspicious Tints:\n"; print_r(array_slice($tint_colors, 0, 20));
?>
