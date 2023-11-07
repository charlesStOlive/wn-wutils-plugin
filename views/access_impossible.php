<!DOCTYPE html>
<html lang="<?=App::getLocale()?>">
    <head>
        <meta charset="utf-8">
        <title><?=Lang::get('waka.wutils::lang.page.access_denied.label')?></title>
        <link href="<?=Url::to('/modules/system/assets/css/styles.css')?>" rel="stylesheet">
        <link href="<?=Url::asset('modules/system/assets/ui/icons.css')?>" rel="stylesheet">

        
        
    </head>
    <body>
        <div class="container">
            <h1><i class="icon-ban"></i> <?=Lang::get('waka.wutils::lang.page.access_denied.label')?></h1>
            <p class="lead"><?=Lang::get($explain)?></p>
            <a href="javascript:;" onclick="history.go(-1); return false;"><?=Lang::get('backend::lang.page.404.back_link')?></a>
            <br><br>
            <a href="<?=Backend::url('')?>"><?=Lang::get('backend::lang.page.access_denied.cms_link')?></a>
        </div>
    </body>
</html>
