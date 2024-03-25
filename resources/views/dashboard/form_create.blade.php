<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />
<?php
require_once getDashboardViewPath("component/form/delete.form.blade.php");
$route = getTheRoute("forms_schema", $action, $route_args);
?>

<form action="{{$route}}" method="post" id="main-form">
    @csrf
    @if($action == "edit")
    @method('patch')
    @endif
    <div class="row">

        <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
            <div class="row">
                <div class="input-wrapper col-12">
                    <label for="title" class="control-label {{trnsAlignBlockCls()}}">{{__local("Title")}}</label>
                    <input value="{{getValueFromOldOrDB('title', $DB)}}" class="form-control mb-3" id="title" name="title" type="text" data-label="{{__local('Title')}}" placeholder="{{__local('Title')}}" required="required">
                </div>

                <div class="input-wrapper col-12">
                    <label for="type" class="control-label {{trnsAlignBlockCls()}}">{{__local("Type")}}</label>
                    <input value="{{getValueFromOldOrDB('type', $DB)}}" class="form-control mb-3" id="type" name="type" type="text" data-label="{{__local('Type')}}" placeholder="{{__local('Type')}}" required="required">
                </div>

                <div class="input-wrapper col-12 mb-3">
                    <div class="child-element custom-control custom-switch text-center">
                        <input type="checkbox" class="custom-control-input" name="is_login_required" id="is_login_required" value="ON" {{getValueFromOldOrDB('is_login_required', $DB) ? 'checked' : ''}} data-label="{{__local('is Login Required to Submit Form ?')}}">
                        <label class="custom-control-label" for="is_login_required">{{__local('is Login Required to Submit Form ?')}}</label>
                    </div>
                </div>

                <div class="input-wrapper col-12 mb-3">
                    <div class="child-element custom-control custom-switch text-center">
                        <input type="checkbox" class="custom-control-input" name="is_captcha_required" id="is_captcha_required" value="ON" {{getValueFromOldOrDB('is_captcha_required', $DB) ? 'checked' : ''}} data-label="{{__local('Activate Captcha')}}">
                        <label class="custom-control-label" for="is_captcha_required">{{__local('Activate Captcha')}}</label>
                    </div>
                </div>

                <div class="input-wrapper wrapper-form-input-items col-12 border p-2 border border-success">
                    <h3 class="{{trnsAlignBlockCls()}} title-introduce border-bottom pb-2">{{__local('Form Elements')}}</h3>

                    <?php
                    $schemaValue = getValueFromOldOrDB('schema', $DB);
                    $isSchemaHasValue = !empty($schemaValue) ? "has-value=true" : "";

                    $schemaValueArray = $isSchemaHasValue ? json_decode($schemaValue, true) : [getDefaultClonableItem()];

                    ?>

                    <input type="hidden" id="schema" name="schema" class="form-control the-json" {{$isSchemaHasValue}} data-label="{{__local('Form Elements')}}">

                    <div class="clonable-block" data-options='{"afterToggle" : "afterToggleFormInput" , "afterDelete" : "afterDeleteFormInput"}'>
                        @foreach($schemaValueArray as $index => $item)
                        <?php $number = $index + 1; ?>
                        <div data-details='{!!json_encode($item)!!}' data-default-details='{!!json_encode(getDefaultClonableItem())!!}' class="clonable item-input child-element rounded border border-dark p-2 mb-2 depth-1" style="border-width: 3px !important;">

                            <label for="name_{{$number}}" class="clonable-increment-for {{trnsAlignBlockCls()}}">{{__local('Name')}}</label>
                            <input type="text" class="form-control clonable-increment-id name-one input-one" readonly="readonly" value="{{$item['name'] ?? ''}}" data-group-id="name" data-field="name" id="name_{{$number}}" data-label="{{__local('Name')}}"><br>

                            <div class="action-wrapper w-100 mt-3">
                                <button type="button" class="btn text-success clonable-button-add"><i class="h3 bi-plus-square"></i></button>
                                <button type="button" class="btn text-danger clonable-button-close delete-action json-type"><i class="h3 bi-x-square"></i></button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>


                <?php do_action("form_{$action}_col_left", $DB) ?>

            </div>
        </div>

        <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
        
            <div class="input-wrapper n-border p-3">
                <label class="{{trnsAlignBlockCls()}}" for="status">{{__local("Status")}}</label>

                <select name="status" class="select2-simple w-100" id="status" data-label="{{__local('Status')}}">
                    <?= join("", getStatusPageOption(getValueFromOldOrDB('status', $DB))) ?>
                </select>

                @if($action == "edit")
                <div class="text-left {{trnsAlignCls()}} mt-2">
                    <?php
                    $link = getShowFormLink(getTypee($DB));
                    ?>
                    <label class="mx-1">{{__local("link")}} :</label><a class="d-inline-block align-middle link-info bg-info text-white rounded p-2 mb-2" href="<?= $link ?>" target="_blank"><?= __local("Show Form Schema") ?></a><button class="btn-clipboard btn badge badge-white ml-2" data-clipboard-text="<?= $link ?>" type="button"><i class="bi bi-clipboard-check h5"></i></button>
                </div>
                @endif

                <div class="action-button-wrapper" data-label="{{__local('Form Schema')}}">
                    <?php require_once getDashboardViewPath("component/buttons/add.resource.blade.php") ?>
                </div>
            </div>


            <div class="input-wrapper n-border border-success p-3 mt-3 rounded text-center">
                @if($action == "create")
                <button type="button" id="open_import_code" onclick="$('.wrapper_import_code').removeClass('d-none');$(event.target).addClass('d-none')" class="btn btn-primary">{{__local('import')}}</button>

                <div class="wrapper_import_code d-none">

                    <label for="import_code" class="control-label {{trnsAlignBlockCls()}}">{{__local("Import Code")}}</label>
                    <input value="{{getValueFromOldOrDB('import_code', $DB)}}" class="form-control mb-3" id="import_code" name="import_code" type="text" data-label="{{__local('Import Code')}}" placeholder="{{__local('Import Code')}}" required="required">
                    <button type="button" id="submit_import_code" onclick='$("#main-form").submit()' class="btn btn-success">{{__local('Submit')}}</button>

                </div>
                @elseif($action == "edit")
                <button type="button" id="formSchemaMap" class="btn btn-warning create-map-from-inputs" data-options='{"#title" : {"get":"val"} , "#type" : {"get":"val"} , "#schema" : {"get":"val"} , "#status" : {"get":"val"} , "#is_login_required" : {"get":"prop" , "type" : "checked"} , "#is_captcha_required" : {"get":"prop" , "type" : "checked"} }'>{{__local('Export')}}</button>
                <button class="btn-clipboard d-none" id="formSchemaMap-copy" data-clipboard-text="" type="button"><i class="bi bi-clipboard-check h5"></i></button>
                @endif
            </div>

            <div class="input-wrapper wrapper-properties n-border border-primary p-3 mt-3 rounded" data-json='{{json_encode(getFormInputTypes("*"))}}'>
                <h3 class="{{trnsAlignBlockCls()}} title-introduce border-bottom pb-2">{{__local('Properties')}}</h3> <span class="h6 text-help-properties text-danger {{trnsAlignBlockCls()}}">{{__local('to see Properties values click on item')}}</span>
                <div class="wrapper">
                    <div class="input-wrapper-form-properties">
                        <label for="name" class="control-label {{trnsAlignBlockCls()}}">{{__local("Name")}}</label>
                        <input {!!isFormInputPropertiesRequiredHtml("name")!!} value="" class="form-control input-form-prop-one mb-3" id="name" name="name" type="text" data-label="{{__local('Name')}}" placeholder="{{__local('Name')}}">
                    </div>

                    <div class="input-wrapper-form-properties mb-3">
                        <label for="type-input-form" class="control-label {{trnsAlignBlockCls()}}">{{__local('input Type')}}</label>
                        <select {!!isFormInputPropertiesRequiredHtml("type-input-form")!!} name="type-input-form" class="select2-simple input-form-prop-one w-100" id="type-input-form" data-label="{{__local('input Type')}}">
                            <?= join("", getFormInputTypesOption()) ?>
                        </select>
                    </div>


                    <div class="input-wrapper-form-properties">
                        <label for="id-form" class="control-label {{trnsAlignBlockCls()}}">{{__local("ID")}}</label>
                        <input {!!isFormInputPropertiesRequiredHtml("id-form")!!} dir="ltr" value="" class="form-control input-form-prop-one mb-3" id="id-form" name="id-form" type="text" data-label="{{__local('ID')}}" placeholder="{{__local('ID')}}">
                    </div>

                    <div class="input-wrapper-form-properties">
                        <label for="class" class="control-label {{trnsAlignBlockCls()}}">{{__local("Class")}}</label>
                        <input {!!isFormInputPropertiesRequiredHtml("class")!!} dir="ltr" value="" class="form-control input-form-prop-one mb-3" id="class" name="class" type="text" data-label="{{__local('Class')}}" placeholder="{{__local('Class')}}">
                    </div>

                    <div class="input-wrapper-form-properties">
                        <label for="class-wrapper" class="control-label {{trnsAlignBlockCls()}}">{{__local("Class Wrapper")}}</label>
                        <input {!!isFormInputPropertiesRequiredHtml("class-wrapper")!!} dir="ltr" value="" class="form-control input-form-prop-one mb-3" id="class-wrapper" name="class-wrapper" type="text" data-label="{{__local('Class Wrapper')}}" placeholder="{{__local('Class Wrapper')}}">
                    </div>

                    <div class="input-wrapper-form-properties extra-html-attributes-properties">
                        <label for="extra-html-attributes" class="control-label {{trnsAlignBlockCls()}}">{{__local("Extra Html Attributes")}}</label>
                        <input {!!isFormInputPropertiesRequiredHtml("extra-html-attributes")!!} dir="ltr" value="" class="form-control input-form-prop-one mb-3" id="extra-html-attributes" name="extra-html-attributes" type="text" data-label="{{__local('Extra Html Attributes')}}" placeholder="{{__local('Extra Html Attributes')}}">
                        <div class="tip d-none">
                            <p class="description {{trnsAlignBlockCls()}}"></p>
                            <div class="sample"></div>
                        </div>
                    </div>

                    <div class="input-wrapper-form-properties">
                        <label for="placeholder-form" class="control-label {{trnsAlignBlockCls()}}">{{__local("Placeholder")}}</label>
                        <input {!!isFormInputPropertiesRequiredHtml("placeholder-form")!!} value="" class="form-control input-form-prop-one mb-3" id="placeholder-form" name="placeholder-form" type="text" data-label="{{__local('Placeholder')}}" placeholder="{{__local('Placeholder')}}">
                    </div>

                    <div class="input-wrapper-form-properties mb-2">
                        <div class="child-element custom-control custom-switch text-center">
                            <input {!!isFormInputPropertiesRequiredHtml("is_required")!!} type="checkbox" class="custom-control-input input-form-prop-one the-value" name="is_required" id="is_required" value="ON">
                            <label class="custom-control-label" for="is_required">{{__local('is Required ?')}}</label>
                        </div>
                    </div>

                    <div class="input-wrapper-form-properties">
                        <label for="server-rules" class="control-label {{trnsAlignBlockCls()}}">{{__local("Server validation classes")}}</label>
                        <textarea {!!isFormInputPropertiesRequiredHtml("server-rules")!!} dir="ltr" value="" class="form-control input-form-prop-one mb-3" id="server-rules" name="server-rules" data-label="{{__local('Server validation classes')}}" placeholder='["email:true"]'></textarea>
                    </div>

                </div>
            </div>

            <?php do_action("form_{$action}_col_right", $DB) ?>

        </div>

    </div>
</form>


<x-dashboard.cpo::footer />