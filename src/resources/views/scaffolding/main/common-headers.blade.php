<!-- Token to guard against Cross-Site Request Forgery. -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Files for jQuery and jQuery-UI. -->
<script type="text/javascript" src="/js/lib/jquery/jquery-3.7.1.min.js"></script>
<script type="text/javascript" src="/js/lib/jquery-ui-1.14.1/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="/js/lib/jquery-ui-1.14.1/jquery-ui.min.css" />

@verbatim
<!-- Automatically add the CSRF token to every jQuery AJAX request. -->
<script type="text/javascript">
jQuery.ajaxSetup({
	headers : {
		'X-CSRF-TOKEN' : jQuery('meta[name="csrf-token"]').attr('content')
	}
});
</script>
@endverbatim

<!-- Application-specific JavaScript files. -->
<script type="text/javascript" src="/js/system-admin/main.js"></script>
<script type="text/javascript" src="/js/system-admin/custom.js"></script>

<!-- Application-specific Cascading Style Sheets. -->
<link rel="stylesheet" type="text/css" href="/css/system-admin/main.css" />
<link rel="stylesheet" type="text/css" href="/css/system-admin/custom.css" />
