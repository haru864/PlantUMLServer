<?php

function convertUmlToPNG(string $uml): string
{
    $tempDir = __DIR__ . '/tmp';
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
    }
    $umlFile = tempnam($tempDir, 'uml');
    $pngFile = tempnam($tempDir, 'png');
    $handle = fopen($umlFile, "w");
    fwrite($handle, $uml);
    fclose($handle);
    unlink($umlFile);
    unlink($pngFile);
    return "hogehoge";
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET["action"] == "edit") {
        $html = file_get_contents('editor.html');
    } else if ($_GET["action"] == "learn") {
        $html = file_get_contents('problems.html');
    } else {
        $html = file_get_contents('index.html');
    }
    echo $html;
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // file_put_contents('post_data.txt', var_export($_POST, true));
    $uml = $_POST["uml"];
    $pngStr = convertUmlToPNG($uml);
    // file_put_contents('response_data.txt', var_export($htmlSrc, true));
    // if ($_POST["action"] === 'download') {
    //     header("Content-Type: text/plain");
    //     header('Content-Disposition: attachment; filename="example.txt"');
    //     echo $htmlSrc;
    //     return;
    // }
    $array = ['png' => $pngStr];
    echo json_encode($array);
} else {
    echo "Server Error: Invalid Request Method";
}
