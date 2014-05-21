<form class="studip_form">
    <input type="text" name="search" size="50" value="<?= $search->query ?>" placeholder="<?= _('Suchbegriff') ?>">
    <input type="image" src
    <?= \Studip\Button::create(_('Suchen')) ?>
</form>


<? if ($search->results): ?>
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
