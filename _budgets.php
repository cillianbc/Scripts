<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<title>B​udget Balancer | AdWords Search​ & Display​</title>
	
	<script type="text/javascript">
    function cookieExists(name) {
        var nameToFind = name + "=";
        var cookies = document.cookie.split(';');
        for (var i = 0; i < cookies.length; i++) {
            if (cookies[i].trim().indexOf(nameToFind) === 0) return true;
        }
        return false;
    }
	function cookieGet(name) {
		var nameToFind = name + "=";
        var cookies = document.cookie.split(';');
        for (var i = 0; i < cookies.length; i++) {
            if (cookies[i].trim().indexOf(nameToFind) === 0) {
				return cookies[i].trim().replace(nameToFind,"");
			}
        }
        return "";
	}
	function cookieSet(name, value) {
		var now = new Date();
		now.setTime(now.getTime() + 30 * 24 * 60 * 60 * 1000); // keep it for 30 days
		document.cookie = ""+name +"="+ value + ";expires=" + now.toGMTString() + ";path=/;" + document.cookie;
	}
	function monthsToDate(curDate, count) {
		var m = curDate.getMonth();
		var y = curDate.getFullYear();
		if (count < 0) {
			for (var i = 0; i < -count; i++) {
				m--;
				if (m < 0) {
					m=11;
					y--;
				}
			}
		}
		else {
			for (var i = 0; i < count; i++) {
				m++;
				if (m > 11) {
					m=0;
					y++;
				}
			}
		}
		curDate.setFullYear(y);
		curDate.setMonth(m);
		return curDate;
	}
	function UpdateBudgetTablePressed() {
		var sd = $("#StartDateBudget").val();
		var ed = $("#EndDateBudget").val();
		setDateBudget("_StartDateBudget", sd, -3);
		setDateBudget("_EndDateBudget", ed, 1);
		sd = $("#StartDateBudget").val();
		ed = $("#EndDateBudget").val();
		
		$.ajax({
			method: "GET",
			url: 'js_ajax/_get_budget_table.php',
			data: { s: sd, e: ed }
		})
		.done(function( msg ) {
			$("#BudgetTable").html(msg);
		});
	}
	function getMonthString(date) {
		var y = date.getFullYear();
		var m = date.getMonth()+1;
		if (m < 10) {
			return y.toString()+"-0"+m.toString();
		}
		else {
			return y.toString()+"-"+m.toString();
		}
			
		
	}
	function setDateBudget(name, value, addMonths) {
		if (value == "") {
			var now = new Date();
			now.setDate(1);
			now = monthsToDate(now, addMonths);
			value = getMonthString(now);
		}
		cookieSet(name, value);
		
		var elemName = "#"+name.replace("_","");
		if ($(elemName).val() === "") {
				$(elemName).val(value);
			}
	}
	function UpdateClientsCanBeAdded() {
		$.ajax({
			url: 'js_ajax/_get_table_possible_clients.php'
		})
		.done(function( msg ) {
			$("#ClientsCanBeAdded").html(msg);
		});
	}
	function AddClientCanBe(td_id, td_name) {
		AddNewClientPressed($('#'+td_id).text(), $('#'+td_name).text());
	}
	function AddNewClientButtonPressed() {
		AddNewClientPressed('','');
	}
	function AddNewClientPressed(_id, _name) {
		$('#NewClientId').val(_id);
		$('#NewClientName').val(_name);
		
		$('#modalNewClientLabel').text('​​Add New Account');
		$('#SaveNewClientButton').data('Action','Add');
		$('#NewClientAlgorithm').prop('checked',false);
		
		_ro = $('#NewClientId').attr('readonly');
		if (_ro != undefined) {
			$('#NewClientId').removeAttr('readonly');
		}
		
		$('#DeleteClientButton').hide();
		$('#modalNewClient').modal('show');
	}
	function UpdateClientPressed(td_id, td_name, alg) {
		$('#NewClientId').val($('#'+td_id).text());
		$('#NewClientName').val($('#'+td_name).text());
		
		$('#modalNewClientLabel').text('Update ​​Account');
		$('#SaveNewClientButton').data('Action','Update');
		_alg = true;
		if (alg == 0) {_alg = false;}
		$('#NewClientAlgorithm').prop('checked',_alg);
		
		_ro = $('#NewClientId').attr('readonly');
		if (_ro == undefined) {
			$('#NewClientId').attr('readonly','');
		}
		
		$('#DeleteClientButton').show();
		$('#modalNewClient').modal('show');
	}
	
	$(document).ready(function(){
		if (!cookieExists("_StartDateBudget")) {
			setDateBudget("_StartDateBudget", "", -3);
		}
		if ($("#StartDateBudget").val() === "") {
			var mon = cookieGet("_StartDateBudget");
			$("#StartDateBudget").val(mon);
		}
		if (!cookieExists("_EndDateBudget")) {
			setDateBudget("_EndDateBudget", "", 1);
		}
		if ($("#EndDateBudget").val() === "") {
			var mon = cookieGet("_EndDateBudget");
			$("#EndDateBudget").val(mon);
		}
		UpdateBudgetTablePressed();
		UpdateClientsCanBeAdded();
	});
	</script>
	<noscript>Your browser does not support JavaScript!</noscript>

</head>

<body>
<div class="container-fluid">
	<div class="row">
        <div class="col-lg-12">
			<h3>Budget Balancer | ​AdWords Search ​& Display ​Network​s</h3>
			<form class="form-inline">
				<div class="form-group">
					<label for="StartDateBudget">Period:</label>
					<input class="form-control" type="month" id="StartDateBudget">
				</div>
				<div class="form-group">
					<label for="EndDateBudget"> - </label>
					<input class="form-control" type="month" id="EndDateBudget">
				</div>
				<input onclick="UpdateBudgetTablePressed()" class="btn btn-default" type="button" value="Refresh​​">
				<input data-toggle="modal" onclick="AddNewClientButtonPressed()" class="btn btn-default" type="button" value="Add Account">
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<table id="BudgetTable" class="table table-bordered"></table>
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

<!-- Modal New Budget -->
<div class="modal fade" id="modalNewBudget" data-cellid="" data-clientid="" data-month="" tabindex="-1" role="dialog" aria-labelledby="modalNewBudgetLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="modalNewBudgetLabel">New Budget</h4>
			</div>
			<div class="modal-body">
				<label id="NewBudgetSumLabel" for="NewBudgetSum">2015-15:</label>
				<input id="NewBudgetSum" type="number" data-clientid="111">
			</div>
			<div class="modal-footer">
				<button id="SaveNewBudgetSum" type="button" class="btn btn-primary">Save</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
				<form class="form-horizontal">
					<div class="form-group">
						<label for="NewClientId" class="col-sm-2 control-label">Client ID:</label>
						<div class="col-sm-3">
							<input id="NewClientId" type="text" class="form-control" maxlength="12" placeholder="ID">
						</div>
					</div>
					<div class="form-group">
						<label for="NewClientName" class="col-sm-2 control-label">Account name:</label>
						<div class="col-sm-10">
							<input id="NewClientName" type="text" class="form-control" maxlength="250" placeholder="Name">
						</div>
					</div>
					<div class="form-group">
						<label for="NewClientNameClassCheckbox" class="col-sm-2 control-label"> </label>
						<div class="col-sm-10">
							<div id="NewClientNameClassCheckbox" class="checkbox">
								<label>
									<input id="NewClientAlgorithm" type="checkbox"> Percents will be taken from campaign's names
								</label>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<div class="col-xs-2" style="padding-right:0;padding-left:0;text-align:left;">
					<button id="DeleteClientButton" type="button" class="btn btn-danger">Delete</button>
				</div>
				<div class="col-xs-10" style="padding-right:0;padding-left:0;">
					<button id="SaveNewClientButton" type="button" class="btn btn-primary">Save</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>



<script>
	// events from #modalNewBudget
	$('#modalNewBudget').on('show.bs.modal', function (e) {
		// output client name and month
		//console.group("show.bs.modal");
		//console.log("show.bs.modal",e,e.relatedTarget);
		var selected_td_id = e.relatedTarget.id; // clicked cell id 
		var selected_td_id_search = "#"+selected_td_id;
		var client_id_cell = '#'+$(selected_td_id_search).data('clientid');
		var client_id = $(client_id_cell).text();
		//console.info(client_id);
		var client_name_cell = '#'+$(selected_td_id_search).data('clientname');
		var client_name = $(client_name_cell).text();
		//console.info(client_name);
		var mon = $(selected_td_id_search).data('month');
		//console.info(mon);
		var sum = $(selected_td_id_search).text();
		//console.info(sum);
		var month_title_th_id_search = "#"+$(selected_td_id_search).data('monthtitle');
		var month_title = $(month_title_th_id_search).text();
		//console.info(month_title);
		
		// set data to #modalNewBudget
		$('#modalNewBudget').attr("data-cellid", selected_td_id);
		$('#modalNewBudget').attr("data-clientid", client_id);
		$('#modalNewBudget').attr("data-month", mon);
		//console.info($('#modalNewBudget').attr("data-cellid"), $('#modalNewBudget').attr("data-clientid"), $('#modalNewBudget').attr("data-month"));
		
		// set visible attributes
		$('#modalNewBudgetLabel').text(client_name);
		$('#NewBudgetSumLabel').text(month_title+":");
		$('#NewBudgetSum').val(sum);
		//console.groupEnd();
	});
	$("#SaveNewBudgetSum").on("click", function(){
		//console.group("SaveNewBudgetSum click");
		var sum = $('#NewBudgetSum').val();
		var client_id = $('#modalNewBudget').attr("data-clientid");
		var mon = $('#modalNewBudget').attr("data-month");
		//console.log(client_id, mon, sum);
		
		$.ajax({
			method: "POST",
			url: 'js_ajax/_set_new_budget.php',
			data: { client_id: client_id, mon: mon, sum: sum }
		})
		.done(function( msg ) {
			//console.info("msg: "+msg);
			
			$.ajax({
				method: "POST",
				url: 'js_ajax/_get_budget.php',
				data: { client_id: client_id, mon: mon }
			})
			.done(function( bud ) {
				//console.info("bud: "+bud);
				var selected_td_id = "#"+$('#modalNewBudget').attr("data-cellid");
				$(selected_td_id).text(parseFloat(bud).toFixed(2));
			});
		});
		
		//console.groupEnd();
		$('#modalNewBudget').modal('hide');
	});
	
	// events from #modalNewClient
	$('#DeleteClientButton').on('click', function(){
		var Client_Id = $('#NewClientId').val();
		$.ajax({
			method: "POST",
			url: 'js_ajax/_delete_client.php',
			data: { client_id: Client_Id }
		})
		.done(function( msg ) {
			var updated_rows = parseInt(msg);
			if (updated_rows > 0) {
				UpdateBudgetTablePressed();
				UpdateClientsCanBeAdded();
			}
		});
		
		$("#modalNewClient").modal('hide');
	});
	$("#SaveNewClientButton").on("click", function(){
		var Client_Id = $('#NewClientId').val();
		var Client_Name = $('#NewClientName').val();
		var Client_Alg = $('#NewClientAlgorithm').prop('checked');
		
		_command = $("#SaveNewClientButton").data('Action');
		if (_command == 'Add') {
			$.ajax({
				method: "POST",
				url: 'js_ajax/_add_new_client.php',
				data: { client_id: Client_Id, client_name: Client_Name, client_alg: Client_Alg }
			})
			.done(function( msg ) {
				var updated_rows = parseInt(msg);
				if (updated_rows > 0) {
					UpdateBudgetTablePressed();
					UpdateClientsCanBeAdded();
				}
			});
		}
		else if (_command == 'Update') {
			$.ajax({
				method: "POST",
				url: 'js_ajax/_update_client.php',
				data: { client_id: Client_Id, client_name: Client_Name, client_alg: Client_Alg }
			})
			.done(function( msg ) {
				var updated_rows = parseInt(msg);
				if (updated_rows > 0) {
					UpdateBudgetTablePressed();
				}
			});
		}
		
		$("#modalNewClient").modal('hide');
	});
</script>
</body>
</html>