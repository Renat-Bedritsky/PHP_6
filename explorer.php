<?php 

$dir = $_GET['dir'] ?? '.\\';   // Если 'dir' существует, то принимает 'dir', иначе \
$dir = realpath($dir);   // Абсолютный путь к файлу
chdir($dir);   // Изменяет текущий каталог на указанный
$curDir = getcwd();   // Получает имя текущего каталога
$arHere = scandir($curDir);   // Получает список файлов и каталогов, расположенных по указанному пути

// echo __FILE__.'<br>';
// echo $_SERVER['PHP_SELF'];
// echo preg_match('/\/explorer\.php$/', $_SERVER['PHP_SELF']); 

if (preg_match('/\/explorer\.php$/', $_SERVER['PHP_SELF']) == 1) {   // Если в адресной строке есть explorer.php
    header('location: /admin2/index.php');   // Выполняется перевод на index.php
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/admin2/style.css">
</head>
<body>

<div class="window">

<?php foreach ($arHere as $path) {
    if ($path == '.') continue;
        if ($path == '..') { ?>

            <a href="/admin2/index.php/?dir=<?= $dir.'\\'.$path; ?>">Назад</a><br>

        <?php } else { if (is_dir($path)) { ?>

            <a href="/admin2/index.php/?dir=<?= $dir . '\\' . $path; ?>"><?= $path; ?></a>   <!-- Формируется список -->

            <a href="#">Переименовать</a>
        
            <a href="#">Удалить<a><br>

        <?php } else { ?>

            <a><?= $path; ?></a>

            <a href="#">Переименовать</a>
            
            <a href="#">Удалить<a><br>

        <?php }}} ?>

<p class="root"><a href="/admin2/index.php">Корень</a></p>

</div>
    
</body>
</html>