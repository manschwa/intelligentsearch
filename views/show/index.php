<? if ($search->query): ?>
    <? if ($search->error): ?>
        <p><?= htmlReady($search->error) ?></p>
    <? else: ?>
        <h3><?= sprintf(_('Suchergebnisse für %s'), $search->query) ?></h3>
    <? endif; ?>
<? endif; ?>


    <? if ($search->results): ?>
    <section class="search_results">
            <? foreach ($search->resultPage() as $result): ?>
            <article>
                <? if (!$search->filter): ?>
                    <p class="result_type"><?= IntelligentSearch::getTypeName($result['type']) ?></p>
                <? endif; ?>
                <a href="<?= URLHelper::getURL($result['link']) ?>"><?= htmlReady($result['title']) ?></a>
            <?= IntelligentSearch::getInfo($result, $search->query) ?>
            </article>
    <? endforeach; ?>
    </section>
    <?
 endif;
