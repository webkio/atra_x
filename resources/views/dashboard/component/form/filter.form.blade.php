<form data-route="<?= request()->url() ?>" action="" id="filter-form" method="post" novalidate>
    <?= csrf_field() ?>
    <a href="#block-wrapper-filter" data-toggle="collapse">
        <h4 class="col-12 text-sm-center btn btn-info"><?= __local('Filter') ?></h4>
    </a>

    <div class="collapse" id="block-wrapper-filter" data-parent="#filter-form">
        <div class="row <?= trnsAlignCls() ?>" id="filter-wrapper">
            <div class="filter-checklist <?= trnsAlignCls() ?>">

                <div class="btn-group dropright">
                    <button type="button" class="btn btn-info waves-effect waves-light dropdown-toggle filter-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="mdi mdi-chevron-right"></i><?= __local('Order By') ?>
                    </button>
                    <div class="switch-list dropdown-menu">

                    </div>
                </div>
                <button type="button" id="clear-filter" class="btn btn-danger waves-effect waves-light"><?= __local('Clear Filter') ?></button>
                <div class="select2-wrapper sort-wrapper input-wrapper-filter active" data-name="sort">
                    <select id="sort-select2" class="select2 select2-simple" data-options='{"minimumResultsForSearch":true}'>
                        <input type="text" id="sort" class="d-none the-value select2-content-list" value="">
                    </select>
                </div>
            </div>
        </div>
        <div class="text-sm-right mb-5">
            <button type="submit" class="btn btn-lg btn-block active-bg text-dark"><?= __local('Find') ?></button>
        </div>
    </div>

</form>