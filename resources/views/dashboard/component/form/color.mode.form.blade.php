<form action="" data-action="/<?= getColorModeLink([], "URL_SCHEMA") ?>" id="color_mode_form" method="POST" novalidate>
    <?= csrf_field() ?>

    <input type="hidden" id="<?= getCurrentColorModeKey() ?>" name="<?= getCurrentColorModeKey() ?>" value="<?= getCurrentColorModeToggle() ?>">

</form>