<?php
$res = "c:\\Users\\MAHENDRA REDDY\\AndroidStudioProjects\\Learning\\app\\src\\main\\res";
$en_file = $res . "\\values\\strings.xml";
$targets = ["hi" => "hi", "te" => "te"];

function translate_text($text, $target) {
    // Note: Python's deep_translator uses web scraping, PHP requires API key or similar.
    // This is a placeholder for the Google Translate API.
    return $text . " (translated)";
}

foreach ($targets as $code => $gcode) {
    $out_dir = $res . "\\values-" . $code;
    if (!is_dir($out_dir)) mkdir($out_dir, 0777, true);
    $out_file = $out_dir . "\\strings.xml";
    
    echo "Translating to $code ($gcode)...\n";
    
    $existing_keys = [];
    if (file_exists($out_file) && filesize($out_file) > 100) {
        $xml = simplexml_load_file($out_file);
        if ($xml !== false) {
            foreach ($xml->string as $str) {
                if (isset($str['name'])) {
                    $existing_keys[(string)$str['name']] = (string)$str;
                }
            }
        }
    }
    
    $tree = simplexml_load_file($en_file);
    if ($tree === false) continue;
    
    $changes = 0;
    foreach ($tree->string as $strNode) {
        $name = (string)$strNode['name'];
        $text = (string)$strNode;
        
        if (substr_count($text, "%") >= 2) {
            $strNode['formatted'] = "false";
        }
        
        if ($name === 'app_name' || trim($text) === '' || !preg_match("/[a-zA-Z]/", $text)) {
            continue;
        }
        
        if (isset($existing_keys[$name]) && $existing_keys[$name] !== $text) {
            $strNode[0] = $existing_keys[$name];
            continue;
        }
        
        $translated = translate_text($text, $gcode);
        $strNode[0] = str_replace("\\", "", $translated);
        $changes++;
    }
    
    $tree->asXML($out_file);
    echo "Finished $code. Translated $changes new strings.\n";
}
echo "ALL DONE\n";
?>
