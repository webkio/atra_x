<?php 
$targetUrl = getTypeSetStatusLink($GLOBALS['current_page'] , $route_args);
$extraClass = !empty($extraClass) ? $extraClass : "";
?>

<form action="<?= $targetUrl ?>" class="status-form-x <?= $extraClass ?>" id="status-form" method="POST" novalidate>
<?= csrf_field() ?>
<?= method_field("PATCH") ?>

<?php if(!isset($item)) $item = $DB; ?>
<input type="hidden" id="action" name="action" value="">
<input type="hidden" id="id" name="id" value="<?= getTypeID($item); ?>">
<?= !empty($extraInput) ? join("\n" , $extraInput) : "" ?>
<input type="hidden" id="redirect" name="redirect" value="<?= redirectAfterAction(getDashboardEditForm()) ?>">
</form>