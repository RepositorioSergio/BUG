<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Affirm Example Code - PHP - Charge Management</title>
	<link rel="stylesheet" href="style.css">
	<script>
		// Grab checkout token
		<?php 
			$checkout_token = $_REQUEST['checkout_token'];
			$charge_id = $_REQUEST['charge_id'];
			$env = $_REQUEST['env'];
			echo "TOKEN: " . $checkout_token;
		?>
	</script>
</head>
<body>
	<h1>Charge Management</h1>
	<div id="content">
		<div id="forms">
			<div class="column">
			<table>
				<tr >
					<td colspan="2">
						<h4>Charge Request Data</h4>
					</td>
				</tr>
				<tr>
					<td>
						<label for="charge_id">Charge ID</label>
					</td>
					<td>
						<input type="text" id="charge_id" name="charge_id" value="<?php echo $charge_id ?>">
					</td>
				</tr>
				<tr>
					<td>
						<label for="order_id">Order ID</label>
					</td>
					<td>
						<input type="text" id="order_id">
					</td>
				</tr>
				<tr>
					<td>
						<label for="refund_amount">Refund Amount</label>
					</td>
					<td>
						<input type="text" id="refund_amount">
					</td>
				</tr>
				<tr>
					<td>
						<label for="shipping_carrier">Shipping Carrier</label>
					</td>
					<td>
						<input type="text" id="shipping_carrier">
					</td>
				</tr>
				<tr>
					<td>
						<label for="shipping_tracking">Shipping Tracking #</label>
					</td>
					<td>
						<input type="text" id="shipping_tracking">
					</td>
				</tr>			
			</table>
		</div>
		<div class="column">
			<table id="charge_response">
				<tr >
					<td colspan="2">
						<h4>Charge Response Data</h4>
					</td>
				</tr>
				<tr>
					<td>
						<label for="response-transaction_id">Transaction ID</label>
					</td>
					<td>
						<input type="text" id="response-transaction_id" name="response-transaction_id">
					</td>
				</tr>			
				<tr>
					<td>
						<label for="response-update_id">Update ID</label>
					</td>
					<td>
						<input type="text" id="response-update_id" name="response-update_id">
					</td>
				</tr>	
				<tr>
					<td>
						<label for="response-order_id">Order ID</label>
					</td>
					<td>
						<input type="text" id="response-order_id">
					</td>
				</tr>
				<tr>
					<td>
						<label for="response-state">State</label>
					</td>
					<td>
						<input type="text" id="response-state">
					</td>
				</tr>
				<tr>
					<td>
						<label for="response-balance">Balance</label>
					</td>
					<td>
						<input type="text" id="response-balance">
					</td>
				</tr>
				<tr>			
				<tr>
					<td>
						<label for="response-payable">Amount Payable</label>
					</td>
					<td>
						<input type="text" id="response-payable">
					</td>
				</tr>
				<tr>
					<td>
						<label for="response-refunded">Refunded Amount</label>
					</td>
					<td>
						<input type="text" id="response-refunded">
					</td>
				</tr>
				<tr>
					<td>
						<label for="response-auth_exp">Auth Expires</label>
					</td>
					<td>
						<input type="text" id="response-auth_exp">
					</td>
				</tr>		
				<tr>
					<td>
						<label for="response-refund_exp">Refund Until</label>
					</td>
					<td>
						<input type="text" id="response-refund_exp">
					</td>
				</tr>		
			</table>
		</div>
		<div id="actions">
			<br/>
			<h4>Charge Actions</h4>
			<div id="voidCharge">Void Charge</div>
			<div id="captureCharge">Capture Charge</div>
			<div id="refundCharge">Refund Charge</div>
			<div id="readCharge">Read Charge</div>
			<div id="updateCharge">Update Charge</div>
			<a id="startOver" href="checkout.html">start over</a>
		</div>
	</div>
		<div id="response">
			<h4>Response</h4>
			<pre id="responseData"></pre>
		</div>
	</div>
	<script>

	// Declare form variables
	var responseJson,chargeId,orderId,shippingCarrier,shippingTracking;

	var responseData = document.getElementById("responseData");
	var response_orderId = document.getElementById('response-order_id');
	var response_state = document.getElementById('response-state');
	var response_balance = document.getElementById('response-balance');
	var response_payable = document.getElementById('response-payable');
	var response_refunded = document.getElementById('response-refunded');
	var response_transactionId = document.getElementById('response-transaction_id');
	var response_updateId = document.getElementById('response-update_id');
	var response_authExp = document.getElementById('response-auth_exp');
	var response_refundExp = document.getElementById('response-refund_exp');

	// Define button actions
	document.getElementById('voidCharge').addEventListener('click', voidCharge, false);
	document.getElementById('captureCharge').addEventListener('click', captureCharge, false);	
	document.getElementById('refundCharge').addEventListener('click', refundCharge, false);	
	document.getElementById('updateCharge').addEventListener('click', updateCharge, false);
	document.getElementById('readCharge').addEventListener('click', readCharge, false);

	// Get form values
	function getForm() {
		orderId = document.getElementById('order_id').value;
		chargeId = document.getElementById('charge_id').value;
		refundAmount = document.getElementById('refund_amount').value;
		shippingCarrier = document.getElementById('shipping_carrier').value;
		shippingTracking = document.getElementById('shipping_tracking').value;
	}

	function formatMoney(a) {
		var amount = "$" + a/100;
		return amount;
	}

	// Define ajax request and response actions

	function ajaxRequest(a) {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (xhttp.readyState === 4) {

				if (xhttp.status === 500) {
					responseData.innerHTML = "Bad request";
				}
				if (xhttp.status === 404) {	
					responseData.innerHTML = "Missing charge ID";	
				}
				if (xhttp.status === 200) {
					responseJson = JSON.parse(xhttp.responseText);
					prettyJson = JSON.stringify(responseJson, null, 4);
					responseData.innerHTML = prettyJson;

					if (a.action === "read") {
						response_orderId.value = responseJson.order_id;
						response_state.value = responseJson.status;
						response_balance.value = formatMoney(responseJson.balance);
						response_payable.value = formatMoney(responseJson.payable);
						response_refunded.value = formatMoney(responseJson.refunded_amount);
						response_authExp.value = responseJson.expires;
						response_refundExp.value = responseJson.refund_expires;
					}				

					if (a.action === "capture") {
						response_orderId.value = responseJson.order_id;
						response_transactionId.value = responseJson.transaction_id;
						response_state.value = "captured";
						response_balance.value = formatMoney(responseJson.amount);
						response_payable.value = formatMoney(responseJson.amount - responseJson.fee);
					}

					if (a.action === "update") {
						response_updateId.value = responseJson.id;
						response_orderId.value = responseJson.order_id;
					}

					if (a.action === "refund") {
						response_transactionId.value = responseJson.transaction_id;
						response_orderId.value = responseJson.order_id;
						response_refunded.value = formatMoney(responseJson.amount);
					}				

					if (a.action === "void") {
						response_transactionId.value = responseJson.transaction_id;
						response_state.value = "voided";
					}
				}
				else {
					responseJson = JSON.parse(xhttp.responseText);
					prettyJson = JSON.stringify(responseJson, null, 4);
					responseData.innerHTML = prettyJson;
				}
			}
			else {
				responseData.innerHTML = "Processing...";
			}
		};
		xhttp.open("POST", "affirm.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(a.params);
	}

	// Define request types and options

	function readCharge() {
		getForm();
		var options = {
			action: "read",
			params: "env=" + "<?php echo $env ?>" + "&action=read&charge_id=" + chargeId
		}
		ajaxRequest(options);
	};

	function voidCharge() {
		getForm();
		var options = {
			action: "void",
			params: "env=" + "<?php echo $env ?>" + "&action=void&charge_id=" + chargeId
		}
		ajaxRequest(options);
	};

	function captureCharge() {
		getForm();
		var options = {
			action: "capture",
			params: "env=" + "<?php echo $env ?>" + "&action=capture&charge_id=" + chargeId + "&order_id=" + orderId
		}
		ajaxRequest(options);
	};

	function refundCharge() {
		getForm();
		var options = {
			action: "refund",
			params: "env=" + "<?php echo $env ?>" + "&action=refund&charge_id=" + chargeId + "&refund_amount=" + refundAmount
		}
		ajaxRequest(options);
	};

	function updateCharge() {
		getForm();
		var options = {
			action: "update",
			params: "env=" + "<?php echo $env ?>" + "&action=update&charge_id=" + chargeId + "&order_id=" + orderId + "&carrier=" + shippingCarrier + "&tracking=" + shippingTracking
		}
		ajaxRequest(options);
	};

	</script>
</body>
</html>