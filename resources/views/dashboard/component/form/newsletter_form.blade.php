<?php 

$label = "";

if($type == "email"){
    $label = __local("Email");
}

?>

<form method="post" class="text-center newsletter_form" action="{{route('newsletter.create')}}">
        @csrf
        <input type="hidden" name="type" id="type" value="email" data-label="{{__local('Type Newsletter')}}">
        <div class="form-group">
                        <input type="text" name="client_id" id="client_id" class="form-control border-0 text-right" placeholder="نام شما " value="{{old('client_id')}}" data-label="{{$label}}" />
                    </div>
                    <div class="form-group">
                        <input type="email" name="client_id" id="client_id" class="form-control border-0 text-right" placeholder="ایمیل شما" value="{{old('client_id')}}" data-label="{{$label}}" required="email" />
                    </div>
                    <div>
                        <button class="btn btn-lg btn-primary btn-block border-0" type="submit">عضویت </button>
                    </div>
        
</form>


                
            