<form class="studip_form">
    <input type="text" name="search" size="50" value="<?= $search->query ?>" placeholder="<?= _('Suchbegriff') ?>">
    <?= \Studip\Button::create(_('Suchen')) ?>
</form>


<? if ($search->results): ?>
    <p><?= sprintf(_('%s Ergebnisse in %s Sekunden'), $search->count, round($search->time, 4)) ?></p>
    <section class="search_results">
        <? foreach ($search->resultPage() as $result): ?>
            <article>
                <a href="<?= URLHelper::getLink($result->link) ?>"><?= $result->title ?></a>
                <?= $result->info ?>
            </article>
        <? endforeach; ?>
    </section>
    <?
 endif;
