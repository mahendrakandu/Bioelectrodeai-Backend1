<?php
$files = glob('../app/src/main/res/values-*/strings.xml');
foreach ($files as $file) {
    if (!is_file($file)) continue;
    try {
        $xml = simplexml_load_file($file);
        if ($xml === false) continue;
        
        $changes = 0;
        foreach ($xml->string as $stringNode) {
            $text = (string)$stringNode;
            if ($text !== '') {
                $new_text = $text;
                
                if (strpos($new_text, "'") !== false && strpos($new_text, "\\'") === false) {
                    $new_text = str_replace("'", "\\'", $new_text);
                }
                
                if (strpos($new_text, "&") !== false && strpos($new_text, "&amp;") === false) {
                    $new_text = str_replace("&", "&amp;", $new_text);
                }
                
                if ($new_text !== $text) {
                    $stringNode[0] = $new_text;
                    $changes++;
                }
            }
        }
        
        if ($changes > 0) {
            $xml->asXML($file);
            echo "Fixed $changes strings in $file\n";
        }
    } catch (Exception $e) {
        echo "Error parsing $file: " . $e->getMessage() . "\n";
    }
}
?>
