<form class="studip_form">
    <input type="text" style="display: inline;" name="search" size="50" value="<?= $search->query ?>" placeholder="<?= _('Suchbegriff') ?>">
    <input type="image" src="<?= Assets::image_path('icons/24/blue/search.png') ?>">
</form>

<? if ($search->error): ?>
    <p><?= htmlReady($search->error) ?></p>
<? endif; ?>


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
