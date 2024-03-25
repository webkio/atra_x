<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />


<div class="row">
    <div class="col-12">
            <form action="" method="post">
                <div class="row">
                    <div class="col-12 featured-tags mb-3" dir="ltr">
                        <?php 
                           $modelLabel = getHistoryActionModelLabel($DB);
                           $modelAction = getHistoryActionModelAction($DB);
                        ?>

                        <div class="badge badge-large badge-pill badge-info">{{$modelLabel}}</div>
                        <div class="badge badge-large badge-pill badge-primary">{{$modelAction}}</div>
                        <div class="badge badge-large badge-pill badge-warning">{{getTypeAttr($DB , "by_raw")}} ({{getTypeAttr($DB , "by")}})</div>
                    </div>

                    <div class="input-wrapper before-change col-12">
                        <label for="">{{__local('Before')}}</label>
                        <div class="border board border-secondary rounded p-2 mb-3" dir="ltr" id="title" name="" data-label="Before" placeholder="Before" required="required">{!!highlightJson(getValueFromOldOrDB('archive_raw_before', $DB) , json_decode(getTypeAttr($DB , "changes"), true) , "rgb(244 67 54 / 33%)")!!}</div>
                    </div>

                    <div class="input-wrapper after-change col-12">
                        <label for="">{{__local('After')}}</label>
                        <div class="border board border-secondary rounded p-2 mb-3" dir="ltr" id="title" name="" data-label="After" placeholder="After" required="required">{!!highlightJson(getValueFromOldOrDB('archive_raw_after', $DB) , json_decode(getTypeAttr($DB , "changes"), true) , "rgb(192 234 143)")!!}</div>
                    </div>
                </div>
            </form>
    </div>



</div>



<x-dashboard.cpo::footer />