<?php

$FILE_FORMAT_TEXT = "txt";
$FILE_FORMAT_PNG = "png";
$FILE_FORMAT_SVG = "svg";

$contentTypeMapping = array(
    $FILE_FORMAT_TEXT => "text/plain",
    $FILE_FORMAT_PNG => "image/png",
    $FILE_FORMAT_SVG => "image/svg+xml"
);
$fileExtensionMapping = array(
    $FILE_FORMAT_TEXT => ".utxt",
    $FILE_FORMAT_PNG => ".png",
    $FILE_FORMAT_SVG => ".svg"
);

function generateImageFile(string $uml, string $format): string
{
    global $contentTypeMapping;
    global $fileExtensionMapping;

    if (!array_key_exists($format, $contentTypeMapping)) {
        throw new Exception("Invalid file format");
    }

    $tempDir = __DIR__ . '/tmp';
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
    }

    $umlFile = tempnam($tempDir, 'uml_');
    $handle = fopen($umlFile, "w");
    fwrite($handle, $uml);
    fclose($handle);

    $jarDirPath = __DIR__ . "/lib/plantuml-1.2023.13.jar ";
    $outDirPath = __DIR__ . "/tmp";
    $cmd = "java -jar " . $jarDirPath . " -o " . $outDirPath . " " . $umlFile;
    if ($format === "txt") {
        $cmd .= " -tutxt";
    } else if ($format === "svg") {
        $cmd .= " -tsvg";
    }
    shell_exec($cmd);

    $outFile = $umlFile . $fileExtensionMapping[$format];
    unlink($umlFile);

    return $outFile;
}

function getAnswerUmlByProblemID(string $id): string
{
    $json_data = file_get_contents(__DIR__ . '/problems/problems.json');
    $problems = json_decode($json_data, true);
    $uml = '';
    foreach ($problems as $problem) {
        if ($problem['id'] == $id) {
            $uml = $problem['uml'];
            break;
        }
    }
    if ($uml == '') {
        throw new Exception("Invalid problem-ID");
    }
    return $uml;
}

register_shutdown_function(function () {
    $tmpDir = __DIR__ . '/tmp/';
    $files = glob($tmpDir . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
});

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // file_put_contents('test/get_param.txt', var_export($_GET, true));
    if ($_GET["action"] === "edit") {
        echo file_get_contents('editor.html');
    } else if ($_GET["action"] === "problems") {
        include('problems.php');
    } else if ($_GET["action"] === "solve") {
        $id = $_GET["id"];
        $ansUml = getAnswerUmlByProblemID($id);
        $answerImageFilePath = generateImageFile($ansUml, "png");
        $data = file_get_contents($answerImageFilePath);
        $type = pathinfo($answerImageFilePath, PATHINFO_EXTENSION);
        $b64EncodedAnswerPNG = 'data:image/' . $type . ';base64,' . base64_encode($data);
        unlink($answerImageFilePath);
        include('solve.php');
    } else {
        echo file_get_contents('index.html');
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // file_put_contents('test/post_param.txt', var_export($_POST, true));
    $uml = $_POST["uml"];
    if ($_POST["action"] === "display") {
        $pngFilePath = generateImageFile($uml, "png");
        $data = file_get_contents($pngFilePath);
        $type = pathinfo($pngFilePath, PATHINFO_EXTENSION);
        $b64EncodedPNG = 'data:image/' . $type . ';base64,' . base64_encode($data);
        unlink($pngFilePath);
        echo $b64EncodedPNG;
    } else if ($_POST["action"] === "download") {
        $format = $_POST["format"];
        $contentType = $contentTypeMapping[$format];
        header("Content-Type: " . $contentType);
        header('Content-Disposition: attachment; filename="download_file"' . $format);
        $outFilePath = generateImageFile($uml, $format);
        readfile($outFilePath);
        unlink($outFilePath);
    } else {
        echo "Server Error: Invalid Request Parameter";
    }
} else {
    echo "Server Error: Invalid Request Method";
}
