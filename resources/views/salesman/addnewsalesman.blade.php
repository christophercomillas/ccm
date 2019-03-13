
{{--{{ dd( Illuminate\Support\Facades\Auth::user()->all()) }}--}}
<div class="form-container"> 
	<form method="POST" action="{{ route('addnewsalesman') }}" method="POST" id="_saveSalesman">
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Code</label>
					<input type="text" class="form form-control inp-b" id="sman_code" readonly="readonly" name="sman_code" autocomplete="off" value="{{ $smancode }}">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Fullname <small>(Firstname Middle Initial Lastname)</small></label>
					<input type="text" class="form form-control inp-b up" id="fullname" name="fullname" autocomplete="off" autofocus>
				</div>
				<div class="response-dialog">										
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$('input#fullname').focus();
</script>