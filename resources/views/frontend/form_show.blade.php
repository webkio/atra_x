@include("frontend.parts.header")

<div class="container">
    {!!showFormHtml(getTypee($DB) , $id , $DB)!!}
</div>


@include("frontend.parts.footer")