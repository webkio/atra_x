<form action="<?= getTypeDeleteLink($GLOBALS['current_page'], $route_args) ?>" id="delete-form" method="POST" novalidate>
<?= csrf_field() ?>
<?= method_field('DELETE') ?>
<?php if(!isset($item)) $item = $DB; ?>
<input type="hidden" id="id" name="id" value="<?= getTypeID($item); ?>">
<input type="hidden" id="redirect" name="redirect" value="index">

</form>