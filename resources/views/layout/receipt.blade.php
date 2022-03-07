<?php

use App\Classes\Hook;

?>
		<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{!! $title ?? __( 'Unamed Page' ) !!}</title>
	{{--	<link rel="stylesheet" href="{{ asset( 'css/app.css' ) }}">--}}
	
	<script>
        /**
         * constant where is registered
         * global custom components
         * @param {Object}
         */
        window.nsExtraComponents = new Object;

        /**
         * describe a global NexoPOS object
         * @param {object} ns
         */
        window.ns = {nsExtraComponents};

        /**
         * store the server date
         * @param {string}
         */
        window.ns.date = {
            current: '{{ app()->make( \App\Services\DateService::class )->toDateTimeString() }}',
            serverDate: '{{ app()->make( \App\Services\DateService::class )->toDateTimeString() }}',
            timeZone: '{{ ns()->option->get( "ns_datetime_timezone" ) }}',
            format: `{{ ns()->option->get( 'ns_datetime_format' ) }}`
        }

        /**
         * define the current language selected by the user or
         * the language that applies to the system by default.
         */
        window.ns.language = '{{ app()->getLocale() }}';
        window.ns.langFiles =   <?php echo json_encode(Hook::filter('ns.langFiles', [
			'NexoPOS' => asset("/lang/" . app()->getLocale() . ".json"),
		]));?>
	</script>
	<script src="{{ asset( ns()->isProduction() ? 'js/lang-loader.min.js' : 'js/lang-loader.js' ) }}"></script>
	@include( 'common.header-socket' )
	
	<style>
        .inline-block {
            display: inline-block;
        }

        .bb {
            border-bottom: 2px dashed #999;
        }

        #receipt {
            /* box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); */
            padding: 2mm;
            margin: 0 auto;
            width: 80mm;
            background: #FFF;
            font-family: "Futura";
            font-size: 2.2mm;
            color: rgb(40, 40, 40);
        }

        .identity {
            text-align: center;
        }

        .identity h1 {
            font-family: "PROGRESS PERSONAL USE";
            font-size: 12mm;
            letter-spacing: 1px;
            position: relative;
            color: black;
        }

        .company_name {
            font-size: 0.7rem;
            font-weight: normal;
            position: absolute;
            top: 41px;
            right: 10px;

        }

        .legal,
        .order_info {
            display: flex;
            flex-direction: row;
            justify-content: space-evenly;
            padding: 6px 0
        }

        .order_content {
            padding: 10px;
            font-size: 1.4em;
        }

        .order_content .item {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            padding-bottom: 10px;
        }

        .order_content .item .unit {
            padding-left: 4px;
            color: rgb(118, 118, 118)
        }

        .summary, .payment {
            display: flex;
            flex-direction: column;
            font-size: 1.4em;
            padding: 5px 10px;
        }
        .summary_line, .payment_line{
            display: flex;
            flex-direction: row;
            justify-content: end;
            padding: 5px 0;
        }

        .sum_label, .sum_value{
            width: 30%;
            text-align: right;
        }

        .total{
            display: flex;
            flex-direction: row;
            justify-content: space-evenly;
            font-size: 2em;
            padding: 20px 0;
        }

        .footer{
            padding: 10px 20px;
            text-align: center;
            font-size: 1.2em;
            color: rgb(118, 118, 118)
        }
        .footer_line{
            padding: 4px 0;
        }
        .website{
            color:black;
        }
	</style>
</head>
<body class="container">
@yield( 'layout.base.body' )
@section( 'layout.base.footer' )
	@include( '../common/footer' )
@show
</body>
</html>