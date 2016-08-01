<? if ($search && $search->countResultPages() > 1): ?>
    <div class='pagination'>
        <? foreach ($search->getPages(Request::get('page')) as $page): ?>
            <a href='<?= URLHelper::getLink('', array('search' => $search->query, 'filter' => $search->filter, 'page' => $page)) ?>' class='<?= Request::get('page') == $page ? 'current' : ''?>'>
                <?= $page + 1 ?>
            </a> 
        <? endforeach; ?>
    </div>
<? endif; ?>