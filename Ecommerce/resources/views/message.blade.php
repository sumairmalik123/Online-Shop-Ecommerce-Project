@if (Session::has('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4><i class="icon fa fa-check"></i> Success!</h4>
    {{ Session::get('success') }}
</div>
@endif

@if (Session::has('error'))
<div class="alert alert-danger alert-dismissible __web-inspector-hide-shortcut__">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4> {{ Session::get('error') }}
</div>
@endif
