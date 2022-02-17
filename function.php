<?php  

/**
 * Gets the user data using the passed user id
 * @param  [integer] $user_id [the user id of the user]
 * @return [type]          [description]
 */
function getUserData($user_id){

	if(!is_numeric($user_id)){
		return [
			"message" => "Invalid data type passed as user id",
			"code" => "error",
			"data" => null
		];
	}
	require "database/connection.php";

	$user_id = (int)$user_id;
	// query that get the user's data from the tsble in the daatabase
	$query = "SELECT * FROM `users` WHERE `id`= $user_id LIMIT 1";
	//
	$result = mysqli_query($conn, $query);
	//check the sql query is true 
	if($result){
		
		//if it is true fetch the result and return it
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

		return [
			"message" => "operation successful",
			"code" => "success",
			"data" => $row
		];


	}else{

		return [
			"message" => mysqli_error($conn),
			"code" => "error",
			"data" => null
		];

	}


}


/**
 * Generates a new unique invoice id
 * @param  [type] $products_object [description]
 * @return [type]                  [description]
 */
function generateInvoiceKey($products_object){
	require "../database/connection.php";
	//the below part generate the unique id for each invoice transaction
	$invoice_key = uniqid(rand()). uniqid();

	//check if this $invoice_id exists
	
	$check_query = "SELECT * FROM `invoices` WHERE `invoice_key`= '{$invoice_key}' LIMIT 1";

	$check_result = mysqli_query($conn, $check_query);

	if(mysqli_num_rows($check_result) == 1){
		//there is a key already..
		//regenerate by calling a function below
		$invoice_key = generateInvoiceId($products_object);
	}else{
		// if the invoice does not exist encode the product and insert into the table
		$products_object = json_encode($products_object);
		$create_query = "INSERT INTO `invoices` (`invoice_key`, `products_data`, `date_created`) VALUES('$invoice_key', '{$products_object}', NOW())";

		$create_result = mysqli_query($conn, $create_query);

		if($create_result){

			return $invoice_key;
		}else{
			return null;
		}


	}

}

/**
 * Checks if an invoice key is valid
 * @param  [type] $key [description]
 * @return [type]      [description]
 */
function checkInvoiceKey($key, $check_success_page = null){
	if(file_exists("database/connection.php")){
		require "database/connection.php";
	}else if(file_exists("../database/connection.php")){
		require "../database/connection.php";
	}

	$key = mysqli_real_escape_string($conn, trim((string)$key));

	if($check_success_page != null){
		$query = "SELECT * FROM `invoices` WHERE `invoice_key` = '{$key}' AND `is_paid` = 1 AND `is_payment_success_page_shown` = 0 LIMIT 1";
	}else{
		$query = "SELECT * FROM `invoices` WHERE `invoice_key` = '{$key}' AND `is_paid` = 0 LIMIT 1";
	}

	

	$result = mysqli_query($conn, $query);

	if($result){
		if(mysqli_num_rows($result) == 1){
			//there is a match
			return [
				"message" => "operation successful",
				"data" => mysqli_fetch_array($result, MYSQLI_ASSOC),
				"code" => "valid"
			];
		}else{
			return [
				"message" => "operation successful",
				"data" => null,
				"code" => "invalid"
			];
		}
	}else{
		return [
				"message" => mysqli_error($conn),
				"data" => null,
				"code" => "error"
			];

	}

}

/**
 * Checks if the payment success page has been shown already
 * @param  [type] $invoice_key [description]
 * @return [type]              [description]
 */
function checkIfSuccessIsAlreadyShown($invoice_key){
	if(file_exists("database/connection.php")){
		require "database/connection.php";
	}else if(file_exists("../database/connection.php")){
		require "../database/connection.php";
	}


	$invoice_key = mysqli_real_escape_string($conn, trim((string)$invoice_key));
	//check if the transaction has already been made...this is represented as 1 on the table 
	$check_query = "SELECT `invoice_key` FROM `invoices` WHERE `is_payment_success_page_shown` = 1 AND `invoice_key` = '{$invoice_key}' LIMIT 1";

	$check_result = mysqli_query($conn, $check_query);

	if($check_result){

		if(mysqli_num_rows($check_result) == 1){
			//page has been shown already
			return [
				"message" => "success page shown already",
				"data" => null,
				"code" => "shown"
			];
		}else{
			return [
				"message" => "success page not shown",
				"data" => null,
				"code" => "not-shown"
			];

		}

	}else{
		//error
		return [
			"message" => "Error with database: ".mysqli_error($conn),
			"data" => null,
			"code" => "error"
		];
	}

}




function setSuccessAlreadyShown($invoice_key){
	if(file_exists("database/connection.php")){
		require "database/connection.php";
	}else if(file_exists("../database/connection.php")){
		require "../database/connection.php";
	}


	//first check that this $invoice key is valid
	$check_invoice_key = checkInvoiceKey($invoice_key, true);

	if($check_invoice_key['code'] == "valid"){
		//update
		$update_query = "UPDATE `invoices` SET `is_payment_success_page_shown` = 1 WHERE `invoice_key` = '{$invoice_key}' LIMIT 1";
		$update_result = mysqli_query($conn, $update_query);

		if($update_result){

			return [
				"message" => "operation successful",
				"data" => null,
				"code" => "success"
			];

		}else{

			return [
				"message" => "operation not successful: ".mysqli_error($conn),
				"data" => null,
				"code" => "error"
			];


		}


	}else{

		return [
				"message" => "invalid invoice key",
				"data" => null,
				"code" => "error"
			];

	}

	




}


/**
 * Creates a new payment link
 * @param  [type] $payment_vendor [description]
 * @param  [type] $customer_email [description]
 * @param  [type] $amount_in_kobo [description]
 * @return [type]                 [description]
 */
function createPaymentLink($payment_vendor, $customer_email, $amount_in_kobo, $invoice_key){

	switch($payment_vendor){
		case "paystack":
			require "Classes/PaystackMessenger.php";
			$paystack = new PaystackMessenger;
			$paystack->sendInvoicePayment($customer_email, $amount_in_kobo, $invoice_key);

	}


}



/**
 * Clears the invoice after payment ans set to used
 * @param  [type] $payment_vendor  [description]
 * @param  [type] $transaction_ref [description]
 * @param  [type] $invoice_key     [description]
 * @return [type]                  [description]
 */
function clearInvoice($payment_vendor, $transaction_ref, $invoice_key){
	require "../database/connection.php";
	//check the $invoice key for validity
	$check_invoice_key = checkInvoiceKey($invoice_key);

	if($check_invoice_key['code'] == "valid"){
		//moving on
		
		$update_query = "UPDATE `invoices` SET `payment_vendor`='{$payment_vendor}', `transaction_ref` = '{$transaction_ref}', `is_paid` = 1 
		WHERE `invoice_key` = '{$invoice_key}' LIMIT 1";

		$update_result = mysqli_query($conn, $update_query);

		if($update_result){
			//done
			return [
				"message" => "invoice cleared",
				"data" => null,
				"code" => "success"
			];


		}else{
			//error
			return [
				"message" => "invoice not cleared: ".mysqli_error($conn),
				"data" => null,
				"code" => "error"
			];
		}

	}else{
		//invalid $invoice key
		return [
				"message" => "invoice not cleared: Invalid invoice key",
				"data" => null,
				"code" => "error"
			];

	}


}


/**
 * Sends an email invoice to the customer for confirmation
 * @param  [type] $products_object [description]
 * @return [type]                  [description]
 */
function sendUserInvoice($products_object){
	require "../database/connection.php";
	require "../Classes/MailMessenger.php";

	$products_object = json_decode($products_object);
	
	// echo "<pre>";
	// print_r($products_object);
	// echo "</pre>";

	$customer_firstname = $products_object->customer->firstname;
	$customer_lastname = $products_object->customer->lastname;
	$customer_email = $products_object->customer->email;
	$customer_phone = $products_object->customer->phone;

	$total_price = number_format($products_object->total_price);
	$total_weight = number_format($products_object->total_weight);

	//generate an invoice id and save to the table
	$unique_invoice_key = generateInvoiceKey($products_object);

	if($unique_invoice_key != null){
		//also you might need to add an expiry date to this invoice
		$payment_url = "http://localhost/shopMayor/make_payments.php?invkey={$unique_invoice_key}";

			$products = $products_object->products;

	$products_table = "<table>
						<tr>
							<td><b>Product name</b></td>
							<td><b>Product description</b></td>
							<td><b>Weight</b></td>
							<td><b>Price</b></td>
						</tr>
						<tbody>
						";

	foreach($products as $product){
			$products_table .= "<tr>
									<td>{$product->product_name}</td>
									<td>{$product->product_description}</td>
									<td>{$product->product_weight}</td>
									<td>{$product->product_price}</td>
								</tr>";

	}

	$products_table .= "</tbody></table>";


	$products_table .= "<table style='margin-top: 50px;'>
							<tr>
								<td><b>Total Weight</b></td>
								<td><b>Total Price</b></td>
							</tr>

							<tbody>
								<tr>
									<td>{$total_weight}</td>
									<td>{$total_price}</td>
								</tr>
							</tbody>
						</table>";

	//prepare the mail content..
	$mail_content = " <!doctype html>
						<html>
						  <head>
						    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
						    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
						    <title>Please Confirm Order Payment</title>
						    <style>
						      /* -------------------------------------
						          GLOBAL RESETS
						      ------------------------------------- */
						      
						      /*All the styling goes here*/
						      
						      img {
						        border: none;
						        -ms-interpolation-mode: bicubic;
						        max-width: 100%; 
						      }

						      body {
						        background-color: #f6f6f6;
						        font-family: sans-serif;
						        -webkit-font-smoothing: antialiased;
						        font-size: 14px;
						        line-height: 1.4;
						        margin: 0;
						        padding: 0;
						        -ms-text-size-adjust: 100%;
						        -webkit-text-size-adjust: 100%; 
						      }

						      table {
						        border-collapse: separate;
						        mso-table-lspace: 0pt;
						        mso-table-rspace: 0pt;
						        width: 100%; }
						        table td {
						          font-family: sans-serif;
						          font-size: 14px;
						          vertical-align: top; 
						      }

						      /* -------------------------------------
						          BODY & CONTAINER
						      ------------------------------------- */

						      .body {
						        background-color: #f6f6f6;
						        width: 100%; 
						      }

						      /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
						      .container {
						        display: block;
						        margin: 0 auto !important;
						        /* makes it centered */
						        max-width: 580px;
						        padding: 10px;
						        width: 580px; 
						      }

						      /* This should also be a block element, so that it will fill 100% of the .container */
						      .content {
						        box-sizing: border-box;
						        display: block;
						        margin: 0 auto;
						        max-width: 580px;
						        padding: 10px; 
						      }

						      /* -------------------------------------
						          HEADER, FOOTER, MAIN
						      ------------------------------------- */
						      .main {
						        background: #ffffff;
						        border-radius: 3px;
						        width: 100%; 
						      }

						      .wrapper {
						        box-sizing: border-box;
						        padding: 20px; 
						      }

						      .content-block {
						        padding-bottom: 10px;
						        padding-top: 10px;
						      }

						      .footer {
						        clear: both;
						        margin-top: 10px;
						        text-align: center;
						        width: 100%; 
						      }
						        .footer td,
						        .footer p,
						        .footer span,
						        .footer a {
						          color: #999999;
						          font-size: 12px;
						          text-align: center; 
						      }

						      /* -------------------------------------
						          TYPOGRAPHY
						      ------------------------------------- */
						      h1,
						      h2,
						      h3,
						      h4 {
						        color: #000000;
						        font-family: sans-serif;
						        font-weight: 400;
						        line-height: 1.4;
						        margin: 0;
						        margin-bottom: 30px; 
						      }

						      h1 {
						        font-size: 35px;
						        font-weight: 300;
						        text-align: center;
						        text-transform: capitalize; 
						      }

						      p,
						      ul,
						      ol {
						        font-family: sans-serif;
						        font-size: 14px;
						        font-weight: normal;
						        margin: 0;
						        margin-bottom: 15px; 
						      }
						        p li,
						        ul li,
						        ol li {
						          list-style-position: inside;
						          margin-left: 5px; 
						      }

						      a {
						        color: #3498db;
						        text-decoration: underline; 
						      }

						      /* -------------------------------------
						          BUTTONS
						      ------------------------------------- */
						      .btn {
						        box-sizing: border-box;
						        width: 100%; }
						        .btn > tbody > tr > td {
						          padding-bottom: 15px; }
						        .btn table {
						          width: auto; 
						      }
						        .btn table td {
						          background-color: #ffffff;
						          border-radius: 5px;
						          text-align: center; 
						      }
						        .btn a {
						          background-color: #ffffff;
						          border: solid 1px #3498db;
						          border-radius: 5px;
						          box-sizing: border-box;
						          color: #3498db;
						          cursor: pointer;
						          display: inline-block;
						          font-size: 14px;
						          font-weight: bold;
						          margin: 0;
						          padding: 12px 25px;
						          text-decoration: none;
						          text-transform: capitalize; 
						      }

						      .btn-primary table td {
						        background-color: #3498db; 
						      }

						      .btn-primary a {
						        background-color: #3498db;
						        border-color: #3498db;
						        color: #ffffff; 
						      }

						      /* -------------------------------------
						          OTHER STYLES THAT MIGHT BE USEFUL
						      ------------------------------------- */
						      .last {
						        margin-bottom: 0; 
						      }

						      .first {
						        margin-top: 0; 
						      }

						      .align-center {
						        text-align: center; 
						      }

						      .align-right {
						        text-align: right; 
						      }

						      .align-left {
						        text-align: left; 
						      }

						      .clear {
						        clear: both; 
						      }

						      .mt0 {
						        margin-top: 0; 
						      }

						      .mb0 {
						        margin-bottom: 0; 
						      }

						      .preheader {
						        color: transparent;
						        display: none;
						        height: 0;
						        max-height: 0;
						        max-width: 0;
						        opacity: 0;
						        overflow: hidden;
						        mso-hide: all;
						        visibility: hidden;
						        width: 0; 
						      }

						      .powered-by a {
						        text-decoration: none; 
						      }

						      hr {
						        border: 0;
						        border-bottom: 1px solid #f6f6f6;
						        margin: 20px 0; 
						      }

						      /* -------------------------------------
						          RESPONSIVE AND MOBILE FRIENDLY STYLES
						      ------------------------------------- */
						      @media only screen and (max-width: 620px) {
						        table.body h1 {
						          font-size: 28px !important;
						          margin-bottom: 10px !important; 
						        }
						        table.body p,
						        table.body ul,
						        table.body ol,
						        table.body td,
						        table.body span,
						        table.body a {
						          font-size: 16px !important; 
						        }
						        table.body .wrapper,
						        table.body .article {
						          padding: 10px !important; 
						        }
						        table.body .content {
						          padding: 0 !important; 
						        }
						        table.body .container {
						          padding: 0 !important;
						          width: 100% !important; 
						        }
						        table.body .main {
						          border-left-width: 0 !important;
						          border-radius: 0 !important;
						          border-right-width: 0 !important; 
						        }
						        table.body .btn table {
						          width: 100% !important; 
						        }
						        table.body .btn a {
						          width: 100% !important; 
						        }
						        table.body .img-responsive {
						          height: auto !important;
						          max-width: 100% !important;
						          width: auto !important; 
						        }
						      }

						      /* -------------------------------------
						          PRESERVE THESE STYLES IN THE HEAD
						      ------------------------------------- */
						      @media all {
						        .ExternalClass {
						          width: 100%; 
						        }
						        .ExternalClass,
						        .ExternalClass p,
						        .ExternalClass span,
						        .ExternalClass font,
						        .ExternalClass td,
						        .ExternalClass div {
						          line-height: 100%; 
						        }
						        .apple-link a {
						          color: inherit !important;
						          font-family: inherit !important;
						          font-size: inherit !important;
						          font-weight: inherit !important;
						          line-height: inherit !important;
						          text-decoration: none !important; 
						        }
						        #MessageViewBody a {
						          color: inherit;
						          text-decoration: none;
						          font-size: inherit;
						          font-family: inherit;
						          font-weight: inherit;
						          line-height: inherit;
						        }
						        .btn-primary table td:hover {
						          background-color: #34495e !important; 
						        }
						        .btn-primary a:hover {
						          background-color: #34495e !important;
						          border-color: #34495e !important; 
						        } 
						      }

						    </style>
						  </head>
						  <body>
						    <span class='preheader'>This is preheader text. Some clients will show this text as a preview.</span>
						    <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='body'>
						      <tr>
						        <td>&nbsp;</td>
						        <td class='container'>
						          <div class='content'>

						            <!-- START CENTERED WHITE CONTAINER -->
						            <table role='presentation' class='main'>

						              <!-- START MAIN CONTENT AREA -->
						              <tr>
						                <td class='wrapper'>
						                  <table role='presentation' border='0' cellpadding='0' cellspacing='0'>
						                    <tr>
						                      <td>
						                        <p>Hi {$customer_firstname} {$customer_lastname},</p>
						                        <p>Here is the invoice to your pending products delivery order</p>
						                        <p>To proceed, please click the button below to make payment</p>
						                        <p>Here is the summary: 
						                        	{$products_table}
						                        </p>
						                        <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='btn btn-primary'>
						                          <tbody>
						                            <tr>
						                              <td align='left'>
						                                <table role='presentation' border='0' cellpadding='0' cellspacing='0'>
						                                  <tbody>
						                                    <tr>
						                                      <td> <a href='{$payment_url}' target='_blank'>Make Payment</a> </td>
						                                    </tr>
						                                  </tbody>
						                                </table>
						                              </td>
						                            </tr>
						                          </tbody>
						                        </table>
						                        <p>Please get back to us as quickly as possible if you think this email was sent in error.</p>
						                        <p>Shop Mayor Team.</p>
						                      </td>
						                    </tr>
						                  </table>
						                </td>
						              </tr>

						            <!-- END MAIN CONTENT AREA -->
						            </table>
						            <!-- END CENTERED WHITE CONTAINER -->

						            <!-- START FOOTER -->
						            <div class='footer'>
						              <table role='presentation' border='0' cellpadding='0' cellspacing='0'>
						                <tr>
						                  <td class='content-block'>
						                    <span class='apple-link'>Company address here</span>
						                    <br> Want to find out more about this transaction? <a href='http://i.imgur.com/CScmqnj.gif'>Contact Us</a>.
						                  </td>
						                </tr>
						                <tr>
						                  <td class='content-block powered-by'>
						                    Powered by <a href='#'>Shop Mayor</a>.
						                  </td>
						                </tr>
						              </table>
						            </div>
						            <!-- END FOOTER -->

						          </div>
						        </td>
						        <td>&nbsp;</td>
						      </tr>
						    </table>
						  </body>
						</html>";




	$mailer = new MailMessenger;

	$feedback = $mailer->send($customer_email, "Your Product Invoice", $mail_content);

	if($feedback == "invoice-sent"){
		echo json_encode([
			"message" => "Invoice sent to user's email",
			"data" => null,
			"code" => "success"
		]);
	}




	}

	






}