<div class="user-overview text-center text-dark">
    <img width="75" class="rounded-circle border" src="<?= getUserGravatarByEmail(getCurrentUserAttr("email" , null)) ?>" id="avatar">
    <strong class="d-block default-color"><?= getCurrentUserAttr("fullname") ?></strong>
    <small class="badge badge-pill active-bg text-black"><?= (getUserRoles())[getCurrentUserAttr("role")] ?></small>
    <div class="w-100"></div>
    <a href="<?= getCurrentUserProfileEdit() ?>" class="btn btn-primary mt-2">{{__local('Profile')}}</a>
    
    <?php require_once getDashboardViewPath("component/form/logout.form.blade.php"); ?>
</div>