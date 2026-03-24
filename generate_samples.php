<?php

$downloads_dir = "C:\\Users\\MAHENDRA REDDY\\Downloads";

function generate_ecg() {
    global $downloads_dir;
    $file = fopen($downloads_dir . '/Sample_ECG_Dataset.csv', 'w');
    fputcsv($file, ['timestamp', 'amplitude_mV']);

    for ($i = 0; $i < 500; $i++) {
        $t = $i * 0.01;
        $beatPhase = ($i % 80) / 80.0;
        
        $pWave = (0.1 <= $beatPhase && $beatPhase <= 0.2) ? sin(($beatPhase - 0.1) * 10 * M_PI) * 0.15 : 0.0;
        
        $qrsComplex = 0.0;
        if (0.3 <= $beatPhase && $beatPhase <= 0.33) {
            $qrsComplex = -0.15 * sin(($beatPhase - 0.3) * 33 * M_PI);
        } elseif (0.33 < $beatPhase && $beatPhase <= 0.36) {
            $qrsComplex = 1.8 * sin(($beatPhase - 0.33) * 33 * M_PI);
        } elseif (0.36 < $beatPhase && $beatPhase <= 0.4) {
            $qrsComplex = -0.25 * sin(($beatPhase - 0.36) * 25 * M_PI);
        }
        
        $tWave = (0.6 <= $beatPhase && $beatPhase <= 0.8) ? sin(($beatPhase - 0.6) * 5 * M_PI) * 0.25 : 0.0;
        
        $baseline = sin($t * M_PI * 0.5) * 0.1;
        $noise = (rand(0, 1000) / 1000.0 - 0.5) * 0.1;
        
        $amp = $pWave + $qrsComplex + $tWave + $baseline + $noise;
        fputcsv($file, [number_format($t, 3), number_format($amp, 4)]);
    }
    fclose($file);
}

function generate_eeg() {
    global $downloads_dir;
    $file = fopen($downloads_dir . '/Sample_EEG_Dataset.csv', 'w');
    fputcsv($file, ['Time(s)', 'Value']);

    for ($i = 0; $i < 500; $i++) {
        $t = $i * 0.01;
        $alpha = sin($t * 2 * M_PI * 10.0) * (0.4 + rand(0, 1000)/1000.0 * 0.2);
        $beta = sin($t * 2 * M_PI * 22.0) * (0.15 + rand(0, 1000)/1000.0 * 0.1);
        $delta = sin($t * 2 * M_PI * 1.5) * 0.3;
        $noise = (rand(0, 1000)/1000.0 - 0.5) * 0.2;
        
        $amp = $alpha + $beta + $delta + $noise;
        fputcsv($file, [number_format($t, 3), number_format($amp, 4)]);
    }
    fclose($file);
}

function generate_emg() {
    global $downloads_dir;
    $file = fopen($downloads_dir . '/Sample_EMG_Dataset.csv', 'w');
    fputcsv($file, ['idx', 'reading']);

    for ($i = 0; $i < 500; $i++) {
        $t = $i * 0.01;
        $is_bursting = sin($t * 2 * M_PI * 0.8) > 0.3;
        
        if ($is_bursting) {
            $amp = (rand(0, 1000)/1000.0 - 0.5) * 4.0 * sin($t * 2 * M_PI * 150.0) + (rand(0, 1000)/1000.0 - 0.5) * 2.0;
        } else {
            $amp = (rand(0, 1000)/1000.0 - 0.5) * 0.3;
        }
        fputcsv($file, [$i, number_format($amp, 4)]);
    }
    fclose($file);
}

try {
    generate_ecg();
    generate_eeg();
    generate_emg();
    echo "Successfully generated 3 sample datasets in " . $downloads_dir . "\n";
} catch (Exception $e) {
    echo "Error generating files: " . $e->getMessage() . "\n";
}

?>
