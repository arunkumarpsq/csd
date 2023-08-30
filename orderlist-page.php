<?php
add_action( 'woocommerce_account_view-orderlist-order_endpoint', 'orderlistorder_details_endpoint_content' );
function orderlistorder_details_endpoint_content() {    
	global $current_user;
	$current_user_id = $current_user->ID;
	$ordermeta = get_order_meta_using_user_role();
	if(current_user_can('dealer')){ ?>
		<div class="woocommerce-error">Not allowed to access this page. Return to <a href="<?php echo site_url();?>/my-account/" class="wc-forward">My account</a></div>
	<?php
	} else {
		$order_id = isset($_GET['id'])?$_GET['id']:'';
		if($order_id){

			
			$all = common_meta_query_for_received_orders();
			$meta_query = $all['meta_query'];
			$post_status = $all['post_status'];
			$post_status_curr = $all['post_status_curr'];
				$customer_orders = get_posts( array(
									'numberposts' => -1,
									'meta_query'  => $meta_query,
									//'meta_key'    => $ordermeta,
									//'meta_value'  => $current_user_metaid,
									'post_type'   => wc_get_order_types(),
									'post__in' => array($order_id),
									'post_status' =>  $post_status_curr/*array(
										'wc-completed', 
										'wc-processing', 
										'wc-pending', 
										'wc-on-hold', 
										'wc-cancelled', 
										'wc-refunded', 
										'wc-failed',
										'wc-paymentreceived'
										)*/
								) );
			
			if(!empty($customer_orders)){
				$order = wc_get_order( $order_id );
				wc_get_template(
				  'myaccount/view-order.php',
				  array(
					'order'    => $order,
					'order_id' => $order->get_id(),
				  )
				);

				wc_get_template('order/order-details-customer.php', array('order' => $order));
			} else { ?>
				<div class="woocommerce-error">Invalid order. <a href="<?php echo site_url();?>/my-account/" class="wc-forward">My account</a></div>
			<?php }
		} else { ?>
		<div class="woocommerce-error">Invalid order. <a href="<?php echo site_url();?>/my-account/" class="wc-forward">My account</a></div>
	<?php 
		}   
	}
}

function my_account_menu_order() {
	global $current_user;
	$current_user_id = $current_user->ID;
	$ordermeta = get_order_meta_using_user_role();
	if($ordermeta == 'depot' || $ordermeta == 'urc' || $ordermeta == 'dealer' || current_user_can('depot-nodal') || current_user_can('urc-nodal')){
		
		$menuOrder = array(
			'dashboard'          => __( 'Dashboard', 'woocommerce' ),
			'orderlist-orders'   => __( 'Received Demands', 'woocommerce' ),
			'depot-import'   => __( 'Order Import' , 'woocommerce' ),
			'order-export'   => __( 'Order Export' , 'woocommerce' ),
			'average-processing'   => __( 'Average Processing' , 'woocommerce' ),
			'edit-account'      => __( 'Settings', 'woocommerce' ),
			'update-mobile-email' => __( 'Update Mobile Email', 'woocommerce' ),
			'customer-logout'    => __( 'Logout', 'woocommerce' )
		);
		if(current_user_can('manualorder')){
			$menuOrder = array(
				'dashboard'          => __( 'Dashboard', 'woocommerce' ),
				'orders'             => __( 'Demands', 'woocommerce' ),
				'orderlist-orders'   => __( 'Received Demands', 'woocommerce' ),
				'update-mobile-email'          => __( 'Update Mobile Email', 'woocommerce' ),
				'edit-account'      => __( 'Settings', 'woocommerce' ),
				'customer-logout'    => __( 'Logout', 'woocommerce' )
			);
		}
	} else {
		$menuOrder = array(
			'dashboard'          => __( 'Dashboard', 'woocommerce' ),
			'orders'             => __( 'Demands', 'woocommerce' ),
			'edit-address'       => __( 'Billing Address', 'woocommerce' ),
			'profile'          => __( 'Update Registration Form', 'woocommerce' ),
			'update-mobile-email'  => __( 'Update Mobile Email', 'woocommerce' ),
			'edit-account'      => __( 'Change Password', 'woocommerce' ),
			'customer-logout'    => __( 'Logout', 'woocommerce' )
		);
	}

	$new = array( 'shoppage' => 'Shop' );

	$menuOrder = array_slice( $menuOrder, -1, 0, true ) 
	+ $new 
	+ array_slice( $menuOrder, 0, NULL, true );
 
	return $menuOrder;
 }
 add_filter ( 'woocommerce_account_menu_items', 'my_account_menu_order' );
 //Add order in orderlist admin dashboard - Ends

 add_filter( 'woocommerce_get_endpoint_url', 'misha_hook_endpoint', 10, 4 );
function misha_hook_endpoint( $url, $endpoint, $value, $permalink ){
	if( $endpoint === 'shoppage' ) {
		$url = wc_get_page_permalink( 'shop' );
	}
	return $url;
}

function common_meta_query_for_received_orders(){
	$meta_query = array();
	global $current_user;
	$current_user_id = $current_user->ID;
	$ordermeta = get_order_meta_using_user_role();
	$currentusername = get_user_display_name($current_user_id);

	$post_status = array('wc-completed', 'wc-delivered');
	$userroletitle = $ordermeta;
	$current_user_metaid = $current_user_id;
	$depot_code = get_user_meta($current_user_id, 'depot_code', true);
	$urc_code = get_user_meta($current_user_id, 'urc_code', true);
	if(current_user_can('depot-nodal')){
		$current_user_metaid = 0;
		$ordermeta = 'depot';
		$userroletitle = 'Depot Nodal Officer';
		if($depot_code){
		    $current_user_metaid = get_depot_from_depot_code($depot_code);
		}
	} else if(current_user_can('urc-nodal')){
		$current_user_metaid = 0;
		$ordermeta = 'urc';
		$userroletitle = 'URC Nodal Officer';
		if($urc_code){
		    $current_user_metaid = get_urc_from_urc_code($urc_code);
		}
	}
	$post_status_curr = $post_status;
	if(current_user_can('depot') || current_user_can('depot-nodal')){
		$post_status = array();
		$post_status_curr  = array();
		$excludestatus = array('wc-on-hold', 'wc-pending', 'wc-failed', 'wc-cancelled');
		$statusarray = wc_get_order_statuses();
		if(!empty($statusarray)){
			foreach($statusarray as $key => $value){
				//if(!in_array($key, $excludestatus)){
					//array_push($post_status, $key);
					array_push($post_status_curr, $key);
				//}
			}
		}
		array_push($post_status, 'wc-processing');
		///change for waiting 
		array_push($post_status, 'wc-waiting');
		///change for waiting 
		$meta_query = array(
			'relation' => 'AND',
				array(
            		'relation' => 'OR',
		            array(
		                'key'     => $ordermeta,
	  					'value'   => $current_user_metaid
		            ),
		            array(
		                'key' => 'depot_code',
		                'value' => $depot_code
		            )
				)
			 );
	} else if(current_user_can('urc') || current_user_can('urc-nodal')){
		$post_status = array();
		$post_status_curr  = array();
		$statusarray = wc_get_order_statuses();
		if(!empty($statusarray)){
			foreach($statusarray as $key => $value){
					array_push($post_status, $key);
				array_push($post_status_curr, $key);
			}
		}
		$urcname = get_user_display_name($current_user_id);
		//array_push($post_status, 'wc-poreleased');
		$meta_query = array(
			'relation' => 'AND',
			 	array(
            		'relation' => 'OR',
		            array(
		                'key'     => $ordermeta,
	  					'value'   => $current_user_metaid
		            ),
		            array(
		                'key' => 'urc_code',
		                'value' => $urc_code
		            ),
		            array(
		                'key' => 'urcname',
		                'value' => $urcname
		            )
				)
			);

	} else if(current_user_can('dealer')){
		$dealer_code = get_user_meta($current_user_id, 'dealer_code', true);
		$dealername = get_user_display_name($current_user_id);
		$meta_query = array(
			'relation' => 'AND',
			 	array(
            		'relation' => 'OR',
		            array(
		                'key'     => $ordermeta,
	  					'value'   => $current_user_id
		            ),
		            array(
		                'key' => 'dealer_code',
		                'value' => $dealer_code
		            ),
		            array(
		                'key' => 'dealername',
		                'value' => $dealername
		            )
				)
			);
	} else {
		$meta_query = array(
			'relation' => 'AND',
				 array(
				  'key'     => 'invaliduser',
				  'value'   => 0
				  )
			 );
	}
	$all = array(
			'meta_query' => $meta_query,
			'post_status' => $post_status,
			'post_status_curr' => $post_status_curr,
			'userroletitle' => $userroletitle
		);
	return $all;
}

add_action( 'woocommerce_account_orderlist-orders_endpoint', 'orderlistorders_endpoint_content' );
function orderlistorders_endpoint_content() {
	 
	global $current_user;
	$current_user_id = $current_user->ID;
	$ordermeta = get_order_meta_using_user_role();
	$currentusername = get_user_display_name($current_user_id);
	$ot = isset($_GET['ot'])?test_input($_GET['ot']):'';	
	$os = isset($_GET['os'])?test_input($_GET['os']):'';
	$ss = isset($_GET['ss'])?test_input($_GET['ss']):'';
	$limit = isset($_GET['lt'])?test_input($_GET['lt']):'5';
	$year = isset($_GET['yr'])?test_input($_GET['yr']):date('Y');
	$month = isset($_GET['mn'])?test_input($_GET['mn']):date('n');
	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} else if ( get_query_var('page') ) {
		$paged = get_query_var('page');
	} else {
		$paged = 1;
	}    
	$current_page = $paged;
	$urlstr = $_SERVER['REQUEST_URI'];
	preg_match('/\/my-account\/orderlist-orders\/(?P<id>\d+)/', $urlstr, $matches);
	if(!empty($matches) && !empty($matches['id'])){
		$current_page = $matches['id'];
		$paged = $matches['id'];
	}
	

			$all = common_meta_query_for_received_orders();
			$meta_query = $all['meta_query'];
			$post_status = $all['post_status'];
			$post_statusall = $all['post_status_curr'];
			$userroletitle = $all['userroletitle'];
	?>



	<?php
	if($ss){
		$post_status = array($ss);
	}
	if(!empty($ot) && !empty($os) && empty($ss)){
		$post_status = $post_statusall;
	}
	$args = array(
	  'post_type'=>wc_get_order_types(),
	  'post_status' => $post_status,
	  'meta_query'  => $meta_query,
	'posts_per_page'=> $limit,
	'paged' => $paged);

	if(!empty($year) && !empty($month)){
		$datestart = '1-'.$month.'-'.$year;
		$nextyear = $year;
		$nextmonth = $month+1;
		if($nextmonth == 13){
			$nextmonth = 1;
			$nextyear += 1;
		}
		$dateend = '1-'.$nextmonth.'-'.$nextyear;
		$args['date_query'] = array(
							"before" => esc_attr( $dateend ).' 00:00:00',
							'after' => esc_attr( $datestart ).' 00:00:00'
				        );
	}
	if(!empty($ot) && !empty($os)){
		$ot = wc_clean(test_input($ot));
		$os = wc_clean(test_input($os));
		if($ot == 'id'){
			if (strpos($os, '-') !== false) {
				$osarray = explode('-', $os);
				$os = $osarray[1];
			}
				$args['post__in']  = array($os);
			
		} else if($ot == 'uname' || $ot == 'customer' || $ot == 'canteen' || $ot == 'chip' || $ot == 'pan'){
			if($ot == 'uname'){
				$orderkey = 'username';
			} else if($ot == 'customer'){
				$orderkey = 'user_registration_beneficiary_name';
			} else if($ot == 'canteen'){
				$orderkey = 'user_registration_card_id';
			} else if($ot == 'chip'){
				$orderkey = 'user_registration_chip_number';
			} else if($ot == 'pan'){
				$orderkey = 'user_registration_pan_number';
			}
			array_push($meta_query, array('key' =>$orderkey, 'value' => esc_attr( $os ), 'compare' => 'IN'));
			$args['meta_query'] =  $meta_query;
			
		} else if($ot == 'index'){
			$plist = get_product_by_sku(esc_attr( $os ));
			$orders_ids = get_orders_ids_by_product_id( $plist, $post_status );
			if($orders_ids){
				$args['post__in']  = $orders_ids;
			} else {
				$args['post__in']  = array(0);
			}

		} else if($ot == 'product' ){
			/*$productargs = array(      
			    'post_type'   => 'product',
			    's'       => esc_attr( $os ),
			    'post_status' => $post_status
			); 
			$productargs_query = get_posts($productargs);*/
			$productargs_query = get_page_by_title( esc_attr( $os ), OBJECT, 'product' );
			if($productargs_query){
				$plist = $productargs_query->ID;
				$orders_ids = get_orders_ids_by_product_id( $plist, $post_status );
				if($orders_ids){
					$args['post__in']  = $orders_ids;
				} else {
					$args['post__in']  = array(0);
				}
			} else {
				$args['post__in']  = array(0);
			}
		} else if($ot == 'indent' ){
			$searchdate = esc_attr( $os );
			$date = DateTime::createFromFormat("d/m/Y" , $searchdate);
			$searchdate = $date->format('Y-m-d');
			$searchdate1 = $date->format('d-m-Y');
			$osnew = date('d-m-Y', strtotime($searchdate. ' +1 day'));
			$args['date_query'] = array(
				                "before" => esc_attr( $osnew ).' 00:00:00',
								'after' => esc_attr( $searchdate1 ).' 00:00:00'
					        );
		} else if($ot == 'pay'){
			array_push($meta_query, array('key' =>'_payment_method', 'value' => esc_attr( $os )));
			$args['meta_query'] =  $meta_query;

		} else {
			$args['post__in']  = array(0);
		}		
	}
	query_posts($args);
	$totalorders = 0;
	if ( have_posts() ) {
		global $wp_query;
        $totalorders = $wp_query->found_posts;
    }
	?>



	<div class="sear-section-top ">
		<div class="sear-section sear-section-left">
			<p>
				<strong><?php echo ucfirst($userroletitle);?> - </strong>
				<?php echo ucfirst($currentusername);?>
			</p>
			<p>Number of demands: <?php echo $totalorders;?></p>
		</div>
		<?php 
		if(current_user_can('depot') || current_user_can('urc') || current_user_can('depot-nodal') ||  current_user_can('urc-nodal')){ 
			if($post_statusall){
			?>
		<div class="sear-section sear-section-left sear-sectiontop">

			<form method="get" class="orderlist-status-search" id="formpagelimit" action="<?php echo site_url();?>/my-account/orderlist-orders/">
				<label for="month">Demands Per Page:</label>
				<select name="lt" id="limit" class="select" required>
					<option value="5" <?php if($limit == '5'){ echo 'selected';}?>>5</option>
				  	<option value="10" <?php if($limit == '10'){ echo 'selected';}?>>10</option>
				  	<option value="20" <?php if($limit == '20'){ echo 'selected';}?>>20</option>
				  	<option value="50" <?php if($limit == '50'){ echo 'selected';}?>>50</option>
				</select>

				<?php if(!empty($ss)){ ?>
				<input type="hidden" name="ss" value="<?php echo $ss;?>">
				<?php } ?>

				<?php if(!empty($ot)){ ?>
				<input type="hidden" name="ot" value="<?php echo $ot;?>">
				<?php } if(!empty($os)){ ?>
				<input type="hidden" name="os" value="<?php echo $os;?>">
				<?php } ?>

				<input type="hidden" name="mn" class="hiddenmonth" value="<?php echo $month;?>">
				<input type="hidden" name="yr" class="hiddenyear" value="<?php echo $year;?>">

			</form>

			<form method="get" class="orderlist-status-search" id="formmonthyear" action="<?php echo site_url();?>/my-account/orderlist-orders/">
				<span>
			        <label for="month">Month:</label>
			        <select id="orderlistmonth" name="mn">
			          <option value="1" <?php if($month == '1') echo 'selected';?>>January</option>
			          <option value="2" <?php if($month == '2') echo 'selected';?>>February</option>
			          <option value="3" <?php if($month == '3') echo 'selected';?>>March</option>
			          <option value="4" <?php if($month == '4') echo 'selected';?>>April</option>
			          <option value="5" <?php if($month == '5') echo 'selected';?>>May</option>
			          <option value="6" <?php if($month == '6') echo 'selected';?>>June</option>
			          <option value="7" <?php if($month == '7') echo 'selected';?>>July</option>
			          <option value="8" <?php if($month == '8') echo 'selected';?>>August</option>
			          <option value="9" <?php if($month == '9') echo 'selected';?>>September</option>
			          <option value="10" <?php if($month == '10') echo 'selected';?>>October</option>
			          <option value="11" <?php if($month == '11') echo 'selected';?>>November</option>
			          <option value="12" <?php if($month == '12') echo 'selected';?>>December</option>
			        </select>
			      </span>
				<span>
					<label for="year">Year:</label>
					<input type="hidden" id="hiddenorderlistyear" value="<?php echo $year;?>">
					<select id="orderlistyear" name="yr">
					</select>
				</span>
				<?php if(!empty($ss)){ ?>
				<input type="hidden" name="ss" value="<?php echo $ss;?>">
				<?php } ?>

				<?php if(!empty($ot)){ ?>
				<input type="hidden" name="ot" value="<?php echo $ot;?>">
				<?php } if(!empty($os)){ ?>
				<input type="hidden" name="os" value="<?php echo $os;?>">
				<?php } ?>

				<?php if(!empty($limit)){ ?>
				<input type="hidden" name="lt" value="<?php echo $limit;?>">
				<?php } ?>


				<button type="submit" value="Search" class=" searchbtn">Search</button>
				
			</form>



		</div>
		<div class="sear-section sear-section-middle ">
			<form method="get" class="orderlist-status-search" id="formorderstatus" action="<?php echo site_url();?>/my-account/orderlist-orders/">
				<select name="ss" id="ordersearchstatus" class="select" required>
					<option value="" >Sort By:</option>
					<?php 
					/*foreach($post_statusall as $poststatus){ 
						$poststatusslug = $poststatus;
						$poststatusslug = str_replace('wc-', '', $poststatusslug);
						?>
				  	<option value="<?php echo $poststatus;?>" <?php if($ss == $poststatus){ echo 'selected';}?>>
				  		<?php echo ucfirst($poststatusslug);?>
				  	</option>
				  	<?php }*/ ?>
				  	<?php 
					$statusarray = wc_get_order_statuses();
					if(!empty($statusarray)){
						foreach($statusarray as $key => $value){
							if(in_array($key, $post_statusall)){ 
								$poststatusslug = $key;
								//$poststatusslug = str_replace('wc-', '', $poststatusslug);
						?>
				  	<option value="<?php echo $poststatusslug;?>" <?php if($ss == $poststatusslug){ echo 'selected';}?>>
				  		<?php echo $value;?>
				  	</option>
				  	<?php 
				  			}
				  		} 
				  	}?>
				</select>
				<?php if(!empty($ot)){ ?>
				<input type="hidden" name="ot" value="<?php echo $ot;?>">
				<?php } if(!empty($os)){ ?>
				<input type="hidden" name="os" value="<?php echo $os;?>">
				<?php } ?>

				<?php if(!empty($limit)){ ?>
				<input type="hidden" name="lt" value="<?php echo $limit;?>">
				<?php } ?>

				<input type="hidden" name="mn" class="hiddenmonth" value="<?php echo $month;?>">
				<input type="hidden" name="yr" class="hiddenyear" value="<?php echo $year;?>">

				<?php if(!empty($ss)){ ?>
				<a href="<?php echo site_url();?>/my-account/orderlist-orders/" class="searchclear">Clear</a>
				<?php } ?>
			</form>
		</div>
		<?php 
			} 
		} 
		?>
		<div class="sear-section sear-section-right">
			<form method="get" class="orderlist-search" action="<?php echo site_url();?>/my-account/orderlist-orders/">
				<select name="ot" id="ordersearchtype" class="select" required>
					<option value="" >Search By:</option>
				  	<option value="id" <?php if($ot == 'id'){ echo 'selected';}?>>Demand Id</option>
				  	<option value="uname" <?php if($ot == 'uname'){ echo 'selected';}?>>Username</option>
				  	<option value="customer" <?php if($ot == 'customer'){ echo 'selected';}?>>Customer Name</option>
				  	<option value="index" <?php if($ot == 'index'){ echo 'selected';}?>>Index number</option>
				  	<option value="product" <?php if($ot == 'product'){ echo 'selected';}?>>Name of the product</option>
				  	<option value="canteen" <?php if($ot == 'canteen'){ echo 'selected';}?>>Canteen Card Number</option>
				  	<option value="chip" <?php if($ot == 'chip'){ echo 'selected';}?>>Chip Number</option>
				  	<option value="pan" <?php if($ot == 'pan'){ echo 'selected';}?>>PAN number</option>
				  	<option value="indent" <?php if($ot == 'indent'){ echo 'selected';}?>>Date of placing indent</option>
				  	<option value="pay" <?php if($ot == 'pay'){ echo 'selected';}?>>Payment Method</option>
				</select>
				<div class="sear-fld-wrap">
					<div class="allsearchbox">
						<?php if($ot == 'pay'){ ?>
						<select name="os" id="searchpay" class="select"  required>
							<option value="razorpay">Online Payment</option>
							<option value="bacs">Direct Bank Transfer</option>
						</select>
						<?php } else { ?>
							<input type="text" id="ordersearch" class="search-field input-text ordersearch <?php if($ot == 'indent'){ echo 'ordersearchdate'; } ?>" placeholder="Search for" value="<?php echo $os;?>" required name="os">					
						<?php } ?>
					</div>
					<button type="submit" value="Search" class=" searchbtn">Search</button>
				</div>

				<?php if(!empty($ss)){ ?>
				<input type="hidden" name="ss" value="<?php echo $ss;?>">
				<?php } ?>

				<?php if(!empty($limit)){ ?>
				<input type="hidden" name="lt" value="<?php echo $limit;?>">
				<?php } ?>

				<input type="hidden" name="mn" class="hiddenmonth" value="<?php echo $month;?>">
				<input type="hidden" name="yr" class="hiddenyear" value="<?php echo $year;?>">


				<?php if(!empty($ot) && !empty($os)){ ?>
				<a href="<?php echo site_url();?>/my-account/orderlist-orders/" class="searchclear">Clear</a>
				<?php } ?>
			</form>
		</div>
	</div>

	<?php
	if ( have_posts() ) {
		global $wp_query;
	?>
	<div class="table-responsive orderlistcontent">
		<table class="woocommerce-orders-table table-bordered table-striped woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table orderlisttable">
		   <thead>
			  <tr>
				 <th class="woocommerce-orders-table_header woocommerce-orders-table_header-order-number"><span class="nobr">Demand Number</span></th>
				 <th class="woocommerce-orders-table_header woocommerce-orders-table_header-order-podetails" style="width:30%"><span class="nobr">PO details</span></th>
				 <?php if(!current_user_can('dealer')){ ?>
				 <th class="woocommerce-orders-table_header woocommerce-orders-table_header-order-mode" style="width:20%"><span class="nobr">Payment from Beneficiary</span></th>
				 <th class="woocommerce-orders-table_header woocommerce-orders-table_header-order-payrev" style="width:20%"><span class="nobr">Payment Received by CSD</span></th>				 
				 <?php } ?>
				 <th class="woocommerce-orders-table_header woocommerce-orders-table_header-order-orderstatus" ><span class="nobr">Demand Status</span></th>
				 <th class="woocommerce-orders-table_header woocommerce-orders-table_header-order-actions" style="width:20%"><span class="nobr">Action</span></th>
			  </tr>
		   </thead>
		   <tbody> 
			<?php
			 while ( have_posts() ) : the_post();
				$orderid = get_the_ID();
				$order      = wc_get_order( $orderid );
				$item_count = $order->get_item_count() - $order->get_item_count_refunded();
				$userid = $order->get_user_id();
				$ordertype = get_post_meta($orderid, 'ordertype', true);
				if($ordertype == 'Manual'){
					$beneficiary_name = get_post_meta($orderid, 'user_registration_beneficiary_name', true);
				} else {
					$beneficiary_name = get_user_meta($userid, 'user_registration_beneficiary_name', true);
				}
				$user_info = get_userdata($userid);
				$state = get_post_meta($order->get_id(), 'state', true);
				$depot = get_post_meta($order->get_id(), 'depot', true);
				$urc = get_post_meta($order->get_id(), 'urc', true);
				$dealer = get_post_meta($order->get_id(), 'dealer', true);
				$basicprice = $order->get_formatted_order_total();
				$hiddentotalorderamount = $order->get_total_tax() + $order->get_subtotal();
				$hiddentotalorderamount = round($hiddentotalorderamount);
				//$hiddentotalorderamount = $order->get_total();
				
				$supply_order = get_post_meta($order->get_id(), 'supply_order', true);
				$officialreceipt = get_post_meta($order->get_id(), 'officialreceipt', true);
				$authorityletter = get_post_meta($order->get_id(), 'authorityletter', true);
				$color_id = get_post_meta($order->get_id(), 'color_id', true);
				$urc_verified = get_post_meta($order->get_id(), 'urc_verified', true);
				$depot_decision = get_post_meta($order->get_id(), 'depot_decision', true);
				$supply_order = get_post_meta($orderid, 'supply_order', true);
				$pay_details_uploaded = get_post_meta($orderid, 'pay_details_uploaded', true);
				$checkstatus = $order->get_status();
				$checkverify = get_post_meta($orderid, 'urc_verified', true);
				$checkdecision = get_post_meta($orderid, 'depot_decision', true);
				$checkpaymentreceipt = get_post_meta($orderid, 'payment_receipt', true);
				$payment_receipt_loan = get_post_meta($orderid, 'payment_receipt_loan', true);
				$supply_order = get_post_meta($orderid, 'supply_order', true);
				//$pdf_url = wp_nonce_url( admin_url( 'admin-ajax.php?action=generate_wpo_wcpdf&template_type=invoice&order_ids=' . $order->get_id() . '&my-account'), 'generate_wpo_wcpdf' );
			
	$pdf_url = wp_nonce_url( add_query_arg( array(
            'action'        => 'generate_wpo_wcpdf',
            'document_type' => 'invoice',
            'order_ids'     => $order->get_id(),
            'order_key'=>$order->get_order_key(),
        ), admin_url( 'admin-ajax.php' ) ), 'generate_wpo_wcpdf' );
				$merged_pdf_size = get_field('merged_pdf_size', 'option');
				$official_receipt_size = get_field('official_receipt_size', 'option');
				$supply_order_size = get_field('supply_order_size', 'option');
				$authority_letter_size = get_field('authority_letter_size', 'option');
				
				$upload_merged_supply_order_pdf = get_field('upload_merged_supply_order_pdf', 'option');
				$mergedsupply = get_post_meta($order->get_id(), 'mergedsupply', true);
				?>
			  <tr class="woocommerce-orders-table_row woocommerce-orders-table_row--status-<?php echo esc_attr( $order->get_status() ); ?> order">
				   <td class="woocommerce-orders-table_cell woocommerce-orders-table_cell-order-number" data-title="Order">
				   		<?php 
				   		if(current_user_can('dealer')){
				   			$orderlink = '#';
				   		} else {
				   			$orderlink = get_permalink(get_option( 'woocommerce_myaccount_page_id' )).'view-orderlist-order/?id='.$order->get_id();
				   		} ?>
				   		<div class="demndnumber">
				      	<a href="<?php echo $orderlink; ?>">
							<?php echo esc_html( _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?>
						</a>
						</div>

						<div class="datedetails">
				      		<div class="dateintend">
				      			<p>
				      				<span class="label-small pt0">Indent</span>
				      				<?php echo esc_attr( $order->get_date_created()->date( 'd/m/y' ) ); ?>
				      			</p>
				      		</div>
				      		<?php 
				      		$podate = get_post_meta($orderid, 'podate', true);  //get_post_meta($orderid, 'order_podate', true);
				      		$deliverydate = get_post_meta($orderid, 'order_deliverydate', true);
				      		if($podate){
				      		?>
				      		<div class="datepo">
				      			<p>
				      				<span class="label-small">PO</span>
				      				<?php echo $podate;?>
				      			</p>
				      		</div>
				      		<?php } if($deliverydate){?>
				      		<div class="datedelivery">
				      			<p>
				      				<span class="label-small">Delivery</span>
				      				<?php echo $deliverydate;?>
				      			</p>
				      		</div>
				      		<?php } ?>
				      	</div>
				   </td>
				   <td class="woocommerce-orders-table_cell woocommerce-orders-table_cell-order-podetails" data-title="podetails">
				   		<div class="beneficiarydetails">
				      		<div class="beneficiaryname">
				      			<p><?php echo $beneficiary_name;?></p>
				      		</div>
				      		<?php 
				      		if($ordertype == 'Manual'){ } else{
				      			if(current_user_can('urc') || current_user_can('depot') || current_user_can('depot-nodal') || current_user_can('urc-nodal')){ ?>
									<a href="#" class="view_customer btn btn-info btn-small" data-customerid="<?php echo $userid;?>">View Customer</a>
									<div class="hiddencustomerdata" id="hiddencustomerdata<?php echo $userid;?>" style="display:none;">
										<div class="modal-body">
											<?php echo get_all_customer_details_from_customer_id($userid); ?>
										</div>
									</div>
							<?php 
								} 
							}
							?>
				      	</div>
				      	<div class="podetails">
				      		<?php 
				      		$usernameorder = '-';
				      		$order_user_registration_item = get_post_meta( $order->get_id(), 'order_user_registration_item', true );
				      		if($order_user_registration_item){
				      			$usernamelabel = $order_user_registration_item['username']['label'];
								$usernamedefault = $order_user_registration_item['username']['default'];
								if($usernamedefault){
									$usernameorder = $usernamedefault;
								}
				      		}
				      		?>
				      		<div class="podetail">
					      		<p>
				      				<span class="label-small pt0">Username:</span>
				      				<span class=" itemdetails-value"><?php echo $usernameorder;?></span>
				      			</p>
				      		</div>
				      		<div class="podetail">
				      			<p>
				      				<span class="label-small pt0">Price Zone:</span>  
				      				<span class=" itemdetails-value">
				      					<?php echo $state;?>
				      				</span>
				      			</p>
				      		</div>
				      		<div class="podetail">
				      			<p>
				      				<span class="label-small pt0">Dealer:</span>
				      				<span class=" itemdetails-value">
				      					<?php echo get_user_display_name($dealer);?>
				      				</span>
				      			</p>
				      		</div>
				      		<div class="podetail">
				      			<p>
				      				<span class="label-small pt0">Depot:</span>
				      				<span class=" itemdetails-value">
				      					<?php echo get_user_display_name($depot);?>
				      				</span>
				      			</p>
				      		</div>
				      		<div class="podetail">
				      			<p>
				      				<span class="label-small pt0">URC:</span>
				      				<span class=" itemdetails-value"> 
				      					<?php echo get_user_display_name($urc);?>
				      				</span>
				      			</p>
				      		</div>
				      		<div class="podetail">
				      			<p><span class="label-small pt0">Item:</span>
				      			<?php 
						   		foreach ( $order->get_items() as $item_id => $item ) {
						   			$name = '-';
						   			$psku = '-'; 
						   			$capacity = '-';
								   	$product = $item->get_product();
								   	//$name = $product->get_title();

								   	$currentproduct = wc_get_product($item->get_product_id());
								   	if($currentproduct){
								   		$name = $currentproduct->get_title();
								   		$psku = $currentproduct->get_sku();
								   	}
								   	
								   	$capacity = get_field('engine_capacity', $item->get_product_id());
								   	if(!$capacity){ 
						      			$capacity = '-'; 
						      		}
						      		if(!$psku){ 
						      			$psku = '-'; 
						      		}
						      		if(!$name){ 
						      			$name = '-'; 
						      		}

						   		?>
							      	<div class="itemdetails">
							      		<div class="itemdetail">
							      			<p>
							      				<span class=" itemdetails-value">
							      					<?php echo $name;?>
							      				</span>
							      			</p>
							      		</div>
							      		
							      		<div class="itemcolor">
							      			<p><span class="label-small pt0">SKU:</span>  
							      				<span class=" itemdetails-value"><?php echo $psku;?></span>
							      			</p>
							      		</div>
							      		<?php 
							      		$product_category = get_post_meta( $order->get_id(), 'product_category', true );
				      					if(!empty($product_category)){ ?>
							      		<div class="itemcolor">
							      			<p><span class="label-small pt0">Category : </span>
							      				<span class=" itemdetails-value"><?php echo $product_category;?></span>
							      			</p>
							      		</div>
							      		<?php } ?>
							      		<?php 
							      		if($color_id){ 
							      			$color = get_term_by( 'id', $color_id, 'colors' );
											$color_name = isset( $color->name ) ? $color->name : ''; 
							      			?>
								      		<div class="itemcolor">
								      			<p><span class="label-small pt0">Color:</span>  
								      				<span class=" itemdetails-value"><?php echo $color_name;?></span>
								      			</p>
								      		</div>
							      		<?php } ?>

							      	</div>
				      			<?php } ?>
				      		</p></div>
				      		<div class="podetail">
				      			<?php

								$firstonlinepayment = get_post_meta($order->get_id(), 'firstonlinepayment', true);
								if($firstonlinepayment){
									echo '<div class="paymode"><p><span class="label-small pt0">First Online Payment: </span> '.wc_price($firstonlinepayment).'</p></div>';
								}
								$secondonlinepayment = get_post_meta($order->get_id(), 'secondonlinepayment', true);
								if($secondonlinepayment){
									echo '<div class="paymode"><p><span class="label-small pt0">Second Online Payment: </span> '.wc_price($secondonlinepayment).'</p></div>';
								}

								$balanceamount = get_post_meta($order->get_id(), 'balanceamount', true);
								$loan_amount = get_post_meta($order->get_id(), 'loan_amount', true);
								if($balanceamount && $loan_amount){
									if($loan_amount){
										echo '<div class="paymode"><p><span class="label-small pt0">Loan payment by bank : </span> '.wc_price($loan_amount).'</p></div>';
									}
									if($balanceamount){
										echo '<div class="paymode"><p><span class="label-small pt0">Balance payment through online mode: </span> '.wc_price($balanceamount).'</p></div>';
									}
								}
								?>

				      			<p>
				      				<span class="label-small pt0">Total Amount:</span>
				      				<span class=" itemdetails-value">
				      					<?php 
				      					//echo $basicprice;
				      					$grandtotal = $order->get_total_tax() + $order->get_subtotal();
										$grandtotal = number_format(round($grandtotal), 2, '.', '');
				      					echo wc_price($grandtotal);
				      					?>
				      				</span>
				      				<input type="hidden" id="hiddentotalorderamount<?php echo $order->get_id();?>" value="<?php echo $hiddentotalorderamount;?>">
				      			</p>
				      		</div>
				      		<?php 
				      		$statusor = $order->get_status();
				      		if( ( (current_user_can('depot') || current_user_can('depot-nodal') ) || $statusor == 'completed' || $statusor == 'delivered') ){ ?>
					      		<div class="podetailview">

					      			<?php 
					      			$showsupply = get_post_meta($order->get_id(), 'showsupply', true);
									if(empty($showsupply) || $showsupply == 'merged'){
					      			?>

						      			<?php if(!empty($mergedsupply) && get_attached_file($mergedsupply)){ ?>
						      			<a href="<?php echo wp_get_attachment_url($mergedsupply);?>" <?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'download'; } else { echo 'target="_blank"';} ?> id="mergedsupplyfile<?php echo $order->get_id();?>" data-filename="<?php echo basename( get_attached_file( $mergedsupply ) );?>">
						      				<?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'Download'; } else { echo 'View';} ?> Merged PDF
						      			</a></br>

						      			<?php } else { ?>
							      			

							      			<?php if(!empty($supply_order)){ ?>
							      			<a href="<?php echo wp_get_attachment_url($supply_order);?>" <?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'download'; } else { echo 'target="_blank"';} ?> id="supplyfile<?php echo $order->get_id();?>" data-filename="<?php echo basename( get_attached_file( $supply_order ) );?>">
							      				<?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'Download'; } else { echo 'View';} ?> Supply Order
							      			</a></br>

							      			<?php } if(!empty($officialreceipt)){ ?>
							      			<a href="<?php echo wp_get_attachment_url($officialreceipt);?>" <?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'download'; } else { echo 'target="_blank"';} ?> id="officialfile<?php echo $order->get_id();?>" data-filename="<?php echo basename( get_attached_file( $officialreceipt ) );?>">
							      				<?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'Download'; } else { echo 'View';} ?> Official Receipt
							      			</a></br>

							      			<?php } if(!empty($authorityletter)){ ?>
							      			<a href="<?php echo wp_get_attachment_url($authorityletter);?>" <?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'download'; } else { echo 'target="_blank"';} ?> id="authorityfile<?php echo $order->get_id();?>" data-filename="<?php echo basename( get_attached_file( $authorityletter ) );?>">
							      				<?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'Download'; } else { echo 'View';} ?> Authority Letter
							      			</a></br>
							      			<?php } ?>
						      			<?php } ?>

					      			<?php } else { ?>

					      				<?php 
					      				if((!empty($supply_order) && file_exists(get_attached_file($supply_order)) ) 
											|| (!empty($officialreceipt) && file_exists(get_attached_file($officialreceipt)) )
											|| (!empty($authorityletter) && file_exists(get_attached_file($authorityletter)) )
											){ ?>
					      					<?php if(!empty($supply_order)){ ?>
							      			<a href="<?php echo wp_get_attachment_url($supply_order);?>" <?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'download'; } else { echo 'target="_blank"';} ?> id="supplyfile<?php echo $order->get_id();?>" data-filename="<?php echo basename( get_attached_file( $supply_order ) );?>">
							      				<?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'Download'; } else { echo 'View';} ?> Supply Order
							      			</a></br>

							      			<?php } if(!empty($officialreceipt)){ ?>
							      			<a href="<?php echo wp_get_attachment_url($officialreceipt);?>" <?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'download'; } else { echo 'target="_blank"';} ?> id="officialfile<?php echo $order->get_id();?>" data-filename="<?php echo basename( get_attached_file( $officialreceipt ) );?>">
							      				<?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'Download'; } else { echo 'View';} ?> Official Receipt
							      			</a></br>

							      			<?php } if(!empty($authorityletter)){ ?>
							      			<a href="<?php echo wp_get_attachment_url($authorityletter);?>" <?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'download'; } else { echo 'target="_blank"';} ?> id="authorityfile<?php echo $order->get_id();?>" data-filename="<?php echo basename( get_attached_file( $authorityletter ) );?>">
							      				<?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'Download'; } else { echo 'View';} ?> Authority Letter
							      			</a></br>
							      			<?php } ?>
							      		<?php } else if(!empty($mergedsupply) && get_attached_file($mergedsupply)){ ?>
						      			<a href="<?php echo wp_get_attachment_url($mergedsupply);?>" <?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'download'; } else { echo 'target="_blank"';} ?> id="mergedsupplyfile<?php echo $order->get_id();?>" data-filename="<?php echo basename( get_attached_file( $mergedsupply ) );?>">
						      				<?php if(current_user_can('urc') || current_user_can('urc-nodal')){ echo 'Download'; } else { echo 'View';} ?> Merged PDF
						      			</a></br>

						      			<?php }	 ?>

					      			<?php } ?>
					      		</div>
				      		<?php
				      		 } ?>
				      	</div>
				   </td>
				   <?php if(!current_user_can('dealer')){ ?>
				   <td class="woocommerce-orders-table_cell woocommerce-orders-table_cell-order-Mode" data-title="Status">
				      <div class="paymodedetails">
				      		<?php 

				      		echo get_bank_info_top($order);
				      		
				      		/*$payment_method_title =  $order->get_payment_method_title();
				      		echo '<div class="paymode"><p><span class="label-small pt0">Payment method</span> '.$payment_method_title.'</p></div></br>';
							
							$transaction_id =  $order->get_transaction_id();
							if($transaction_id){
								$payment_method =  $order->get_payment_method();
								$transid = 'Transaction';
								if($payment_method == 'razorpay'){
									$transid = 'Razorpay';
								}
								echo '<div class="paymode"><p><span class="label-small pt0">'.$transid.' PAY ID</span> '.$transaction_id.'</p></div></br>';
								
								$razorpayorderid = get_razorpay_order_id($transaction_id);
								if($razorpayorderid){
									echo '<div class="paymode"><p><span class="label-small pt0">'.$transid.' ORDER ID</span> '.$razorpayorderid.'</p></div></br>';
								}
							}

							if ( $order->get_date_paid() ) {
								$paydate = wc_format_datetime( $order->get_date_paid() );
								$paytime = wc_format_datetime( $order->get_date_paid(), get_option( 'time_format' ) );
								echo '<div class="paymode"><p><span class="label-small pt0">Paid on</span> '.$paydate.' @ '.$paytime.'</p></div></br>';
							}*/

							//echo get_bank_info($order);
							/*ob_start();	

					   	  	if($checkstatus == 'failed' || $checkstatus == 'cancelled' || $checkstatus == 'refunded'){ ?>

					   	  		<div class="  <?php echo get_badge_color_from_order_status($order->get_status());?>">
					   	  			<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
					   	  		</div>

					   	  	<?php } else if($checkstatus == 'pending' || $checkstatus == 'on-hold'){ ?>

					   	  		<div class=" <?php echo get_badge_color_from_order_status($order->get_status());?>">
					   	  			Payment Pending
					   	  		</div>

					   	  	<?php }
					   	  	$payment_status = ob_get_clean();
					   	  	if($payment_status ){
						   	  		echo '<div class="paymode"><p><span class="label-small pt0">Payment Status</span> '.$payment_status.'</p></div>';
					   	  	}
				      		?>

				      		<?php 
				      		$mode_of_payment = get_post_meta($order->get_id(), 'mode_of_payment', true);
				      		//if($pay_details_uploaded == 'yes'){ 
				      			if(!empty($checkpaymentreceipt)){
				      				if($mode_of_payment == 'Direct Bank Transfer' && isJSON($checkpaymentreceipt)){
										$actionurl = json_decode($checkpaymentreceipt)->url;
									} else {
										$actionurl = wp_get_attachment_url($checkpaymentreceipt);
									}

				      			?>
								<a href="<?php echo $actionurl;?>" target="_blank" class="woocommerce-button button payreceipt btn btn-primary btn-small" id="payreceipt<?php echo $orderid;?>" data-id="<?php echo $orderid;?>" >
									Payment Receipt
								</a>
								</br>
								<?php
								 }
								if(!empty($payment_receipt_loan)){
									if($mode_of_payment == 'Direct Bank Transfer' && isJSON($payment_receipt_loan)){
										$actionurl = json_decode($payment_receipt_loan)->url;
									} else {
										$actionurl = wp_get_attachment_url($payment_receipt_loan);
									}
								?>
								<a href="<?php echo $actionurl;?>" target="_blank" class="woocommerce-button button payreceipt btn btn-primary btn-small" id="payreceipt<?php echo $orderid;?>" data-id="<?php echo $orderid;?>" >
									Loan Payment
								</a>
								</br>
							<?php }
							//}
							*/
				      		?>
				      	</div>
				   </td>
				   <td class="woocommerce-orders-table_cell woocommerce-orders-table_cell-order-Mode" data-title="Status">
				   		<?php 
				   		$payment_received_by_csd = get_all_payment_received_by_csd($orderid);
				   		echo $payment_received_by_csd;
				   		?>
				   </td>
				   <?php } ?>
				   <td class="woocommerce-orders-table_cell woocommerce-orders-table_cell-order-orderstatus" data-title="Status">
				      <div class=" withouthyper <?php echo get_badge_color_from_order_status($order->get_status());?>">
				      	<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
				      </div>
				      <?php if(!current_user_can('dealer')){ ?>
					      <div class="action-wrap">
					   	  	<a href="<?php echo site_url();?>/my-account/view-orderlist-order/?id=<?php echo $orderid;?>" class="btn btn-small btn-primary " target="_blank">
					   	  		View
					   	  	</a>
					   	  </div>
				   	  <?php } ?>
				   	  <?php if(current_user_can('depot')){
                  ?>
					               <form method="post" name="update_status">
					                  <!-- <button type="submit" name="marked_as_reject">Reject</button> -->          
					                  <button type="submit" name="marked_as_completed" id="proceedtopay">Proceed to Pay</button>
					               </form>
					</div>
					<?php 
					//print_r($checkstatus);
					   if (isset($_POST["marked_as_completed"])) 
					           {
					           $completed_status = $order->get_id();
					           $order = new WC_Order($completed_status);
					           $order->update_status('pending', 'order_note');
					  
					          
					    }
					    /*if (isset($_POST["marked_as_reject"])) 
					           {
					           $completed_status = $order->get_id();
					           $order = new WC_Order($completed_status);
					           $order->update_status('cancelled', 'order_note');
					          
					    }*/
					    }
					    ?>

				   	   <?php 
				   	   $get_payment_method = $order->get_payment_method();
				   	   $sms_payment_gateway = get_field('sms_payment_gateway', 'option');
				   	   if(empty($sms_payment_gateway)){
				   	   	$get_payment_method = 'razorpay';
				   	   }
				   	   if((current_user_can('depot') || current_user_can('depot-nodal')) && $checkstatus  == 'processing' && $get_payment_method == 'razorpay'){ ?>
				   	   		<?php 
				   	   		$enable_sms_button_for_depot = get_field('enable_sms_button_for_depot', 'option');
				   	   		$manual_sms_count = get_post_meta($orderid, 'manual_sms_count', true);
				   	   		//update_post_meta($orderid, 'manual_sms_count', ''); 
			                if($enable_sms_button_for_depot && $manual_sms_count<2){
			                    $adminmobile = get_field('admin_phone', 'option');
			                    if($adminmobile){
				   	   		?>
					   	   		<div class="action-wrap">
							   	  	<a href="#" data-id="<?php echo $orderid;?>" class="btn btn-small btn-primary send_sms_to_scpl">
							   	  		Send SMS to SCPL
							   	  	</a>
							   	</div>
							   	
						   	  	<span class="errormsg smserror<?php echo $orderid;?>" style="display:none;">Something went wrong</span>
						   	  	<span class="successmsg smssuccess<?php echo $orderid;?>" style="display:none;">Message sent</span>
					   	  	<?php } } ?>
					   	  	<?php if($enable_sms_button_for_depot && $manual_sms_count){ ?>
						   	  	<span class="successmsg">Number of SMS sent: <?php echo $manual_sms_count;?></span>
						   	<?php } ?>
				   	   <?php } ?>
				   </td>
				   <td class="woocommerce-orders-table_cell woocommerce-orders-table_cell-order-orderstatus" data-title="orderstatus">
						<?php
						
						if ( current_user_can('urc') || current_user_can('urc-nodal')) {
							if($checkstatus  == 'poreleased' || $checkstatus  == 'rejectedbyurc'){
									if($order->has_status( 'rejectedbyurc')){
										$badge = get_badge_color_from_order_status('rejectedbyurc');
										echo '<div class="woocommerce-button withouthyper '.$badge.' checkverify woobutton ">Rejected by URC</div>';
									} else { ?>
						
									<a href="#" class="woocommerce-button button checkverify verifydocuments btn btn-primary btn-small" id="verifydocuments<?php echo $orderid;?>" data-id="<?php echo $orderid;?>">Verify Documents</a>
									<a href="#" class="woocommerce-button   rejectbtnurc btn btn-danger btn-small" id="rejectbtnurc<?php echo $orderid;?>" data-by="urc" data-id="<?php echo $orderid;?>"> Reject</a>
									<?php } ?>
								<?php 

							} 
							echo $invoice = '<a href="'.esc_attr($pdf_url).'" class=" btn btn-primary btn-small" target="_blank">Summary</a>';
						} else if ( current_user_can('dealer') ) {
							if($order->has_status( 'completed')){								
								$delivery_code_send = get_post_meta( $orderid, 'delivery_code_send', true );
								$customer_id        = get_post_meta( $orderid, '_customer_user', true );
								$dealer_id          = get_post_meta( $orderid, 'dealer', true );
								$delivery_status = get_post_meta( $orderid, 'delivery_status', true );

								if($order->has_status( 'wc-delivered') || $delivery_status == 'yes'){  
									$badge = get_badge_color_from_order_status($order->get_status());
									echo '<div class="woocommerce-button withouthyper '.$badge.'   ">Delivered</div>';							
								} else if ( $delivery_code_send ) {
									$customer_otp_verified = get_post_meta( $orderid, 'customer_otp_verified', true );
									$dealer_otp_verified   = get_post_meta( $orderid, 'dealer_otp_verified', true );							
									if ( $customer_otp_verified ) {
										echo '<p style="color:green">Customer Verified</p>';
									}									
									if ( $dealer_otp_verified ) {
										echo '<p style="color:green">Dealer Verified</p>';
									}									
									if ( ! $customer_otp_verified ) : ?>
										<form method="post">
											<div class="podetail">
												<input type="text" name="customer_otp" maxlength="6" placeholder="Customer OTP">
												<input type="submit" name="customer_otp_verification" value="Verify" class="woocommerce-button button woobutton btn btn-primary btn-small">
												<input type="hidden" name="order_id" value="<?php echo $orderid; ?>">
												<input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
											</div>
											<?php if ( isset( $_GET['order_id'] ) && isset( $_GET['customer'] ) && $orderid == $_GET['order_id'] && ! $_GET['customer'] ) : ?>
												<p style="color:red">Invalid/Expired Customer OTP</p>
											<?php endif; ?>	
											<?php 
												if ( isset( $_COOKIE['customermobiledigits'.$orderid] ) ){
													$mobiledigits = $_COOKIE['customermobiledigits'.$orderid];
													setcookie( 'customermobiledigits'.$orderid, '', time() - 3600, '/' );
													echo $message = '<span >Mobile - ('.$mobiledigits.')</span></br>';
												}
											?>
													
											<a href="<?php echo home_url( 'my-account/orderlist-orders?action=send_delivery_code&order_id=' . $orderid . '&customer_id=' . $customer_id ); ?>" style="width:max-content">
												Resend customer delivery code
											</a>
										</form>
									<?php endif; ?>			
									<?php if  ( ! $dealer_otp_verified  ) :  ?>
										<form method="post">
											<div class="podetail">
												<input type="text" name="dealer_otp" maxlength="6" placeholder="Dealer OTP">
												<input type="submit" name="dealer_otp_verification" value="Verify" class="woocommerce-button button woobutton btn btn-primary btn-small">
												<input type="hidden" name="order_id" value="<?php echo $orderid; ?>">
												<input type="hidden" name="dealer_id" value="<?php echo $dealer_id; ?>">
											</div>
											<?php if ( isset( $_GET['order_id'] ) && isset( $_GET['dealer'] ) && $orderid == $_GET['order_id'] && ! $_GET['dealer'] ) : ?>
												<p style="color:red">Invalid/Expired Dealer OTP</p>
											<?php endif; ?>
											<?php 
												if ( isset( $_COOKIE['dealermobiledigits'.$orderid] ) ){
													$mobiledigits = $_COOKIE['dealermobiledigits'.$orderid];
													setcookie( 'dealermobiledigits'.$orderid, '', time() - 3600, '/' );
													echo $message = '<span >Mobile - ('.$mobiledigits.')</span></br>';
												}
											?>
											
											<a href="<?php echo home_url( 'my-account/orderlist-orders?action=send_delivery_code&order_id=' . $orderid . '&dealer_id=' . $dealer_id ); ?>" style="width:max-content">
												Resend dealer delivery code
											</a>
										</form>
									<?php endif; ?>			
									<?php if ( ! $customer_otp_verified || ! $dealer_otp_verified ) : ?>
										<p>OTP will expire in 2 minutes</p>
									<?php endif;
								} else {
									if ( $order->has_status( 'completed' ) ) {
										$send_code_url = home_url( 'my-account/orderlist-orders?action=send_delivery_code&order_id=' . $orderid . '&customer_id=' . $customer_id . '&dealer_id=' . $dealer_id );
										echo '<p></p><a href="' . $send_code_url . '" style="width:max-content" class="woocommerce-button button woobutton btn btn-primary btn-small">Send Delivery Codes</a></br>'; //Send delivery codes
									}
								}	
							}						
						}  else if( current_user_can('depot') || current_user_can('depot-nodal')){ 
								if( $order->has_status('processing') || $order->has_status('paymentreceived') ){ 
									$hide_payment_button = get_field('hide_payment_button', 'option');
									if(!$hide_payment_button){
									?>
										<a href="#" class="woocommerce-button button payment_recvd_depot btn btn-primary btn-small" id="payment_recvd_depot<?php echo $orderid;?>" data-id="<?php echo $orderid;?>">
											Payment received by CSD
										</a>
										<?php 
									}
									if($upload_merged_supply_order_pdf){
									?>
					      				<a href="#" class="woocommerce-button button mergedsupplyupload uploadorderfile btn btn-primary btn-small" data-size="<?php echo $merged_pdf_size;?>" data-type="mergedsupply" data-name="Merged PDF"  id="mergedsupplyupload<?php echo $orderid;?>" data-id="<?php echo $orderid;?>">
											<?php if(!empty($mergedsupply) && file_exists(get_attached_file($mergedsupply))){ echo 'Change'; } else { echo 'Upload';} ?> Merged PDF File
										</a>

					      			<?php
					      			} else {
					      				?>
					      				<a href="#" class="woocommerce-button button supplyorderupload uploadorderfile btn btn-primary btn-small" data-size="<?php echo $supply_order_size;?>" data-type="supply_order" data-name="Supply Order" id="supplyorderupload<?php echo $orderid;?>" data-id="<?php echo $orderid;?>">
											<?php if(!empty($supply_order) ){ echo 'Change'; } else { echo 'Upload';} ?> Supply Order
										</a>
										<a href="#" class="woocommerce-button button officialreceiptupload uploadorderfile btn btn-primary btn-small" data-size="<?php echo $official_receipt_size;?>" data-type="officialreceipt" data-name="Official Receipt"  id="officialreceiptupload<?php echo $orderid;?>" data-id="<?php echo $orderid;?>">
											<?php if(!empty($officialreceipt) ){ echo 'Change'; } else { echo 'Upload';} ?> Official Receipt
										</a>
										<a href="#" class="woocommerce-button button authorityletterupload uploadorderfile btn btn-primary btn-small" data-size="<?php echo $authority_letter_size;?>" data-type="authorityletter" data-name="Authority Letter"  id="authorityletterupload<?php echo $orderid;?>" data-id="<?php echo $orderid;?>">
											<?php if(!empty($authorityletter) ){ echo 'Change'; } else { echo 'Upload';} ?> Authority Letter
										</a>
					      			<?php	
					      			}

					      			/*if(!empty($supply_order) ){ ?>
					      				<a href="#"  data-id="<?php echo $orderid;?>" class="changesupplyorder btn btn-small btn-primary">Change Local Supply Order</a>
					      			<?php } else { ?>
					      				<a href="#" class="woocommerce-button button upload_supply_order btn btn-primary btn-small" id="upload_supply_order<?php echo $orderid;?>" data-id="<?php echo $orderid;?>">
											Upload Local Supply Order
										</a>
					      			<?php } */?>	

									<?php 
									echo '<div class="depotdecisn" id="depotdecisn'.$orderid.'">';
											if( $order->has_status('processing')){
												
												$ornumber = get_post_meta($orderid, 'ornumber', true);	
												if($hide_payment_button){
													$ornumber = true;
												}
												$uploadeddocs = false;
												if($upload_merged_supply_order_pdf){
													if(!empty($mergedsupply)){
														$uploadeddocs = true;
													}
												} 

												if(!empty($supply_order) && !empty($officialreceipt) && !empty($authorityletter)){
													$uploadeddocs = true;
												}
												if(($uploadeddocs == false) || empty($ornumber)){
													$msg = "'Please upload supply order";
													if(!$hide_payment_button){
														$msg .= " and Payment received by CSD";													
													}
													$msg .= " before approving the demand'";
													
													echo '<a href="#" class="woocommerce-button button  btn btn-primary btn-small" id="dummyapprove" onclick="popup_message('.$msg.');">Approve</a>';
												} else { ?>
												<a href="<?php echo get_permalink(101).'?id='.$orderid;?>" class="woocommerce-button button approvebtnlist btn btn-primary btn-small" id="approvebtnlist<?php echo $orderid;?>" data-id="<?php echo $orderid;?>" data-btntype="approved">
													Approve
												</a>
												</br>	
											<?php 
												} 
											}
											?>
											<?php 
											$hide_additional_payment_button = get_field('hide_additional_payment_button', 'option');
											if($hide_additional_payment_button){} else {
											?>

											<a href="#" class="woocommerce-button button additionalpay btn btn-primary btn-small" id="additionalpay<?php echo $orderid;?>" data-id="<?php echo $orderid;?>" >
												Additional payment Required
											</a>
											</br>


											<?php } ?>
											
									<?php 
									echo '</div>';								
								} else {
									if($order->has_status( 'refunded')){ } else if($order->has_status( 'rejectedbydepot') || $order->has_status( 'rejectedbyurc')){?>
										<a href="#" class="woocommerce-button   refundorder btn btn-danger btn-small" id="refundorder<?php echo $orderid;?>" data-by="depot" data-id="<?php echo $orderid;?>" data-uid="">Refund</a>
									<?php
									}
								}
								echo $invoice = '<a href="'.esc_attr($pdf_url).'" class=" btn btn-primary btn-small" target="_blank">Summary</a></br>';
						} 

						if( current_user_can('depot') || current_user_can('depot-nodal') || current_user_can('urc') || current_user_can('urc-nodal') || current_user_can('dealer')){
							echo $ordernote = '<a href="#" class=" btn btn-primary btn-small ordernote" data-id="'. $orderid.'">Notes</a></br>';
						}
						///print_r($checkstatus);	
						if(current_user_can('depot')){
						if($checkstatus  == 'processing' || $checkstatus  == 'waiting'){

							//if($order->has_status( 'waiting')){
								?>
	
									<a href="#" class="woocommerce-button   rejectbtnlist btn btn-danger btn-small" id="rejectedbydepot<?php echo $orderid;?>" data-by="urc" data-id="<?php echo $orderid;?>">Reject</a>

								<?php 
							//} 
							?>
							<!-- <a href="#" class="woocommerce-button button rejectbtnlist btn btn-danger btn-small" id="rejectbtn<?php echo $orderid;?>" data-id="<?php echo $orderid;?>" data-btntype="rejected">
						Reject</a></br> -->
						<?php	}
						}
							
						               if( current_user_can('depot')){
											$show_additional_documents = get_field('show_additional_documents', 'option');
											if($show_additional_documents){
											?>

											<a href="#" class="woocommerce-button button additionaldoc btn btn-primary btn-small" id="additionaldoc<?php echo $orderid;?>" data-id="<?php echo $orderid;?>" >
												Additional Documents Required
											</a>
											</br>

											<?php
										 } 
										}?>
						<span class="errormsg errormsg<?php echo $orderid;?>" style="display:none;">Something went wrong</span>
					</td>
				</tr>				
			<?php  
			endwhile;
			?>
			</tbody>
			</table>
		</div>
		<?php 
		$maxpage = $wp_query->max_num_pages;
		$searchpage = '/';
		if(!empty($ot) || !empty($os) || !empty($ss) || !empty($limit) || !empty($month) || !empty($year)){
			$searchpage = '/?';
			$getstart = '';
			if(!empty($ot) && !empty($os)){
				if($searchpage != '/?'){ $getstart = '&'; }
				$searchpage .= $getstart.'ot='.$ot.'&os='.$os;
			}
			if(!empty($ss)){
				if($searchpage != '/?'){ $getstart = '&'; }
				$searchpage .= $getstart.'ss='.$ss;
			}
			if(!empty($limit)){
				if($searchpage != '/?'){ $getstart = '&'; }
				$searchpage .= $getstart.'lt='.$limit;
			}
			if(!empty($month) && !empty($year)){
				if($searchpage != '/?'){ $getstart = '&'; }
				$searchpage .= $getstart.'mn='.$month.'&yr='.$year;
			}
		}
		/*if(!empty($ot) && !empty($os)){
			$searchpage .= '?ot='.$ot.'&os='.$os;
		}
		if(!empty($ss) && empty($ot) && empty($os)){
			$searchpage .= '?ss='.$ss;
		}
		if(!empty($ss) && (!empty($ot) || !empty($os))){
			$searchpage .= '&ss='.$ss;
		}*/
		if ( 1 < $wp_query->max_num_pages ) : ?>
			<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination orderlistpagination">
				<?php if ( $current_page != 1 ) : ?>
					<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( site_url().'/my-account/orderlist-orders/'. (1) ).$searchpage; ?>"><?php esc_html_e( 'First', 'woocommerce' ); ?></a>
					<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( site_url().'/my-account/orderlist-orders/'.($current_page - 1 ) ).$searchpage; //esc_url( site_url().'/my-account/orderlist-orders/?page='.($current_page - 1 ) )?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
				<?php endif; ?>

				<?php if ( intval( $wp_query->max_num_pages ) != $current_page ) : ?>
					<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( site_url().'/my-account/orderlist-orders/'.($current_page + 1)  ).$searchpage; ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
					<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( site_url().'/my-account/orderlist-orders/'.($maxpage)  ).$searchpage; ?>"><?php esc_html_e( 'Last', 'woocommerce' ); ?></a>
				
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php
		wp_reset_query();
	} else { 
		$nomsg = '';
		$count = count($post_status);
		if(!empty($ot) || !empty($os) || !empty($ss)){ } else {
			if($count == 1){
				if(current_user_can('depot') || current_user_can('depot-nodal') ){
					$nomsg = ' with status processing';
				} else if(current_user_can('urc') ||  current_user_can('urc-nodal')){
					$nomsg = ' with status supply order issued';
				}
			}
		}
		?>
		<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
			No demands found<?php echo $nomsg;?>.
		</div>
	<?php }
 }
