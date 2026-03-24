<?php
$res = "c:\\Users\\MAHENDRA REDDY\\AndroidStudioProjects\\Learning\\app\\src\\main\\res";
$languages = ["hi" => "hi", "te" => "te"]; // Simplified from full Python list

function fix_existing_file($fpath) {
    echo "Fixing $fpath ...\n";
    $tree = simplexml_load_file($fpath);
    if ($tree === false) return;
    $fixed = 0;
    
    foreach ($tree->string as $el) {
        $text = (string)$el;
        if (strpos($text, "\\") !== false) {
            $el[0] = str_replace(["\\'", "\\\\"], ["'", ""], $text);
            $fixed++;
        }
        if (substr_count($text, "%") >= 2) {
            $el['formatted'] = "false";
        }
    }
    $tree->asXML($fpath);
    echo "  Fixed $fixed strings\n";
}

echo "=== Fix & Generate Strings ===\n";
foreach ($languages as $lang => $code) {
    $fpath = $res . "\\values-" . $lang . "\\strings.xml";
    if (file_exists($fpath) && filesize($fpath) > 100) {
        fix_existing_file($fpath);
    }
}

$en_path = $res . "\\values\\strings.xml";
if (file_exists($en_path)) {
    echo "Fixing English strings.xml for % formatting ...\n";
    $tree = simplexml_load_file($en_path);
    if ($tree !== false) {
        foreach ($tree->string as $el) {
            if (substr_count((string)$el, "%") >= 2) {
                $el['formatted'] = "false";
            }
        }
        $tree->asXML($en_path);
    }
}
echo "\n=== Done ===\n";
?>
