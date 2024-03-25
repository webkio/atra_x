<form id="search-form" action="<?= getSearchBase() ?>" data-uri="<?= getRouteURI(getSearchNameRoute() , false) ?>" class="col-4" method="get">
    <input type="search" id="search-inp" class="form-control" placeholder="<?= __local('Search') ?> ..." value="<?= getSearchTerm() ?>">
    <div class="w-100 text-center mb-2"><input type="submit" class="btn btn-primary mt-2" value="<?= __local('Search') ?>"></div>
</form>