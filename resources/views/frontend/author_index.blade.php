@include("frontend.parts.header")

<h1>Archive Author {{getTypeFullname($DB)}}</h1>


@foreach($DB->post_types as $post_type)
<div class="post_type_wrapper">
    <h1>{{getTypeTitle($post_type)}}</h1>
</div>
@endforeach

{{showPageLink($DB->historyAction)}}

@include("frontend.parts.footer")