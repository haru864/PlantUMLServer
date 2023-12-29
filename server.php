<?php

function convertUmlToPNG(string $uml): string
{
    $tempDir = __DIR__ . '/tmp';
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
    }

    $umlFile = tempnam($tempDir, 'uml_');
    $handle = fopen($umlFile, "w");
    fwrite($handle, $uml);
    fclose($handle);

    $jarDirPath = __DIR__ . "/lib/plantuml-1.2023.13.jar ";
    $pngDirPath = __DIR__ . "/tmp";
    $cmd = "java -jar " . $jarDirPath . " -o " . $pngDirPath . " " . $umlFile;
    $res = shell_exec($cmd);
    // file_put_contents('test/debug.txt', $cmd . PHP_EOL . $res);

    $pngFile = $umlFile . ".png";
    $type = pathinfo($pngFile, PATHINFO_EXTENSION);
    $data = file_get_contents($pngFile);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

    unlink($umlFile);
    unlink($pngFile);

    return $base64;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // file_put_contents('test/get_param.txt', var_export($_GET, true));
    if ($_GET["action"] == "edit") {
        $html = file_get_contents('editor.html');
    } else if ($_GET["action"] == "learn") {
        $html = file_get_contents('problems.html');
    } else {
        $html = file_get_contents('index.html');
    }
    echo $html;
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // file_put_contents('test/post_param.txt', var_export($_POST, true));
    $uml = $_POST["uml"];
    $b64EncodedPNG = convertUmlToPNG($uml);
    // file_put_contents('response_param.txt', var_export($htmlSrc, true));
    // if ($_POST["action"] === 'download') {
    //     header("Content-Type: text/plain");
    //     header('Content-Disposition: attachment; filename="example.txt"');
    //     echo $htmlSrc;
    //     return;
    // }
    echo $b64EncodedPNG;
} else {
    echo "Server Error: Invalid Request Method";
}
