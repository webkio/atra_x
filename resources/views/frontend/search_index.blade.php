@include("frontend.parts.header")
    <div class="mt-2"></div>
    {{searchForm()}}

    @if(count($DB))
    @foreach($DB as $post_type)
    <div class="post_type_wrapper">
        <h1>{{getTypeTitle($post_type)}}</h1>
    </div>
    @endforeach
    {{showPageLink($DB)}}
    @else
    <div class="bg-warning text-dark">Not Found</div>
    @endif
@include("frontend.parts.footer")