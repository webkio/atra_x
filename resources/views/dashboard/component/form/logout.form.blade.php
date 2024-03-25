<?php
if (auth()->check()) :
    $logoutRoute = getTheRoute("user", "logout", []);
?>
    <form action="<?= $logoutRoute ?>" class="mt-2" id="logout-form" method="POST" novalidate>
        <?= csrf_field() ?>
        <?= method_field('DELETE') ?>
        <input class="btn btn-danger" type="submit" value="<?= __local('Logout') ?>">
    </form>

<?php endif; ?>