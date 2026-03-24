<?php

$tint_colors = [];

$files = glob('../app/src/main/res/layout/*.xml');
foreach ($files as $file) {
    $content = file_get_contents($file);
    
    preg_match_all('/app:backgroundTint="([^"]+)"/', $content, $matches_app);
    foreach ($matches_app[1] as $match) {
        if (strpos($match, 'color') === false) {
            if (!isset($tint_colors[$match])) $tint_colors[$match] = 0;
            $tint_colors[$match]++;
        }
    }
    
    preg_match_all('/android:backgroundTint="([^"]+)"/', $content, $matches_android);
    foreach ($matches_android[1] as $match) {
        if (strpos($match, 'color') === false) {
            if (!isset($tint_colors[$match])) $tint_colors[$match] = 0;
            $tint_colors[$match]++;
        }
    }
}

arsort($tint_colors);
echo "Suspicious Background Tints:\n";
$i = 0;
foreach ($tint_colors as $color => $count) {
    if ($i++ >= 20) break;
    echo "['$color', $count]\n";
}

?>
