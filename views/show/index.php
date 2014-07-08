<? if ($search->query): ?>
    <? if ($search->error): ?>
        <p><?= htmlReady($search->error) ?></p>
    <? else: ?>
        <h3><?= sprintf(_('Suchergebnisse für %s'), $search->query) ?></h3>
    <? endif; ?>
<? else: ?>
    <h3><?= _('Suche') ?></h3>
    <form class="studip_form">
        <input type="text" name="search" size="60" placeholder="<?= _('Suchbegriff') ?>">
        <?= \Studip\Button::create(_('Suchen')) ?>
    </form>
<? endif; ?>


<? if ($search->results): ?>
    <section class="search_results">
        <? foreach ($search->resultPage(Request::get('page')) as $result): ?>
            <article>
                <? if (!$search->filter): ?>
                    <p class="result_type"><?= IntelligentSearch::getTypeName($result['type']) ?></p>
                <? endif; ?>
                <a href="<?= URLHelper::getURL($result['link']) ?>"><?= htmlReady($result['title']) ?></a>
                <?= IntelligentSearch::getInfo($result, $search->query) ?>
            </article>
        <? endforeach; ?>
    </section>
<? endif; ?>
<?= $this->render_partial('show/_pagination.php', array('search' => $search)) ?>