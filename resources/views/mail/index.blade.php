<!DOCTYPE html>
<html lang="{{$GLOBALS['site_language']}}" dir="{{$GLOBALS['lang']['direction']}}">

<head>
    <!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:Pixel=
sPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8=
">
    <meta name="viewport" content="width=device-width">
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<![endif]-->
    <title></title>
    <!--[if !mso]><!-->
    <!--<![endif]-->
    <style type="text/css">
        body {
            margin: 15px !important;
            padding: 0;
            font-family: helvetica, sans-serif;
            ;
        }

        table,
        td,
        tr {
            vertical-align: middle;
            border-collapse: collapse;
        }

        * {
            line-height: inherit;
        }

        a[x-apple-data-detectors=true] {
            color: inherit !important;
            text-decoration: none !important;
        }

        /* custom style */
        .text-center {
            text-align: center;
        }

        .text-danger {
            color: #ff4f4f;
        }

        .text-white {
            color: #fff;
        }

        .text-d-none {
            text-decoration: none;
        }

        .bg-silver {
            background-color: #f4f4f4;
        }

        .bg-blue {
            background-color: #03a9f4;
        }

        .bg-green {
            background-color: #4caf50;
            color: white !important;
        }

        .bg-orange {
            background-color: #faa303;
        }

        .rounded {
            border-radius: 4px;
        }

        .d-none {
            display: none;
        }

        .d-in-block {
            display: inline-block;
        }

        .border-left-blue {
            border-left: 3px solid #03a9f4;
            ;
        }

        .mt-1 {
            margin-top: 5px;
        }

        .mt-2 {
            margin-top: 10px;
        }

        .mt-3 {
            margin-top: 15px;
        }

        .mt-4 {
            margin-top: 20px;
        }

        .mt-5 {
            margin-top: 25px;
        }

        .p-1 {
            padding: 5px;
        }

        .p-2 {
            padding: 10px;
        }

        .p-3 {
            padding: 15px;
        }

        .p-4 {
            padding: 20px;
        }

        .p-5 {
            padding: 25px;
        }

        .pl-1 {
            padding-left: 5px;
        }

        .pl-2 {
            padding-left: 10px;
        }

        .pl-3 {
            padding-left: 15px;
        }

        .pl-4 {
            padding-left: 20px;
        }

        .pl-5 {
            padding-left: 25px;
        }

        .lh-1 {
            line-height: 10px;
        }

        .lh-2 {
            line-height: 20px;
        }

        .lh-3 {
            line-height: 30px;
        }
        
        .body{
            font-size:large;
        }
    </style>

</head>

<body class="body" dir="{{$GLOBALS['lang']['direction']}}">
    <header class="bg-silver text-center p-2 rounded">
        <a href="{{getRootURL(false)}}"><img width="50" src="{{getWebsiteLogo()}}" alt="Logo"></a>
    </header>
    <section class="content-wrapper mt-2 p-2 bg-silver">
        <h1 class="title bg-orange p-1 rounded d-in-block">{!!$data['subject']!!}</h1>
        @if(empty($data['no-did-you']))
        <div class="notice text-danger">{!!__local('if you did not send this request ignore it')!!}</div>
        @endif

        <div class="description mt-2 pl-2 lh-3 border-left-blue">{!!$data['content']!!}</div>
    </section>
    <footer class="bg-silver p-2 mt-2">
        <b class="">{{getRootURL()}}</b>
    </footer>
</body>

</html>