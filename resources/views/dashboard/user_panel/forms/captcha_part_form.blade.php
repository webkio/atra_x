<div class="input-wrapper mt-3 col-12">
    <label for="captcha-code" class="control-label">{{__local('Captcha')}}</label>
    <img src="{{generateCaptcha()}}" class="mb-2 captcha-image"><br>
    <div class="w-100 text-center">
        <i class="bi bi-arrow-clockwise h3 text-success cursor-pointer captcha-reload" title="{{__local('reload captcha')}}"></i>
    </div>
    <input value="{{old('captcha-code')}}" type="text" class="form-control {{trnsAlignReverseCls()}}" name="captcha-code" id="captcha-code" placeholder="{{__local('enter the characters shown in the image')}}" data-label="{{__local('Captcha Code')}}">
</div>