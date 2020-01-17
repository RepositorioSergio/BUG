<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Affirm Example Code - PHP - Order Confirmation</title>
	<link rel="stylesheet" href="style.css">
	<script>
		// Grab checkout token
		<?php 
			$checkout_token = $_REQUEST['checkout_token'];
			$env = $_REQUEST['env'];
			echo "TOKEN: " . $checkout_token;
		?>
	</script>
</head>
<body>
	<h1>Charge Authorization</h1>
	<div id="content">
		<div id="status">
			Authorizing your charge...
		</div>
		<input type="button" style="visibility:hidden" value="Proceed to Charge Management" id="proceed">
		</input>
		<div id="details">
			<h4>Your Order Details</h4>
			<table>
				<tr>
					<td>Charge ID</td>
					<td id="charge_id"></td>
				</tr>
				<tr>
					<td>Order ID</td>
					<td id="order_id"></td>
				</tr>
			</table>
		</div>
		<div id="response">
			<h4>Authorization Response Data</h4>
			<pre><div id="responseData">
			</div></pre>
		</div>
	</div>
	<script>
	var chargeId;

	var requestStatus = document.getElementById('status');
	var charge_id = document.getElementById('charge_id');
	var order_id = document.getElementById('order_id');
	var responseData = document.getElementById('responseData');

	document.getElementById('proceed').addEventListener('click', function(){location = "charge_management.php?charge_id="+chargeId+"&env=<?php echo $env?>"}, false);
	// Define ajax request and response actions
	function ajaxRequest(a) {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (a.action === "auth") {
				if (xhttp.readyState === 4) {
					if (xhttp.status === 500) {
						responseData.innerHTML = "Bad request";
						requestStatus.innerHTML = "Authorization failed! Your order still requires payment.";
					}		
					if (xhttp.status === 200) {
						// responseData.innerHTML = xhttp.responseText;
						responseJson = JSON.parse(xhttp.responseText);
						prettyJson = JSON.stringify(responseJson, null, 4);

						chargeId = responseJson.id;
						var orderId = responseJson.order_id; 
						
						responseData.innerHTML = prettyJson;
						charge_id.innerHTML = chargeId;
						order_id.innerHTML = orderId;
						requestStatus.innerHTML = "Authorization successful! Your order is complete.";
						proceed.style = "visibility:visible";
					}
					else {
						// responseData.innerHTML = xhttp.responseText;
						responseJson = JSON.parse(xhttp.responseText);
						prettyJson = JSON.stringify(responseJson, null, 4);
						responseData.innerHTML = prettyJson;
						if (responseJson.code === "checkout-token-used"){
							requestStatus.innerHTML = "This checkout token has already been used.";
							readCharge();
						}
						else {	
							requestStatus.innerHTML = "Authorization failed! Your order still requires payment.";
						}
					}
				}
			}
			if (a.action === "read") {
				if (xhttp.readyState === 4) {
					responseJson = JSON.parse(xhttp.responseText);
					chargeId = responseJson.entries[0].id;
					requestStatus.innerHTML = "Last charge ID found: " + chargeId;
					// requestStatus.innerHTML = "hi!";
					// requestStatus.innerHTML = xhttp.responseText;
					proceed.style = "visibility:visible";
				}
				else {
					requestStatus.innerHTML = "Fetching latest charge..."
				}
			}
		};
		xhttp.open("POST", "affirm.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(a.params);
	}

	// Define request types and options
	function authCharge() {
		var options = {
			action: "auth",
			params: "env=<?php echo $env ?>&action=auth&checkout_token=<?php echo $checkout_token ?>"
		}
		ajaxRequest(options);
	};

	function readCharge() {
		var options = {
			action: "read",
			params: "env=<?php echo $env ?>&action=read"
		}
		ajaxRequest(options);
	};

	authCharge();

	</script>
</body>
</html>