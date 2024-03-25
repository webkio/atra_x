<x-dashboard.cpo::header />
<!-- Right Content -->
<div class="col-12 pt-3">
<x-dashboard.upanel.forms::reset_password_form :DB="$DB" :route_args="$route_args" />
<x-dashboard.cpo::footer />