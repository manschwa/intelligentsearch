<? if ($search->countResultPages() > 1): ?>
    <? foreach ($search->getPages(Request::get('page')) as $page): ?>
        <a href='<?= URLHelper::getLink('', array('search' => $search->query, 'page' => $page)) ?>'><?= $page ?></a> 
    <? endforeach; ?>
<? endif; ?>