<?php
require_once 'config/config.php';
require_once 'core/Db.php';
require_once 'core/Linkshortener.php';

if ( isset($_POST['link']) && !empty($_POST['link']) ) {
    $linkShortener = new Linkshortener();

    if (filter_var($_POST['link'], FILTER_VALIDATE_URL)) {
        $result = $linkShortener->getShortLink($_POST['link']);

        if ( $result['success'] ) {
            $resultLink = $result['data']['hash'];
        } else {
            $messageRed = $result['error']['message'];
        }
    } else {
        $messageRed = "Не валидный URL";
    }
}

if ( isset($_GET['hash']) && !empty($_GET['hash']) ) {
    $linkShortener = new Linkshortener();

    $url = $linkShortener->getUrlByHash($_GET['hash']);

    if ( !empty($url) ) {
        header("Location: {$url}", 200);
    } else {
        header("Location: /404.php", 404);
    }
}

?>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Сократитель ссылок</title>
        <link rel="stylesheet" type="text/css" href="/assets/css/style.css?v=<?=rand()?>" />
    </head>
    <body>
        <div class="container">

            <div class="messages">
                <? if ( isset($messageRed) && !empty($messageRed) ): ?>
                    <div class="alert alert-danger"><?=$messageRed?></div>
                <? endif; ?>
            </div>

            <div class="form-block">
                <form action="/" method="post">
                    <div class="title text-center">Сократитель ссылок</div>
                    <div class="form-group">
                        <input class="form-control" type="text" name="link" placeholder="URL-адрес" maxlength="1024" value="<?=(isset($_POST['link']) && !empty($_POST['link']))? $_POST['link'] : ''?>">
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="text" placeholder="Результат" value="<?=(isset($resultLink) && !empty($resultLink))? SITE_NAME . $resultLink : ''?>" readonly>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" type="submit">Генерировать</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>