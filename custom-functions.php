<?php
function test_input($data) {
    $data = filter_var($data, FILTER_SANITIZE_STRIPPED);
	$data = trim($data);
	$data = stripslashes($data);
    $data = strip_tags($data);
    $data = esc_sql($data);
    $data = htmlspecialchars($data);
    //$data = mysql_real_escape_string($data);
    //$data = wpdb::_real_escape($data );
	return $data;
}

add_action( 'wp_ajax_nopriv_customer_collect_payment_receipt', 'customer_collect_payment_receipt' );
add_action( 'wp_ajax_customer_collect_payment_receipt', 'customer_collect_payment_receipt' );
function customer_collect_payment_receipt() {   
    $status = false;
    $order_id = test_input($_REQUEST['id']);
    $bank = test_input($_REQUEST['bank']);
    $holder = test_input($_REQUEST['holder']);
    $accountnumber = test_input($_REQUEST['accountnumber']);
    $ifsc = test_input($_REQUEST['ifsc']);
    $utr = test_input($_REQUEST['utr']);
    $amountpaid = test_input($_REQUEST['amountpaid']);
    $paymentdate = test_input($_REQUEST['paymentdate']);

    $loanbank = test_input($_REQUEST['loanbank']);
    $loanholder = test_input($_REQUEST['loanholder']);
    $loanaccountnumber = test_input($_REQUEST['loanaccountnumber']);
    $loanifsc = test_input($_REQUEST['loanifsc']);
    $loanutr = test_input($_REQUEST['loanutr']);
    $loanamountpaid = test_input($_REQUEST['loanamountpaid']);
    $loanpaymentdate = test_input($_REQUEST['loanpaymentdate']);

    $amount_deposited_in = test_input($_REQUEST['amount_deposited_in']);
    $amount_deposited_in_loan = test_input($_REQUEST['amount_deposited_in_loan']);
    $payment_remark = test_input($_REQUEST['payment_remark']);

    delete_post_meta($order_id, 'amount_deposited_in');
    delete_post_meta($order_id, 'amount_deposited_in_loan');
    delete_post_meta($order_id, 'payment_remark');

    delete_post_meta($order_id, 'bank');
    delete_post_meta($order_id, 'holder');
    delete_post_meta($order_id, 'accountnumber');
    delete_post_meta($order_id, 'ifsc');
    delete_post_meta($order_id, 'utr');
    delete_post_meta($order_id, 'amountpaid');
    delete_post_meta($order_id, 'paymentdate');
    delete_post_meta($order_id, 'loanbank');
    delete_post_meta($order_id, 'loanholder');
    delete_post_meta($order_id, 'loanaccountnumber');
    delete_post_meta($order_id, 'loanifsc');
    delete_post_meta($order_id, 'loanutr');
    delete_post_meta($order_id, 'loanamountpaid');
    delete_post_meta($order_id, 'loanpaymentdate');

    if(!empty($order_id) && !empty($bank) && !empty($holder) && !empty($accountnumber) && !empty($ifsc) && !empty($utr)  && !empty($amountpaid) && !empty($paymentdate) && !empty($amount_deposited_in)){
        $status = true;
        update_post_meta($order_id, 'bank', $bank);
        update_post_meta($order_id, 'holder', $holder);
        update_post_meta($order_id, 'accountnumber', $accountnumber);
        update_post_meta($order_id, 'ifsc', $ifsc);
        update_post_meta($order_id, 'utr', $utr);
        update_post_meta($order_id, 'amountpaid', $amountpaid);

        update_post_meta($order_id, 'amount_deposited_in', $amount_deposited_in);
        if($payment_remark){
            update_post_meta($order_id, 'payment_remark', $payment_remark);
        }
        update_post_meta($order_id, 'paymentdate', $paymentdate);

        $status =  '<div class=" badge  badge-warning">Payment from Beneficiary </div>';
        $status .=  '<div class="paymode"><p><span class="label-small pt0">Bank</span> <span id="hiddbank'.$order_id.'">'.$bank.'</span></p></div>';
        $status .=  '<div class="paymode"><p><span class="label-small pt0">First Account Holder</span> <span id="hiddholder'.$order_id.'">'.$holder.'</span></p></div>';
        $status .=  '<div class="paymode"><p><span class="label-small pt0">Account Number</span> <span id="hiddaccountnumber'.$order_id.'">'.$accountnumber.'</span></p></div>';
        $status .= '<div class="paymode"><p><span class="label-small pt0">IFSC</span> <span id="hiddifsc'.$order_id.'">'.$ifsc.'</span></p></div>';
        $status .=  '<div class="paymode"><p><span class="label-small pt0">UTR </span> <span id="hiddutr'.$order_id.'">'.$utr.'</span></p></div>';
        $status .=  '<div class="paymode"><p><span class="label-small pt0">Amount Paid </span> <span id="hiddamountpaid'.$order_id.'">'.$amountpaid.'</span></p></div>';
        $status .= '<div class="paymode"><p><span class="label-small pt0">Date of Payment</span> <span id="hiddpaymentdate'.$order_id.'">'.$paymentdate.'</span></p></div>';

        if(!empty($loanbank) && !empty($loanholder) && !empty($loanaccountnumber) && !empty($loanifsc) && !empty($loanutr)  && !empty($loanamountpaid) && !empty($loanpaymentdate) ){
		        update_post_meta($order_id, 'loanbank', $loanbank);
		        update_post_meta($order_id, 'loanholder', $loanholder);
		        update_post_meta($order_id, 'loanaccountnumber', $loanaccountnumber);
		        update_post_meta($order_id, 'loanifsc', $loanifsc);
		        update_post_meta($order_id, 'loanutr', $loanutr);
		        update_post_meta($order_id, 'loanamountpaid', $loanamountpaid);

                update_post_meta($order_id, 'amount_deposited_in_loan', $amount_deposited_in_loan);

		        update_post_meta($order_id, 'loanpaymentdate', $loanpaymentdate);
  
            $status .=  '<div class=" badge  badge-warning">Payment from Loan</div>';
            $status .= '<div class="paymode"><p><span class="label-small pt0">Bank</span> <span id="hiddloanbank'.$order_id.'">'.$loanbank.'</span></p></div>';
            $status .=  '<div class="paymode"><p><span class="label-small pt0">First Account Holder</span> <span id="hiddloanholder'.$order_id.'">'.$loanholder.'</span></p></div>';
            $status .=  '<div class="paymode"><p><span class="label-small pt0">Account Number</span> <span id="hiddloanaccountnumber'.$order_id.'">'.$loanaccountnumber.'</span></p></div>';
            $status .=  '<div class="paymode"><p><span class="label-small pt0">IFSC</span> <span id="hiddloanifsc'.$order_id.'">'.$loanifsc.'</span></p></div>';
            $status .=  '<div class="paymode"><p><span class="label-small pt0">UTR </span> <span id="hiddloanutr'.$order_id.'">'.$loanutr.'</span></p></div>';
            $status .=  '<div class="paymode"><p><span class="label-small pt0">Amount Paid </span> <span id="hiddloanamountpaid'.$order_id.'">'.$loanamountpaid.'</span></p></div>';
            $status .=  '<div class="paymode"><p><span class="label-small pt0">Date of Payment</span> <span id="hiddloanpaymentdate'.$order_id.'">'.$loanpaymentdate.'</span></p></div>';
		}
        send_depot_mail_if_payment_details_uploaded($order_id);
    }
    echo $status;
    die();
}


//pre_get_posts
//parse_query
add_action('pre_get_users','alter_query_csd_headquarters_user_admin');

function alter_query_csd_headquarters_user_admin($query) {
    //gets the global query var object
    global $wp_query, $pagenow;

    if(!is_user_logged_in() ) return;

    //if(!current_user_can('csddirectorate') ) return;
    
    if ( !is_admin() ) return;

    /*$screen = get_current_screen();
    if ( $screen->base != 'users' ) return;*/

    if ( $pagenow != 'users.php' ) return;

    if(current_user_can('csddirectorate') ) 
        $query->set('role__in' ,array('subscriber')); //, 'urc'

    if(current_user_can('sub-directorate') ) 
        $query->set('role__in' ,array('urc', 'urc-nodal'));
    if(current_user_can('csdho') ) 
        $query->set('role__in' ,array('depot', 'dealer', 'depot-nodal'));
    if(current_user_can('csdheadquarters') ) 
        $query->set('role__in' ,array('depot', 'dealer', 'urc', 'depot-nodal', 'oem'));
    if(current_user_can('registrations') ) 
        $query->set('role__in' ,array('subscriber'));

}

add_filter('admin_head', 'hide_other_roles_for_csddirectorate');
function hide_other_roles_for_csddirectorate($views){
    global $current_user;
    if(in_array('csddirectorate', $current_user->roles) 
        || in_array('sub-directorate', $current_user->roles) 
        || in_array('csdho', $current_user->roles)
        || in_array('csdheadquarters', $current_user->roles)
        || in_array('registrations', $current_user->roles)
        ){

        echo '<style>
            ul.subsubsub .all,
            ul.subsubsub .administrator,
            ul.subsubsub .csddirectorate,
            ul.subsubsub .sub-directorate,
            ul.subsubsub .csdheadquarters,
            ul.subsubsub .csdho,
            ul.subsubsub .state,
            ul.subsubsub .shop_manager,
            ul.subsubsub .none,
            ul.subsubsub .manualorder,
            ul.subsubsub .operations,
            ul.subsubsub .test,
            ul.subsubsub .registrations,
            ul.subsubsub .oem,
            #user-approvation-result,
            tr.user-role-wrap,
            .notice,
            .user-pass1-wrap,
            #ure_select_other_roles,
            .row-actions > .capabilities,
            .row-actions > .view,
            .column-uv > .row-actions
            {
                display:none !important;
            }
        </style>';
    }

    if(in_array('csddirectorate', $current_user->roles) || in_array('registrations', $current_user->roles)){
        echo '<style>
            ul.subsubsub .depot-nodal,
            ul.subsubsub .urc-nodal,
            ul.subsubsub .urc,
            ul.subsubsub .depot,
            ul.subsubsub .dealer,
            #changeit, select#new_role, #menu-tools 
            {
                display:none !important;
            }
        </style>';
    }

    if(in_array('registrations', $current_user->roles)){
        echo '<style>
            .bulkactions, #submit {
                display:none;
            }
        </style>';
    }

    if(in_array('sub-directorate', $current_user->roles)){
        echo '<style>
            ul.subsubsub .subscriber,
            ul.subsubsub .depot-nodal,
            ul.subsubsub .depot,
            ul.subsubsub .dealer
            {
                display:none !important;
            }
        </style>';
    }

    if(in_array('csdho', $current_user->roles)){
        echo '<style>
            ul.subsubsub .subscriber,
            ul.subsubsub .urc,
            ul.subsubsub .urc-nodal
            {
                display:none  !important;
            }

            .bulkactions {
                display:block;
            }
        </style>';
    }

    if(in_array('csdheadquarters', $current_user->roles)){
        echo '<style>
            ul.subsubsub .subscriber,
            ul.subsubsub .urc-nodal,
            .woocommerce-layout__activity-panel-tabs,
            .refund-items,
            #woocommerce-order-actions,
            #wpo_wcpdf_send_emails,
            #wpo_wcpdf-data-input-box,
            #woocommerce-order-downloads,
            #advanced-sortables,
            .page-title-action
            {
                display:none !important;
            }
            ul.subsubsub .oem {
                display:block !important;
            }
        </style>';
    }

    echo '<style>
    .uv_status.status-1, .uv_status.status-0 {
         cursor: default !important; 
    }
    .wp-list-table.widefat.fixed.striped.table-view-list.users {
        table-layout: auto !important;
    }
    </style>';

}

function add_empty_editable_role($all_roles) {
    global $current_user;
    $screen = get_current_screen();
    if(in_array('sub-directorate', $current_user->roles)){
        foreach ( $all_roles as $name => $role ) {
            if ($name != 'urc') {
                unset($all_roles[$name]);
            }
        }
    } else if(in_array('csdheadquarters', $current_user->roles)){
        foreach ( $all_roles as $name => $role ) {
            if ($name == 'urc' || $name == 'depot' || $name == 'dealer') { } else{
                unset($all_roles[$name]);
            }
        }
    }
    return $all_roles;
}
add_filter('editable_roles', 'add_empty_editable_role');


add_action( 'init', 'create_product_taxonomy_for_products', 0 );
function create_product_taxonomy_for_products() {

    // Now register the non-hierarchical taxonomy like tag
      register_taxonomy('brands','product',array(
        'hierarchical' => true,
        'label' => __('Brands', 'textdomain'),
      ));
 
    // Now register the non-hierarchical taxonomy like tag
      register_taxonomy('fuel_type','product',array(
        'hierarchical' => true,
        'label' => __('Fuel Type', 'textdomain'),
      ));

      add_filter('widget_text', 'do_shortcode');

}


function check_if_no_user_restriction(){
    $status = false;
    $user = wp_get_current_user();
    if($user){
        $allowed_roles = array(  'administrator');
        if ( array_intersect( $allowed_roles, $user->roles ) ) {
           $status = true;
        }
    }
    return $status;
}

function disable_userverification_plugin_updates( $value ) {
  if ( isset($value) && is_object($value) ) {
    if ( isset( $value->response['user-verification/user-verification.php'] ) ) {
      unset( $value->response['user-verification/user-verification.php'] );
    }
    if ( isset( $value->response['razorpay-woocommerce-2.6.0/woo-razorpay.php'] ) ) {
      unset( $value->response['razorpay-woocommerce-2.6.0/woo-razorpay.php'] );
    }
  }
  return $value;
}
add_filter( 'site_transient_update_plugins', 'disable_userverification_plugin_updates' );


function filter_woocommerce_states( $states ) {
    $states['IN']['LA'] = __('Ladakh', 'woocommerce');
    return $states; 
}; 
add_filter( 'woocommerce_states', 'filter_woocommerce_states', 10, 1 );


function sv_conditional_email_recipient( $recipient, $order ) {

    // Bail on WC settings pages since the order object isn't yet set yet
    // Not sure why this is even a thing, but shikata ga nai
    $page = $_GET['page'] = isset( $_GET['page'] ) ? $_GET['page'] : '';
    if ( 'wc-settings' === $page ) {
        return $recipient; 
    }
    
    // just in case
    if ( ! $order instanceof WC_Order ) {
        return $recipient; 
    }

    $depot = get_post_meta($order->get_id(), 'depot', true);
    $userdata = get_userdata($depot);
    if($userdata){
        $recipient .= ', '.$userdata->user_email;
    }
    return $recipient;
}
add_filter( 'woocommerce_email_recipient_new_order', 'sv_conditional_email_recipient', 10, 2 );




// display an 'Out of Stock' label on archive pages
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_stock', 10 );
function woocommerce_template_loop_stock() {
    global $product;
    if ( ! $product->managing_stock() && ! $product->is_in_stock() ) {
        $check = false;
        $out_of_stock_explanation_enable = get_field('out_of_stock_explanation_enable', 'option');
        $out_of_stock_explanation = get_field('out_of_stock_explanation', 'option');
        if($out_of_stock_explanation_enable && $out_of_stock_explanation){
            $check = 'outofstk_expln';
        }
        $outofstock = '<p class="stock out-of-stock1 '.$check.'" style="line-height: 20px;padding-top: 11px;top:38%;"><span>Temporarily Not Available</span>';
        if($check){
            $outofstock .= '<span class="outofstk_expln_icon"><span class="dashicons dashicons-editor-help"></span></span>';
        }
        $outofstock .= '</p>';
        echo $outofstock;
    }
}

// Shop Page Header
add_filter( 'woocommerce_breadcrumb_defaults', 'add_page_content_woocommerce_breadcrumbs', 90, 1 );
function add_page_content_woocommerce_breadcrumbs($defaults) {
    $checkpage = get_permalink();
    if(is_shop()){
        $pageid = get_option( 'woocommerce_shop_page_id' ); 
        $shoppost = get_post($pageid);
        $getcontent = apply_filters('the_content',  $shoppost->post_content);
        $defaults['wrap_before'] = '<div class="storefront-breadcrumb"><div class="page-title-header"><div class="container"><h1 class="woocommerce-products-header__title page-title">Shop</h1><nav class="woocommerce-breadcrumb" >';
        $defaults['wrap_after'] = '</nav><div class="shop_content"><p>'.$getcontent.'</p></div></div></div></div>';
    }/* else if(($checkpage == site_url().'/login/')){
        $defaults = array();
    }*/ else if(is_singular('product')){
        $defaults['wrap_after'] = '<span class="backbtn"><button onclick="goBack()"><span class="backarrow"><</span>Back</button></span></nav></div></div>';
    } else if(!is_front_page()){
        global $post; 
        if($post){
            $currentpost = get_post($post->ID);
            if($currentpost){
                $gettitle = apply_filters('the_content',  $currentpost->post_title);
                $defaults['wrap_before']  = '<div class="storefront-breadcrumb innerpages"><div class="page-title-header"><div class="container"><h1 class="woocommerce-products-header__title page-title">'.$gettitle.'</h1><nav class="woocommerce-breadcrumb" >';
                $defaults['wrap_after'] = '</nav><div class="shop_content"></div></div></div></div>';
            }
        }
        
    }
    return $defaults;
}

add_action( 'get_header', 'bbloomer_remove_storefront_sidebar' );
function bbloomer_remove_storefront_sidebar() {
   if ( is_product() ) {
        remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
        remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
   }
}

add_action( 'wp_ajax_nopriv_change_dealer_dropdown', 'change_dealer_dropdown' );
add_action( 'wp_ajax_change_dealer_dropdown', 'change_dealer_dropdown' );
function change_dealer_dropdown() {   
    $status = false;
    $selectedstate = test_input($_REQUEST['id']);
    $pid = test_input($_REQUEST['pid']);
    $tag = get_term_by('slug', $selectedstate,'pa_state'); 
    if($tag){
        $tag_id =  $tag->term_id;
        $state_code = get_field('state_code', 'pa_state_' .$tag_id);
        if($state_code){
            $brand_code_array = array();
            //$terms = get_the_terms( $pid , 'brands' ); print_r($state_code);die();

            /*$terms = get_the_terms($pid, 'brands' );
            if($terms){
                foreach($terms as $term){
                    $brandtag_id = $term->term_id;
                    $brand_code = get_field('brand_code', 'brands_'.$brandtag_id);
                    if(!empty($brand_code)){
                        array_push($brand_code_array, $brand_code);
                    }
                }
            } */

            $firmcode = get_field('firmcode', $pid);
            if(!empty($firmcode)){
                $brand_code_array = array($firmcode);
            }


            if(!empty($brand_code_array)){
                $state_dealers = get_users(array(
                            'role__in' => 'dealer',
                            'meta_query'  => array(
                                'relation' => 'AND',
                                array(
                                    'key'     => 'state_code',
                                    'value'   => $state_code,
                                    'compare' => '='
                                ),
                                array(
                                    'key'     => 'brand_code',
                                    'value'   => $brand_code_array,
                                    'compare' => 'IN'
                                )/*,
                                array(
                                    'key'     => 'ur_user_status',
                                    'value'   => '1',
                                    'compare' => '='
                                )*/
                            ),
                            'orderby' => 'display_name', 
                            'order' => 'ASC'
                        ));
                if($state_dealers){
                    $status = '<option value="">Select Dealer</option>';
                    foreach($state_dealers as $state_dealer){
                            $status .= '<option value="'.$state_dealer->ID.'">'.$state_dealer->display_name.'</option>';
                    }
                }
            }

        } 

        /*$state_dealers = get_field('state_dealers', 'pa_state_' .$tag_id);
        if($state_dealers){
            $status = '<option value="">Select Dealer</option>';
            foreach($state_dealers as $state_dealer){
                $userdata = get_userdata($state_dealer);
                if($userdata){
                    $status .= '<option value="'.$state_dealer.'">'.$userdata->display_name.'</option>';
                }
            }
        }*/
    }
    echo $status;
    die();
}

add_action( 'wp_ajax_nopriv_change_depot_dropdown', 'change_depot_dropdown' );
add_action( 'wp_ajax_change_depot_dropdown', 'change_depot_dropdown' );
function change_depot_dropdown() {   
    $status = false;
    $selecteddealer = test_input($_REQUEST['id']);
    $pid = test_input($_REQUEST['pid']);
    if($selecteddealer){
        $depot_code = get_user_meta($selecteddealer, 'depot_code', true);
        if($depot_code){
            $depotusers = get_users(array(
                            'role__in' => 'depot',
                            'meta_query'  => array(
                                'relation' => 'AND',
                                array(
                                    'key'     => 'depot_code',
                                    'value'   => $depot_code,
                                    'compare' => '='
                                ),
                                array(
                                    'key'     => 'ur_user_status',
                                    'value'   => '1',
                                    'compare' => '='
                                )
                            ),
                            'orderby' => 'display_name', 
                            'order' => 'ASC'
                        ));
            if($depotusers){
                $status = '<option value="">Select Depot</option>';
                //$status .= '<option value="'.$depotusers[0]->ID.'">'.$depotusers[0]->display_name.'</option>';
                $status .= '<option value="'.$depot_code.'">'.$depotusers[0]->display_name.'</option>';
            }

        }

        /*$depotusers = get_users( [ 'role__in' => [ 'depot' ], 'meta_key' => 'parent_dealer', 'meta_value' => $selecteddealer ] );
        if($depotusers){
            $status = '<option value="">Select Dealer</option>';
                $status .= '<option value="'.$depotusers[0]->ID.'">'.$depotusers[0]->display_name.'</option>';
        }*/
    }
    echo $status;
    die();
}

function get_social_sharing() {
    global $post;       
    $buffy = '';
   // @todo single-post-thumbnail appears to not be in used! please check
    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
    $twitter_user = get_option('tds_tweeter_username');
    $buffy .= '<span class="socialshare"><p>Share</p><ul>';
    $buffy .= '<li class="fb">
                    <a target="_blank" class="td-social-sharing-buttons td-social-facebook" href="http://www.facebook.com/sharer.php?u=' . urlencode( esc_url( get_permalink() ) ) . '" onclick="window.open(this.href, \'mywin\',\'left=50,top=50,width=600,height=350,toolbar=0\'); return false;">
                        <i class="facebook-icon"></i>
                    </a>
                </li>
                <li class="twitter">   
                    <a target="_blank" class="td-social-sharing-buttons td-social-twitter" href="https://twitter.com/intent/tweet?text=' . htmlspecialchars(urlencode(html_entity_decode($post->post_title, ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') . '&url=' . urlencode( esc_url( get_permalink() ) ) . '&via=' . urlencode( $twitter_user ? $twitter_user : get_bloginfo( 'name' ) ) . '"  >
                        <i class="twitter-icon"></i>
                    </a>
                </li>
                <li class="instagram">   
                    <a target="_blank" class="td-social-sharing-buttons td-social-instagram" href="https://www.instagram.com/accounts/login/?hl=en"  >
                        <i class="instagram-icon"></i>
                    </a>
                </li>';
    $buffy .= '</ul></span>';
    return $buffy;
}

add_action( 'woocommerce_after_shop_loop_item_title', 'prima_custom_shop_item', 5);
function prima_custom_shop_item() {
    global $post, $product;
    if($product->get_sku())
    /* product sku */
    echo '<p class="listsku">SKU: '.$product->get_sku().'</p>';
}

add_filter('woocommerce_order_button_text','custom_order_button_text',1);
function custom_order_button_text($order_button_text) {
    $order_button_text = 'Generate Demand'; //Submit Demand
    return $order_button_text;
}

add_action( 'wp_ajax_nopriv_update_view_user_link', 'update_view_user_link' );
add_action( 'wp_ajax_update_view_user_link', 'update_view_user_link' );
function update_view_user_link() {   
    $status = false;
    $selecteduser = test_input($_REQUEST['id']);
    $selectedusertype = test_input($_REQUEST['type']);
    if($selecteduser){
        if($selectedusertype == 'depot'){
            $depot_code = $selecteduser;
            $selecteduser = '';
            $depotusers = get_users(array(
                            'role__in' => 'depot',
                            'meta_query'  => array(
                                'relation' => 'AND',
                                array(
                                    'key'     => 'depot_code',
                                    'value'   => $depot_code,
                                    'compare' => '='
                                ),
                                array(
                                    'key'     => 'ur_user_status',
                                    'value'   => '1',
                                    'compare' => '='
                                )
                            ),
                            'orderby' => 'display_name', 
                            'order' => 'ASC'
                        ));
            if($depotusers){
                $selecteduser = $depotusers[0]->ID;
            }
        }
        $user_info = get_userdata($selecteduser);
        if($user_info){
            $user_id = $user_info->ID;
            $user_displayname = $user_info->display_name;
            $user_email = $user_info->user_email;
            $user_address = get_field($selectedusertype.'_address', 'user_'.$selecteduser);
            if(empty($user_address)){
                $user_address = '-';
            }
            $contact_person = get_field('contact_person', 'user_'.$selecteduser);
            if(empty($contact_person)){
                $contact_person = '-';
            }
            if($selectedusertype == 'urc' || $selectedusertype == 'depot'){
                $contact_no = get_field('contact_no', 'user_'.$selecteduser);
            } else {
               $contact_no = get_field('billing_phone', 'user_'.$selecteduser); 
            }
            if(empty($contact_no)){
                $contact_no = '-';
            }
            $nameofbank = get_field('nameofbank', 'user_'.$selecteduser);
            if(empty($nameofbank)){
                $nameofbank = '-';
            }
            $ifsccode = get_field('ifsccode', 'user_'.$selecteduser);
            if(empty($ifsccode)){
                $ifsccode = '-';
            }
            $acno = get_field('acno', 'user_'.$selecteduser);
            if(empty($acno)){
                $acno = '-';
            }
            $user_gstn = get_field($selectedusertype.'_gstn', 'user_'.$selecteduser);
            $user_code = get_field($selectedusertype.'_code', 'user_'.$selecteduser);
            $urtype = ucfirst($selectedusertype);
            if($urtype == 'Urc'){
                $urtype = 'URC';
            }
            $formhtml = '<div class="form-group">';
            $formhtml .= '<label for="bank-name" class="col-form-label">'.$urtype.' Code:</label>';
            $formhtml .= '<span class="form-control">'.$user_code.'</span>';
            $formhtml .= '</div>';
            $formhtml .= '<div class="form-group">';
            $formhtml .= '<label for="bank-name" class="col-form-label">Name of '.$urtype.':</label>';
            $formhtml .= '<span class="form-control">'.$user_displayname.'</span>';
            $formhtml .= '</div>';
            $formhtml .= '<div class="form-group">';
            $formhtml .= '<label for="ifsc" class="col-form-label">'.$urtype.'\'s Address:</label>';
            $formhtml .= '<span class="form-control">'.$user_address.'</span>';
            $formhtml .= '</div>';
            $formhtml .= '<div class="form-group">';
            $formhtml .= '<label for="ifsc" class="col-form-label">Contact Person Name:</label>';
            $formhtml .= '<span class="form-control">'.$contact_person.'</span>';
            $formhtml .= '</div>';
            $formhtml .= '<div class="form-group">';
            $formhtml .= '<label for="ifsc" class="col-form-label">Phone:</label>';
            $formhtml .= '<span class="form-control">'.$contact_no.'</span>';
            $formhtml .= '</div>';
            $formhtml .= '<div class="form-group">';
            $formhtml .= '<label for="holder" class="col-form-label">Email:</label>';
            $formhtml .= '<span class="form-control">'.$user_email.'</span>';
            $formhtml .= '</div>';
            if($selectedusertype == 'depot'){
                $formhtml .= '<div class="form-group">';
                $formhtml .= '<label for="holder" class="col-form-label">Bank:</label>';
                $formhtml .= '<span class="form-control">'.$nameofbank.'</span>';
                $formhtml .= '</div>';
                $formhtml .= '<div class="form-group">';
                $formhtml .= '<label for="holder" class="col-form-label">IFSC code:</label>';
                $formhtml .= '<span class="form-control">'.$ifsccode.'</span>';
                $formhtml .= '</div>';
                $formhtml .= '<div class="form-group">';
                $formhtml .= '<label for="holder" class="col-form-label">Account No.:</label>';
                $formhtml .= '<span class="form-control">'.$acno.'</span>';
                $formhtml .= '</div>';
            }
            $status = $formhtml;
        }
    }
    echo $status;
    die();
}
//Add 4 related products
add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args', 20 );
function jk_related_products_args( $args ) {
    $args['posts_per_page'] = 4; // 4 related products
    $args['columns'] = 4; // arranged in 1 columns
    return $args;
}

add_filter( 'facetwp_pager_html', function( $output, $params ) {
    $output = str_replace('&gt;&gt;</a>','Next<span>&gt;</span></a>',$output);
    $output = str_replace('&lt;&lt;</a>','<span>&lt;</span>Previous</a>',$output);
    return $output;
}, 10, 2 );

//Remove product added to cart message in checkout page
add_filter( 'wc_add_to_cart_message_html', '__return_false' );

//add_filter('login_errors', 'filter_login_errors', 10, 1);   
function filter_login_errors( $error ) {
    /*if(!empty($error) && strpos($error, 'captcha') !== false){
        return $error;
    } elseif(!empty($error)){
        return 'Invalid login details';
    }*/
    if(!empty($error) ){
        if( strpos($error, 'The password you entered for username') !== false){
            return 'Invalid login details';
        } else if( strpos($error, 'Name/Password') !== false){
            return 'Invalid login details';
        }
    }
    return $error;
    
}

add_action('admin_delete_product_variations', 'delete_variations');

function delete_variations(){
    
    if(isset($_POST['submit'])){
        $delete_number_variation = $_POST['delete-number-variation'];
        if($delete_number_variation < 1){
            echo 'Please enter Number of variation delete at a time greater than 0';
        }else{
              
            
            
            $variation_ids = wp_parse_id_list(
              get_posts(
                array(              
                  'post_type'   => 'product_variation',
                  'fields'      => 'ids',
                  'post_status' => array( 'any', 'trash', 'auto-draft' ),
                  'numberposts' => $delete_number_variation, // phpcs:ignore WordPress.VIP.PostsPerPage.posts_per_page_numberposts
                )
              )
            );

            if ( ! empty( $variation_ids ) ) {
                $i = 0;
                  foreach ( $variation_ids as $variation_id ) {
                    
                      do_action( 'woocommerce_before_delete_product_variation', $variation_id );
                      wp_delete_post( $variation_id, true );
                      wp_delete_object_term_relationships( $variation_id, wc_get_attribute_taxonomy_names() );
                      do_action( 'woocommerce_delete_product_variation', $variation_id );
                    $i++;
                  }
                echo $i.' Product variations deleted';
                
            }else{                
                echo 'All product variations deleted';            
            }
        }
    }
}

function action_woocommerce_order_status_changed( $order_id, $this_status_transition_from, $this_status_transition_to, $instance ) { 
    send_sms_and_email_for_order_status_change($order_id);   
}; 
add_action( 'woocommerce_order_status_changed', 'action_woocommerce_order_status_changed', 10, 4 ); 

function send_sms_and_email_for_order_status_change( $order_id='', $orderstatus='' ) {
    $order = new WC_Order( $order_id );
    if($order){ 
        $updatelastdate = false; 
        $statuschangedate = true;
        $pid = get_product_from_order($order_id);
        $wheel = check_if_product_is_two_or_four_wheeler($pid);
        if($wheel){
            $updatelastdate = true;
        }
        $last_purchase_date = date("d-m-Y", strtotime($order->get_date_created()) );
        //$last_purchase_date = date('Y-m-d');

        $orderidwithprefix = change_woocommerce_order_number($order_id);
        $user_id = get_post_meta( $order_id, '_customer_user', true );
        $mobile = get_post_meta( $order_id, 'user_registration_mobile', true );
        if(empty($mobile)){
            $ordertype = get_post_meta( $order_id, 'ordertype', true );
            if($ordertype != 'Manual'){
                $mobile = get_user_meta( $user_id, 'user_registration_mobile', true );
            }
        }
        $beneficiary_name = get_post_meta( $order_id, 'user_registration_beneficiary_name', true );
        $customer = new WC_Customer( $user_id );
        $customeremail = $customer->get_email();
        
        if(empty($orderstatus)){
            $orderstatus = $order->get_status();
        }
        $currentdate = date('Y-m-d');

        $msg = 'Dear '.$beneficiary_name.',';
        if($orderstatus == 'delivered'){
            $msg .= '  AFD1 item against';
        } 
        $msg .= ' Your Demand '.$orderidwithprefix;
        if($orderstatus == 'on-hold'){
            $x = 37;
            //$msg .= ' has been received on the CSD AFD portal and current status is on hold. Kindly upload payment receipt and payment details on CSD portal for further processing by Depot.';
			$msg .= ' has been received on the CSD AFD portal and is put on hold. Kindly login and check Notes for your further action. Team CSD';
        }  else if($orderstatus == 'processing'){
            $x = 40;
            // $msg .= ' has been received on the CSD AFD portal and current Status is Processing. Beneficiaries may please note that processing of documents at depot and issue of local supply order will take upto three working days. Please bear with us. We will together make it an interesting experience.';
			$msg .= ' has been received on the CSD AFD portal and current Status is Processing. Beneficiaries may please note that processing of documents at depot and issue of local supply order will take upto three working days. Please bear with us. Team CSD.';
            //$msg .= ' It would take 3 working days for CSD to process the demand of AFD1 item.';
            //$msg .= ' Beneficiaries may please note that processing of documents at depot and issue of local supply order will take upto three working days. Please bear with us. We will together make it an interesting experience.';
        }  else if($orderstatus == 'poreleased'){
            $x = 36;
            update_post_meta($order_id, 'statuschangedate', $currentdate);
            // $msg .= ' has been processed by CSD and current Status is Local Supply Order issued. Please visit the URC for physical verification of documents and collection of Local Supply Order from URC.';
			   $msg .= ' has been processed by CSD and current Status is Local Supply Order issued. Please visit the URC for physical verification of documents and collection of Local Supply Order from URC. Team CSD.';
        }  else if($orderstatus == 'completed'){
            $x = 35;
            // $msg .= ' has been completed and local supply order/authority letter handed over to you by URC. Order Status is changed to Completed. Please visit the Dealer to collect the AFD1 item. Please send us a feedback about your experience of buying AFD1 item from CSD using feedback link in your login.';
			$msg .= ' has been completed and local supply order/authority letter handed over to you by URC. Order Status is changed to Completed. Please visit the Dealer to collect the AFD1 item. Team CSD.';
            //$msg .= ' Please send us a feedback about your experience of buying AFD1 item from CSD using feedback link in your login.';
        }  else if($orderstatus == 'Fedbydepot'){
            $x = 34;
            update_post_meta($order_id, 'depotrejectdate', $currentdate);
            $last_purchase_date = '';
            // $msg .= ' has been rejected by Depot, Please see notes under Recent Demands for details.';
			   $msg .= ' has been rejected by Depot, Please see notes under Recent Demands for details. Please approach URC for refund process. Team CSD.';
        } else if($orderstatus == 'rejectedbyurc'){
            $x = 39;
            $last_purchase_date = '';
            // $msg .= ' has been rejected by URC, Please see notes under Recent Demands for details.';
			   $msg .= ' has been rejected by URC, Please see notes under Recent Demands for details.Please approach URC for refund process. Team CSD.';
        } else if($orderstatus == 'pending'){
            $x = 14;
            // $msg .= ' is in Pending Payment Status. Please make payment so that it can be processed further by CSD.';
			  $msg .= ' is in Pending Payment Status. Please make payment so that it can be processed further by CSD.';
        } else if($orderstatus == 'failed'){
            $x = 15;
            $last_purchase_date = '';
            // $msg .= ' status is changed to Failed as Payment has not been received by CSD.';
			$msg .= ' status is changed to Failed as Payment has not been received by CSD.';
        } else if($orderstatus == 'cancelled'){
            $x = 30;
            $last_purchase_date = '';
            // $msg .= ' has been cancelled.';
			$msg .= ' has been cancelled. Please approach URC to submit refund application. Team CSD.';
        } else if($orderstatus == 'refunded'){
            $x = 16;
            $last_purchase_date = '';
            $msg .= ' has been Refunded.';
        } else if($orderstatus == 'delivered'){
            $x = 33;
            // $msg .= ' has been delivered by the dealer';
			$msg .= ' has been delivered by the dealer. Team CSD';
        }  else if($orderstatus == 'uploaded'){
            $x = 18;
            $updatelastdate = false;
            $statuschangedate = false;
            $msg .= ' - Both payment receipt and payment details are saved';
        }  else if($orderstatus == 'sale_closed'){
            $x = 30;
            $msg = 'Sale is closed. Please cancel your demand #'.$orderidwithprefix;
        } else {
            $x = 19;
            $orderstatusname = wc_get_order_status_name($orderstatus);
            if($orderstatusname){
                $msg .= ' is '.$orderstatusname.'. Team CSD.';
            }
        }

        if($wheel){
            update_user_meta($user_id, 'last_purchase_'.$wheel.'_wheeler', $last_purchase_date);
        } else {
            update_user_meta($user_id, 'last_purchase_white_goods', $last_purchase_date);
        }
        if($statuschangedate){
            //update_post_meta($order_id, 'statuschangedate', $currentdate);
        }
            $message  = sprintf( __( $msg, 'user-registration' ));
            $subject = 'Demand #'.$orderidwithprefix;
            $email_header = email_template_header($subject);
            $email_footer = email_template_footer();
            $mail_details = $email_header;
            $mail_details .= '<p>Demand: #' . $orderidwithprefix . "<br/>";
            $mail_details .= '<p>' . $message . "<br/>";
            $mail_details .= $email_footer;
            $sender =  get_option( 'woocommerce_email_from_name' );//get_option('blogname');
            $sender_email = get_option( 'woocommerce_email_from_address' );//get_option('admin_email');
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $headers[] = 'From: ' . $sender . ' < ' . $sender_email . '>' . "\r\n";
            //if ($reply_email != "") {
                $headers[] = 'Reply-To:' . $sender . ' < ' . $sender_email . '>' . "\r\n";
            //}
            @wp_mail($customeremail, $subject, $mail_details, $headers);

            include_once( 'apis/class-smsgateway.php' );
            
            if($mobile){
                $response = SMSGateWay::send_sms( $mobile, $message, $x );
                echo $otp_send = strpos( $response, 'Message submitted successfully' ) !== false  ? true : false; 
            }
            send_admin_sms_after_order_changed_to_processing($order_id);
    }
}

function send_admin_sms_after_order_changed_to_processing($order_id='', $type=''){
    $status = false;
    include_once( 'apis/class-smsgateway.php' );
    if($order_id){
        $order = wc_get_order( $order_id );
        if($order){
            $orderstatus = $order->get_status();
            if($orderstatus == 'processing'){
                $enable_processing_sms = get_field('enable_processing_sms', 'option');
                $adminmobile = get_field('admin_phone', 'option');
                    
                $date = $order->get_date_created()->date( 'd/m/y' );
                $orderidwithprefix = change_woocommerce_order_number($order_id);
                $beneficiary_name = get_post_meta( $order_id, 'user_registration_beneficiary_name', true );
                $depotname = get_post_meta( $order_id, 'depotname', true );
                $card = get_post_meta( $order_id, 'user_registration_card_id', true );
                $chip = get_post_meta( $order_id, 'user_registration_chip_number', true );
                $price = $order->get_total();
                //$mode_of_payment = get_post_meta($order_id, 'mode_of_payment', true);
                $payment_method_check = false;
                $get_payment_method = $order->get_payment_method();
                $sms_payment_gateway = get_field('sms_payment_gateway', 'option');
                $adminmessage = "Demand ID: ".$orderidwithprefix." Date: ".$date." Depot: ".$depotname." Beneficiary: ".$beneficiary_name." Grocery card : ".$card." Chip Number: ".$chip." Price: ".$price." Status: Processing Payment Mode: ".$get_payment_method.". Team CSD.";
                if(empty($sms_payment_gateway) || $get_payment_method == 'razorpay'){
                    $payment_method_check = true;
                }  
                if($type == 'manual'){
                    $enable_processing_sms = true;
                }

                if($payment_method_check){
                    if($enable_processing_sms){
                    
                        if($adminmobile){
                            $response = SMSGateWay::send_sms( $adminmobile, $adminmessage, 20 );
                            $status = $otp_send = strpos( $response, 'Message submitted successfully' ) !== false  ? true : false; 
                            if($status){
                                //$order->add_order_note( 'Processing SMS sent to SCPL' );
                            }
                            if($status && $type == 'manual'){
                                $count = get_post_meta($order_id, 'manual_sms_count', true); 
                                if(empty($count)){
                                    $count = 0;
                                }
                                $countnew = $count+1;
                                update_post_meta($order_id, 'manual_sms_count', $countnew); 
                            }
                        }
                    }
                

                    $sms_to_depot_nodal = get_field('sms_to_depot_nodal', 'option');
                    if($sms_to_depot_nodal){
                        $depot_code = get_post_meta( $order_id, 'depot_code', true ); 
                        if($depot_code){
                            $depotnodalusers = get_users(array(
                                    'role__in' => array('depot', 'depot-nodal'),
                                    'meta_key'    => 'depot_code',
                                    'meta_value'  => $depot_code
                                ));
                            if($depotnodalusers){
                                foreach($depotnodalusers as $depotnodaluser){
                                    $nodalphone = get_user_meta($depotnodaluser->ID, 'billing_phone', true);
                                    $nodalname = get_user_display_name($depotnodaluser->ID);
                                    if($nodalphone){
                                        $depotnadalmessage = "Dear ".$depotname." Depot, Demand ID: ".$orderidwithprefix." Date: ".$date." Beneficiary: ".$beneficiary_name." Grocery card: ".$card." Chip Number: ".$chip." Price: ".$price." Status: Processing Payment Mode: ".$get_payment_method.". Team CSD.";
                                        $response = SMSGateWay::send_sms( $nodalphone, $depotnadalmessage, 21 );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    echo $status;
}
add_action( 'wp_ajax_send_admin_sms_processing', 'send_admin_sms_processing' );
add_action( 'wp_ajax_nopriv_send_admin_sms_processing', 'send_admin_sms_processing' );
function send_admin_sms_processing() {
    $status = false;
    $orderid = test_input($_REQUEST['id']);
    echo $status = send_admin_sms_after_order_changed_to_processing($orderid, 'manual');
    
    die();
}

add_action('admin_footer', 'custom_allimport_js');
function custom_allimport_js(){
    echo '<script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery("#advanced_upload").click(function(e){
                if(!jQuery(".wpallimport-to-existing-items").hasClass("wpallimport-import-to-checked")){
                    alert("Please choose Existing Items")
                    e.preventDefault();
                }
            });
        });        
    </script>';
}

add_action('admin_menu', 'wpdocs_register_my_custom_submenu_page');
 
function wpdocs_register_my_custom_submenu_page() {
    add_submenu_page(
        'tools.php',
        'Delete product variations',
        'Delete product variations',
        'manage_options',
        'product-variation-delete',
        'vi_product_variation_delete_page_callback' );

    add_submenu_page(
        'tools.php',
        'All import settings',
        'All import settings',
        'manage_options',
        'all_import_settings',
        'vi_all_import_settings_callback' );

    add_submenu_page(
        'tools.php',
        'Mobile Verify All Beneficiaries/Dealers',
        'Mobile Verify All Beneficiaries/Dealers',
        'manage_options',
        'mobile_verify_settings',
        'vi_mobile_verify_settings_callback' );
}

function vi_mobile_verify_settings_callback() {
    if( current_user_can('administrator') ) {     
        echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
            echo '<h2>Mobile Verify settings</h2>';
            do_action('admin_all_mobileverify_settings'); ?>
            <form method="post" action="#">
     
                <div id="universal-message-container">                
         
                    <div class="options">
                        <table width="100%">

                            <tr>
                                <th></th>
                                <td>
                                    <input type="submit" name="submit" min="1" value="Verify All Beneficiaries" class="button button-primary" />
                                </td>
                               
                            </tr>
                        </table>
                </div>
            </form>

            <form method="post" action="#">
     
                <div id="universal-message-container">                
         
                    <div class="options">
                        <table width="100%">

                            <tr>
                                <th></th>
                                <td>
                                    <input type="submit" name="submit" min="1" value="Verify All Dealers" class="button button-primary" />
                                </td>
                               
                            </tr>
                        </table>
                </div>
            </form>
        <?php echo '</div>';
    }else{
        echo "You don't have permission to this page";
    }
}

add_action('admin_all_mobileverify_settings', 'vi_admin_all_mobileverify_settings');

function vi_admin_all_mobileverify_settings(){
    if (isset($_POST['submit'])) {
        $status = false;
        $checkuserrole = $_POST['submit'];
        if($checkuserrole == 'Verify All Dealers'){
            $userrole = 'dealer';
        } else {
            $userrole = 'subscriber';
        }

        $allsubscriberusers = get_users(array(
                    'role__in' => array($userrole),
                    'number' => 50,
                    'meta_query' => array(
                            'relation' => 'OR',
                            array(
                                'key' => 'mobile_verified',
                                'value' => '',
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'mobile_verified',
                                'compare' => 'NOT EXISTS',
                            ),
                            array(
                                'key' => 'mobile_verified',
                                'value' => false,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'mobile_verified',
                                'value' => NULL,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'mobile_verified',
                                'value' => 0,
                                'compare' => '=',
                            ),
                        )
                ));
        if($allsubscriberusers){
            $total = count($allsubscriberusers);
            $limit = ceil($total/50);
            for($count = 0; $count< $limit; $count++){
            
                $subscriberusers = get_users(array(
                        'role__in' => array($userrole),
                        'number'       => 50,
                        'offset'       => $count * 50,
                        'meta_query' => array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'mobile_verified',
                                    'value' => '',
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'mobile_verified',
                                    'compare' => 'NOT EXISTS',
                                ),
                                array(
                                    'key' => 'mobile_verified',
                                    'value' => false,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'mobile_verified',
                                    'value' => NULL,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'mobile_verified',
                                    'value' => 0,
                                    'compare' => '=',
                                ),
                            )
                    ));
                if($subscriberusers){
                    foreach($subscriberusers as $subscriberuser){
                        $status = true;
                        update_user_meta( $subscriberuser->ID, 'mobile_verified', true );
                    }
                }
            }
        } else {
            echo 'No '.$userrole.' with empty mobile verified status exists.';
        }
        if($status){
            echo 'Updated 50 '.$userrole.'s. Please click again.';
        }
    }
}

function vi_all_import_settings_callback() {
    if( current_user_can('administrator') ) {     
        echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
            echo '<h2>All import settings</h2>';
            do_action('admin_all_import_settings'); ?>
            <form method="post" action="#">
     
                <div id="universal-message-container">                
         
                    <div class="options">
                        <table width="100%">
                            <tr>
                                <th width="125px">Depot files path</th>
                                <td>
                                    <input type="text" name="depot-file-path" value="<?php echo get_option( 'depot_file_path' ); ?>" style="width:65%" />
                                </td>
                                
                            </tr>

                            <tr>
                                <th></th>
                                <td>
                                    <input type="submit" name="submit" min="1" value="Save" class="button button-primary" />
                                </td>
                               
                            </tr>
                        </table>
                </div><!-- #universal-message-container -->
            </form>
        <?php echo '</div>';
    }else{
        echo "You don't have permission to this page";
    }
}

add_action('admin_all_import_settings', 'vi_admin_all_import_settings');

function vi_admin_all_import_settings(){
    if (isset($_POST['submit'])) {
        $depot_file_path = $_POST['depot-file-path'];
        update_option( 'depot_file_path', $depot_file_path );
    }
}
 
function vi_product_variation_delete_page_callback() {
    if( current_user_can('administrator') ) {     
        echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
            echo '<h2>Delete Product Variations</h2>';
            do_action('admin_delete_product_variations'); ?>
            <form method="post" action="#">
     
                <div id="universal-message-container">                
         
                    <div class="options">
                        <table>
                            <tr>
                                <th>Number of variation delete at a time</th>
                                <td>
                                    <input type="number" name="delete-number-variation" min="1" value="" />
                                </td>
                                <td>
                                    <input type="submit" name="submit" min="1" value="Delete" class="button button-primary" />
                                </td>
                            </tr>
                        </table>
                </div><!-- #universal-message-container -->
            </form>
        <?php echo '</div>';
    }else{
        echo "You don't have permission to this page";
    }
} 

add_action('init', 'test_delete');

function test_delete(){

	$tstss = isset($_GET['tstss'])?$_GET['tstss']:'';
	if($tstss == 1){
		$args = array(
		    'role'    => 'subscriber',
		    'orderby' => 'user_nicename',
		    'order'   => 'ASC'
		);
		$users = get_users( $args );
		if(!empty($users)){
			foreach ($users as $key => $user) {				
				$updated = update_user_meta( $user->ID, 'ur_user_status', 0 );
			}
		}
		die;
	}
}

add_action( 'wp_ajax_nopriv_approve_or_deny_current_user', 'approve_or_deny_current_user' );
add_action( 'wp_ajax_approve_or_deny_current_user', 'approve_or_deny_current_user' );
function approve_or_deny_current_user() {   
    $status = false;
    $date = date('Y-m-d');
    $userid = test_input($_REQUEST['user']);
    $denyreason = test_input($_REQUEST['denyreason']);
    $type = test_input($_REQUEST['type']);
    if($userid && $type){
        $user = get_user_by( 'id', $userid );
        if($userid){
            if($type == 'denied'){
                $ur_user_status = -1;
                update_user_meta($userid, 'access_profile_edit', 1);
                $currentstatus = get_user_meta($userid, 'ur_user_status', true);
                //if($currentstatus == 0){
                    $status_change_count = get_user_meta($userid, 'status_change_count', true);
                    $status_change_count = $status_change_count+1;
                    update_user_meta($userid, 'status_change_count', $status_change_count);
                //}
                update_user_meta($userid, 'ur_changedate', $date);
            } else if($type == 'approved'){
                $ur_user_status = 1;
                $denyreason = '';
                update_user_meta($userid, 'access_profile_edit', 0);
                update_user_meta($userid, 'status_change_count', 0);
                update_user_meta($userid, 'ur_changedate', $date);
            }
            update_user_meta($userid, 'user_edited_count', 0);



            update_user_meta($userid, 'ur_user_status', $ur_user_status);
            update_user_meta($userid, 'denyreason', $denyreason);
            send_user_approval_status_sms_and_email($userid, $type);
            $status = true;
        }
    }
    echo $status;
    die();
}

function send_user_approval_status_sms_and_email($userid='', $type=''){
    if($userid && $type){
        $customer = new WC_Customer( $userid );
        if($customer){
            $customeremail = $customer->get_email();
            $mobile = get_user_meta( $userid, 'user_registration_mobile', true );
            $beneficiary_name = get_user_meta( $userid, 'user_registration_beneficiary_name', true );      
            $denyreason = get_user_meta( $userid, 'denyreason', true );      
            
             $msg = 'Dear '.$beneficiary_name.', '. "<br/>";
             $msg .= 'Your sign up request has been '.$type.' by Admin.'. "<br/>";
             if($denyreason){
                 $msg .= ' Reason for Denial is '. $denyreason."<br/>";
             }
             if($type == 'denied'){
                $x = 23;
                $msg .= ' You may login on portal and update your registration form under menu by filling correct data and resubmit your registration request to admin for approval.'."<br/>";
             } else if($type == 'approved'){
                 $x = 32;
				 $msg .=' You may login now on portal.'. "<br/>";
             } else {
                 $x = 24;
             }
			 
            if($customeremail){ 
                $message  = sprintf( __( $msg, 'user-registration' ));
                $subject = 'Login '.$type;
                $email_header = email_template_header($subject);
                $email_footer = email_template_footer();
                $mail_details = $email_header;
                $mail_details .=  $message;
                $mail_details .= $email_footer;
                //$sender = get_option( 'woocommerce_email_from_name' ); //get_option('blogname');
                //$sender_email = get_option( 'woocommerce_email_from_address' );//get_option('admin_email');
                $sender = get_option( 'user_registration_email_from_name' );
                $sender_email = get_option( 'user_registration_email_from_address' );
                $headers[] = 'Content-Type: text/html; charset=UTF-8';
                $headers[] = 'From: ' . $sender . ' < ' . $sender_email . '>' . "\r\n";
                $headers[] = 'Reply-To:' . $sender . ' < ' . $sender_email . '>' . "\r\n";
                @wp_mail($customeremail, $subject, $mail_details, $headers);
            }
            if($mobile){
                include_once( 'apis/class-smsgateway.php' );
                $message  = str_replace("<br/>", ' ', $msg);
                $response = SMSGateWay::send_sms( $mobile, $message.'. Team CSD.', $x);
                $otp_send = strpos( $response, 'Message submitted successfully' ) !== false  ? true : false; 

            }
        }
    }
}

function send_sms_and_email_after_profile_update( $user_id, $formid ) {

    $profilesms = isset($_SESSION['profilesms'])?$_SESSION['profilesms']:'';
    if(empty($profilesms)){

        $user_edited_count = get_user_meta($user_id, 'user_edited_count', true);
        if($user_edited_count){
            $user_edited_count = $user_edited_count+1;
        } else {
           $user_edited_count = 1; 
        }
        
        update_user_meta($user_id, 'user_edited_count', $user_edited_count);
        
        $_SESSION['profilesms'] = 'sent';
        include_once( 'apis/class-smsgateway.php' );
        $mobile = get_user_meta( $user_id, 'user_registration_mobile', true ); 
        $beneficiary_name = get_user_meta( $user_id, 'user_registration_beneficiary_name', true );
        $msg = 'Dear '.$beneficiary_name.', '. "<br/>";
        // $msg .= 'Your updated registration form has been resubmitted for Admin Approval. You would be notified after Admin Approval.';
		$msg .= 'Your updated registration form has been resubmitted for Admin Approval. You would be notified after Admin Approval.';
        if($mobile){
            $message  = str_replace("<br/>", ' ', $msg);
            $response = SMSGateWay::send_sms( $mobile, $message, 25 );
            $otp_send = strpos( $response, 'Message submitted successfully' ) !== false  ? true : false; 
        }
        $customer = new WC_Customer( $user_id );
        $customeremail = $customer->get_email();
        $message  = sprintf( __( $msg, 'user-registration' ));
        $subject = 'Profile Updated ';
        $email_header = email_template_header($subject);
        $email_footer = email_template_footer();
        $mail_details = $email_header;
        $mail_details .=  $message;
        $mail_details .= $email_footer;
        $sender = get_option( 'user_registration_email_from_name' );
        $sender_email = get_option( 'user_registration_email_from_address' );
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . $sender . ' < ' . $sender_email . '>' . "\r\n";
        $headers[] = 'Reply-To:' . $sender . ' < ' . $sender_email . '>' . "\r\n";
        @wp_mail($customeremail, $subject, $mail_details, $headers);
    }
}
add_action( 'user_registration_save_profile_details', 'send_sms_and_email_after_profile_update', 10, 2 );


function send_ordernote_sms_and_email( $notemsg, $orderid ) {

    if(!empty($notemsg) && !empty($orderid)){
        $user_id = get_post_meta($orderid, '_customer_user', true);
        if($user_id){
            $customeremail = '';
            $ordertype = get_post_meta( $orderid, 'ordertype', true ); 
            if($ordertype == 'Manual'){ 
                $customeremail = get_post_meta( $orderid, '_billing_email', true );
            } else {
                $customer = new WC_Customer( $user_id );
                if($customer){
                    $customeremail = $customer->get_email();
                }
            }
            include_once( 'apis/class-smsgateway.php' );
            $mobile = get_post_meta( $orderid, 'user_registration_mobile', true ); 
            $beneficiary_name = get_post_meta( $orderid, 'user_registration_beneficiary_name', true );
            $msg = 'Dear '.$beneficiary_name.', '. "<br/>";
            $msg .= $notemsg;
            if($mobile){
                // $message  = 'Dear '.$beneficiary_name.', a note has been submitted against your demand. Kindly login to the portal to check for further details. Team CSD.';
				$message  = 'Dear '.$beneficiary_name.', a note has been submitted against your demand. Kindly login to the portal to check for further details and reply. Team CSD.';
                $response = SMSGateWay::send_sms( $mobile, $message, 31 );
                $otp_send = strpos( $response, 'Message submitted successfully' ) !== false  ? true : false; 
            }
            if($customeremail){
                $message  = sprintf( __( $msg, 'user-registration' ));
                $subject = 'Order Note ';
                $email_header = email_template_header($subject);
                $email_footer = email_template_footer();
                $mail_details = $email_header;
                $mail_details .=  $message;
                $mail_details .= $email_footer;
                $sender = get_option( 'woocommerce_email_from_name' );
                $sender_email = get_option( 'woocommerce_email_from_address' );
                $headers[] = 'Content-Type: text/html; charset=UTF-8';
                $headers[] = 'From: ' . $sender . ' < ' . $sender_email . '>' . "\r\n";
                $headers[] = 'Reply-To:' . $sender . ' < ' . $sender_email . '>' . "\r\n";
                @wp_mail($customeremail, $subject, $mail_details, $headers);
            }
        }
    }
}


add_action( 'wp_ajax_nopriv_save_customerfeedback', 'save_customerfeedback' );
add_action( 'wp_ajax_save_customerfeedback', 'save_customerfeedback' );
function save_customerfeedback() {   
    $status = false;
    $orderid = test_input($_REQUEST['id']);
    $customerfeedback = test_input($_REQUEST['customerfeedback']);
    $order = wc_get_order( $orderid ); 
    if($order && $customerfeedback){
        $status = true;
        $orderidwithprefix = change_woocommerce_order_number($orderid);
        $beneficiary = get_post_meta($order->get_id(), 'user_registration_beneficiary_name', true);
        //$beneficiary_email = get_post_meta($order->get_id(), 'user_registration_email', true);
        
        $user_id = get_post_meta( $order->get_id(), '_customer_user', true );
        $customer = new WC_Customer( $user_id );
        $beneficiary_email = $customer->get_email();
        $employment_status = get_post_meta($orderid, 'user_registration_employment_status', true);
        $depotname = get_post_meta($orderid, 'depotname', true);
        $urcname = get_post_meta($orderid, 'urcname', true);
        $date = date('d-m-Y');
        $msg =  'A Feedback has been submitted. Kindly check mail. Depot: '.$depotname.' URC: '.$urcname.' Name: '.$beneficiary.' Demand ID: '.$orderidwithprefix.'  Date: '.$date.'  Employment: '.$employment_status.'. Team CSD';
        update_post_meta($orderid, 'customerfeedback', $customerfeedback);
        $order->add_order_note( ' Feedback - '.$customerfeedback );

        $subject = ' Feedback : Demand ID #'.$orderidwithprefix;
        $email_header = email_template_header($subject);
        $email_footer = email_template_footer();
        $mail_details = $email_header;
        $mail_details = 'Demand : '.$orderidwithprefix . "<br/>";
        $mail_details .= 'Depot : '.$depotname . "<br/>";
        $mail_details .= 'Feedback : '.$customerfeedback . "<br/>";
        $mail_details .= $email_footer;
        $sender =  get_option( 'woocommerce_email_from_name' );
        $sender_email = get_option( 'woocommerce_email_from_address' );
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . $sender . ' < ' . $sender_email . '>' . "\r\n";
        //$headers[] = 'From: ' . $beneficiary . ' < ' . $beneficiary_email . '>' . "\r\n";
        $headers[] = 'Reply-To:' . $beneficiary . ' < ' . $beneficiary_email . '>' . "\r\n";

        //$headers[] = 'Cc: DDGCS <ddgcs@csdindia.gov.in>';
        //$headers[] = 'Cc: AGM AFD <agmafd@csdindia.gov.in>';
        $depotemail = '';
        include_once( 'apis/class-smsgateway.php' );
        $user_id = get_post_meta( $orderid, 'depot', true );
        $depot = get_user_by( 'id', $user_id );
        if($depot){
            $mobile = get_user_meta( $user_id, 'billing_phone', true );
            $depotemail = get_user_meta( $user_id, 'billing_email', true );
            if($depotemail){
                //$headers[] = 'Cc: '.$depotname.' <'.$depotemail.'>';
            }            
            if($mobile){

                $response = SMSGateWay::send_sms( $mobile, $msg, 27 );
                $otp_send = strpos( $response, 'Message submitted successfully' ) !== false  ? true : false; 
            }
        }
        if(!empty($depotemail)){
            $headers[] = 'Cc: R P Karthikeyan <agmafd@csdindia.gov.in>';
            $to = $depotemail;
        } else {
            $to =  'agmafd@csdindia.gov.in';
        }
        $customer_feedback_cc_emails = get_field('customer_feedback_cc_emails', 'option');
        if(!empty($customer_feedback_cc_emails)){
            $customer_feedback_cc_emailsarr = explode(',',$customer_feedback_cc_emails);
            foreach($customer_feedback_cc_emailsarr as $each){
                $headers[] = 'Cc: '.trim($each);
            }
        }

        @wp_mail($to, $subject, $mail_details, $headers);

        $admin_phone = get_field('admin_phone', 'option');
        if($admin_phone){
            $response = SMSGateWay::send_sms( $admin_phone, $msg, 27 );
            $otp_send = strpos( $response, 'Message submitted successfully' ) !== false  ? true : false; 
        }
    }
    echo $status;
    die();
}




add_action( 'wp_ajax_nopriv_save_additionalpay', 'save_additionalpay' );
add_action( 'wp_ajax_save_additionalpay', 'save_additionalpay' );
function save_additionalpay() {   
    $status = false;
    $orderid = test_input($_REQUEST['id']);
    $additionalpayreason = test_input($_REQUEST['additionalpayreason']);
    $additionalpayamount = test_input($_REQUEST['additionalpayamount']);
    $order = wc_get_order( $orderid );
    if(!empty($order) && !empty($additionalpayreason) && !empty($additionalpayamount)){
        $status = true;
        update_post_meta($orderid, 'additionalpayreason', $additionalpayreason);
        update_post_meta($orderid, 'additionalpayamount', $additionalpayamount);
        update_post_meta($orderid, 'additionalpayneeded', $additionalpayamount);
        
        $message = 'Additional Payment of Rs.'.$additionalpayamount.'  is to be made';
        if($additionalpayreason){
            $message .= "<br/>".' Reason: '.$additionalpayreason;
        }

        $order->update_status('wc-pending');
        $order->add_order_note( $message );

        $user_id = get_post_meta( $orderid, '_customer_user', true );
        $mobile = get_post_meta( $orderid, 'user_registration_mobile', true );
        if(empty($mobile)){
            $ordertype = get_post_meta( $orderid, 'ordertype', true );
            if($ordertype != 'Manual'){
                $mobile = get_user_meta( $user_id, 'user_registration_mobile', true );
            }
        }
        $beneficiary_name = get_post_meta( $orderid, 'user_registration_beneficiary_name', true );
        $customer = new WC_Customer( $user_id );
        $customeremail = $customer->get_email();

        $msg = 'Dear '.$beneficiary_name.', '. "<br/>";
        $msg .= $message. "<br/>";
        $orderidwithprefix = change_woocommerce_order_number($orderid);
        $subject = 'Demand #'.$orderidwithprefix.' - Additional payment Required';
        $email_header = email_template_header($subject);
        $email_footer = email_template_footer();
        $mail_details = $email_header;
        $mail_details .= $msg . "<br/>";
        $mail_details .= '<p>Demand: #' . $orderidwithprefix . "<br/>";
        $mail_details .= $email_footer;
        $sender =  get_option( 'woocommerce_email_from_name' );
        $sender_email = get_option( 'woocommerce_email_from_address' );
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . $sender . ' < ' . $sender_email . '>' . "\r\n";
        $headers[] = 'Reply-To:' . $sender . ' < ' . $sender_email . '>' . "\r\n";
        @wp_mail($customeremail, $subject, $mail_details, $headers);

        include_once( 'apis/class-smsgateway.php' );
        
        if($mobile){
            $msg  = str_replace("<br/>", ' ', $msg);
            $response = SMSGateWay::send_sms( $mobile, $msg, 6 );
            $otp_send = strpos( $response, 'Message submitted successfully' ) !== false  ? true : false; 
        }

        $pay_link = create_razor_pay_link( $additionalpayamount, $orderid );
        
        if ( ! empty( $pay_link ) ) {
            update_post_meta( $orderid, 'rzp_additional_pay_link', $pay_link );
        }
    }
    
    echo $status;
    die();
}


add_action( 'wp_ajax_nopriv_save_additionaldoc', 'save_additionaldoc' );
add_action( 'wp_ajax_save_additionaldoc', 'save_additionaldoc' );
function save_additionaldoc() {   
    $status = false;
    $orderid = test_input($_REQUEST['id']);
    $additionaldocremarks = test_input($_REQUEST['additionaldocremarks']);
    $order = wc_get_order( $orderid );
    if(!empty($order) && !empty($additionaldocremarks) ){
        $status = true;
        update_post_meta($orderid, 'additionaldocremarks', $additionaldocremarks);
        update_post_meta($orderid, 'additionaldocneeded', '1');
        $beneficiary_name = get_post_meta( $orderid, 'user_registration_beneficiary_name', true );
        
        //$sms = 'Dear '.$beneficiary_name.', Additional documents are requested by depot. Kindly check your mail.';
		$sms = 'Dear '.$beneficiary_name.', Additional documents are requested by depot. Kindly check your registered mail. Team CSD.';
        $message = "Additional documents are requested by depot. <br/> Remarks: ".$additionaldocremarks;
        $order->add_order_note( $message );
        $order->update_status('wc-on-hold');
        

        $user_id = get_post_meta( $orderid, '_customer_user', true );
        $mobile = get_post_meta( $orderid, 'user_registration_mobile', true );
        if(empty($mobile)){
            $ordertype = get_post_meta( $orderid, 'ordertype', true );
            if($ordertype != 'Manual'){
                $mobile = get_user_meta( $user_id, 'user_registration_mobile', true );
            }
        }
        $customer = new WC_Customer( $user_id );
        $customeremail = $customer->get_email();

        $msg = 'Dear '.$beneficiary_name.', '. "<br/>";
        $msg .= $message. "<br/>";
        $orderidwithprefix = change_woocommerce_order_number($orderid);
        $subject = 'Demand #'.$orderidwithprefix.' - Additional documents Required';
        $email_header = email_template_header($subject);
        $email_footer = email_template_footer();
        $mail_details = $email_header;
        $mail_details .= $msg . "<br/>";
        $mail_details .= '<p>Demand: #' . $orderidwithprefix . "<br/>";
        $mail_details .= $email_footer;
        $sender =  get_option( 'woocommerce_email_from_name' );
        $sender_email = get_option( 'woocommerce_email_from_address' );
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . $sender . ' < ' . $sender_email . '>' . "\r\n";
        $headers[] = 'Reply-To:' . $sender . ' < ' . $sender_email . '>' . "\r\n";
        @wp_mail($customeremail, $subject, $mail_details, $headers);

        include_once( 'apis/class-smsgateway.php' );
        
        if($mobile){
            $response = SMSGateWay::send_sms( $mobile, $sms, 29 );
            $otp_send = strpos( $response, 'Message submitted successfully' ) !== false  ? true : false; 
        }
    }
    
    echo $status;
    die();
}




add_action('init','alter_woo_hooks');
function alter_woo_hooks() {
    $WC_Form_Handler =  new WC_Form_Handler();
    $UR_Form_Handler = new UR_Form_Handler();
    remove_action( 'wp_loaded', array( $WC_Form_Handler, 'process_login' ), 20 );
    remove_action( 'wp_loaded', array( $UR_Form_Handler, 'process_login' ), 20 );
    remove_action( 'template_redirect', array( $WC_Form_Handler, 'save_account_details' ) );
}
add_action( 'wp_loaded',  'vi_process_login' , 10 );  
add_action( 'template_redirect',  'vi_save_account_details' , 20 );

function vi_process_login(){

    // Custom error messages.
        $messages = array(
            'username_is_required' => get_option( 'user_registration_message_username_required', __( 'Username is required.', 'user-registration' ) ),
            'empty_password'       => get_option( 'user_registration_message_empty_password', null ),
            'invalid_username'     => get_option( 'user_registration_message_invalid_username', null ),
            'unknown_email'        => get_option( 'user_registration_message_unknown_email', __( 'A user could not be found with this email address.', 'user-registration' ) ),
            'pending_approval'     => get_option( 'user_registration_message_pending_approval', null ),
            'denied_access'        => get_option( 'user_registration_message_denied_account', null ),
        );

        $nonce_value     = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
        $nonce_value     = isset( $_POST['user-registration-login-nonce'] ) ? $_POST['user-registration-login-nonce'] : $nonce_value;
        $recaptcha_value = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : '';

        $recaptcha_enabled = get_option( 'user_registration_login_options_enable_recaptcha', 'no' );
        $recaptcha_version = get_option( 'user_registration_integration_setting_recaptcha_version' );
        $secret_key        = 'v3' === $recaptcha_version ? get_option( 'user_registration_integration_setting_recaptcha_site_secret_v3' ) : get_option( 'user_registration_integration_setting_recaptcha_site_secret' );

        if ( ! empty( $_POST['login'] ) && wp_verify_nonce( $nonce_value, 'user-registration-login' ) ) {

            if(!empty($_POST['password'])){
                $key="01234567890123456789012345678901"; // 32 bytes
                $vector="1234567890123412"; // 16 bytes         
                $decrypted = myDecrypt($_POST['password'], $key, $vector);       
                $decrypted1= explode(':',$decrypted);	
               //echo $decrypted1[0];				
                
                //$_POST['password'] = $decrypted;
				$salt = "ipDaloveyBuohgGTZwcodeRJ1avofZ7HbZjzJbanDS8gtoninjaYj48CW";
				$storedPassword = $decrypted1[1];
				$saltedPostedPassword = $salt;
			 
				// instantiate PasswordHash to check if it is a valid password
				$hasher = new PasswordHash(8,false);
				$check = $hasher->CheckPassword($saltedPostedPassword, $storedPassword);
				if($check){
					$_POST['password'] = $decrypted1[0];
				}
            }

            try {
                $creds = array(
                    'user_password' => $_POST['password'],
                    'remember'      => isset( $_POST['rememberme'] ),
                );
            //print_r($creds);
                $username         = trim( $_POST['username'] );
                $validation_error = new WP_Error();
                $validation_error = apply_filters( 'user_registration_process_login_errors', $validation_error, $_POST['username'], $_POST['password'] );

                if ( 'yes' == $recaptcha_enabled || '1' == $recaptcha_enabled ) {
                    if ( ! empty( $recaptcha_value ) ) {

                        $data = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $recaptcha_value );
                        $data = json_decode( wp_remote_retrieve_body( $data ) );

                        if ( empty( $data->success ) || ( isset( $data->score ) && $data->score < apply_filters( 'user_registration_recaptcha_v3_threshold', 0.5 ) ) ) {
                            throw new Exception( '<strong>' . __( 'ERROR:', 'user-registration' ) . '</strong>' . __( 'Error on google reCaptcha. Contact your site administrator.', 'user-registration' ) );
                        }
                    } else {
                        throw new Exception( '<strong>' . __( 'ERROR:', 'user-registration' ) . '</strong>' . get_option( 'user_registration_form_submission_error_message_recaptcha', __( 'Captcha code error, please try again.', 'user-registration' ) ) );
                    }
                }

                if ( $validation_error->get_error_code() ) {
                    throw new Exception( '<strong>' . __( 'ERROR:', 'user-registration' ) . '</strong>' . $validation_error->get_error_message() );
                }

                if ( empty( $username ) ) {
                    throw new Exception( '<strong>' . __( 'ERROR:', 'user-registration' ) . '</strong>' . $messages['username_is_required'] );
                }

                if ( is_email( $username ) && apply_filters( 'user_registration_get_username_from_email', true ) ) {
                    $user = get_user_by( 'email', $username );

                    if ( isset( $user->user_login ) ) {
                        $creds['user_login'] = $user->user_login;
                    } else {
                        throw new Exception( '<strong>' . __( 'ERROR:', 'user-registration' ) . '</strong>' . $messages['unknown_email'] );
                    }
                } else {
                    $creds['user_login'] = $username;
                }

                // On multisite, ensure user exists on current site, if not add them before allowing login.
                if ( is_multisite() ) {
                    $user_data = get_user_by( 'login', $username );

                    if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
                        add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
                    }
                }

                // Perform the login
                $user = wp_signon( apply_filters( 'user_registration_login_credentials', $creds ), is_ssl() );

                if ( is_wp_error( $user ) ) {
                    // Set custom error messages.
                    if ( ! empty( $user->errors['empty_password'] ) && ! empty( $messages['empty_password'] ) ) {
                        $user->errors['empty_password'][0] = sprintf( '<strong>%s:</strong> %s', __( 'ERROR', 'user-registration' ), $messages['empty_password'] );
                    }
                    if ( ! empty( $user->errors['invalid_username'] ) && ! empty( $messages['invalid_username'] ) ) {
                        $user->errors['invalid_username'][0] = $messages['invalid_username'];
                    }
                    if ( ! empty( $user->errors['pending_approval'] ) && ! empty( $messages['pending_approval'] ) ) {
                        $user->errors['pending_approval'][0] = sprintf( '<strong>%s:</strong> %s', __( 'ERROR', 'user-registration' ), $messages['pending_approval'] );
                    }
                    if ( ! empty( $user->errors['denied_access'] ) && ! empty( $messages['denied_access'] ) ) {
                        $user->errors['denied_access'][0] = sprintf( '<strong>%s:</strong> %s', __( 'ERROR', 'user-registration' ), $messages['denied_access'] );
                    }

                    $message = $user->get_error_message();
                    $message = str_replace( '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', '<strong>' . esc_html( $username ) . '</strong>', $message );
                    throw new Exception( $message );
                } else {
                    if ( in_array( 'administrator', $user->roles ) && 'yes' === get_option( 'user_registration_login_options_prevent_core_login', 'no' ) ) {
                        $redirect = admin_url();
                    } else {
                        if ( ! empty( $_POST['redirect'] ) ) {
                            $redirect = $_POST['redirect'];
                        } elseif ( wp_get_raw_referer() ) {
                            $redirect = wp_get_raw_referer();
                        } else {
                            $redirect = get_home_url();
                        }
                    }

                    wp_redirect( wp_validate_redirect( apply_filters( 'user_registration_login_redirect', $redirect, $user ), $redirect ) );
                    exit;
                }
            } catch ( Exception $e ) {
                //ur_add_notice( apply_filters( 'login_errors', $e->getMessage() ), 'error' );
                do_action( 'user_registration_login_failed' );
            }
        }
}

function vi_save_account_details(){

    $nonce_value = wc_get_var( $_REQUEST['save-account-details-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

        if ( ! wp_verify_nonce( $nonce_value, 'save_account_details' ) ) {
            return;
        }

        if ( empty( $_POST['action'] ) || 'save_account_details' !== $_POST['action'] ) {
            return;
        }

        wc_nocache_headers();

        $user_id = get_current_user_id();

        if ( $user_id <= 0 ) {
            return;
        }


                

        $account_first_name   = ! empty( $_POST['account_first_name'] ) ? wc_clean( wp_unslash( $_POST['account_first_name'] ) ) : '';
        $account_last_name    = ! empty( $_POST['account_last_name'] ) ? wc_clean( wp_unslash( $_POST['account_last_name'] ) ) : '';
        $account_display_name = ! empty( $_POST['account_display_name'] ) ? wc_clean( wp_unslash( $_POST['account_display_name'] ) ) : '';
        $account_email        = ! empty( $_POST['account_email'] ) ? wc_clean( wp_unslash( $_POST['account_email'] ) ) : '';
        $pass_cur             = ! empty( $_POST['password_current'] ) ? $_POST['password_current'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $pass1                = ! empty( $_POST['password_1'] ) ? $_POST['password_1'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $pass2                = ! empty( $_POST['password_2'] ) ? $_POST['password_2'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $save_pass            = true;

        if(!empty($_POST['password_current'])){
			
			$key="01234567890123456789012345678901"; // 32 bytes
			$vector="1234567890123412"; // 16 bytes         
		    $decrypted = myDecrypt($_POST['password_current'], $key, $vector);      
			$decrypted1= explode(':',$decrypted);	
			
			
			
			$salt = "ipDaloveyBuohgGTZwcodeRJ1avofZ7HbZjzJbanDS8gtoninjaYj48CW";
			$storedPassword = $decrypted1[1];
			$saltedPostedPassword = $salt;
		 
			// instantiate PasswordHash to check if it is a valid password
			$hasher = new PasswordHash(8,false);
			$check = $hasher->CheckPassword($saltedPostedPassword, $storedPassword);
			if($check){
				$_POST['password_current']=$decrypted1[0];
				$pass_cur = $decrypted1[0];
				
			}
            
        }

        if(!empty($_POST['password_1'])){
			
			$key="01234567890123456789012345678901"; // 32 bytes
			$vector="1234567890123412"; // 16 bytes         
		    $decrypted = myDecrypt($_POST['password_1'], $key, $vector);      
			$decrypted1= explode(':',$decrypted);	
			
			
			$salt = "ipDaloveyBuohgGTZwcodeRJ1avofZ7HbZjzJbanDS8gtoninjaYj48CW";
			$storedPassword = $decrypted1[1];
			$saltedPostedPassword = $salt;
		 
			// instantiate PasswordHash to check if it is a valid password
			$hasher = new PasswordHash(8,false);
			$check = $hasher->CheckPassword($saltedPostedPassword, $storedPassword);
			if($check){
				$_POST['password_1']=$decrypted1[0];
				$pass1 = $decrypted1[0];
				
			}
        }   

        if(!empty($_POST['password_2'])){
			
			$key="01234567890123456789012345678901"; // 32 bytes
			$vector="1234567890123412"; // 16 bytes         
		    $decrypted = myDecrypt($_POST['password_2'], $key, $vector);      
			$decrypted1= explode(':',$decrypted);	
			
			
            $salt = "ipDaloveyBuohgGTZwcodeRJ1avofZ7HbZjzJbanDS8gtoninjaYj48CW";
			$storedPassword = $decrypted1[1];
			$saltedPostedPassword = $salt;
		 
			// instantiate PasswordHash to check if it is a valid password
			$hasher = new PasswordHash(8,false);
			$check = $hasher->CheckPassword($saltedPostedPassword, $storedPassword);
			if($check){
				$_POST['password_2']=$decrypted1[0];
			     $pass2 = $decrypted1[0];
				
			}
        }

        // Current user data.
        $current_user       = get_user_by( 'id', $user_id );
        $current_first_name = $current_user->first_name;
        $current_last_name  = $current_user->last_name;
        $current_email      = $current_user->user_email;

        // New user data.
        $user               = new stdClass();
        $user->ID           = $user_id;
        //$user->first_name   = $account_first_name;
        //$user->last_name    = $account_last_name;
        //$user->display_name = $account_display_name;

        // Prevent display name to be changed to email.
        if ( is_email( $account_display_name ) ) {
            $notices = WC()->session->get( 'wc_notices' ); // Get Woocommerce notices from session
                // if any validation errors
                if( $notices && isset( $notices['error'] ) ) {
                    // remove all of them
                    WC()->session->__unset( 'wc_notices' );
                    // Add one custom one instead                   
                }
            wc_add_notice( __( 'Display name cannot be changed to email address due to privacy concern.', 'woocommerce' ), 'error' );
        }

        // Handle required fields.
        $required_fields = apply_filters(
            'woocommerce_save_account_details_required_fields',
            array(
                'account_first_name'   => __( 'First name', 'woocommerce' ),
                'account_last_name'    => __( 'Last name', 'woocommerce' ),
                'account_display_name' => __( 'Display name', 'woocommerce' ),
                'account_email'        => __( 'Email address', 'woocommerce' ),
            )
        );

        foreach ( $required_fields as $field_key => $field_name ) {
            if ( empty( $_POST[ $field_key ] ) ) {
                $notices = WC()->session->get( 'wc_notices' ); // Get Woocommerce notices from session
                // if any validation errors
                if( $notices && isset( $notices['error'] ) ) {
                    // remove all of them
                    WC()->session->__unset( 'wc_notices' );
                    // Add one custom one instead                   
                }
                /* translators: %s: Field name. */
                wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), '<strong>' . esc_html( $field_name ) . '</strong>' ), 'error', array( 'id' => $field_key ) );
            }
        }

        if ( $account_email ) {
            $account_email = sanitize_email( $account_email );
            if ( ! is_email( $account_email ) ) {
                $notices = WC()->session->get( 'wc_notices' ); // Get Woocommerce notices from session
                // if any validation errors
                if( $notices && isset( $notices['error'] ) ) {
                    // remove all of them
                    WC()->session->__unset( 'wc_notices' );
                    // Add one custom one instead                   
                }
                wc_add_notice( __( 'Please provide a valid email address.', 'woocommerce' ), 'error' );
            } elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
                $notices = WC()->session->get( 'wc_notices' ); // Get Woocommerce notices from session
                // if any validation errors
                if( $notices && isset( $notices['error'] ) ) {
                    // remove all of them
                    WC()->session->__unset( 'wc_notices' );
                    // Add one custom one instead                   
                }
                wc_add_notice( __( 'This email address is already registered.', 'woocommerce' ), 'error' );
            }
            $user->user_email = $account_email;
        }

        if ( ! empty( $pass_cur ) && empty( $pass1 ) && empty( $pass2 ) ) {
            $notices = WC()->session->get( 'wc_notices' ); // Get Woocommerce notices from session
                // if any validation errors
                if( $notices && isset( $notices['error'] ) ) {
                    // remove all of them
                    WC()->session->__unset( 'wc_notices' );
                    // Add one custom one instead                   
                }
            wc_add_notice( __( 'Please fill out all password fields.', 'woocommerce' ), 'error' );
            $save_pass = false;
        } elseif ( ! empty( $pass1 ) && empty( $pass_cur ) ) {
            $notices = WC()->session->get( 'wc_notices' ); // Get Woocommerce notices from session
                // if any validation errors
                if( $notices && isset( $notices['error'] ) ) {
                    // remove all of them
                    WC()->session->__unset( 'wc_notices' );
                    // Add one custom one instead                   
                }
            wc_add_notice( __( 'Please enter your current password.', 'woocommerce' ), 'error' );
            $save_pass = false;
        } elseif ( ! empty( $pass1 ) && empty( $pass2 ) ) {
            $notices = WC()->session->get( 'wc_notices' ); // Get Woocommerce notices from session
                // if any validation errors
                if( $notices && isset( $notices['error'] ) ) {
                    // remove all of them
                    WC()->session->__unset( 'wc_notices' );
                    // Add one custom one instead                   
                }
            wc_add_notice( __( 'Please re-enter your password.', 'woocommerce' ), 'error' );
            $save_pass = false;
        } elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
            $notices = WC()->session->get( 'wc_notices' ); // Get Woocommerce notices from session
                // if any validation errors
                if( $notices && isset( $notices['error'] ) ) {
                    // remove all of them
                    WC()->session->__unset( 'wc_notices' );
                    // Add one custom one instead                   
                }
            wc_add_notice( __( 'New passwords do not match.', 'woocommerce' ), 'error' );
            $save_pass = false;
        } elseif ( ! empty( $pass1 ) && ! wp_check_password( $pass_cur, $current_user->user_pass, $current_user->ID ) ) {
            $notices = WC()->session->get( 'wc_notices' ); // Get Woocommerce notices from session
            // if any validation errors
            if( $notices && isset( $notices['error'] ) ) {
                // remove all of them
                WC()->session->__unset( 'wc_notices' );
                // Add one custom one instead                   
            }           
            wc_add_notice( __( 'Your current password is incorrect.', 'woocommerce' ), 'error' );
            $save_pass = false;
        }elseif(isset($pass_cur) && isset($pass1)){
            $useroldpasswords = get_user_meta(get_current_user_id(), 'useroldpasswords', true);

            $current_user = wp_get_current_user();
            if($pass1 ==  $current_user->user_login){
                $notices = WC()->session->get( 'wc_notices' ); // Get Woocommerce notices from session
                // if any validation errors
                if( $notices && isset( $notices['error'] ) ) {
                    // remove all of them
                    WC()->session->__unset( 'wc_notices' );
                    // Add one custom one instead                   
                }
                wc_add_notice( __( 'The password should not be same as the user ID. ', 'woocommerce' ),'error');
                $save_pass = false;
            }
        
            if($pass_cur ==  $pass1){
                $notices = WC()->session->get( 'wc_notices' ); // Get Woocommerce notices from session
                // if any validation errors
                if( $notices && isset( $notices['error'] ) ) {
                    // remove all of them
                    WC()->session->__unset( 'wc_notices' );
                    // Add one custom one instead                   
                }
                wc_add_notice( __( 'New password should not be equal to current password', 'woocommerce' ),'error');
                $save_pass = false;
            }
            
            if(!empty($useroldpasswords) && in_array($pass1, $useroldpasswords)){
                $notices = WC()->session->get( 'wc_notices' ); // Get Woocommerce notices from session
                // if any validation errors
                if( $notices && isset( $notices['error'] ) ) {
                    // remove all of them
                    WC()->session->__unset( 'wc_notices' );
                    // Add one custom one instead                   
                }
                wc_add_notice( __( 'The password should be significantly different from previous passwords.', 'woocommerce' ),'error');
                $save_pass = false;
            }        
        }

        if ( $pass1 && $save_pass ) {
            $user->user_pass = $pass1;
        }

        // Allow plugins to return their own errors.
        $errors = new WP_Error();
        do_action_ref_array( 'woocommerce_save_account_details_errors', array( &$errors, &$user ) );

        if ( $errors->get_error_messages() ) {
            
            foreach ( $errors->get_error_messages() as $error ) {
                wc_add_notice( $error, 'error' );
            }
        }

        if ( $save_pass ) {
            wp_update_user( $user );

            // Update customer object to keep data in sync.
            $customer = new WC_Customer( $user->ID );

            if ( $customer ) {
                // Keep billing data in sync if data changed.
                if ( is_email( $user->user_email ) && $current_email !== $user->user_email ) {
                    $customer->set_billing_email( $user->user_email );
                }

               /* if ( $current_first_name !== $user->first_name ) {
                    $customer->set_billing_first_name( $user->first_name );
                }

                if ( $current_last_name !== $user->last_name ) {
                    $customer->set_billing_last_name( $user->last_name );
                }*/

                $customer->save();
            }

            $notices = WC()->session->get( 'wc_notices' ); // Get Woocommerce notices from session

            // if any validation errors
            if( $notices && isset( $notices['error'] ) ) {

                // remove all of them
                WC()->session->__unset( 'wc_notices' );

                // Add one custom one instead
               
            }

            wc_add_notice( __( 'Account details changed successfully.', 'woocommerce' ) );

            do_action( 'woocommerce_save_account_details', $user->ID );

            wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
            exit;
        }
}


add_action( 'wp_enqueue_scripts', 'vi_enque_scripts' );

function vi_enque_scripts(){
    wp_enqueue_script( 'crypto-js', get_theme_file_uri( '/assets/js/crypto-js.min.js' ), array(), '20181214', true );
}

function myCrypt($value, $key, $iv){
    $encrypted_data = openssl_encrypt($value, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($encrypted_data);
}
function myDecrypt($value, $key, $iv){
    $value = base64_decode($value);
    $data = openssl_decrypt($value, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    if(empty($data)){
        $data = rand();
    }
    return $data;
}
