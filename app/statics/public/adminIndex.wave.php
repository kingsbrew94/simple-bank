<!DOCTYPE html>
<html lang="en">

<head>
    <title>{~$app_name~}</title><meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    {~usecss('css/admin/bootstrap.min')~}
    {~usecss('css/admin/bootstrap-responsive.min')~}
    @if $pageType === 'adminLogin':
        @usecss('css/admin/matrix-login')
        @usejs('libs/angular/angular.min')
    @elif $pageType === 'adminDashboard':
        @usecss('css/admin/fullcalendar')
        @usecss('css/admin/matrix-style')
        @usecss('css/admin/matrix-media')
    @else:
        @usecss('css/admin/colorpicker')
        @usecss('css/admin/uniform')
        @usecss('css/admin/select2')
        @usecss('css/admin/matrix-style')
        @usecss('css/admin/matrix-media')
        @usecss('css/admin/bootstrap-wysihtml5')
    @endif
    @usecss('fonts/admin/font-awesome/css/font-awesome')
    @usecss('snackbar')
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>
<body>
    {:children}
    <input type="hidden" id="snackBarMessage" value="{~ $snackBarMessage ~}"/>
    <input type="hidden" id="snackBarState"   value="{~ $snackBarState ? 'success' : 'error' ~}"/>
    @if $pageType === 'adminLogin':
        <wv:comp.admin.loginJS />
        @usejs('libs/brew/servo')
        @usejs('js/admin/custom/adlogin')
    @elif $pageType === 'adminDashboard':
        <wv:comp.admin.dashboardJS />
    @else:
        @usejs('js/admin/jquery.min')
        @usejs('js/admin/jquery.ui.custom')
        @usejs('js/admin/bootstrap.min')
        @usejs('js/admin/bootstrap-colorpicker')
        @usejs('js/admin/masked')
        @usejs('js/admin/jquery.uniform')
        @usejs('js/admin/select2.min')
        @usejs('js/admin/matrix')
        @usejs('js/admin/matrix.form_common')
        @usejs('js/admin/wysihtml5-0.3.0')
        @usejs('js/admin/jquery.peity.min')
        @usejs('js/admin/bootstrap-wysihtml5')
        <script>
		    $('.textarea_editor').wysihtml5();
	    </script>
    @endif
    @usejs('snackbar')
    @usejs('libs/brew/lib',['data-main' => statics('js/src',false)])
    @usejs('js/admin/custom/loader')
</body>
</html>
