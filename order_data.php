<?php


if (
    isset($_POST['line_item_total']) &&
    isset($_POST['line_items_names']) &&
    isset($_POST['line_items_price']) &&
    isset($_POST['line_items_quantity']) &&
    isset($_POST['line_items_sku']) &&
    isset($_POST['order_number']) &&
    isset($_POST['ship_order_id']) &&
	isset($_POST['orderKey'])
) {
		$api_key = 'd5ff25504ed043839b10022e49c33c4f';
		$api_secret = 'c91fc531b2b043218e8091ebfcd4ad22';

		// Construct the Authorization header
		$authorization = base64_encode("$api_key:$api_secret");
		// Assuming you have received the input data as POST parameters
		$line_item_total = $_POST['line_item_total'];
		$line_items_names = $_POST['line_items_names'];
		$line_items_price = $_POST['line_items_price'];
		$line_items_quantity = $_POST['line_items_quantity'];
		$line_items_sku = $_POST['line_items_sku'];
		$orderNumber = $_POST['order_number'];
		$orderId = $_POST['ship_order_id'];
		$orderKey = $_POST['orderKey'];
		$orderDate = $_POST['orderDate'];
		$orderStatus = $_POST['orderStatus'];
		$billingEmail = $_POST['billingEmail'];
		$billingPhone = $_POST['billingPhone'] ?? '';
		$shipName = $_POST['shippingName'] ?? '';
		$shippingAddress1 = $_POST['shippingAddress1'];
		$shippingAddress2 = $_POST['shippingAddress2'];
		$shippingCity = $_POST['shippingCity'];
		$shippingCountry = $_POST['shippingCountry'];
		$shippingPostcode = $_POST['shippingPostcode'];
		$shippingState = $_POST['shippingState'];
		$paymentMethod = $_POST['paymentMethod'];


		// Convert the comma-separated strings to arrays
		$totalArray = explode(',', $line_item_total);
		$namesArray = explode(',', $line_items_names);
		$priceArray = explode(',', $line_items_price);
		$quantityArray = explode(',', $line_items_quantity);
		$skuArray = explode(',', $line_items_sku);

		// Combine the arrays into the final array
		$items = [];

		foreach ($namesArray as $index => $name) {
			$items[] = [
				'lineItemKey' => "line-item-$index", // You might want to generate a unique key here
				'sku' => $skuArray[$index],
				'name' => $name,
				'weight' => [
					'value' => 0, // You might want to adjust this based on your actual data
					'units' => 'ounces',
				],
				'quantity' => intval($quantityArray[$index]),
				'unitPrice' => floatval($priceArray[$index]),
			];
		}

		$billAddress = [
			'name' => null,
			'street1' => null,
			'city' => null,
			'state' => null,
			'postalCode' => null,
			'country' => null,
			'phone' => null,
			'residential' => null,
		];

	$shippingAddress = [
		    'name' => $shipName,
			'street1' => $shippingAddress1,
			'street2' => $shippingAddress2,
			'city' => $shippingCity,
			'state' => $shippingState,
			'postalCode' => $shippingPostcode,
			'country' => $shippingCountry,
			'phone' => $billingPhone,
		];

		// The rest of your ShipStation API request payload
		$orderPayload = [
			'customerEmail' => $billingEmail,
			'orderId' => $orderId,
			'orderNumber' => $orderNumber,
			'orderKey' => $orderKey,
			'items' => $items,
			'orderDate' => $orderDate,
			'orderStatus' => $orderStatus,
			'shipTo' => $shippingAddress,
			"billTo" => $billAddress,
			'paymentMethod' => $paymentMethod
		];
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://ssapi.shipstation.com/orders/createorder",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => json_encode($orderPayload),
		CURLOPT_HTTPHEADER => array(
			"Host: ssapi.shipstation.com",
			'Authorization: Basic '.$authorization,
			"Content-Type: application/json"
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;
	}
?>
