<!-- Searchbar -->
<form novalidate="novalidate" style="text-align: center">
    <input type="text" style="width: 35%; vertical-align: middle; margin: 10px" name="search" tabindex="1" value="<?= $this->search->query ?>" placeholder="<?= _('Suchbegriff') ?>">
    <?= \Studip\Button::create(_('Suchen'), 'searching')?>
    <?= \Studip\Button::create(_('Zurücksetzen'), 'reset')?>
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
                <p class="avatar"><?= $result['avatar'] ?></p>
                <p class="result_type"><?= $result['name'] ?></p>
                <a href="<?= URLHelper::getURL($result['link']) ?>"><?= Icon::create($result['']) ?> <?= htmlReady($result['title']) ?></a>
                <?= $this->search->getInfo($result, $this->search->query) ?>
                <hr>
            </article>
        <? endforeach; ?>
    </section>
<? elseif ($this->search->query && !$this->search->error): ?>
    <?= _('Leider keine Treffer.') ?>
<? endif; ?>
<?= $this->render_partial('show/_pagination.php', array('search' => $this->search)) ?>