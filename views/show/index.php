<!-- Searchbar -->
<form novalidate="novalidate" style="text-align: center">
    <input type="text" style="width: 45%" name="search" value="<?= $search->query ?>" placeholder="<?= _('Suchbegriff') ?>">
</form>

<? if ($search->query): ?>
    <? if ($search->error): ?>
        <p><?= htmlReady($search->error) ?></p>
    <? else: ?>
        <h3><?= sprintf(_('Suchergebnisse f�r "%s"'), htmlReady($search->query)) ?></h3>
    <? endif; ?>
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
<? elseif ($search->query && !$search->error): ?>
    <?= _('Leider keine Treffer.') ?>
<? endif; ?>
<?= $this->render_partial('show/_pagination.php', array('search' => $search)) ?>