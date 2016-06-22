<!-- Searchbar -->
<form novalidate="novalidate" style="text-align: center">
    <input type="text" style="width: 45%" name="search" tabindex="1" value="<?= $this->search->query ?>" placeholder="<?= _('Suchbegriff') ?>">
</form>

<? if ($this->search->query): ?>
    <? if ($this->search->error): ?>
        <p><?= htmlReady($this->search->error) ?></p>
    <? else: ?>
        <h3><?= sprintf(_('Suchergebnisse für "%s"'), htmlReady($this->search->query)) ?></h3>
    <? endif; ?>
<? endif; ?>

<? if ($this->search->results): ?>
    <section class="search_results">
        <? foreach ($this->search->resultPage(Request::get('page')) as $result): ?>
            <article>
                <p class="result_type"><?= $result['name'] ?></p>
                <a href="<?= URLHelper::getURL($result['link']) ?>"><?= htmlReady($result['title']) ?></a>
                <?= IntelligentSearch::getInfo($result, $this->search->query) ?>
            </article>
        <? endforeach; ?>
    </section>
<? elseif ($this->search->query && !$this->search->error): ?>
    <?= _('Leider keine Treffer.') ?>
<? endif; ?>
<?= $this->render_partial('show/_pagination.php', array('search' => $this->search)) ?>