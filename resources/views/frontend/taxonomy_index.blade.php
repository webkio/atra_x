@include("frontend.parts.header")
    
    <h1>{{getTypeTitle($DB)}}</h1>
    <div class="content">
        {!!getTypeBody($DB)!!}
    </div>
   
    @foreach($DB->post_types as $post_type)
        <div class="post_type_wrapper">
            <h2>{{getTypeTitle($post_type)}}</h2>
        </div>
    @endforeach
                            
    {{showPageLink($DB->post_types)}}
@include("frontend.parts.footer")