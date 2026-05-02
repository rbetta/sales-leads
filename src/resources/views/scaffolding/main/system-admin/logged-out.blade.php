<!doctype html>
<html>
	<head>

		<title>
			@section('title')
			Sales Lead System
			@show
		</title>
		
		@include('scaffolding.main.common-headers')

		@section('html-head-append')
		@show

	</head>
	<body>
		<div id="all_content">
			<div id="header">
				<!-- BEGIN SECTION: header -->
				@section('header')
				@show

				<!-- END SECTION: header -->
			</div>
			<div id="content">
				<div id="main">
					<!-- BEGIN SECTION: main -->

					@section('main')
					@show

					<!-- END SECTION: main -->
				</div>
				<div class="clear"></div>
			</div>
			<div id="footer">
				<!-- BEGIN SECTION: footer -->

				@section('footer')
				@show

				<!-- END SECTION: footer -->
			</div>
		</div>
	</body>
</html>
