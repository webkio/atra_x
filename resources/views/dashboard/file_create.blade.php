<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />

<?php 
$route = getTheRoute($GLOBALS['current_page'], $action, $route_args);
?>

    <form action="{{$route}}" method="post" data-source="dashboard" class="main-form" id="upload-form">
        @csrf
        <div class="row">
            <div class="col-12">
                
            <h5 id="max-size" class="{{trnsAlignCls()}} title-introduce" data-size="{{file_upload_max_size()}}"><span>{{__local('Max File Size')}} : </span><b><?= getMaxUploadSizeMB() ?></b></h5>
            <div class="file-drop-zone">
                <div class="mask-progress">0%</div>
                <center>
                    <h2 class="title-introduce">{{__local('Drag Your Files Here')}}</h2>
                    <div class="action-wrapper d-none">
                        <button type="button" id="upload-files" class="btn btn-lg btn-primary mb-2">{{__local('Upload')}}</button><br>
                        <button type="button" onclick="javascript:location.reload()" class="btn btn-lg btn-danger">{{__local('Delete')}}</button><br>
                    </div>
                </center>
            </div>

            <div class="select-file-wrapper mt-3">
                <label for="select-file">{{__local('Drag or Select by File Explorer')}}</label>
                <input type="file" multiple name="select-file" id="select-file">
            </div>

            <ul class="file-list mt-4 {{trnsAlignCls()}}">
                <strong class="mb-3 title-introduce">{{__local('File List')}}</strong>

            </ul>
            </div>

        </div>
    </form>


<x-dashboard.cpo::footer />