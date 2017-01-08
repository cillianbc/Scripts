<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<title>Bidding | AdWords Search</title>
	
	<script type="text/javascript">
	function UpdateSettingsTablePressed() {
		$.ajax({
			method: "POST",
			url: 'js_ajax/_s2_get_settings_table.php',
			async: true,
			dataType: 'html',
			contentType: 'application/json; charset=utf-8'
		})
		.done(function( msg ) {
			$("#SettingsTable").html(msg);
		});
	}
	function UpdateClientsCanBeAdded() {
		$.ajax({
			method: "POST",
			url: 'js_ajax/_s2_get_table_possible_clients.php',
			async: true,
			dataType: 'html',
			contentType: 'application/json; charset=utf-8'
		})
		.done(function( msg ) {
			$("#ClientsCanBeAdded").html(msg);
		});
	}
	function Prepare_unselected_keywords_div() {
		$('#unselected_keywords_div').html(''+
				'<div class=\"form-group col-xs-12\" style=\"padding-left:15px;padding-right:15px;margin-bottom:0;\">'+
				'<label for=\"unselected_keywords_table\">Exclude Keyword(s) from bidding (optional)</label>'+
				'<table id=\"unselected_keywords_table\" class=\"table table-bordered table-condensed\" style=\"margin-bottom:5px;\">'+
					'<tr>'+
						'<th>Keyword ID</th>'+
						'<th>Keyword Text</th>'+
					'</tr>'+
					'<tr>'+
						'<td><input id=\"unselected_keywords_cell_add_id\" type=\"number\" class=\"form-control\" placeholder=\"Keyword ID\" style=\"width:100%;\"></td>'+
						'<td><input id=\"unselected_keywords_cell_add_text\" type=\"text\" class=\"form-control\" placeholder=\"Keyword text\" maxlength=\"250\" style=\"width:100%;\"></td>'+
					'</tr>'+
				'</table>'+
				'<div class=\"form-inline\">'+
					'<a class=\"btn btn-default\" onclick=\"Unselected_keywords_Add()\" role=\"button\">Add</a>'+
					'<a class=\"btn btn-default\" onclick=\"Unselected_keywords_Delete()\" role=\"button\" style=\"margin-left:5px;\">Exclude</a>'+
				'</div>'+
			'</div>');
	}
	function Unselected_keywords_table_add_row(uk_id, uk_text) {
		if (uk_id != '') {
			var row_id = 'unselected_keywords_row_'+uk_id;
			var r = $('#'+row_id);

			if (r.length == 0) {
				var tableRef = $('#unselected_keywords_table tbody')[0];
				var newRow = tableRef.insertRow(tableRef.rows.length-1);
				newRow.outerHTML = '<tr id=\"'+row_id+'\"><td class="col_id">'+uk_id+'</td><td class="col_text">'+uk_text+'</td></tr>';
				return true;
			}
		}
		return false;
	}
	function Unselected_keywords_Add() {
		if (Unselected_keywords_table_add_row($('#unselected_keywords_cell_add_id').val(), $('#unselected_keywords_cell_add_text').val())) {
			$('#unselected_keywords_cell_add_id').val('');
			$('#unselected_keywords_cell_add_text').val('');
		}
	}
	function Unselected_keywords_Delete() {
		var uk_id = $('#unselected_keywords_cell_add_id').val();
		if (uk_id != '') {
			var row_id = 'unselected_keywords_row_'+uk_id;
			var r = $('#'+row_id);

			if (r.length > 0) {
				r.remove();
			}
		}
	}
	function ReplaceProc(str) {
		return str.replace('%','');
	}
	function FreqToHours(str) {
		if (str == 'Never') {
			return 0;
		}
		else if (str == '1 hour') {
			return 1;
		}
		else if (str.indexOf(' hours') >= 0) {
			return parseInt(str.replace(' hours',''));
		}
		else if (str.indexOf(' days') >= 0) {
			return 24*parseInt(str.replace(' days',''));
		}
		return 0;
	}
	function HoursToFreq(n) {
		if (n == 0) {
			return 'Never';
		}
		else if (n == 1) {
			return '1 hour';
		}
		else if (n > 24) {
			return ''+(n/24)+' days';
		}
		else {
			return ''+n+' hours';
		}
	}
	function NumberToRange(str) {
		switch (str) {
			case 'today + yesterday':
				return 1;
				break;
			case 'today + last 3 days':
				return 3;
				break;
			case 'today + last 7 days':
				return 7;
				break;
			default:
				return 0;
		}
	}
	function ClickSave(Action) {
		var Client_Name = '';
		var Freq_run = '';
		var Start_time = '';
		var KW = '';
		var Date_range = '';
		var Unsel_kws = '';

		if (Action != 'delete') {
			Client_Name = $('#NewClientName').val();
			Freq_run = FreqToHours($('#NewFreqRun').val());
			Start_time = $('#NewStartTime').val();
			KW = $('#NewKeywordsImpressions').val();
			Date_range = NumberToRange($('#NewDateRange').val());

			var ar_kw = [];
			var all_rows = $('#unselected_keywords_table tr');
			for (var i = 0; i < all_rows.length; i++) {
				var cur_id = all_rows[i].id;
				if (cur_id != '') {
					var uk_id = $('#unselected_keywords_table tr#'+cur_id+' td.col_id').text();
					var uk_text = $('#unselected_keywords_table tr#'+cur_id+' td.col_text').text();
					ar_kw.push({id:uk_id, text:uk_text});
				}
			}
			Unsel_kws = JSON.stringify(ar_kw);
		}
		//console.log(Unsel_kws);
		
		var Client_Id = $('#NewClientId').val();

		//console.log($('#cpc_10_19').val());
		//return;
		$.ajax({
			method: "GET",
			url: 'js_ajax/_s2_save_setting_row.php',
			data: {
				action: Action,
				client_id: Client_Id,
				client_name: Client_Name,
				script_num: 1,
				freq_run: Freq_run,
				start_time: Start_time,
				kw_imp: KW,
				date_range: Date_range,
				cpc_10_19: $('#cpc_10_19').val(),
				cpc_20_24: $('#cpc_20_24').val(),
				cpc_25_29: $('#cpc_25_29').val(),
				cpc_30_49: $('#cpc_30_49').val(),
				cpc_50_59: $('#cpc_50_59').val(),
				cpc_60_79: $('#cpc_60_79').val(),
				cpc_80_ff: $('#cpc_80_ff').val(),
				unsel_kws: Unsel_kws
			},
			contentType: 'application/json; charset=utf-8',
			async: true,
			dataType: 'text'
		})
		.fail(function() {
			alert('Data saving has been finished with an error!');
		})
		.done(function( msg ) {
			//console.log(msg);
			var updated_rows = parseInt(msg);
			if (updated_rows > 0) {
				UpdateSettingsTablePressed();
				UpdateClientsCanBeAdded();
			}
		});
		
		$('#NewClientId').val('');
		$('#NewClientName').val('');
		$("#modalNewClient").modal('hide');
	}
	function AddNewClientButton() {
		$('#NewClientId').val('');
		$('#NewClientName').val('');
		AddClientCommon();
	}
	function AddClientCanBe(td_id, td_name) { // click on button ADD
		$('#NewClientId').val($('#'+td_id).text());
		$('#NewClientName').val($('#'+td_name).text());
		AddClientCommon();
	}
	function AddClientCommon() {
		$('#NewClientId').prop('readonly',false);
		$('#NewStartTime').val('');
		$('#NewFreqRun').val('Never');
		$('#NewKeywordsImpressions').val('0');
		$('#NewDateRange').val('today');

		$('#cpc_10_19').val('0');
		$('#cpc_20_24').val('0');
		$('#cpc_25_29').val('0');
		$('#cpc_30_49').val('0');
		$('#cpc_50_59').val('0');
		$('#cpc_60_79').val('0');
		$('#cpc_80_ff').val('0');
		
		$('#DeleteClientButton').hide();
		$('#SaveNewClient').attr('onclick', 'ClickSave(\'add\')');
		$('#modalNewClientLabel').text('Add New Account');
		Prepare_unselected_keywords_div();
		$('#modalNewClient').modal('show');
}
	function EditNewClientButton(num_row) {
		$('#NewClientId').val( $('#'+num_row+' td.col_id').text() );
		$('#NewClientId').prop('readonly',true);
		$('#NewClientName').val($('#'+num_row+' td.col_name').text());
		$('#NewFreqRun').val($('#'+num_row+' td.col_freq_run').text());
		$('#NewStartTime').val($('#'+num_row+' td.col_start_time').text().replace(':00',''));
		$('#NewKeywordsImpressions').val($('#'+num_row+' td.col_kw_imp').text());
		$('#NewDateRange').val($('#'+num_row+' td.col_date_range').text());

		$('#cpc_10_19').val(ReplaceProc($('#'+num_row+' td.col_cpc_10_19').text()));
		$('#cpc_20_24').val(ReplaceProc($('#'+num_row+' td.col_cpc_20_24').text()));
		$('#cpc_25_29').val(ReplaceProc($('#'+num_row+' td.col_cpc_25_29').text()));
		$('#cpc_30_49').val(ReplaceProc($('#'+num_row+' td.col_cpc_30_49').text()));
		$('#cpc_50_59').val(ReplaceProc($('#'+num_row+' td.col_cpc_50_59').text()));
		$('#cpc_60_79').val(ReplaceProc($('#'+num_row+' td.col_cpc_60_79').text()));
		$('#cpc_80_ff').val(ReplaceProc($('#'+num_row+' td.col_cpc_80_ff').text()));

		$('#DeleteClientButton').show();
		$('#SaveNewClient').attr('onclick', 'ClickSave(\'update\')');
		$('#modalNewClientLabel').text('Update Account');
		Prepare_unselected_keywords_div();
		Fill_unselected_keywords_table($('#NewClientId').val());
		//$('#modalNewClient').modal('show');
	}
	function Fill_unselected_keywords_table(client_id) {
		$.ajax({
			method: "GET",
			url: 'js_ajax/_s2_get_unselected_kw_list.php',
			data: {id: client_id},
			contentType: 'application/json; charset=utf-8',
			async: true,
			dataType: 'text'
		})
		.fail(function() {
			alert('Unselected keywords haven\'t been received!');
		})
		.done(function( msg ) {
			//console.log(msg);
			var r = JSON.parse(msg);
			//console.log(r);
			for (var i = 0; i < r.length; i++) {
				Unselected_keywords_table_add_row(r[i].id, r[i].text)
			}
			$('#modalNewClient').modal('show');
		});
	}

	$(document).ready(function(){
		UpdateSettingsTablePressed();
		UpdateClientsCanBeAdded();
	});
	</script>
	<noscript>Your browser does not support JavaScript!</noscript>

</head>

<body>
<div class="container-fluid">
	<div class="row">
        <div class="col-lg-12">
			<h3>AdWords Search Network Bidding (not Display Network)</h3>
			<form class="form-inline">
				<a class="btn btn-default" onclick="AddNewClientButton()" role="button">Add Account</a>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<table id="SettingsTable" class="table table-bordered"></table>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<a class="btn btn-default" data-toggle="collapse" href="#collapseClientsCanBeAdded" aria-expanded="true" aria-controls="collapseClientsCanBeAdded">Add a client from the list of existing clients</a>
			<div class="collapse in" id="collapseClientsCanBeAdded">
				<table id="ClientsCanBeAdded" class="table table-bordered"></table>
			</div>
		</div>
	</div>
</div>

<!-- Modal Adding New Client -->
<div class="modal fade" id="modalNewClient" tabindex="-1" role="dialog" aria-labelledby="modalNewClientLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="modalNewClientLabel">New client</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="form-group col-xs-3" style="padding-left:15px;padding-right:5px;">
						<label for="NewClientId">Client ID</label>
						<input id="NewClientId" type="text" class="form-control" maxlength="12" placeholder="ID" style="width:100%;">
					</div>
					<div class="form-group col-xs-9" style="padding-left:5px;padding-right:15px;">
						<label for="NewClientName">Account name</label>
						<input id="NewClientName" type="text" class="form-control" maxlength="250" placeholder="Name" style="width:100%;">
					</div>
				</div>
				<div class="row">
					<div class="form-group col-xs-3" style="padding-left:15px;padding-right:5px;">
						<label for="NewFreqRun"><br>Frequency to bid</label>
						<select id="NewFreqRun" class="form-control" style="width:100%;">
  							<option>1 hour</option>
  							<option>3 hours</option>
  							<option>4 hours</option>
  							<option>6 hours</option>
  							<option>8 hours</option>
  							<option>12 hours</option>
  							<option>24 hours</option>
  							<option>3 days</option>
  							<option>7 days</option>
  							<option>Never</option>
						</select>
					</div>
					<div class="form-group col-xs-3" style="padding-left:5px;padding-right:5px;">
						<label for="NewStartTime">First bidding at:<br>(hour of day)</label>
						<input id="NewStartTime" type="number" class="form-control" placeholder="0-23" style="width:100%;">
					</div>
					<div class="form-group col-xs-3" style="padding-left:5px;padding-right:5px;">
						<label for="NewKeywordsImpressions">Min. Impressions req'd since last bidding</label>
						<input id="NewKeywordsImpressions" type="number" class="form-control" placeholder="Keywords impressions above" style="width:100%;">
					</div>
					<div class="form-group col-xs-3" style="padding-left:5px;padding-right:15px;">
						<label for="NewDateRange"><br>Bid using data from</label>
						<select id="NewDateRange" class="form-control" style="width:100%;">
  							<option>today</option>
  							<option>today + yesterday</option>
  							<option>today + last 3 days</option>
  							<option>today + last 7 days</option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-xs-12" style="padding-left:15px;padding-right:15px;">
						<label for="cpc_table">When Avg Posn is:</label>
						<table id="cpc_table" class="table table-bordered table-condensed" style="margin-bottom:0;">
							<tr>
								<th>1.0 - 1.9</th>
								<th>2.0 - 2.4</th>
								<th>2.5 - 2.9</th>
								<th>3.0 - 4.9</th>
								<th>5.0 - 5.9</th>
								<th>6.0 - 7.9</th>
								<th>8.0 - ~</th>
							</tr>
							<tr>
								<th colspan="7" style="text-align:center;">Change Max. CPC ( + / - ) by %</th>
							</tr>
							<tr>
								<td><input id="cpc_10_19" type="number" class="form-control" placeholder="1.0-1.9" style="width:100%;"></td>
								<td><input id="cpc_20_24" type="number" class="form-control" placeholder="2.0-2.4" style="width:100%;"></td>
								<td><input id="cpc_25_29" type="number" class="form-control" placeholder="2.5-2.9" style="width:100%;"></td>
								<td><input id="cpc_30_49" type="number" class="form-control" placeholder="3.0-4.9" style="width:100%;"></td>
								<td><input id="cpc_50_59" type="number" class="form-control" placeholder="5.0-5.9" style="width:100%;"></td>
								<td><input id="cpc_60_79" type="number" class="form-control" placeholder="6.0-7.9" style="width:100%;"></td>
								<td><input id="cpc_80_ff" type="number" class="form-control" placeholder="8.0- ~" style="width:100%;"></td>
							</tr>
						</table>
					</div>
				</div>
				<div id="unselected_keywords_div" class="row">
				</div>
			</div>
			<div class="modal-footer">
				<div class="col-xs-2" style="padding-right:0;padding-left:0;text-align:left;">
					<button id="DeleteClientButton" type="button" class="btn btn-danger" onclick="ClickSave('delete')">Delete</button>
				</div>
				<div class="col-xs-10" style="padding-right:0;padding-left:0;">
					<button id="SaveNewClient" type="button" class="btn btn-primary" onclick="ClickSave()">Save</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>