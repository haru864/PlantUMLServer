<?php
$jsonData = file_get_contents(__DIR__ . '/problems/problems.json');
$problems = json_decode($jsonData, true);
file_put_contents('test/debug.txt', "hogehoge");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profiles</title>
</head>

<body>
    <h1>Problems</h1>
    <div>id, title, theme</div>
    <?php foreach ($problems as $problem) : ?>
        <div>
            <?php echo $problem['id'] ?>,
            <a href="server.php?action=solve&id=<?php echo urlencode($problem['id']); ?>"><?php echo $problem['title']; ?></a>,
            <?php echo $problem['theme'] ?>
        </div>
    <?php endforeach; ?>
</body>

</html>