<?php 

function delDir($dir, $way) {   // Функция для удаления папки (директория, объект)
    $files = array_diff(scandir($way), ['.','..']);   // Возвращает массив, полученный от $dir, кроме '.' и '..'
    foreach ($files as $file) {
        (is_dir($way.'/'.$file)) ? delDir($way.'/'.$file, $way.'\\'.$file) : unlink($way.'/'.$file);   // Удаление вложенных файлов и папок
    }
    rmdir($way);   // Удаление директории
    header("location: /admin2/?dir=$dir");   // Выполняется перевод на текущую директорию
}


$dir = $_GET['dir'] ?? '.\\';   // Если 'dir' существует, то принимает 'dir', иначе \
$dir = realpath($dir);   // Абсолютный путь к файлу
chdir($dir);   // Изменяет текущий каталог на указанный
$curDir = getcwd();   // Получает имя текущего каталога
$arHere = scandir($curDir);   // Получает список файлов и каталогов, расположенных по указанному пути


if (preg_match('/\/explorer\.php$/', $_SERVER['PHP_SELF']) == 1) {   // Если в адресной строке есть explorer.php
    header('location: /admin2/index.php');   // Выполняется перевод на index.php
}


if (isset($_GET['edit'])) {
    $edit = $_GET['edit'];
    $content = file_get_contents($dir.'/'.$edit);   // file_get_contents читает содержимое файла
    echo "
    <style>.window{display:none;}</style>   <!-- Когда форма активна, остальная страница скрыта -->
    <form method='POST' action='' class='formEdit'>
        <textarea name='content'>$content</textarea>
        <span class='editButton'>
            <button name='edit' type='submit' value='editYes'>Сохранить</button> 
            <button name='edit' type='submit' value='editNo'>Отмена</button>
        </span>
    </form>
    ";   // Форма для редактирования
    if (isset($_POST['edit'])) {
        if ($_POST['edit'] == 'editYes') {   // Если пользователь нажал кнопку "Сохранить"
            file_put_contents($dir.'/'.$edit, "\xEF\xBB\xBF" . $_POST['content']);   // file_put_contents записывает содержимое textarea в файл (путь к файлу, "кодировка UTF-8" . путь к textarea)
            header("location: /admin2/?dir=$dir");   // Выполняется перевод на текущую директорию
        }
        else if ($_POST['edit'] == 'editNo') header("location: /admin2/?dir=$dir");   // Выполняется перевод на текущую директорию
    }
}


if (isset($_GET['rename'])) {   // Если GET 'rename' существует
    $info = pathinfo($dir.'\\'.$_GET['rename']);
    echo '
    <form method="POST" class="formNewName">
        <input type="text" name="rename" placeholder="'.$info['filename'].'">
        <button>Ok</button>
    </form>
    ';   // Вызов формы для нового имени
    if (isset($_POST['rename'])) {   // Если POST 'rename' существует
        if (preg_match('/^[a-zа-яё0-9 -_]+$/ui', $_POST['rename'])) {   // Если проходит проверку   TODO
            if (isset($info['extension'])) {
            rename($_GET['rename'], $dir.'\\'.$_POST['rename'].'.'.$info['extension']);   // Переименование файла (старое имя, новое имя)
            }
            else rename($_GET['rename'], $dir.'\\'.$_POST['rename']);   // Переименование папки (старое имя, новое имя)
        }
        header("location: /admin2/?dir=$dir");   // Выполняется перевод на текущую директорию
    }
}


if (isset($_GET['delete'])) {
    echo '
    <div class="formDelete">
        Удалить? 
        <form method="POST">
            <input type="hidden" name="deleteYes">
            <button>Да</button>
        </form> 
        <form method="POST">
            <input type="hidden" name="deleteNo">
            <button>Нет</button>
        </form>
    </div>
    ';   // Вызов формы: Удалить? ДА НЕТ
    $way = $dir.'\\'.$_GET['delete'];   // Присваивание пути к папке
    if (isset($_POST['deleteYes'])) {
        if ($_GET['type'] == 'dir') delDir($dir, $way);   // Вызов функции (директория, объект)
        else {
            unlink($_GET['delete']);   // Удаление файла
            header("location: /admin2/?dir=$dir");   // Выполняется перевод на текущую директорию
        }
    }
    else if (isset($_POST['deleteNo'])) {
        header("location: /admin2/?dir=$dir");  // Выполняется перевод на текущую директорию
    }
}


if(isset($_POST['type']) && isset($_POST['newWay'])) {
    $newWay = $_POST['newWay'];
    if (preg_match('/^[a-zA-Zа-яА-ЯёЁ0-9 -_]+$/ui', $newWay)) {   // Если проходит проверку   TODO
        $newWay = $dir.'\\'.$newWay;   // Присваивание пути
        $type = $_POST['type'];
        if ($type == 'dir') {
            $i = 2;
            $empty = $newWay;
            while (file_exists($newWay)) {   // Если существует
                $newWay = $empty;   // Убрать цифру
                $newWay = $newWay.'_'.$i;   // Добавить цифру
                $i++;
            }
            mkdir($newWay);
            header("location: /admin2/?dir=$dir");
        }
        else if ($type == 'file' && isset($_POST['format'])) {
            if ($_POST['format'] == 'txt') $newWay .= '.txt';
            else if ($_POST['format'] == 'html') $newWay .= '.html';
            else if ($_POST['format'] == 'css') $newWay .= '.css';
            else if ($_POST['format'] == 'js') $newWay .= '.js';
            else if ($_POST['format'] == 'php') $newWay .= '.php';
            $i = 2;
            $empty = $newWay;
            while (file_exists($newWay)) {   // Если существует
                $newWay = $empty;   // Убрать цифру
                $index = strripos($newWay, '.');   // Имя файла до '.'
                if ($index !== false) {
                    $newWay = substr($newWay, 0, $index)."_$i".substr($newWay, $index);   // Имя файла, порядковый номер, расширение
                }
                else $newWay .= "_$i";
                $i++;
            }
            $fb = fopen($newWay, "w");   // Открывает файл. W - только для записи
            fclose($fb);
            header("location: /admin2/?dir=$dir");
        }
    }
}
            
function getFilesSize($path){
    $fileSize = 0;
    $dir = scandir($path);

    foreach($dir as $file)
    {
        if (($file!='.') && ($file!='..'))
            if(is_dir($path . '/' . $file))
                $fileSize += getFilesSize($path.'/'.$file);
            else
                $fileSize += filesize($path . '/' . $file);
    }
    return $fileSize;
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/admin2/style.css">
    <link rel="stylesheet" href="/admin2/media.css">
</head>
<body>

<?php

if (isset($_GET['open'])) {   // Если существует $_GET['open]
    echo '<style>body{backgroun-color: #fff !important;} .window{display:none;}</style>';   // Скрывает сраницу
    $open = $_GET['open'];
    $content = file_get_contents($dir.'/'.$open);   // file_get_contents читает содержимое файла
    echo $content;
}

?>

<div class="window">

<?php foreach ($arHere as $path) {
    if ($path == '.') continue;
        if ($path == '..') { if ($dir == 'F:\Server\xampp\htdocs\admin2') {echo "<p style='margin: 10px 0 0 400px;'>$dir</p>"; continue;} ?>

            <p class="back"><a href="/admin2/index.php/?dir=<?= $dir.'\\'.$path; ?>">Назад</a><?php echo "<span style='margin-left:360px;'>$dir</span>"; ?><p>

        <?php } else { if (is_dir($path)) { ?>

            <p class="string dirString"><a href="/admin2/?dir=<?= $dir.'\\'.$path; ?>"><?= $path; ?></a>   <!-- Формируется список папок -->

            <?php $zero = 40 - strlen($path); for ($i = 0; $i < $zero; $i++) echo '<span style="color:white;">*</span>';
            if (getFilesSize($path) <= 1024) echo '<span>'.getFilesSize($path).' байт</span>'; // Функция для определения размера папки в байтах
            else if (getFilesSize($path) <= (1024*1024)) echo '<span>'. round(getFilesSize($path)/1024) .' Кбайт</span>'; // Функция для определения размера папки в Кбайтах
            else echo '<span>'. round(getFilesSize($path)/1024/1024) .' Мбайт</span>'; // Функция для определения размера папки в Мбайтах
            ?>

            <?php if ($path == 'uploads') {echo '<span style="float:right;">Доступ запрещён</span><hr>'; continue;} ?>   <!-- Запрещает трогать эти папки -->

            <span class="rename"><a href="/admin2/?dir=<?= $dir; ?>&rename=<?= $path; ?>">Переименовать</a></span>
        
            <span class="delete"><a href="/admin2/?dir=<?= $dir ?>&delete=<?= $path; ?>&type=dir">Удалить<a></p><hr>

        <?php } else { ?>

            <p class="string"><a><?= $path; ?></a>   <!-- Формируется список файлов -->

            <?php $zero = 40 - strlen($path); for ($i = 0; $i <= $zero; $i++) echo '<span style="color:white;">*</span>';
            if (filesize($path) <= 1024) echo '<span>'.filesize($path).' байт</span>'; // Размера файла в байтах
            else if (filesize($path) <= (1024*1024)) echo '<span>'. round(filesize($path)/1024) .' Кбайт</span>'; // Размера файла в килобайтах
            else echo '<span>'. round(filesize($path)/1024/1024) .' Мбайт</span>'; // Размера файла в мегабайтах
            ?>

            <?php

            if ($path == 'index.php' || $path == 'explorer.php' || $path == 'style.css' || $path == 'media.css' || $path == 'uploader.php') {
                echo '<span style="float:right;">Доступ запрещён</span><hr>'; continue;
            } 
            
            ?>   <!-- Запрещает трогать эти файлы -->

            <span class="rename"><a href="/admin2/?dir=<?= $dir; ?>&rename=<?= $path; ?>">Переименовать</a></span>
            
            <span class="delete"><a href="/admin2/?dir=<?= $dir ?>&delete=<?= $path; ?>&type=file">Удалить</a></span>

            <span class="edit"><a href="/admin2/?dir=<?= $dir; ?>&edit=<?= $path; ?>">Редактировать</a></span>
        
                <?php $pathHTML = pathinfo($path); if ($pathHTML['extension'] == 'html') { ?>

                <span class="open"><a href="/admin2/?dir=<?= $dir ?>&open=<?= $path; ?>" target="blank">Открыть</a></span>   <!-- Открывает html файлы -->

                <?php } ?>
        
            </p><hr>

        <?php }
    }
}

if (!isset($arHere[2])) echo '<p class="string">Пусто<p>';

?>

<p class="root"><a href="/admin2/">Корень</a></p>

<hr>

<form method="POST" class="create">   <!-- Форма для создания нового объекта -->
Новый файл <input type="text" name="newWay" placeholder="Имя">
Файл <input type="radio" name="type" value="file" id="file">
<label for="file">
    <select name="format">
        <option value="txt">txt</option>
        <option value="html">html</option>
        <option value="css">css</option>
        <option value="js">js</option>
        <option value="php">php</option>
    </select>
</label>
Папка<input type="radio" name="type" value="dir">
<button>Создать</button>
</form>

<hr>

<form action="/admin2/uploader.php" class="loadFile" method="POST" enctype="multipart/form-data">   <!-- Форма для загрузки изображений -->
    Загрузить файл на сервер
    <p><input type="file" multiple name="files[]"></p>
    <button>Отправить</button>
</form>

</div>

</body>
</html>