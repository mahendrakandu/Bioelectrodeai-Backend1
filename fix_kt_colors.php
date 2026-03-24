<?php
function rsearch($folder, $pattern) {
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
    $fileList = array();
    foreach($files as $file) {
        $fileList = array_merge($fileList, $file);
    }
    return $fileList;
}

$files = rsearch('../app/src/main/java/com/simats/learning', '/.*\.kt$/');
$count = 0;

foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);
    
    $new_content = preg_replace('/(android\.graphics\.)?Color\.parseColor\("#(000000|1A1A1A|212121|333333|424242|444444)"\)/i', 'androidx.core.content.ContextCompat.getColor(if (this is android.content.Context) this else (if (this is android.view.View) this.context else null)!!, com.simats.learning.R.color.text_primary)', $content);
    $new_content = preg_replace('/(android\.graphics\.)?Color\.parseColor\("#(666666|757575|888888|999999)"\)/i', 'androidx.core.content.ContextCompat.getColor(if (this is android.content.Context) this else (if (this is android.view.View) this.context else null)!!, com.simats.learning.R.color.text_secondary)', $new_content);

    if ($content !== $new_content) {
        file_put_contents($file, $new_content);
        $count++;
    }
}

echo "Updated $count Kotlin files.\n";
?>
