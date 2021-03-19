<?php 

function delDir($dir) {
    $files = array_diff(scandir($dir), ['.','..']);   // Возвращает массив, полученный от $dir, кроме '.' и '..'
    foreach ($files as $file) {
        (is_dir($dir.'/'.$file)) ? delDir($dir.'/'.$file) : unlink($dir.'/'.$file);
    }
    rmdir($dir);   // Удаление директории
    header("location: /admin2/?dir=$dir");   // Выполняется перевод на текущую директорию
}

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

// $rename = $_GET['rename'] ?? false;   // Если 'rename' существует, то принимает 'rename', иначе false
// $rename = $dir.'\\'.$rename;   // Присваивание пути

$formats = 'php|html|txt|js|css';

if (isset($_GET['rename'])) {   // Если GET 'rename' существует
    echo '<form method="POST" class="formNewName"><input type="text" name="rename"><button>Ok</button></form>';   // Вызов формы для нового имени
    if (isset($_POST['rename'])) {   // Если POST 'rename' существует
        if (preg_match('/^[a-zа-яё0-9 -_]+(\.('.$formats.'))?$/ui', $_POST['rename'])) {   // Если проходит проверку
            rename($_GET['rename'], $dir.'\\'.$_POST['rename']);   // Переименование (старое имя, новое имя)
        }
        header("location: /admin2/?dir=$dir");   // Выполняется перевод на текущую директорию
    }
}

if (isset($_GET['delete'])) {
    echo '<div class="formDelete">Удалить? <form method="POST"><input type="hidden" name="deleteYes"><button>Да</button></form> <form><input type="hidden" name="deleteNo"><button>Нет</button></form></div>';   // Вызов формы: Удалить? ДА НЕТ
    $deleteDir = $dir.'\\'.$_GET['delete'];   // Присваивание пути
    if (isset($_POST['deleteYes'])) {
        if ($_GET['type'] == 'dir') delDir($deleteDir);   // Вызов функции
        else unlink($_GET['delete']);   // Удаление файла
        if (isset($_GET['delete'])) {
            header("location: /admin2/?dir=$dir");   // Выполняется перевод на текущую директорию
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/admin2/style2.css">
</head>
<body>

<div class="window">

<?php foreach ($arHere as $path) {
    if ($path == '.') continue;
        if ($path == '..') { ?>

            <p class="back"><a href="/admin2/index.php/?dir=<?= $dir.'\\'.$path; ?>">Назад</a><p>

        <?php } else { if (is_dir($path)) { ?>

            <p class="string dirString"><a href="/admin2/?dir=<?= $dir . '\\' . $path; ?>"><?= $path; ?></a>   <!-- Формируется список -->

            <span class="rename"><a href="/admin2/?dir=<?= $dir; ?>&rename=<?= $path; ?>">Переименовать</a></span>
        
            <span class="delete"><a href="/admin2/?dir=<?= $dir ?>&delete=<?= $path; ?>&type=dir">Удалить<a></p>

        <?php } else { ?>

            <p class="string"><a><?= $path; ?></a>   <!-- Формируется список -->

            <span class="rename"><a href="/admin2/?dir=<?= $dir; ?>&rename=<?= $path; ?>">Переименовать</a></span>
            
            <span class="delete"><a href="/admin2/?dir=<?= $dir ?>&delete=<?= $path; ?>&type=file">Удалить<a></span></p>

        <?php }}} ?>

<p class="root"><a href="/admin2/">Корень</a></p>

<form method="POST">
Новый файл <input type="text" name="newFile">
<i
</form>

</div>

</body>
</html>