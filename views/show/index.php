<form class="studip_form">
    <input type="text" name="search" size="50" value="<?= $query ?>" placeholder="<?= _('Suchbegriff') ?>">
    <?= \Studip\Button::create(_('Suchen')) ?>
</form>


<? if ($results): ?>
<p><?= sprintf(_('%s Ergebnisse in %s Sekunden'), $counter, round($time, 4)) ?></p>
    <section class="search_results">
        <? foreach ($results as $result): ?>
        <article>
            <a href="<?= URLHelper::getLink($result->link) ?>"><?= $result->title ?></a>
            <?= $result->info ?>
        </article>
        <? endforeach; ?>
    </section>
<? endif;
