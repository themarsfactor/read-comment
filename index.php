<?php 
ini_set("display_errors", "on");
require "functions/functions.php";
require 'includes/admin_data.php';

if (isset($_GET['id'])) {
	// code...
	$id = (int)$_GET['id'];

	//echo $id;
	//get the user info
	$user_data = getUserData($id)['data'];
 
}

 ?>



<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Products List</title>
	<link rel="stylesheet" type="text/css" href="thirdparties/bootstrap/css/bootstrap.min.css">

	<!-- Animate CSS-->
    <link href="css/animate.css" rel="stylesheet" type="text/css">

    <!-- Fontawesome CSS-->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- Bootstrap core JavaScript-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <!-- Page level plugin JavaScript-->
    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <!-- Main CSS-->
    <link rel="stylesheet" href="css/style.css">
    <!-- Main CSS-->
    <link rel="stylesheet" href="css/admin.css">
    
	
</head>
<body id="contact">
	<div id="mySidebar" class="sidebar">
        <div class="sidebar-sticky">
            <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow" id="navbarMenu">
                <ul>
                    <li><a class="navbar-brand mr-0 px-3" href="#">shopMAYOR®</a></li>
                    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
                    </ul>
            </nav>
            <ul class="nav flex-column mx-auto">
                <li class="nav-item active"> <a class=" active" href="admin.php"><i class="fa fa-dashboard" aria-hidden="true"></i> Dashboard</a> </li>
                <li class="nav-item"> <a href="clients.php"><i class="fa fa-users" aria-hidden="true"></i> Users List</a> </li>

                <li class="nav-item"><a href="#"><i class="fa fa-male" aria-hidden="true"></i> Manage Admin</a> </li>
                <li class="nav-item"> <a href="create_schedule.php"><i class="fa fa-calendar" aria-hidden="true"></i> Create Schedule</a> </li>
            </ul>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Extra</span>
                <a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
                    <span data-feather="plus-circle"></span>
                </a>
            </h6>
            <ul class="list-unstyled SAFe®">
                <li class=""> <a href="logout.php" class="download"><i class="fa fa-sign-out" aria-hidden="true"></i> Log out</a> </li>
            </ul>
        </div>
    </div>


    <div id="main">
        <button class="openbtn" onclick="openNav()">☰</button>
        <div class="contain">

        	<div class="row">
                <div class="col-md-12">
                <div class="col-md-4 mt-4 ml-sm-auto">      
                 <input type="search" id="myInput" onkeyup="searchList()" name="" class="form-control" placeholder="search for products status">
                </div>
                </div>
                            
                </div>


            <main role="main" class="col-md-8 ml-sm-auto col-lg-12 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Welcome <?php echo $username ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group mr-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                            <span data-feather="calendar"></span>
                            This week
                        </button>
                    </div>
                </div>


                <div class="container table-responsive">
                    <table class="table table-striped table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">

                       
                    
			<div class="container-fluid " id="products">
		
		

				</div>

                   </table>


                   
                   </div> 
                    
            </main>

        </div>

        <footer>

            &copy; 2030 Minimalistic Website &nbsp;<span class="separator">|</span> Design by <a href="#">Me</a>


        </footer>
    </div>

    <script>
        // for data tables
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
    <script>
        function openNav() {
            document.getElementById("mySidebar").style.width = "250px";
            document.getElementById("main").style.marginLeft = "250px";
        }

        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
            document.getElementById("main").style.marginLeft = "0";
        }
    </script>
    



<script type="text/javascript" src="thirdparties/jquery/jquery.min.js"></script>
<script type="text/javascript" src="thirdparties/bootstrap/js/bootstrap.min.js"></script>
<script src="js/scripts.js"></script>

<script type="text/javascript">

	document.addEventListener("DOMContentLoaded", function(){

	
		getProducts().then((data) => {

				data = JSON.parse(data);

				//console.log(data);

				let productsCode = `
				<div class="row">
					<div class="col-md-4">
						<form class="form-inline">
						  <label class="sr-only" for="inlineFormInputName2">Name</label>
						  <select class="custom-select my-1 mr-sm-2" id='bulk-action-option'>
						  	<option value=''>Select Bulk Action</option>
						  	<option value='generate_invoice'>Bulk Generate Invoices</option>
						  	<option value='delete_products'>Bulk Delete Products</option>
						  </select>
						  <button type="button" class="btn btn-primary" id="bulk-action-btn">Go</button>
						</form>
					</div>
				</div>

				<table class = 'table mt-2' id='myTable'>
					<thead>
						<tr classs = 'header'>
							<th></th>
							<th>Products</th>
							<th>Tracking Number</th>
							<th>Weight </th>
							<th>Description</th>
							<th> Ship_Method</th>
							<th> Make Update</th>
							<th>#</th>
							<th>#</th>

						</tr>

					</thead>
					<tbody>`;

				for(let i = 0; i < data.length; i++){

					let productStatus;
					let shipMethod;
					let options;
					let options2; //options for ship method

					if(data[i]['products_status'] == 0){
						productStatus = "Storage";

						options = `
									<option value='storage' selected>Storage</option>
									<option value='shipped'>Shipped</option>
									<option value='delivered'>Delivered</option>`;
					}

					else if(data[i]['products_status'] == 1){
						productStatus = "Shipped";

						options = `<option value='storage'>Storage</option>
									<option value='shipped' selected>Shipped</option>
									<option value='delivered'>Delivered</option>`
					}
					else if(data[i]['products_status'] == 2){
						productStatus = "Delivered";

						options = `<option value='storage'>Storage</option>
									<option value='shipped'>Shipped</option>
									<option value='delivered' selected>Delivered</option>`
					}


					//deal with shipping methods

					if (data[i]['ship_method'] == 'normal') {

						shipMethod = "nomal";

						options2 =`<option value='normal' selected>Normal</option>
							<option value='express'>Express</option>`



					}
					else if (data[i]['ship_method'] == 'express') {
						shipMethod = "Express";

						options2 =`<option value='normal'>Normal</option>
							<option value='express' selected>Express</option>`

					}


					//deal with the options


					productsCode += `<tr>
								<td><input type='checkbox' id="prod_${data[i]['product_id']}" class="checked-products"></td>
								<td id='product_name_${data[i]['product_id']}'>${data[i]['product']}</td>
								<td id='product_tracking_num_${data[i]['product_id']}'>${data[i]['tracking']}</td>
								<td id='product_weight_${data[i]['product_id']}'>${data[i]['weight']}</td>
								<td id='product_description_${data[i]['product_id']}'>${data[i]['description']}</td>

								<td><span class='badge badge-primary' id='check_${data[i]['product_id']}'>${shipMethod}</span>	

								<select class='' id='${data[i]['product_id']}_method_phase' onchange='changeMethod(${data[i]['product_id']});'>
									${options2}
								</select>
								

								
								</td>


								<td><span class='badge badge-info' id='para_${data[i]['product_id']}'>${productStatus}</span>


								<select class='' id='${data[i]['product_id']}_delivery_phase' onchange='getStatus(${data[i]['product_id']});'>
									${options}
								</select>
								 </td>


								 <td><button class ='btn btn-sm btn-info' onclick='editProduct(${data[i]['product_id']})'>EDIT</button></td>


								 <td><button class ='btn btn-sm btn-danger' onclick='deleteProduct(${data[i]['product_id']})'>DELETE</button></td>

					      </tr>`;

				}

				productsCode += `</tbody></table>`;

				//insert into the DOM
				$("#products").html(productsCode);






				//Handle Bulk Actions
				(function(){
					//get the bulk action button
					const bulkActionBtn = document.querySelector("#bulk-action-btn");
					//get the select tag with the id of bulk-action-option
					const bulkActionOption = document.querySelector("#bulk-action-option");
					//get the checkbox on the table
					const checkedProductsOptions = document.querySelectorAll(".checked-products");

					//when the bulk action btn is clicked
					bulkActionBtn.onclick = function(){
						//once the btn is clicked...
						//check the option..
						bulk_action_option_value = bulkActionOption.value.trim();

						if(bulk_action_option_value.length != 0){
							//check if any checkbox was clicked..
							//console.log(checkedProductsOptions);
							checked_options = [];
							for(let i = 0; i < checkedProductsOptions.length; i++ ){
								if(checkedProductsOptions[i].checked){
									//add this 
									checked_options.push(checkedProductsOptions[i]);
								}
							}

							// if any checkbox is not clicked
							if(checked_options.length == 0){

								//alert the below notification
								alert("Select a product to perform this operation on");
							}else{
								//handle the bulk options now..

								//if checkbox is click, get the value clicked
								// pass it as a value to switch statement
								
								switch(bulk_action_option_value){
									case "generate_invoice":

									//call a function that generate invoice for the value selected
										generateInvoice(checked_options);
										break;

								}
					
								
								

							}

						}else{
							//please select an option..
							alert("Please select an option");
						}
						

					}

					//function that generate the invoice

					function generateInvoice(checked_options){

						let generate_invoice_msg = "Generate Invoice";
						if(checked_options.length > 1){
							//if selected checkbox is more than one 
							//do the below
							generate_invoice_msg = "Generate Invoices";
						}

						const user_data = <?php echo json_encode($user_data); ?>;
						//set a products_object..
						let products_object = {
							products : [],
							total_price: 0,
							total_weight: 0,
							customer: user_data
						};

				//generate invoice for this ..
				// console.log(checked_options);
				//modal that display the invoice
				let invoiceCode = `<div class="modal" tabindex="-1"  id="invoice-modal" data-backdrop='static'>
				  <div class="modal-dialog modal-lg">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title">${generate_invoice_msg}</h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body" style='max-height: calc(100vh/2); overflow-y: scroll;'>
				      <div class=''>
				      <div class="row">
				      	<div class="col-md-10 m-auto">
				      		<h6>Invoice for Products</h6>
				      		<hr>
				      		<div class="">
				      			<p>Summary of invoice for generated product</p>
				      		</div>
				      	</div>

				      </div>`;

			       let totalWeight = 0;
			      for(let i = 0; i < checked_options.length; i++){

			      		//get the id of the product
			      		currentId = checked_options[i].id;

			      		//explode 
			      		currentIdBox = currentId.split("prod_");

			      		currentId = currentIdBox[1];

			      		//Get the 
			      		// - Product name: product_name_${product_id}.innerText
			      		let productName = document.querySelector(`#product_name_${currentId}`).innerText;
			      		
			      		// - Product tracking number: product_tracking_num_${product_id}
			      		let trackingNumber = document.querySelector(`#product_tracking_num_${currentId}`).innerText;

			      		// - Product weight: product_weight_${product_id}
			      		let productWeight = document.querySelector(`#product_weight_${currentId}`).innerText;
			      		
			      		// - Product description: product_description_${product_id}
			      		let productDescription = document.querySelector(`#product_description_${currentId}`).innerText;	

			      		//create an object that contain all the expected values in the invoice

			      		let product_object = {
			      			product_name : productName,
			      			tracking_number : trackingNumber,
			      			product_weight: productWeight,
			      			product_description: productDescription,
			      			product_price: 5000
			      		}

			      		//push
			      		products_object.products.push(product_object);

			      		totalWeight += parseFloat(productWeight);

			      		products_object.total_weight = totalWeight;
			      		products_object.total_price = 10000;

			      		//invoice body
			      		invoiceCode += `
			      						<div class="row mt-4">
					<div class="col-md-10 m-auto">
					 	<div class="card">

					 		<div class="card-body">
					 			<hr>
							<h6>${productName}</h6>
						<hr>
						<table class="table table-responsive">
							<thead>
								<tr>
									<th>Tracking Number</th>
									<th>Weight</th>
									<th>Product description</th>
									<th>Price</th>
								</tr>
							</thead>

							<tbody>
								<tr>
									<td>${trackingNumber}</td>
									<td>${productWeight}</td>
									<td>${productDescription}</td>
									<td>N5, 000</td>

								</tr>
							</tbody>


						</table>

					 		</div>

					 	</div>


						</div>
						</div>
						`;
		


						 }

						//add the total 
						invoiceCode += `<div class='row'>

										<div class='col-md-6 ml-auto'>
											<table class='table table-responsive'>
												<thead>
													<th>Total Weight</th>
													<th></th>
													<th></th>
													<th>Total Price</th>
												</thead>

												<tbody>
													<tr>
														<td>${totalWeight}</td>
														<td></td>
														<td></td>
														<td>N10, 000</td>
													</tr>

												</tbody>

											</table>

										</div>

										</div>`;



						invoiceCode += `</div>
										</div>
										      <div class="modal-footer">
										        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										        <button type="button" class="btn btn-primary" id='send-invoice-btn'>Send Invoice to User</button>
										      </div>
										    </div>
										  </div>
										</div>`;


						const modalsLoader = document.createElement("div");

						document.body.appendChild(modalsLoader);

						modalsLoader.innerHTML = invoiceCode;

						$("#invoice-modal").modal("show");

						//when the modal closes..
						$("#invoice-modal").on("hidden.bs.modal", function(){
							location.reload();
						})


						document.querySelector("#send-invoice-btn").addEventListener("click", function(){

								//show loader
								showLoader('send-email-loader');

								//the products are needed ..
								console.log(products_object);

								products_object = JSON.stringify(products_object);

								$.post({
									url: "_loaders/send_invoice.php",
									data: { products_object: products_object },
									success: function(feedback){
										console.log(feedback);
										feedback = JSON.parse(feedback);

										if(feedback.code == "success"){
											stopLoader('send-email-loader');

											alert(feedback.message);
											$("#invoice-modal").modal("hide");
										}
									}

								})



						})

							
					}


					


					function showLoader(modal_name){

						let loader = `<div class="modal" tabindex="-1" id="${modal_name}" data-backdrop='static'>
										  <div class="modal-dialog border-0">
										    <div class="modal-content bg-dark text-light">
										      <div class="modal-header">
										        <h5 class="modal-title">Sending Invoice ...</h5>
										      </div>
										      <div class="modal-body">
										     	 <p>Sending email...remember to use a queue here...</p>
										      	<div class="spinner-border" role="status">
												  <span class="sr-only">Loading...</span>
												</div>
										      </div>
										
										    </div>
										  </div>
										</div>`;


						let spinningElement = document.createElement("div");

						spinningElement.id = "spining-loaders-modals";

						document.body.appendChild(spinningElement);

						document.querySelector("#spining-loaders-modals").innerHTML = loader;

						//start the loader
						$(`#${modal_name}`).modal("show");



					}


					function stopLoader(modal_name){
						$(`#${modal_name}`).modal("hide");
					}
				

					


				}());



		})


	})
	// function onShipped(id){


	// 	alert(id);
	// }


async function getProducts(){
	const id = "<?php echo $id; ?>";
	//get the latest items and their status frm db
		return await $.get({
			url: "_loaders/get_products.php",
			data: { id: id}
		})


}





function getStatus(id){

	para = document.querySelector('#para_'+id);
	product_phase = $(`#${id}_delivery_phase`).val();

	//alert(product_phase);
	//alert("The id: " + id + " The phase " + product_phase)

	if (product_phase === 'storage') {
		para.textContent = 'storage';
	}


	else if (product_phase === 'shipped') {
		para.textContent = 'shipped';


	  	$.post({

				url : "_loaders/transit.php",
				data : {id : id},
				success : function(feedback){
					//console.log(feedback);
					feedback =JSON.parse(feedback);
				}


					});
	}

	else if (product_phase ==='delivered') {
		para.textContent = 'delivered';

		$.post({
					url : "_loaders/delivered.php",
					data : {id : id},
					success : function(feedback){

						feedback = JSON.parse(feedback);
						//console.log(feedback);
					}

				})
	}
	else{
		para.textContent= "";
	}



}; 






function changeMethod(id){

	let check = document.querySelector('#check_'+id);
	let method_phase = $(`#${id}_method_phase`).val();

	//alert(method_phase);
	//alert("The id: " + id + " The phase " + method_phase);		

	if (method_phase === 'normal') {
		check.textContent = 'normal';

		$.post({

				url : "_loaders/shipNormal.php",
				data : {id : id},
				success : function(feedback){
					//console.log(feedback);
					feedback =JSON.parse(feedback);
				}


					});

	}


	else if (method_phase === 'express') {
		check.textContent = 'express';


	  	$.post({

				url : "_loaders/shipExpress.php",
				data : {id : id},
				success : function(feedback){
					//console.log(feedback);
					feedback =JSON.parse(feedback);
				}


					});
	}

	else{
		para.textContent= "";
	}



}; 






	function searchList(){


		 var input, filter, table, tr, td, i, txtValue;
	  input = document.getElementById("myInput");
	  filter = input.value.toUpperCase();
	  table = document.getElementById("myTable");
	  tr = table.getElementsByTagName("tr");
	  for (i = 0; i < tr.length; i++) {
	    td = tr[i].getElementsByTagName("td")[1];
	    if (td) {
	      txtValue = td.textContent || td.innerText;
	      if (txtValue.toUpperCase().indexOf(filter) > -1) {
	        tr[i].style.display = "";
	      } else {
	        tr[i].style.display = "none";
	      }
	    }       
	  }
	}


/**
 * Function that delete product
 * */

	function deleteProduct(id){
		//alert(id);
		const res = confirm("Do you want to delete the product?");

		if (res) {

			$.post({


				url : "_loaders/delete_course.php",
				data : {id : id},
				success : function(feedback){
					feedback = JSON.parse(feedback);
					console.log(feedback);

					if (feedback.status == 'success') {

						location.reload();

					}
				}
			})

		}
	}




	


</script>
</body>
</html>