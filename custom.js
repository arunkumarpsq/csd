jQuery('.single_add_to_cart_button').hide();
jQuery('.dropchange').on('change', function(e){
    e.preventDefault();
    var id = jQuery(this).attr('id');
    var currentpostid = jQuery('#currentpostid').val();
    var depotselected = jQuery('#depot').val();
	var urcselected = jQuery('#urc').val();
	var dealerselected = jQuery('#dealer').val();
    jQuery('.msgarea').hide();
    if(id == 'depot'){
    	jQuery('#urc').html('<option value="" selected="selected">Select URC</option>');
    	//jQuery('#dealer').html('<option value="" selected="selected">Select Dealer</option>');
    	jQuery('.depotpricearea').remove();
    	//if(depotselected != ''){
    		jQuery('.loadersectionurc').show();
	    	jQuery.ajax({
		        data: 'action=change_depot_block&depotselected='+depotselected+'&currentpostid='+currentpostid,
		        type: 'POST',
		        url:woocommerce_params.ajax_url,
		        success: function(data) { //alert(data);
		            if(data){
		                //jQuery('.alterdepot').html(data);
		                jQuery('#urc').html(data);
		            } else {
		                jQuery('#errormsg2').show("slow").delay(1000).hide("slow");
		            }
		            jQuery('.loadersectionurc').hide();
		        }
		    });
	    //}
    }
});
jQuery('#checkavailability').click(function(e){
	e.preventDefault();
	var depot = jQuery('#depot').val();
	var urc = jQuery('#urc').val();
	var dealer = jQuery('#dealer').val();
	var color_id = jQuery('#color_id').val();
	var checkcoloravailable = jQuery('#coloravailable').val();
	var currentpostid = jQuery('#currentpostid').val();
	var custompprice = jQuery('#custompprice').val();
	if(checkcoloravailable){
		checkcoloravailable = color_id;
	} else {
		checkcoloravailable = true;
	}
	var state = jQuery( "#state option:selected" ).text();//jQuery('#state').val();
	jQuery('.msgarea').hide();
	if(jQuery('form.cart .out-of-stock').length){
		var outofstock = jQuery('form.cart .out-of-stock').html();
		alert(outofstock);
	} else if(state && depot && urc && dealer && checkcoloravailable){
		var popupshow = jQuery('#hiddenpopuppro').html();
		if(popupshow){
			popup_message(popupshow);
			setTimeout(function(){
				custom_start_product_submit_ajax(depot, urc, dealer, color_id, state, currentpostid, custompprice);
			}, 800);
		} else {
			custom_start_product_submit_ajax(depot, urc, dealer, color_id, state, currentpostid, custompprice);
		}

		


	} else {
		var msgtext = '';
		if(!checkcoloravailable){
			if(!checkcoloravailable && state && dealer && depot && urc){
				msgtext += 'Color ';
			} else {
				msgtext += 'Color, ';
			}			
		} if(!state){
			msgtext += 'State, ';
		} if(!dealer){
			msgtext += 'Dealer, ';
		} if(!depot){
			msgtext += 'Depot, ';
		}  if(!urc){
			msgtext += 'URC ';
		}
		var text1 = 'fields are';
		if(checkcoloravailable && state && dealer && depot && !urc){
			text1 = 'field is';
		} if(!checkcoloravailable && state && dealer && depot && urc){
			text1 = 'field is';
		}
		var formhtml = '<div class="modal-body">';
		formhtml += '<div class="form-group">';
		formhtml += msgtext+' '+text1+' required.';
		formhtml += '</div>';
		formhtml += '</div>';
		jQuery('#custompopuptitle').html('Missing Details');
		jQuery('#custompopupcontent').html(formhtml);
		jQuery('#custompopup').modal('show');
	}
});

function custom_start_product_submit_ajax(depot='', urc='', dealer='', color_id='', state='', currentpostid='', custompprice=''){
	jQuery.ajax({
        data: 'action=save_depot_urc_dealer_session&depot='+depot+'&urc='+urc+'&dealer='+dealer+'&color_id='+color_id+'&state='+state+'&currentpostid='+currentpostid+'&custompprice='+custompprice,
        type: 'POST',
        url:woocommerce_params.ajax_url,
        success: function(data) { //alert(data);
            if(data){
            	if(data=='dealer_not_approved' || data=='employ_status_check' || data=='ineligible' || data=='years' || data=='datecheck' ){
            		var pophead = 'Eligibility Validation';
            		var formhtml = '<div class="modal-body">';
					formhtml += '<div class="form-group">';
					if(data=='dealer_not_approved'){
						formhtml +=  'Dealer not available temporarily';
						pophead = 'Dealer Unavailable';
					} else if(data=='employ_status_check'){
						formhtml +=  'You are not authorized to buy this item as not meeting the Eligibility Criteria.<span style="color:red";> Please check and update the "Registration Form"(Through Menu->update Registration Form) before placing the demand.</span>';
						pophead = 'Entitlement Validation';
					} else if(data=='datecheck'){
						formhtml +=  jQuery('#datecheckmsg').val();
						pophead = 'Online AFD Portal not available currently';
					} else if(data=='years'){
						formhtml += ' You are not entitled to purchase a Car as you have not completed five years of Service.';
					} else {
						formhtml += ' You are not entitled to purchase the selected product.';
					}
					
					formhtml += '</div>';
					formhtml += '</div>';

					jQuery('#custompopuptitle').html(pophead);

					jQuery('#custompopupcontent').html(formhtml);
					jQuery('#custompopup').modal('show');

            	} else {
            		jQuery('.single_add_to_cart_button').trigger('click'); //alert('test');
            	}
            } else {
                jQuery('.errormsg2').show().delay(1000).fadeOut(300);
            }
        }
    });
}

jQuery('.verifydocuments').on('click', function(e){
	e.preventDefault();
	var orderid = jQuery(this).attr('data-id');
	popup_message('Are you sure to Verify?', "function_verify_current_order("+orderid+");");
});
function function_verify_current_order(orderid=''){
	jQuery('#custompopup').modal('hide');
	jQuery('.waitmsg').html('Please wait until it is completed. ');
	jQuery('.waitmsgmodal').modal('show');
	jQuery.ajax({
    data: 'action=urc_verify_documents&id='+orderid,
    type: 'POST',
    url:woocommerce_params.ajax_url,
	    success: function(data) { //alert(data);
	    	jQuery('.waitmsgmodal').modal('hide');
	        if(data){
	        	jQuery('<div class="woocommerce-button button checkverify ">Verified</div>').insertAfter('#verifydocuments'+orderid);
	        	jQuery('#verifydocuments'+orderid).hide();
	        	var orderpage = jQuery('#orderlistpage').val();
	        	setTimeout(function(){
	        		//window.location.href = orderpage;
	        		location.reload(true);
	        	}, 1500);
	        } else {
	            jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
	        }
	    }
	});
}
jQuery('.rejectbtnurc').on('click', function(e){
	e.preventDefault();
	var btntype = "'"+jQuery(this).attr('data-btntype')+"'";
	var orderid = jQuery(this).attr('data-id');
	popup_message('Are you sure to Reject?', "function_reject_order_by_urc("+orderid+", "+btntype+");");
});
function function_reject_order_by_urc(orderid='', btntypetext=''){
	var btntype = "'"+btntypetext+"'";
	var formhtml = '<form action="" method="post" id="formpaymentreject'+orderid+'">';
	formhtml += '<div class="modal-body">';
	formhtml += '<div class="form-group">';
	formhtml += '<label for="rejectremarks" class="col-form-label">Reason for rejection:</label>';
	formhtml += '<textarea class="form-control" name="rejectremarks" id="remarks'+orderid+'" placeholder="Reason for rejection" required></textarea>';
	formhtml += '</div>';
	formhtml += '</div>';
	formhtml += '<div class="modal-footer">';
	formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>';
	formhtml += '<button type="button" onclick="order_reject_by_urc('+orderid+', '+btntype+');" class="btn btn-primary rejectbtn" data-id="'+orderid+'">Reject</button>';
	formhtml += '</div>';
	formhtml += '<div class="formsubmitmsg"><span class="errormsg errormsg'+orderid+'" style="display:none;">Something went wrong</span>';
	formhtml += '<span class="errormsg remarkerror'+orderid+'" style="display:none;">Reason of rejection is required</span>';
	formhtml += '<span class="successmsg successmsg'+orderid+'" style="display:none;">Order is rejected</span></div>';
	formhtml += '</form>';
	jQuery('#custompopuptitle').html('Reject Order');
	jQuery('#custompopupcontent').html(formhtml);
	jQuery('#custompopup').modal('show');
}
function order_reject_by_urc(orderid='', type=''){
	var remarks = jQuery('#remarks'+orderid).val();
	var status = true;
	if(orderid == ''){
		jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
		status = false;
	} else if(remarks == '' || remarks == 'undefined'){
		jQuery('.remarkerror'+orderid).show().delay(1000).fadeOut(300);
		status = false;
	}
	if(status){
		jQuery.ajax({
		    data: 'action=urc_reject_order&id='+orderid+'&type=rejected&remarks='+remarks,
		    type: 'POST',
		    url:woocommerce_params.ajax_url,
		    success: function(data) { //alert(data);
		        if(data){
		        	location.reload(true);
		        } else {
		            jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
		        }
		    }
		});
	}
}
jQuery('.rejectbtnlist').on('click', function(e){
	e.preventDefault();
	var btntype = "'"+jQuery(this).attr('data-btntype')+"'";
	var orderid = jQuery(this).attr('data-id');
	popup_message('Are you sure to Reject?', "function_reject_form_show("+orderid+", "+btntype+");");
});
function function_reject_form_show(orderid='', btntype=''){
	var btntype = "'rejected'";
	var formhtml = '<form action="" method="post" id="formpaymentreject'+orderid+'">';
	formhtml += '<div class="modal-body">';
	formhtml += '<div class="form-group">';
	formhtml += '<label for="rejectremarks" class="col-form-label">Reason for rejection:</label>';
	formhtml += '<textarea class="form-control" name="rejectremarks" id="remarks'+orderid+'" placeholder="Reason for rejection" required></textarea>';
	formhtml += '</div>';
	formhtml += '</div>';
	formhtml += '<div class="modal-footer">';
	formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>';
	formhtml += '<button type="button" onclick="order_approve_reject_ask('+orderid+', '+btntype+');" class="btn btn-primary rejectbtn" data-id="'+orderid+'">Reject</button>';
	formhtml += '</div>';
	formhtml += '<div class="formsubmitmsg"><span class="errormsg errormsg'+orderid+'" style="display:none;">Something went wrong</span>';
	formhtml += '<span class="errormsg remarkerror'+orderid+'" style="display:none;">Reason of rejection is required</span>';
	formhtml += '<span class="successmsg successmsg'+orderid+'" style="display:none;">Order is rejected</span></div>';
	formhtml += '</form>';
	jQuery('#custompopuptitle').html('Reject Order');
	jQuery('#custompopupcontent').html(formhtml);
	jQuery('#custompopup').modal('show');
}
jQuery('.askbtnlist').on('click', function(e){
	e.preventDefault();
	var btntype = "'"+jQuery(this).attr('data-btntype')+"'";
	var orderid = jQuery(this).attr('data-id');
	var formhtml = '<form action="" method="post" id="formpaymentask'+orderid+'">';
	formhtml += '<div class="modal-body">';
	formhtml += '<div class="form-group">';
	formhtml += '<label for="askremarks" class="col-form-label">Remarks:</label>';
	formhtml += '<textarea class="form-control" name="askremarks" id="remarks'+orderid+'" placeholder="Remarks" required></textarea>';
	formhtml += '</div>';
	formhtml += '</div>';
	formhtml += '<div class="modal-footer">';
	formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
	formhtml += '<button type="button" onclick="order_approve_reject_ask('+orderid+', '+btntype+');" class="btn btn-primary askbtn" data-id="'+orderid+'">Send</button>';	
	formhtml += '</div>';
	formhtml += '<div class="formsubmitmsg"><span class="errormsg errormsg'+orderid+'" style="display:none;">Something went wrong</span>';
	formhtml += '<span class="errormsg remarkerror'+orderid+'" style="display:none;">Remark is required</span>';
	formhtml += '<span class="successmsg successmsg'+orderid+'" style="display:none;">Clarification request sent</span></div>';
	formhtml += '</form>';
	jQuery('#custompopuptitle').html('Ask for Clarification');
	jQuery('#custompopupcontent').html(formhtml);
	jQuery('#custompopup').modal('show');
});
jQuery('.approvebtnlist').on('click', function(e){
	e.preventDefault();
	var btntype = "'"+jQuery(this).attr('data-btntype')+"'";
	var orderid = jQuery(this).attr('data-id');
	popup_message('Are you sure to approve?', "update_approve_or_reject("+orderid+", 'approved');");
});
function update_approve_or_reject(orderid='', type=''){
	jQuery('#custompopup').modal('hide');
		jQuery.ajax({
		    data: 'action=depot_approve_or_reject_order&id='+orderid+'&type='+type,
		    type: 'POST',
		    url:woocommerce_params.ajax_url,
		    success: function(data) { //alert(data);
		        if(data){
		        	if(type == 'approved'){
		        		jQuery('<div class="woocommerce-button button btn btn-primary btn-small">Approved</div>').insertAfter('#depotdecisn'+orderid);
		        	} else if(type == 'rejected'){
		        		jQuery('<div class="woocommerce-button button btn btn-danger btn-small">Rejected</div>').insertAfter('#depotdecisn'+orderid);
		        	} else if(type == 'ask'){
		        		jQuery('<div class="woocommerce-button button btn btn-info btn-small">Clarification sent</div>').insertAfter('#depotdecisn'+orderid);
		        	} 
		        	jQuery('.successmsg'+orderid).show().delay(1000).fadeOut(300);
		        	var orderpage = jQuery('#orderlistpage').val();
		        	setTimeout(function(){
		        		//window.location.href = orderpage;
		        		location.reload(true);
		        	}, 1500);
		        	jQuery('#depotdecisn'+orderid).hide();
		        } else {
		            jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
		        }
		    }
		});
}
function order_approve_reject_ask(orderid='', type=''){
	var payment_details = jQuery('#payment_details'+orderid).val();
	var remarks = jQuery('#remarks'+orderid).val();
	var status = true;
	if(orderid == ''){
		jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
		status = false;
	} else if((payment_details == '' || payment_details == 'undefined') && type == 'approved'){
		jQuery('.errormsgpay'+orderid).show().delay(2500).fadeOut(300);
		status = false;
	} else if((type == 'ask' || type == 'rejected') && (remarks == '' || remarks == 'undefined')){
		jQuery('.remarkerror'+orderid).show().delay(1000).fadeOut(300);
		status = false;
	}
	if(status){
		jQuery.ajax({
		    data: 'action=depot_approve_or_reject_order&id='+orderid+'&type='+type+'&paymentdetails='+payment_details+'&remarks='+remarks,
		    type: 'POST',
		    url:woocommerce_params.ajax_url,
		    success: function(data) { //alert(data);
		        if(data){
		        	if(type == 'approved'){
		        		jQuery('<div class="woocommerce-button button ">Approved</div>').insertAfter('#depotdecisn'+orderid);
		        	} else if(type == 'rejected'){
		        		jQuery('<div class="woocommerce-button button ">Rejected</div>').insertAfter('#depotdecisn'+orderid);
		        	} else if(type == 'ask'){
		        		jQuery('<div class="woocommerce-button button ">Clarification sent</div>').insertAfter('#depotdecisn'+orderid);
		        	} 
		        	jQuery('.successmsg'+orderid).show().delay(1000).fadeOut(300);
		        	var orderpage = jQuery('#orderlistpage').val();
		        	setTimeout(function(){
		        		//window.location.href = orderpage;
		        		location.reload(true);
		        	}, 1500);
		        	jQuery('#depotdecisn'+orderid).hide();
		        } else {
		            jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
		        }
		    }
		});
	}
}

function changepayfile(orderid='', type=''){
	jQuery('#'+type+orderid).show();
	jQuery('#top'+type+orderid).hide();
	jQuery('#change'+type+orderid).val('');
}
jQuery('.upload_payment_receipt').on('click', function(e){
	e.preventDefault();
	var orderid = this.search.split('=')[1];
	if(orderid){
		var hiddpayment_receipt = jQuery('#hiddpayment_receipt'+orderid).val();
		var hiddpayment_receipt_loan = jQuery('#hiddpayment_receipt_loan'+orderid).val();
		var hiddpayment_receipturl = jQuery('#hiddpayment_receipt'+orderid).attr('data-url');
		var hiddpayment_receipt_loanurl = jQuery('#hiddpayment_receipt_loan'+orderid).attr('data-url');
		if(hiddpayment_receipt == undefined) hiddpayment_receipt = '';
		if(hiddpayment_receipt_loan == undefined) hiddpayment_receipt_loan = '';

		var style1 = '';
		var style2 = '';

		var formhtml = '<form action="" method="post" id="formpaymentreceipt'+orderid+'" enctype="multipart/form-data">';
		formhtml += '<div class="modal-body">';
		formhtml += '<div class="form-group">';
		formhtml += '<label for="paymentreceipt-name" class="col-form-label">Payment by beneficiary account:</label>';
		if(hiddpayment_receipt){
			var type = "'"+'paymentreceipt'+"'";
			formhtml += '<div class="changefile changefileofficial" id="toppaymentreceipt'+orderid+'">';
			formhtml += '<span class="changefile-group">';
			formhtml += '<span class="changefile-title">';
			formhtml += '<span title="check.pdf" class="title"><a href="'+hiddpayment_receipturl+'">'+hiddpayment_receipt+'</a></span>';
			formhtml += '</span>';
			formhtml += '<span class="changefile-actions">';
			formhtml += '<a href="#" onclick="changepayfile('+orderid+', '+type+'); return false;" class="thwcfe-action-btn thwcfe-remove-uploaded" title="Remove">X</a>';
			formhtml += '</span>';
			formhtml += '</span>';
			formhtml += '</div>';

			style1 = 'style="display:none;"';
		}
		formhtml += '<input type="file" '+style1+' class="form-control payreceipthidden" name="paymentreceipt'+orderid+'" id="paymentreceipt'+orderid+'" required>';
		formhtml += '<input type="hidden" id="changepaymentreceipt'+orderid+'" value="'+hiddpayment_receipt+'" >';
		
		formhtml += '</div>';
		formhtml += '<div class="form-group">';
		formhtml += '<label for="paymentreceiptloan-name" class="col-form-label">Payment by Loan (optional):</label>';
		if(hiddpayment_receipt_loan){
			var type = "'"+'paymentreceiptloan'+"'";
			formhtml += '<div class="changefile changefileofficial" id="toppaymentreceiptloan'+orderid+'">';
			formhtml += '<span class="changefile-group">';
			formhtml += '<span class="changefile-title">';
			formhtml += '<span title="check.pdf" class="title"><a href="'+hiddpayment_receipt_loanurl+'">'+hiddpayment_receipt_loan+'</a></span>';
			formhtml += '</span>';
			formhtml += '<span class="changefile-actions">';
			formhtml += '<a href="#" onclick="changepayfile('+orderid+', '+type+'); return false;" class="thwcfe-action-btn thwcfe-remove-uploaded" title="Remove">X</a>';
			formhtml += '</span>';
			formhtml += '</span>';
			formhtml += '</div>';
			style2 = 'style="display:none;"';
		}
		formhtml += '<input type="file" '+style2+' class="form-control payreceiptloanhidden" name="paymentreceiptloan'+orderid+'" id="paymentreceiptloan'+orderid+'">';
		formhtml += '<input type="hidden" id="changepaymentreceiptloan'+orderid+'" value="'+hiddpayment_receipt_loan+'" >';
		
		formhtml += '</div>';
		formhtml += '</div>';
		formhtml += '<div class="modal-footer">';
		formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
		formhtml += '<button type="button" onclick="receipt_upload('+orderid+');" class="btn btn-primary uploadbtn" data-id="'+orderid+'">Upload</button>';
		formhtml += '</div>';
		formhtml += '<div class="formsubmitmsg"><span class="errormsg errorpay'+orderid+'" style="display:none;">Please upload file to continue.</span>';
		formhtml += '<span class="errormsg payerrormsg'+orderid+'" style="display:none;">Something went wrong.</span>';
		formhtml += '<span class="errormsg errorfilesize'+orderid+'" style="display:none;">Max file size is 2MB.</span>';
		formhtml += '<span class="successmsg paysuccessmsg'+orderid+'" style="display:none;">Payment receipt is uploaded.</span></div>';		
		formhtml += '</form>';
		jQuery('#custompopuptitle').html('Upload Payment Receipt');
		jQuery('#custompopupcontent').html(formhtml);
		jQuery('#custompopup').modal('show');
	}
});
function receipt_upload(orderid=''){
	var paymentreceipt = jQuery('#paymentreceipt'+orderid).prop('files')[0];
	var receipt = jQuery('#changepaymentreceipt'+orderid).val();
	var loanreceipt = jQuery('#changepaymentreceiptloan'+orderid).val();
	if(orderid == ''){
		jQuery('.payerrormsg'+orderid).show().delay(1000).fadeOut(300);
	} else if(receipt == '' && (!paymentreceipt || paymentreceipt == '' || paymentreceipt == 'undefined')){
		jQuery('.errorpay'+orderid).show().delay(2500).fadeOut(300);
	} else if(receipt != '' && loanreceipt != ''){
		jQuery('.errorpay'+orderid).show().delay(2500).fadeOut(300);
	} else {
		var fileinvalid = false;
		var form_data = new FormData();
		form_data.append('action', 'customer_upload_payment_receipt');
		if(!receipt){
			var file_data = jQuery('#paymentreceipt'+orderid).prop('files')[0];
	        form_data.append('file', file_data);
	        form_data.append('id', orderid);
	        if(file_data.size > 2000000){
	        	fileinvalid = ' Max file size is 2MB.';//true;
	        } else if(file_data.type != 'application/pdf'){
				fileinvalid = ' Only pdf files are allowed to upload.';
			} 
	    }
	    if(!loanreceipt){
	        if(jQuery('#paymentreceiptloan'+orderid).val()){
		        var loanfile_data = jQuery('#paymentreceiptloan'+orderid).prop('files')[0];
		        form_data.append('loanfile', loanfile_data);
		        if(loanfile_data.size > 2000000){
		        	fileinvalid = ' Max file size is 2MB.';
		        } else if(loanfile_data.type != 'application/pdf'){
					fileinvalid = ' Only pdf files are allowed to upload.';
				} 
		    }
		}
	    if(fileinvalid){
	    	jQuery('.errorfilesize'+orderid).html(fileinvalid);
	    	jQuery('.errorfilesize'+orderid).show().delay(2500).fadeOut(300);
	    } else {
	    	make_ajax_submit_benefiicary_payment_receipt(orderid);
	    }
	}
}
function make_ajax_submit_benefiicary_payment_receipt(orderid=''){
	var form_data = new FormData();
	form_data.append('action', 'customer_upload_payment_receipt');
	form_data.append('id', orderid);
	var upload = false;
	var receipt = jQuery('#changepaymentreceipt'+orderid).val();
	var loanreceipt = jQuery('#changepaymentreceiptloan'+orderid).val();
	if(!receipt){
		var file_data = jQuery('#paymentreceipt'+orderid).prop('files')[0];
	    form_data.append('file', file_data);
	    upload = true;
	}
    if(!loanreceipt){
	    if(jQuery('#paymentreceiptloan'+orderid).val()){
	        var loanfile_data = jQuery('#paymentreceiptloan'+orderid).prop('files')[0];
	        form_data.append('loanfile', loanfile_data);
	        upload = true;
	    }
	}
	if(upload){
		jQuery('.uploadbtn').addClass('linkdisabled');
		jQuery('#confirmmsgmodal').modal('hide');
		jQuery('#custompopup').modal('show');
		jQuery('.waitmsg').html('The payment receipt is uploading. ');
		jQuery('.waitmsgmodal').modal('show');
	    jQuery.ajax({
		    data: form_data,
		    type: 'POST',
		    contentType: false,
	        processData: false,
		    url:woocommerce_params.ajax_url,
		    success: function(data) { //alert(data);
		        if(data){
		        	jQuery('.paysuccessmsg'+orderid).show().delay(1000).fadeOut(300);
		        	check_if_both_payment_receipt_and_details_uploaded(orderid);
		        } else {
		            jQuery('.payerrormsg'+orderid).show().delay(1000).fadeOut(300);
		        }
		        jQuery('.waitmsgmodal').modal('hide');
		        jQuery('.uploadbtn').removeClass('linkdisabled');
		    }
		});
	} else {
		jQuery('.errorpay'+orderid).show().delay(2500).fadeOut(300);
	}
}
jQuery('.approvepayment').on('click', function(e){
	e.preventDefault();
	var orderid = jQuery(this).attr('data-id');
	popup_message('Are you sure to Approve?', "function_approvepayment_click("+orderid+");");
});
function function_approvepayment_click(orderid=''){
	jQuery.ajax({
	    data: 'action=depot_approvepayment_order&id='+orderid,
	    type: 'POST',
	    url:woocommerce_params.ajax_url,
	    success: function(data) { //alert(data);
	        if(data){
	        	jQuery('<div class="woocommerce-button button ">Payment Approved</div>').insertAfter('#approvepayment'+orderid);
	        	jQuery('#approvepayment'+orderid).hide();
	        	var orderpage = jQuery('#orderlistpage').val();
	        	setTimeout(function(){
	        		//window.location.href = orderpage;
	        		location.reload(true);
	        	}, 1500);
	        } else {
	            jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
	        }
	    }
	});
}
function refund_order(orderid='', actionby=''){
	if(orderid){
		var remarks = jQuery('#refundremarks'+orderid).val();
		if(remarks == '' || remarks == 'undefined'){
			jQuery('.remarkerror'+orderid).show().delay(1000).fadeOut(300);
			status = false;
		} else {
			jQuery.ajax({
			    data: 'action=refund_current_order&id='+orderid+'&by='+actionby+'&remarks='+remarks,
			    type: 'POST',
			    url:woocommerce_params.ajax_url,
			    success: function(data) { //alert(data);
			        if(data){
			        	jQuery('<div class="woocommerce-button button ">Order is refunded.</div>').insertAfter('#refundorder'+orderid);
			        	jQuery('#refundorder'+orderid).hide();
			        	setTimeout(function(){
			        		location.reload(true);
			        	}, 1500);
			        } else {
			            jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
			        }
			    }
			});
		}
	}
}
jQuery('.refundorder').on('click', function(e){
	e.preventDefault();
	var orderid = jQuery(this).attr('data-id');
	var actionby =  "'"+jQuery(this).attr('data-by')+"'"; //jQuery(this).attr('data-by');
	popup_message('Are you sure to proceed?', "function_refund_current_order("+orderid+", "+actionby+");");
});
function function_refund_current_order(orderid='', actionbytext=''){
	var actionby =  "'"+actionbytext+"'";
	var formhtml = '<form action="" method="post" id="formpaymentrefund'+orderid+'">';
	formhtml += '<div class="modal-body">';
	formhtml += '<div class="form-group">';
	formhtml += '<label for="refundremarks" class="col-form-label">Reason for refund:</label>';
	formhtml += '<textarea class="form-control" name="refundremarks" id="refundremarks'+orderid+'" placeholder="Reason for refund" required></textarea>';
	formhtml += '</div>';
	formhtml += '</div>';
	formhtml += '<div class="modal-footer">';
	formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>';
	formhtml += '<button type="button" onclick="refund_order('+orderid+', '+actionby+');" class="btn btn-primary rejectbtn" data-id="'+orderid+'">Refund</button>';
	formhtml += '</div>';
	formhtml += '<div class="formsubmitmsg"><span class="errormsg errormsg'+orderid+'" style="display:none;">Something went wrong</span>';
	formhtml += '<span class="errormsg remarkerror'+orderid+'" style="display:none;">Reason of refund is required</span>';
	formhtml += '<span class="successmsg successmsg'+orderid+'" style="display:none;">Order is refunded</span></div>';
	formhtml += '</form>';
	jQuery('#custompopuptitle').html('Refund Order');
	jQuery('#custompopupcontent').html(formhtml);
	jQuery('#custompopup').modal('show');
}
jQuery('.view_payment_receipt').on('click', function(e){
	e.preventDefault();
	var url = jQuery(this).attr('href');
	window.open(url, '_blank');
});
jQuery('.view_supply_order').each(function(){
	jQuery(this).attr('download', true);
});
jQuery('.payment_recvd_depot').on('click', function(e){
	e.preventDefault();
	var orderid = jQuery(this).attr('data-id');
	if(orderid){
		var formhtml = '<form action="" method="post" id="formpaymentrecvd'+orderid+'" enctype="multipart/form-data">';
		formhtml += '<div class="modal-body">';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="or-number" class="col-form-label">OR number:*</label>';
		formhtml += '<input type="text" class="form-control" name="ornumber'+orderid+'" id="ornumber'+orderid+'" required>';
		formhtml += '</div>';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="totalamountreceived" class="col-form-label">Total Amount Received:*</label>';
		formhtml += '<input type="number" min="0" step="1" class="form-control numfield totalamountreceived" name="totalamountreceived'+orderid+'" id="totalamountreceived'+orderid+'" required>';
		formhtml += '</div>';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="excesspayment" class="col-form-label">Excess Payment collected (optional):</label>';
		formhtml += '<input type="number" min="0" step="1" class="form-control numfield excesspayment	" name="excesspayment'+orderid+'" id="excesspayment'+orderid+'" >';
		formhtml += '</div>';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="tcscollected" class="col-form-label">TCS collected (optional):</label>';
		formhtml += '<input type="number" min="0" step="1" class="form-control numfield excesspayment" name="tcscollected'+orderid+'" id="tcscollected'+orderid+'" >';
		formhtml += '</div>';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="ordate" class="col-form-label">OR Date:*</label>';
		formhtml += '<input type="text" class="form-control datefield" name="ordate'+orderid+'" id="ordate'+orderid+'" required>';
		formhtml += '</div>';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="bankref" class="col-form-label">CSD Bank Reference No (optional):</label>';
		formhtml += '<input type="text" class="form-control" name="bankref'+orderid+'" id="bankref'+orderid+'">';
		formhtml += '</div>';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="amountreceived" class="col-form-label">Amount Received (from Beneficiary Account) (optional):</label>';
		formhtml += '<input type="number" min="0" step="1" class="form-control numfield" name="amountreceived'+orderid+'" id="amountreceived'+orderid+'" >';
		formhtml += '</div>';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="receiptdate" class="col-form-label">Date of Payment Received (optional):</label>';
		formhtml += '<input type="text" class="form-control datefield paydate" name="receiptdate'+orderid+'" id="receiptdate'+orderid+'" >';
		formhtml += '</div>';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="bankrefloan" class="col-form-label">CSD Bank Reference No (Loan) optional:</label>';
		formhtml += '<input type="text" class="form-control" name="bankrefloan'+orderid+'" id="bankrefloan'+orderid+'" >';
		formhtml += '</div>';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="amountreceivedloan" class="col-form-label">Amount Received (Loan ) optional:</label>';
		formhtml += '<input type="number" min="0" step="1" class="form-control numfield" name="amountreceivedloan'+orderid+'" id="amountreceivedloan'+orderid+'" >';
		formhtml += '</div>';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="receiptdateloan" class="col-form-label">Date of Payment Received (Loan) optional:</label>';
		formhtml += '<input type="text" class="form-control datefield paydate" name="receiptdateloan'+orderid+'" id="receiptdateloan'+orderid+'" >';
		formhtml += '</div>';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="ponumber" class="col-form-label">PO Number:*</label>';
		formhtml += '<input type="text" class="form-control" name="ponumber'+orderid+'" id="ponumber'+orderid+'" required>';
		formhtml += '</div>';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="podate" class="col-form-label">PO Date:*</label>';
		formhtml += '<input type="text" class="form-control datefield" name="podate'+orderid+'" id="podate'+orderid+'" required>';
		formhtml += '</div>';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="podate" class="col-form-label">Remarks By Depot:</label>';
		formhtml += '<input type="text" class="form-control" name="remarks_by_depot'+orderid+'" id="remarks_by_depot'+orderid+'" >';
		formhtml += '</div>';

		formhtml += '</div>';
		formhtml += '<div class="modal-footer">';
		formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
		formhtml += '<button type="button" onclick="payment_recvd_submit('+orderid+');" class="btn btn-primary btnsubmit" data-id="'+orderid+'">Submit</button>';
		formhtml += '</div>';
		formhtml += '<div class="formsubmitmsg"><span class="errormsg errorpay'+orderid+'" style="display:none;">Please enter all required fields.</span>';
		formhtml += '<span class="errormsg payerrormsg'+orderid+'" style="display:none;">Something went wrong.</span>';
		formhtml += '<span class="errormsg errortotalpay'+orderid+'" style="display:none;">Total amount received is less than the price of the item. Payment received by CSD can not be uploaded.</span>';
		formhtml += '<span class="successmsg paysuccessmsg'+orderid+'" style="display:none;">All details are saved.</span></div>';
		formhtml += '</form>';
		formhtml += '<script>jQuery( ".datefield" ).datepicker({format: "dd/mm/yy",endDate: "today",orientation: "top",autoclose: true}); ';
		formhtml += 'jQuery(".numfield").attr("onkeypress", "return isNumberKey(event)");</script>';
		jQuery('#custompopuptitle').html('Add payment Details');
		jQuery('#custompopupcontent').html(formhtml);
		jQuery('#custompopup').modal('show');
	}
});
function payment_recvd_submit(orderid=''){
	var ornumber = jQuery('#ornumber'+orderid).val();
	var ordate = jQuery('#ordate'+orderid).val();
	var ponumber = jQuery('#ponumber'+orderid).val();
	var podate = jQuery('#podate'+orderid).val();
	var bankref = jQuery('#bankref'+orderid).val();
	var hiddensiteurl = jQuery('#hiddensiteurl').val();
	var amountreceived = jQuery('#amountreceived'+orderid).val();
	var receiptdate = jQuery('#receiptdate'+orderid).val();
	var bankrefloan = jQuery('#bankrefloan'+orderid).val();
	var amountreceivedloan = jQuery('#amountreceivedloan'+orderid).val();
	var receiptdateloan = jQuery('#receiptdateloan'+orderid).val();
	var totalamountreceived = jQuery('#totalamountreceived'+orderid).val();
	var excesspayment = jQuery('#excesspayment'+orderid).val();
	var tcscollected = jQuery('#tcscollected'+orderid).val();
	var remarks_by_depot = jQuery('#remarks_by_depot'+orderid).val();
	var hiddentotalorderamount = jQuery('#hiddentotalorderamount'+orderid).val();
	if(orderid == ''){
		jQuery('.payerrormsg'+orderid).show().delay(1000).fadeOut(300);
	} else if(!ornumber || !podate || !ordate || !ponumber || !totalamountreceived){
		jQuery('.errorpay'+orderid).show().delay(2500).fadeOut(300);
	} else {
		if(excesspayment == 'undefined' || excesspayment == ''){
			excesspayment = 0;
		}
		var checkdifference = parseInt(totalamountreceived)-parseInt(excesspayment);
		//if(checkdifference != hiddentotalorderamount){
		if(parseInt(totalamountreceived) < parseInt(hiddentotalorderamount)){
			jQuery('.errortotalpay'+orderid).show().delay(5000).fadeOut(300);
		} else {
			jQuery('.waitmsg').html('The payment details are saving. ');
			jQuery('.waitmsgmodal').modal('show');
			jQuery('.btnsubmit').addClass('linkdisabled');
	        var form_data = new FormData();
	        form_data.append('id', orderid);
	        form_data.append('ornumber', ornumber);
	        form_data.append('ordate', ordate);
	        form_data.append('ponumber', ponumber);
	        form_data.append('podate', podate);
	        form_data.append('bankref', bankref);
	        form_data.append('amountreceived', amountreceived);
	        form_data.append('receiptdate', receiptdate);
	        form_data.append('bankrefloan', bankrefloan);
	        form_data.append('amountreceivedloan', amountreceivedloan);
	        form_data.append('receiptdateloan', receiptdateloan);
	        form_data.append('totalamountreceived', totalamountreceived);
	        form_data.append('excesspayment', excesspayment);
	        form_data.append('tcscollected', tcscollected);
	        form_data.append('remarks_by_depot', remarks_by_depot);
	        form_data.append('action', 'save_payment_received_details_by_depot');
	        jQuery.ajax({
			    data: form_data,
			    type: 'POST',
			    contentType: false,
	            processData: false,
			    url:woocommerce_params.ajax_url,
			    success: function(data) { //alert(data);
			        if(data){
			        	jQuery('#formpaymentrecvd'+orderid)[0].reset();
			        	jQuery('.paysuccessmsg'+orderid).show();
			        	setTimeout(function(){
			        		//window.location.href = orderpage;
			        		location.reload(true);
			        	}, 1500);
			        } else {
			            jQuery('.payerrormsg'+orderid).show().delay(1000).fadeOut(300);
			        }
			        jQuery('.waitmsgmodal').modal('hide');
			        jQuery('.btnsubmit').removeClass('linkdisabled');
			    }
			});
	    }
	}
}
//Collect Payment Receipt
jQuery('.collect_payment_receipt').on('click', function(e){
	e.preventDefault();
	var orderid = this.search.split('=')[1];
	if(orderid){
		var hiddbank = jQuery('#hiddbank'+orderid).html();
		var hiddholder = jQuery('#hiddholder'+orderid).html();
		var hiddaccountnumber = jQuery('#hiddaccountnumber'+orderid).html();
		var hiddifsc = jQuery('#hiddifsc'+orderid).html();
		var hiddutr = jQuery('#hiddutr'+orderid).html();
		var hiddamountpaid = jQuery('#hiddamountpaid'+orderid).html();
		var hiddpaymentdate = jQuery('#hiddpaymentdate'+orderid).html();

		if(hiddbank == undefined) hiddbank = '';
		if(hiddholder == undefined) hiddholder = '';
		if(hiddaccountnumber == undefined) hiddaccountnumber = '';
		if(hiddifsc == undefined) hiddifsc = '';
		if(hiddutr == undefined) hiddutr = '';
		if(hiddamountpaid == undefined) hiddamountpaid = '';
		if(hiddpaymentdate == undefined) hiddpaymentdate = '';

		var hiddloanbank = jQuery('#hiddloanbank'+orderid).html();
		var hiddloanholder = jQuery('#hiddloanholder'+orderid).html();
		var hiddloanaccountnumber = jQuery('#hiddloanaccountnumber'+orderid).html();
		var hiddloanifsc = jQuery('#hiddloanifsc'+orderid).html();
		var hiddloanutr = jQuery('#hiddloanutr'+orderid).html();
		var hiddloanamountpaid = jQuery('#hiddloanamountpaid'+orderid).html();
		var hiddloanpaymentdate = jQuery('#hiddloanpaymentdate'+orderid).html();

		var hiddamount_deposited_in = jQuery('#hiddamount_deposited_in'+orderid).html();
		var hiddamount_deposited_in_loan = jQuery('#hiddamount_deposited_in_loan'+orderid).html();
		var hiddpayment_remark = jQuery('#hiddpayment_remark'+orderid).html();

		if(hiddamount_deposited_in == undefined) hiddamount_deposited_in = '';
		if(hiddamount_deposited_in_loan == undefined) hiddamount_deposited_in_loan = '';
		if(hiddpayment_remark == undefined) hiddpayment_remark = '';
		var selected1 = '';
		var selected2 = '';
		var selected3 = '';
		var selected4 = '';
		var selected5 = '';
		var selected6 = '';
		if(hiddamount_deposited_in == 'Depot Main account') selected1 = ' selected="selected"';
		if(hiddamount_deposited_in == 'CSD HO Main Account') selected2 = ' selected="selected"';
		if(hiddamount_deposited_in_loan == 'Depot Main account') selected3 = ' selected="selected"';
		if(hiddamount_deposited_in_loan == 'CSD HO Main Account') selected4 = ' selected="selected"';
		if(hiddamount_deposited_in == 'Virtual Account (Payment Gateway)') selected5 = ' selected="selected"';
		if(hiddamount_deposited_in_loan == 'Virtual Account (Payment Gateway)') selected6 = ' selected="selected"';
		
		
		if(hiddloanbank == undefined) hiddloanbank = '';
		if(hiddloanholder == undefined) hiddloanholder = '';
		if(hiddloanaccountnumber == undefined) hiddloanaccountnumber = '';
		if(hiddloanifsc == undefined) hiddloanifsc = '';
		if(hiddloanutr == undefined) hiddloanutr = '';
		if(hiddloanamountpaid == undefined) hiddloanamountpaid = '';
		if(hiddloanpaymentdate == undefined) hiddloanpaymentdate = '';

		var d = new Date();
		var strDate = d.getDate() + "/" + (d.getMonth()+1) + "/" + d.getFullYear();
		var formhtml = '<form action="" method="post" id="formcollect_payment_receipt'+orderid+'" enctype="multipart/form-data" autocomplete="off">';
		formhtml += '<div class="modal-body">';
		formhtml += '<div class="row">';
	        formhtml += '<div class="col-md-6">';
				formhtml += '<div class="form-group">';
				formhtml += '<h5 class="col-form-label  badge  badge-warning badgepaydetail"> Details of first payment to CSD </h5></br>';
				formhtml += '<label for="bank-name" class="col-form-label">Name of Bank:</label>';
				formhtml += '<input type="text" autocomplete="off" class="form-control" name="bank'+orderid+'" id="bank'+orderid+'" value="'+hiddbank+'" required>';
				formhtml += '</div>';
				formhtml += '<div class="form-group">';
				formhtml += '<label for="holder" class="col-form-label">Name of First Account Holder:</label>';
				formhtml += '<input type="text" autocomplete="off" class="form-control" name="holder'+orderid+'" id="holder'+orderid+'" value="'+hiddholder+'" required>';
				formhtml += '</div>';
				formhtml += '<div class="form-group">';
				formhtml += '<label for="accountnumber" class="col-form-label">Bank Account Number:</label>';
				formhtml += '<input type="text" autocomplete="off" class="form-control" name="accountnumber'+orderid+'" id="accountnumber'+orderid+'" value="'+hiddaccountnumber+'" required>';
				formhtml += '</div>';
				formhtml += '<div class="form-group">';
				formhtml += '<label for="ifsc" class="col-form-label">IFSC Code:</label>';
				formhtml += '<input type="text" autocomplete="off" class="form-control" name="ifsc'+orderid+'" id="ifsc'+orderid+'" value="'+hiddifsc+'" required>';
				formhtml += '</div>';
				formhtml += '<div class="form-group">';
				formhtml += '<label for="utr" class="col-form-label">UTR Number:</label>';
				formhtml += '<input type="text" autocomplete="off" class="form-control" name="utr'+orderid+'" id="utr'+orderid+'" value="'+hiddutr+'" required>';
				formhtml += '</div>';
				formhtml += '<div class="form-group">';
				formhtml += '<label for="amountpaid" class="col-form-label">Amount paid:</label>';
				formhtml += '<input type="number" autocomplete="off" min="0" step="1" class="form-control numfield" name="amountpaid'+orderid+'" id="amountpaid'+orderid+'" value="'+hiddamountpaid+'" required>';
				formhtml += '</div>';
				formhtml += '<div class="form-group">';
				formhtml += '<label for="paymentdate" class="col-form-label">Date of Payment:</label>';
				formhtml += '<input type="text" autocomplete="off" class="form-control  paydate datefield" name="paymentdate'+orderid+'" id="paymentdate'+orderid+'" value="'+hiddpaymentdate+'" required>';
				formhtml += '</div>';

				formhtml += '<div class="form-group">';
				formhtml += '<label for="paymentdate" class="col-form-label">Amount Deposited in:</label>';
				formhtml += '<select class="select"  name="amount_deposited_in'+orderid+'" id="amount_deposited_in'+orderid+'">';
				formhtml += '<option value="">Select Type</option>';
				//formhtml += '<option value="Depot Main account" '+selected1+'>Depot Main account</option>';
				formhtml += '<option value="CSD HO Main Account" '+selected2+'>CSD HO Main Account</option>';
				formhtml += '<option value="Virtual Account (Payment Gateway)" '+selected5+'>Virtual Account (Payment Gateway)</option>';
				formhtml += '</select>';
				formhtml += '</div>';

				formhtml += '<div class="form-group">';
				formhtml += '<label for="paymentdate" class="col-form-label">Remark (payment):</label>';
				formhtml += '<input type="text" autocomplete="off" class="form-control " name="payment_remark'+orderid+'" id="payment_remark'+orderid+'" value="'+hiddpayment_remark+'">';
				formhtml += '</div>';
			formhtml += '</div>';
	        formhtml += '<div class="col-md-6">';
	        	formhtml += '<div class="form-group">';
	        	formhtml += '<h5 class="col-form-label  badge  badge-warning badgepaydetail">Details of second payment to CSD (Optional)</h5></br>';
				formhtml += '<label for="loanbank-name" class="col-form-label">Name of bank:</label>';
				formhtml += '<input type="text" autocomplete="off" class="form-control" name="loanbank'+orderid+'" id="loanbank'+orderid+'" value="'+hiddloanbank+'" required>';
				formhtml += '</div>';

				formhtml += '<div class="form-group">';
				formhtml += '<label for="loanholder" class="col-form-label">Name of First Account holder:</label>';
				formhtml += '<input type="text" autocomplete="off" class="form-control" name="loanholder'+orderid+'" id="loanholder'+orderid+'" value="'+hiddloanholder+'" required>';
				formhtml += '</div>';

				formhtml += '<div class="form-group">';
				formhtml += '<label for="loanaccountnumber" class="col-form-label">Bank Account Number:</label>';
				formhtml += '<input type="text" autocomplete="off" class="form-control" name="loanaccountnumber'+orderid+'" id="loanaccountnumber'+orderid+'" value="'+hiddloanaccountnumber+'" required>';
				formhtml += '</div>';

				formhtml += '<div class="form-group">';
				formhtml += '<label for="loanifsc" class="col-form-label">IFSC Code:</label>';
				formhtml += '<input type="text" autocomplete="off" class="form-control" name="loanifsc'+orderid+'" id="loanifsc'+orderid+'" value="'+hiddloanifsc+'" required>';
				formhtml += '</div>';

				formhtml += '<div class="form-group">';
				formhtml += '<label for="loanutr" class="col-form-label">UTR Number:</label>';
				formhtml += '<input type="text" autocomplete="off" class="form-control" name="loanutr'+orderid+'" id="loanutr'+orderid+'" value="'+hiddloanutr+'" required>';
				formhtml += '</div>';

				formhtml += '<div class="form-group">';
				formhtml += '<label for="loanamountpaid" class="col-form-label">Amount paid:</label>';
				formhtml += '<input type="number" autocomplete="off" min="0" step="1" class="form-control numfield" name="loanamountpaid'+orderid+'" id="loanamountpaid'+orderid+'" value="'+hiddloanamountpaid+'" required>';
				formhtml += '</div>';

				formhtml += '<div class="form-group">';
				formhtml += '<label for="loanpaymentdate" class="col-form-label">Date of Payment:</label>';
				formhtml += '<input type="text" autocomplete="off" class="form-control datefield paydate" name="loanpaymentdate'+orderid+'" id="loanpaymentdate'+orderid+'" value="'+hiddloanpaymentdate+'" required>';
				formhtml += '</div>';

				formhtml += '<div class="form-group">';
				formhtml += '<label for="paymentdate" class="col-form-label">Amount Deposited in:</label>';
				formhtml += '<select class="select" name="amount_deposited_in_loan'+orderid+'" id="amount_deposited_in_loan'+orderid+'">';
				formhtml += '<option value="">Select Type</option>';
				//formhtml += '<option value="Depot Main account" '+selected3+'>Depot Main account</option>';
				formhtml += '<option value="CSD HO Main Account" '+selected4+'>CSD HO Main Account</option>';
				formhtml += '<option value="Virtual Account (Payment Gateway)" '+selected6+'>Virtual Account (Payment Gateway)</option>';
				formhtml += '</select>';
				formhtml += '</div>';

	        formhtml += '</div>';
        formhtml += '</div>';
		formhtml += '</div>';
		formhtml += '<div class="modal-footer">';
		formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
		formhtml += '<button type="button" onclick="collect_payment_receipt('+orderid+');" class="btn btn-primary custupload" data-id="'+orderid+'">Submit</button>';
		formhtml += '</div>';
		formhtml += '<div class="formsubmitmsg"><span class="errormsg errorpay'+orderid+'" style="display:none;">All payment details from self account are required.</span>';
		formhtml +=  '<span class="errormsg errorpayloan'+orderid+'" style="display:none;"></br>Either remove or fill all loan payment details if loan is taken.</span>';
		formhtml += '<span class="errormsg payerrormsg'+orderid+'" style="display:none;">Something went wrong.</span>';
		formhtml += '<span class="errormsg errortotalpay'+orderid+'" style="display:none;">Total amount paid is less than price of the item.</br> Payment details cannot be uploaded.</span>';
		formhtml += '<span class="successmsg paysuccessmsg'+orderid+'" style="display:none;">Receipt information is submitted.</span></div>';		
		formhtml += '</form>';
		formhtml += '<script type="text/javascript">jQuery( ".datefield" ).datepicker({format: "dd/mm/yy",endDate: "today",orientation: "top",autoclose: true});';
		formhtml += 'jQuery(".numfield").attr("onkeypress", "return isNumberKey(event)");</script>';
		jQuery('#custompopuptitle').html('Details of Payment made to CSD');
		jQuery('#custompopupcontent').html(formhtml);
		jQuery('#custompopup').modal('show');
	}
});
function collect_payment_receipt(orderid=''){
	var bank = jQuery('#bank'+orderid).val();
	var holder = jQuery('#holder'+orderid).val();
	var accountnumber = jQuery('#accountnumber'+orderid).val();
	var ifsc = jQuery('#ifsc'+orderid).val();
	var utr = jQuery('#utr'+orderid).val();
	var hiddensiteurl = jQuery('#hiddensiteurl').val();
	var paymentdate = jQuery('#paymentdate'+orderid).val();
	var amountpaid = jQuery('#amountpaid'+orderid).val();
	var loanbank = jQuery('#loanbank'+orderid).val();
	var loanholder = jQuery('#loanholder'+orderid).val();
	var loanaccountnumber = jQuery('#loanaccountnumber'+orderid).val();
	var loanifsc = jQuery('#loanifsc'+orderid).val();
	var loanutr = jQuery('#loanutr'+orderid).val();
	var loanhiddensiteurl = jQuery('#loanhiddensiteurl').val();
	var loanpaymentdate = jQuery('#loanpaymentdate'+orderid).val();
	var loanamountpaid = jQuery('#loanamountpaid'+orderid).val();

	var amount_deposited_in = jQuery('#amount_deposited_in'+orderid).val();
	var amount_deposited_in_loan = jQuery('#amount_deposited_in_loan'+orderid).val();
	var payment_remark = jQuery('#payment_remark'+orderid).val();

	if(loanamountpaid == 'undefined' || loanamountpaid == '' || loanamountpaid == '0' || amount_deposited_in_loan == ''){
		loanamountpaid = 0;
	}

	var loancheckneeded = false;
	if(loanbank || loanifsc || loanholder || loanaccountnumber || loanutr || loanamountpaid || loanpaymentdate || amount_deposited_in_loan){
		if(!loanbank || !loanifsc || !loanholder || !loanaccountnumber || !loanutr || !loanamountpaid || !loanpaymentdate || !amount_deposited_in_loan){
			loancheckneeded = true;
		}
	}
	if(orderid == ''){
		jQuery('.payerrormsg'+orderid).show().delay(1000).fadeOut(300);
	} else if(!bank || !ifsc || !holder || !accountnumber || !utr || !amountpaid || !paymentdate || !amount_deposited_in){
		jQuery('.errorpay'+orderid).show().delay(2500).fadeOut(300);
		if(loancheckneeded){
			jQuery('.errorpayloan'+orderid).show().delay(2500).fadeOut(300);
		}
	} else if(loancheckneeded){
		jQuery('.errorpayloan'+orderid).show().delay(2500).fadeOut(300);
	} else {
		var hiddentotalorderamount = jQuery('#hiddentotalorderamount'+orderid).val();
		var checksum = parseInt(amountpaid)+parseInt(loanamountpaid);
		/*if(parseInt(checksum) < parseInt(hiddentotalorderamount)){
			jQuery('.errortotalpay'+orderid).show().delay(5000).fadeOut(300);
		} else {*/
			make_ajax_submit_benefiicary_payment_details(orderid);
		//}
	}
}
function make_ajax_submit_benefiicary_payment_details(orderid=''){
	jQuery('#confirmmsgmodal').modal('hide');
	jQuery('.waitmsg').html('The payment details are saving. ');
	jQuery('#custompopup').modal('show');
	jQuery('.waitmsgmodal').modal('show');
	var bank = jQuery('#bank'+orderid).val();
	var holder = jQuery('#holder'+orderid).val();
	var accountnumber = jQuery('#accountnumber'+orderid).val();
	var ifsc = jQuery('#ifsc'+orderid).val();
	var utr = jQuery('#utr'+orderid).val();
	var hiddensiteurl = jQuery('#hiddensiteurl').val();
	var paymentdate = jQuery('#paymentdate'+orderid).val();
	var amountpaid = jQuery('#amountpaid'+orderid).val();
	var loanbank = jQuery('#loanbank'+orderid).val();
	var loanholder = jQuery('#loanholder'+orderid).val();
	var loanaccountnumber = jQuery('#loanaccountnumber'+orderid).val();
	var loanifsc = jQuery('#loanifsc'+orderid).val();
	var loanutr = jQuery('#loanutr'+orderid).val();
	var loanhiddensiteurl = jQuery('#loanhiddensiteurl').val();
	var loanpaymentdate = jQuery('#loanpaymentdate'+orderid).val();
	var loanamountpaid = jQuery('#loanamountpaid'+orderid).val();

	var amount_deposited_in = jQuery('#amount_deposited_in'+orderid).val();
	var amount_deposited_in_loan = jQuery('#amount_deposited_in_loan'+orderid).val();
	var payment_remark = jQuery('#payment_remark'+orderid).val();

    var form_data = new FormData();
    form_data.append('id', orderid);
    form_data.append('bank', bank);
    form_data.append('holder', holder);
    form_data.append('accountnumber', accountnumber);
    form_data.append('ifsc', ifsc);
    form_data.append('utr', utr);
    form_data.append('amountpaid', amountpaid);
    form_data.append('paymentdate', paymentdate);
    form_data.append('loanbank', loanbank);
    form_data.append('loanholder', loanholder);
    form_data.append('loanaccountnumber', loanaccountnumber);
    form_data.append('loanifsc', loanifsc);
    form_data.append('loanutr', loanutr);
    form_data.append('loanamountpaid', loanamountpaid);
    form_data.append('loanpaymentdate', loanpaymentdate);

    form_data.append('amount_deposited_in', amount_deposited_in);
    form_data.append('amount_deposited_in_loan', amount_deposited_in_loan);
    form_data.append('payment_remark', payment_remark);
    form_data.append('action', 'customer_collect_payment_receipt');

    jQuery('.custupload').addClass('linkdisabled');

    jQuery.ajax({
	    data: form_data,
	    type: 'POST',
	    contentType: false,
        processData: false,
	    url:woocommerce_params.ajax_url,
	    success: function(data) { //alert(data);
	        if(data){
	        	jQuery('#formcollect_payment_receipt'+orderid)[0].reset();
	        	jQuery('.paysuccessmsg'+orderid).show();
	        	jQuery('#bankinfo'+orderid).html(data);
	        	check_if_both_payment_receipt_and_details_uploaded(orderid);
	        } else {
	            jQuery('.payerrormsg'+orderid).show().delay(1000).fadeOut(300);
	        }
	        jQuery('.waitmsgmodal').modal('hide');
	        jQuery('.custupload').removeClass('linkdisabled');
	    }
	});
}
function check_if_both_payment_receipt_and_details_uploaded(orderid=''){
	if(orderid){
		var form_data = new FormData();
		form_data.append('id', orderid);
		form_data.append('action', 'show_popup_if_all_payment_details_submitted');

	    jQuery.ajax({
		    data: form_data,
		    type: 'POST',
		    contentType: false,
	        processData: false,
		    url:woocommerce_params.ajax_url,
		    success: function(data) {
		        if(data){
		        	jQuery('#custompopup').modal('hide');
		        	setTimeout(function(){
			        	popup_message('You would not be able to change Payment Details and Payment Receipt uploaded, Would you like to submit your Demand to CSD now? ', "function_submit_demand_to_depot("+orderid+");");
			        }, 500);
		        } else {
		            setTimeout(function(){
		        		location.reload(true);
		        	}, 1500);
		        }
		    }
		});
	}
}

jQuery('.dropstatechange').on('change', function(e){
	e.preventDefault();
	var selectedstate = jQuery(this).val();
	if(selectedstate){
		var pid = jQuery('#currentpostid').val();//jQuery('input[name="product_id"]').val();
		jQuery('.dealerchangeloader').show();
		var checkifvariable = jQuery('.variationpricechange').attr('data-type');
		if(checkifvariable == 'variable'){
			jQuery('.variationpricechange').html('');
			if(selectedstate){
				jQuery('.netpriceblkprice').hide();
			} else {
				jQuery('.netpriceblkprice').show();
			}		
		}
		if (!jQuery('#pa_state option[value="' +selectedstate+ '"]').length) {
			jQuery('#pa_state').val('common').trigger('change');
	    } else {
	    	jQuery('#pa_state').val(selectedstate).trigger('change');
	    }	

	    var checkoutofstock = jQuery('p.out-of-stock').html();
	    if(checkoutofstock){
	    	alert(checkoutofstock);
	    	jQuery('.hiddenoutofstock').show();
	    	jQuery('#checkavailability').hide();
	    	jQuery('.dealerchangeloader').hide();
	    	jQuery('#dealer').html('<option value="">Select Dealer</option>');
	    	jQuery('#depot').html('<option value="">Select Depot</option>');
			jQuery('#urc').html('<option value="">Select URC</option>');
	    } else {
	    	jQuery('.hiddenoutofstock').hide();
	    	jQuery('#checkavailability').show();

			jQuery.ajax({
			    data: 'action=change_dealer_dropdown&id='+selectedstate+'&pid='+pid,
			    type: 'POST',
			    url:woocommerce_params.ajax_url,
			    success: function(data) { //alert(data);
			    	if(data){
			    		jQuery('#dealer').html(data);
			    		
			    	} else {
			    		jQuery('#dealer').html('<option value="">Select Dealer</option>');
			    	}
			    	if(checkifvariable == 'variable'){
			    		if(selectedstate){
			    			var checkifprice = jQuery('.variations_form').find('.woocommerce-variation-price').html();
			    			if(checkifprice){
			    				var newpricevar = '<p class="price">'+checkifprice+'</p>';
			    				jQuery('.variationpricechange').html(newpricevar);
			    			} else {
			    				jQuery('.netpriceblkprice').show();
			    			}		    		
				    	}
			    	}
			    	jQuery('#depot').html('<option value="">Select Depot</option>');
			    	jQuery('#urc').html('<option value="">Select URC</option>');
			    	jQuery('.dealerchangeloader').hide();
			    }
			});
		}
	} else {
		jQuery('#dealer').html('<option value="">Select Dealer</option>');
    	jQuery('#depot').html('<option value="">Select Depot</option>');
		jQuery('#urc').html('<option value="">Select URC</option>');
		jQuery('.hiddenoutofstock').hide();
	    jQuery('#checkavailability').show();
	}
});
jQuery('#dealer').on('change', function(e){
	var selecteddealer = jQuery(this).val();
	var pid = jQuery('input[name="product_id"]').val();
	jQuery('.depotchangeloader').show();
	jQuery('#viewdealerlink').attr('href', '#');
	jQuery.ajax({
	    data: 'action=change_depot_dropdown&id='+selecteddealer+'&pid='+pid,
	    type: 'POST',
	    url:woocommerce_params.ajax_url,
	    success: function(data) { //alert(data);
	    	if(data){
	    		jQuery('#depot').html(data);
	    	} else {
	    		jQuery('#depot').html('<option value="">Select Depot</option>');
	    	}
	    	jQuery('#urc').html('<option value="">Select URC</option>');
	    	jQuery('.depotchangeloader').hide();
	    }
	});
});
function goBack() {
  window.history.back();
}
jQuery('.viewuserlink').on('click', function(e){
	e.preventDefault();
	var selectedusertype = jQuery(this).attr('data-type');
	var selecteduser = jQuery('#'+selectedusertype).val();
	var urtype = jQuery(this).attr('data-urtype');
	if(selecteduser == ''){
		popup_message('No '+selectedusertype+' selected. Please select any '+selectedusertype+' to view details.', "", selectedusertype+' Details');
	} else {
		jQuery.ajax({
		    data: 'action=update_view_user_link&id='+selecteduser+'&type='+selectedusertype,
		    type: 'POST',
		    url:woocommerce_params.ajax_url,
		    success: function(response) {
		    	var formhtml = '';
		    	if(response){
		    		formhtml = response;
		    	} else {
		    		formhtml = 'No '+selectedusertype+' found.';
		    	}
			popup_message(formhtml, "", urtype+' Details');
		    }
		});		
	}
});jQuery('.numfield').attr('onkeypress', "return isNumberKey(event)");
jQuery('.user-registration #card_id').attr('minlength', 19);
jQuery('.user-registration #card_id').attr('onkeypress', "return isAlphaNumeric(event)");
jQuery('.user-registration #chip_number').attr('minlength', 16);
jQuery('.user-registration #chip_number').attr('onkeypress', "return isNumberKey(event)");
jQuery('.user-registration #mobile').attr('minlength', 10);
jQuery('.user-registration #mobile').attr('onkeypress', "return isNumberKey(event)");
jQuery('.user-registration #pan_number').attr('minlength', 10);
//jQuery('.user-registration #pan_number').attr('maxlength', 10);
jQuery('.user-registration #user_login').attr('onkeypress', "return isAlphaNumeric(event)");
jQuery('#load_flatpickr[data-id="date_birth"]').on('change', function(){
	jQuery('#date_birth-error').html('');
	jQuery('#date_birth-error').hide();
});
jQuery('#load_flatpickr[data-id="date_joining"]').on('change', function(){
	jQuery('#date_joining-error').html('');
	jQuery('#date_joining-error').hide();
});
jQuery('#card_id').on('keyup', function(){
	var error = '';
	var enteredval1 = jQuery(this).val();
	if(enteredval1.length == 19){
		var regpan = /^([a-zA-Z]){2}([0-9]){14}([a-zA-Z]){1}([0-9]){2}?$/;
	  	if(regpan.test(enteredval1)){
	  		jQuery('#pan_number-error').html('');
			jQuery('#pan_number-error').hide();
	  	} else { 
	  		jQuery('#pan_number-error').html('Invalid Card number');
			jQuery('#pan_number-error').show();
	  	}
	}
});
jQuery('#pan_number').on('focusout', function(){
	var error = '';
	var enteredval = jQuery(this).val();
	if(enteredval.length == 10){
		var regpan = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/;
	  	if(regpan.test(enteredval)){
	  		jQuery('#pan_number-error').html('');
			jQuery('#pan_number-error').hide();
	  	} else { 
	  		jQuery('#pan_number-error').html('Invalid PAN number');
			jQuery('#pan_number-error').show();
	  	}
	}
});
/*	var error = '';
	var enteredval = jQuery(this).val();
	if(enteredval.length == 10){
		var regpan = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/;
	  	if(regpan.test(enteredval)){
	  		jQuery('#pan_number-error').html('');
			jQuery('#pan_number-error').hide();
	  	} else { 
	  		jQuery('#pan_number-error').html('Invalid PAN number');
			jQuery('#pan_number-error').show();
	  	}
	}
});*/
var d = new Date();
var strDate = d.getDate() + "/" + (d.getMonth()+1) + "/" + d.getFullYear();
jQuery('.user-registration #date_birth').attr('data-max-date', strDate);
jQuery('.user-registration #date_joining').attr('data-max-date', strDate);
jQuery('.paydate').attr('max', strDate);
jQuery('#password_current').attr('required', true);
jQuery(function(){
    var dtToday = new Date();

    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();

    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
        day = '0' + day.toString();

    var maxDate = year + '-' + month + '-' + day;    
    jQuery('.paydate').attr('max', maxDate);
});
function isNumberKey(evt){
 	var charCode = (evt.which) ? evt.which : event.keyCode
 	if (charCode > 31 && (charCode < 48 || charCode > 57))
    return false;
	return true;
}
function isAlphaNumeric(evt) {
	var code = (evt.which) ? evt.which : event.keyCode
	if (!(code > 47 && code < 58) && // numeric (0-9)
	    !(code > 64 && code < 91) && // upper alpha (A-Z)
	    !(code > 96 && code < 123)) { // lower alpha (a-z)
	  return false;
	}
  	return true;
};
function isAlphaNumericPAN(evt) {
	var code = (evt.which) ? evt.which : event.keyCode
	if (!(code > 47 && code < 58) && // numeric (0-9)
	    !(code > 64 && code < 91) ){ // upper alpha (A-Z)
	  return false;
	}
  	return true;
};
jQuery().ready(function(){
jQuery( "a.upload_payment_receipt" ).hover(
  function() {   
   var title = 'Please upload your payment receipt and payment details in order to get approval from depot.';  // extracts the title using the data-title attr applied to the 'a' tag
    jQuery('<div/>', { // creates a dynamic div element on the fly
        text: title,
        class: 'boxpaymentreceipt'
    }).appendTo(this);  // append to 'a' element
  }, function() {
    jQuery(document).find("div.boxpaymentreceipt").remove(); // on hover out, finds the dynamic element and removes it.
  }
);
jQuery( "a.collect_payment_receipt" ).hover(
  function() {   
   var title = 'Please upload your payment receipt and payment details in order to get approval from depot.';  // extracts the title using the data-title attr applied to the 'a' tag
    jQuery('<div/>', { // creates a dynamic div element on the fly
        text: title,
        class: 'boxpaymentreceipt'
    }).appendTo(this);  // append to 'a' element
  }, function() {
    jQuery(document).find("div.boxpaymentreceipt").remove(); // on hover out, finds the dynamic element and removes it.
  }
);
  });
jQuery('.cancel_order_before_verify').on('click', function(e){
	e.preventDefault();
	var orderid = this.search.split('=')[1];
	if(orderid){
		//popup_message('Are you sure to proceed?', "function_change_current_order_status("+orderid+");");
		var formhtml = '<form action="" method="post" id="formcanceldemand'+orderid+'" enctype="multipart/form-data">';
		formhtml += '<div class="modal-body">';

		formhtml += '<div class="form-group">';
		formhtml += '<label for="reason" class="col-form-label">Reason for cancellation:*</label>';
		formhtml += '<input type="text" class="form-control" name="reasonforcancel'+orderid+'" id="reasonforcancel'+orderid+'" required>';
		formhtml += '</div>';
		formhtml += '</div>';
		formhtml += '<div class="modal-footer">';
		formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
		formhtml += '<button type="button" onclick="function_change_current_order_status('+orderid+');" class="btn btn-primary btnsubmit" data-id="'+orderid+'">Submit</button>';
		formhtml += '</div>';
		formhtml += '<div class="formsubmitmsg"><span class="errormsg errorpay'+orderid+'" style="display:none;">Please enter reason for cancellation.</span>';
		formhtml += '<span class="errormsg payerrormsg'+orderid+'" style="display:none;">Something went wrong.</span>';
		formhtml += '<span class="successmsg paysuccessmsg'+orderid+'" style="display:none;">All details are saved.</span></div>';
		formhtml += '</form>';
		jQuery('#custompopuptitle').html('Cancel Demand');
		jQuery('#custompopupcontent').html(formhtml);
		jQuery('#custompopup').modal('show');
	}
});
function function_change_current_order_status(orderid=''){
	var reasonforcancel = jQuery('#reasonforcancel'+orderid).val();
	if(orderid == ''){
		jQuery('.payerrormsg'+orderid).show().delay(1000).fadeOut(300);
	} else if(!reasonforcancel){
		jQuery('.errorpay'+orderid).show().delay(2500).fadeOut(300);
	} else {
		jQuery('#custompopup').modal('hide');
		jQuery('.waitmsg').html('Please wait. ');
		jQuery('.waitmsgmodal').modal('show');
		jQuery.ajax({
	    data: 'action=change_current_order_status&id='+orderid+'&status=cancelled&reason='+reasonforcancel,
	    type: 'POST',
	    url:woocommerce_params.ajax_url,
	    success: function(data) { //alert(data);
	    		jQuery('.waitmsgmodal').modal('hide');
		        location.reload(true);
		    }
		});
	}
}
jQuery('.view_customer').on('click', function(e){
	e.preventDefault();
	var customerid = jQuery(this).attr('data-customerid');
	if(customerid){
		var data = jQuery('#hiddencustomerdata'+customerid).html();
		jQuery('#custompopuptitle').html('View Customer Details');
		jQuery('#custompopupcontent').html(data);
		jQuery('#custompopup').modal('show');
	}
});
jQuery('#ordersearchtype').on('change', function(){
	var checkval = jQuery(this).val();
	var check = '<input type="text" id="ordersearch" class="search-field input-text ordersearch " placeholder="Search for" value="" required name="os" autocomplete="off">';
	if(checkval == 'pay'){
		check = '<select name="os" id="searchpay" class="select"  required><option value="razorpay">Online Payment</option><option value="bacs">Direct Bank Transfer</option></select>';
	}
	
	jQuery('.allsearchbox').html(check);

	jQuery('.ordersearch').val('');
	jQuery('.ordersearch').removeClass('ordersearchdate');
	if(checkval == 'indent'){
		jQuery('.ordersearch').addClass('ordersearchdate');
		jQuery('#ordersearch').datepicker({ format: 'dd/mm/yyyy',  endDate: "today" }); //, dateFormat: 'dd/mm/yy'
	}
});
jQuery('#ordersearchstatus').on('change', function(){
	jQuery('form#formorderstatus').submit();
});
jQuery('#limit').on('change', function(){
	jQuery('form#formpagelimit').submit();
});
jQuery('#orderlistmonth').on('change', function(){
	var selected = jQuery(this).val();
	jQuery('.hiddenmonth').val(selected);
});
jQuery('#orderlistyear').on('change', function(){
	var selected = jQuery(this).val();
	jQuery('.hiddenyear').val(selected);
});

jQuery( ".ordersearchdate" ).datepicker({ format: 'dd/mm/yyyy',  endDate: "today", dateFormat: 'dd/mm/yyyy' }); //, dateFormat: 'yy/mm/dd'
(function () {
	var hiddenorderlistyear = jQuery('#hiddenorderlistyear').val();
	if(hiddenorderlistyear){
	    var year_start = 2020;
	    var year_end = (new Date).getFullYear(); //current year
	    var selected_year = hiddenorderlistyear; // 0 first option

	    var option = '';  //first option
	    
	    for (var i = 0; i <= (year_end - year_start); i++) {
	        var year = (year_start+i);
	        var selected = (year == selected_year) ? ' selected' : '';
	        option += '<option value="' + year + '"'+selected+'>' + year + '</option>';
	    }
	    document.getElementById('orderlistyear').innerHTML = option;
	}
})();


function popup_message(msg='', submitaction='', heading='', successmag='', full=''){

	jQuery(".modal-dialog").removeClass("modalnew");
	var formhtml = '<div class="modal-body">';
	formhtml += '<div class="form-group">';
	formhtml += '<p>'+msg+'</p>';
	formhtml += '</div>';
	if(submitaction){
		formhtml += '<div class="modal-footer">';
	    formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="hide_popup_message();">Cancel</button>';
	    formhtml += '<button type="button" class="btn btn-primary" onclick="'+submitaction+'">Ok</button>';
	    formhtml += '</div>';
	}
	formhtml += '<span class="errormsg popuperrormsg" style="display:none;">Something went wrong.</span>';
	formhtml += '<span class="successmsg popupsuccessmsg" style="display:none;">'+successmag+'</span>';
	formhtml += '</div>';
	jQuery('#custompopuptitle').html(heading);
	jQuery('#custompopupcontent').html(formhtml);
	jQuery('#custompopup').modal('show');

	if(full){
		jQuery(".modal-dialog").addClass("modalnew");
	}
}
function hide_popup_message(){
	jQuery('#custompopup').modal('hide');
	location.reload(true);
}

jQuery('.submit_demand_to_depot').on('click', function(e){
	e.preventDefault();
	var orderid = this.search.split('=')[1];
	var hiddenpaymentstatus = jQuery('#hiddenpaymentstatus'+orderid).val();
	if(hiddenpaymentstatus == 'yes'){
		popup_message('Are you sure to submit?', "function_submit_demand_to_depot("+orderid+");", '', 'Demand is submitted to Depot.');
	} else {
		popup_message('Please upload payment receipt and payment details before submitting to the depot', '', 'Missing Payment Details');
	}
});
function function_submit_demand_to_depot(orderid=''){
	if(orderid){
		jQuery.ajax({
		    data: 'action=change_current_order_status_to_processing&id='+orderid+'&status=processing',
		    type: 'POST',
		    url:woocommerce_params.ajax_url,
		    success: function(data) { 
	    		if(data){
	    			jQuery('.popupsuccessmsg').show();
	    			location.reload(true);
	    		} else {
	    			jQuery('.popuperrormsg').show();
	    		}
		    }
		});
	}
}

(function($) {
    jQuery(document).on('facetwp-refresh', function() {
        if (FWP.loaded) { // after the initial pageload
            FWP.parse_facets(); // load the values
            FWP.set_hash(); // set the new URL
            location.reload();
            return false;
        }
     });
})(jQuery);


if (document.getElementById('username') !=null) document.getElementById( "username" ).autocomplete = "off";
if (document.getElementById('password') !=null) document.getElementById( "password" ).autocomplete = "off";

if (document.getElementById('beneficiary_name') !=null) document.getElementById( "beneficiary_name" ).autocomplete = "off";
if (document.getElementById('card_id') !=null) document.getElementById( "card_id" ).autocomplete = "off";
if (document.getElementById('chip_number') !=null) document.getElementById( "chip_number" ).autocomplete = "off";
if (document.getElementById('mobile') !=null) document.getElementById( "mobile" ).autocomplete = "off";
if (document.getElementById('otp') !=null) document.getElementById( "otp" ).autocomplete = "off";
if (document.getElementById('input_box_1600877767') !=null) document.getElementById( "input_box_1600877767" ).autocomplete = "off";
if (document.getElementById('pan_number') !=null) document.getElementById( "pan_number" ).autocomplete = "off";
if (document.getElementById('user_login') !=null) document.getElementById( "user_login" ).autocomplete = "off";
if (document.getElementById('user_email') !=null) document.getElementById( "user_email" ).autocomplete = "off";
if (document.getElementById('user_pass') !=null) document.getElementById( "user_pass" ).autocomplete = "off";
if (document.getElementById('user_confirm_password') !=null) document.getElementById( "user_confirm_password" ).autocomplete = "off";


jQuery('.send_sms_to_scpl').on('click', function(e){
	e.preventDefault();
	jQuery(this).bind('click', false);
	var id = jQuery(this).attr('data-id');
	var currentlink = jQuery(this);
	if(id){
		jQuery(this).addClass('linkdisabled');
		popup_message('Sending SMS', '', 'Please Wait');
		jQuery.ajax({
	        type: 'POST',
	        url:woocommerce_params.ajax_url,
	        data: {
	            'action': 'send_admin_sms_processing',
	            'id': id,
	        },
	        success: function (result) {
	        	if(result){
	        		//jQuery('.smssuccess'+id).show("slow").delay(1000).hide("slow");
	        		popup_message('SMS sent successfully to SCPL', '', 'SMS sent');
	        		setTimeout(function(){ location.reload();}, 1500);
	        	} else {
	        		jQuery('.smserror'+id).show("slow").delay(1000).hide("slow");
	        	}
	        	jQuery(this).unbind('click', false);
	        	setTimeout(function(){
		        	currentlink.removeClass('linkdisabled');
		        }, 1000);
	        }
	    });
	}
});

jQuery( ".outofstk_expln" ).hover(
  function() {   
   var title = jQuery('#outofstockexpln').val(); 
    jQuery('<div/>', { // creates a dynamic div element on the fly
        text: title,
        class: 'outofstk'
    }).appendTo(this);  // append to 'a' element
  }, function() {
    jQuery(document).find("div.outofstk").remove();
  }
);

jQuery('.selectlive').on('change', function(){
	var value = jQuery(this).val();
	jQuery('.live').hide();
	jQuery('.'+value).fadeIn();
});

jQuery('.selectlivedepot').on('change', function(){
	jQuery('form#depotsubmit').submit();
});


jQuery('.uploadorderfile').on('click', function(e){
	e.preventDefault();
	var orderid = jQuery(this).attr('data-id');
	var size = jQuery(this).attr('data-size');
	var type = jQuery(this).attr('data-type');
	var name = jQuery(this).attr('data-name');
	if(orderid){
		var formhtml = '<form action="" method="post" id="formsupplyorder'+orderid+'" enctype="multipart/form-data">';
		formhtml += '<div class="modal-body">';
		formhtml += '<div class="form-group">';
		formhtml += '<input type="file" class="form-control" name="'+type+''+orderid+'" id="'+type+''+orderid+'" required>';
		formhtml += '</div>';
		formhtml += '</div>';
		formhtml += '<div class="modal-footer">';
		formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
		formhtml += '<button type="button" onclick="order_upload_depot_files(\''+orderid+'\', \''+type+'\', \''+size+'\', \''+name+'\');" class="btn btn-primary " data-id="'+orderid+'">Upload</button>';
		formhtml += '</div>';
		formhtml += '<div class="formsubmitmsg"><span class="errormsg errorpay'+orderid+'" style="display:none;">Please upload file.</span>';
		formhtml += '<span class="errormsg payerrormsg'+orderid+'" style="display:none;">Something went wrong.</span>';
		formhtml += '<span class="errormsg sizeerror'+orderid+'" style="display:none;"></span>';
		formhtml += '<span class="successmsg paysuccessmsg'+orderid+'" style="display:none;">'+name+' is uploaded.</span></div>';
		formhtml += '</form>';
		var poptitle = 'Upload '+name;
		if(name == 'Merged PDF'){
			poptitle += ' having Local Supply Order, Official Receipt and Authority Letter';
		}
		jQuery('#custompopuptitle').html(poptitle);
		jQuery('#custompopupcontent').html(formhtml);
		jQuery('#custompopup').modal('show');
	}
});
function order_upload_depot_files(orderid='', type='', size='', name=''){
	var supplyorder = jQuery('#'+type+orderid).prop('files')[0];

	if(orderid == '' || type == ''){
		jQuery('.payerrormsg'+orderid).show().delay(1000).fadeOut(300);
	} else if(!supplyorder || supplyorder == '' || supplyorder == 'undefined'){
		jQuery('.errorpay'+orderid).show().delay(2500).fadeOut(300);
	} else {
		var supplyordersize = supplyorder.size;
		if(supplyordersize > (parseFloat(size)*1000000) ){
			var msg = name+' max file size allowed is '+size+'MB';
			jQuery('.sizeerror'+orderid).html(msg);
			jQuery('.sizeerror'+orderid).show().delay(6000).fadeOut(300);
		} else {
			jQuery('.waitmsg').html('Document is uploading. ');
			jQuery('.waitmsgmodal').modal('show');
			var file_data = jQuery('#'+type+orderid).prop('files')[0];
	        var form_data = new FormData();
	        form_data.append('file', file_data);
	        form_data.append('filename', type);
	        form_data.append('id', orderid);
	        form_data.append('action', 'custom_upload_file');
	        jQuery.ajax({
			    data: form_data,
			    type: 'POST',
			    contentType: false,
	            processData: false,
			    url:woocommerce_params.ajax_url,
			    success: function(data) { //alert(data);
			    	if(data.success == false){
			    		var errormsg = data.data[0].message;
			    		jQuery('.payerrormsg'+orderid).html(errormsg);
			    		jQuery('.payerrormsg'+orderid).show().delay(2000).fadeOut(300);
			    	} else if(data.success == true){
			        	jQuery('.paysuccessmsg'+orderid).show().delay(2000).fadeOut(300);
			        	setTimeout(function(){
			        		location.reload(true);
			        	}, 500);
			        } else {
			        	jQuery('.payerrormsg'+orderid).html('Something went wrong.');
			            jQuery('.payerrormsg'+orderid).show().delay(2000).fadeOut(300);
			        }
			        jQuery('.waitmsgmodal').modal('hide');
			        setTimeout(function(){
		        		location.reload(true);
		        	}, 2000);
			    }
			});
	    }

	}
}

jQuery('.dashboard-click').on('click', function(e){
	e.preventDefault();
	var id = jQuery(this).attr('data-id'); 
	var name = jQuery(this).attr('data-name');
	var duration = jQuery('.selectlive').val();
	var selectlivedepot = jQuery('.selectlivedepot').val();
	if(id){
		if(id == 'orders_processing'){
			duration = 'all';
		}
		var action = 'show_depot_dashboard_values';
		if(jQuery(this).hasClass('dashboard-specialclick')){
			action = 'show_depot_reject_dashboard';
		}
		if(selectlivedepot != 'all' && (id == 'orders_processing' || id == 'processing_upto_3' || id == 'processing_4_to_7' || id == 'processing_morethan_7')){
			action = 'show_depot_processing_dashboard';
		}
		if(selectlivedepot != 'all' && (id == 'to_be_Verified' || id == 'to_be_Verified_2' || id == 'to_be_Verified_3_to_5' || id == 'to_be_Verified_5')){
			action = 'show_poreleased_dashboard';
		}

		if(id == 'registrations_denied'){
			if(duration == 'tod' || duration == 'yes'){
				action = 'show_registrations_denied_dashboard';
			} else {
				action = '';
			}
		}

		if(selectlivedepot != 'all' && id == 'orders_released' ){
			action = 'show_depot_orders_released_dashboard';
		}
		if(selectlivedepot != 'all' && id == 'orders_completed'){
			action = 'show_depot_orders_completed_dashboard';
		}

		if(action){
			jQuery(this).addClass('linkdisabled');
			var currentlink = jQuery(this);
			jQuery.ajax({
			    data: 'action='+action+'&id='+id+'&duration='+duration+'&name='+name+'&depot='+selectlivedepot,
			    type: 'POST',
			    url:woocommerce_params.ajax_url,
			    success: function(data) { 
		    		if(data){
		    			var formhtml = '<div class="modal-body">';
						formhtml += '<div class="form-group">';
						formhtml += data;
						formhtml += '</div>';
						formhtml += '</div>';

						jQuery('#custompopuptitle').html('');
						jQuery('#custompopupcontent').html(formhtml);
						jQuery('#custompopup').modal('show');
		    		}
		    		currentlink.removeClass('linkdisabled');
			    }
			});
		}
	}
});

jQuery('.additionalpay').on('click', function(e){
	e.preventDefault();
	var orderid = jQuery(this).attr('data-id');
	var formhtml = '<form action="" method="post" id="formadditionalpay'+orderid+'">';
	formhtml += '<div class="modal-body">';
	formhtml += '<div class="form-group">';
	formhtml += '<label for="additionalpayreason" class="col-form-label">Reason for seeking additional funds:</label>';
	formhtml += '<textarea class="form-control" name="additionalpayreason" id="additionalpayreason'+orderid+'" placeholder="Reason" required></textarea>';
	formhtml += '</div>';
	formhtml += '<div class="form-group">';
	formhtml += '<label for="additionalpayamount" class="col-form-label">Please enter additional payment required from beneficiary in Rs:</label>';
	formhtml += '<input type="number" class="form-control numfield" onkeypress="return isNumberKey(event);" name="additionalpayamount" id="additionalpayamount'+orderid+'" placeholder="Amount" required>';
	formhtml += '</div>';
	formhtml += '</div>';
	formhtml += '<div class="modal-footer">';
	formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>';
	formhtml += '<button type="button" onclick="collect_additionalpay('+orderid+');" class="btn btn-primary addpaybtn" id="addpaybtn'+orderid+'" data-id="'+orderid+'">Submit</button>';
	formhtml += '</div>';
	formhtml += '<div class="formsubmitmsg"><span class="errormsg errormsg'+orderid+'" style="display:none;">Something went wrong</span>';
	formhtml += '<span class="errormsg remarkerror'+orderid+'" style="display:none;">All fields are required</span>';
	formhtml += '<span class="successmsg successmsg'+orderid+'" style="display:none;">Additional payment is requested.</span></div>';
	formhtml += '</form>';
	jQuery('#custompopuptitle').html('Additional Payment Required');
	jQuery('#custompopupcontent').html(formhtml);
	jQuery('#custompopup').modal('show');
});

function collect_additionalpay(orderid=''){

	var additionalpayreason = jQuery('#additionalpayreason'+orderid).val();
	var additionalpayamount = jQuery('#additionalpayamount'+orderid).val();
	if(orderid == ''){
		jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
	} else if(additionalpayreason == '' || additionalpayamount == '' ){
		jQuery('.remarkerror'+orderid).show().delay(1000).fadeOut(300);
	} else {
		jQuery('#addpaybtn'+orderid).addClass('linkdisabled');
		jQuery('.waitmsg').html('Additional Payment details are saving. Please wait ');
		jQuery('.waitmsgmodal').modal('show');
		jQuery.ajax({
		    data: 'action=save_additionalpay&id='+orderid+'&additionalpayreason='+additionalpayreason+'&additionalpayamount='+additionalpayamount,
		    type: 'POST',
		    url:woocommerce_params.ajax_url,
		    success: function(data) {
		    	jQuery('.waitmsgmodal').modal('hide');
		        if(data){
		        	jQuery('.successmsg'+orderid).show().delay(1000).fadeOut(300);		        	
		        	setTimeout(function(){
		        		location.reload(true);
		        	}, 1500);
		        } else {
		            jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
		        }
		        jQuery('#addpaybtn'+orderid).removeClass('linkdisabled');

		    }
		});
	}
	
}
jQuery('.customerextrapaylink').on('click', function(e){
	e.preventDefault();
	popup_message('Additional payment cannot be made without paying the first online payment.');
});
jQuery('.customerextrapay').on('click', function(e){
	e.preventDefault();
	var orderid = jQuery(this).attr('data-id');
	jQuery('#customerextrapay'+orderid).addClass('linkdisabled');
	if(orderid){
		jQuery.ajax({
		    data: 'action=show_addition_pay_form&id='+orderid,
		    type: 'POST',
		    url:woocommerce_params.ajax_url,
		    success: function(data) {
		        if(data){
		        	jQuery('#custompopuptitle').html('Additional Payment');
					jQuery('#custompopupcontent').html(data);
					jQuery('#custompopup').modal('show');
		        } 
		        jQuery('#customerextrapay'+orderid).removeClass('linkdisabled');
		    }
		});
	}
});

jQuery('.verifysupplyorder').on('click', function(e){
	e.preventDefault();
	var demandid = jQuery('.demandid').val();
	var demandpan = jQuery('.demandpan').val();
	var demandcard = jQuery('.demandcard').val();
	jQuery('.errormsg').html('');
	if(!demandid  || !demandpan || !demandcard){
		jQuery('.errormsg').html('All fields are required').show().delay(1000).fadeOut(300);
	} else {
		jQuery('.verifysupplyorder').addClass('linkdisabled');
		var formdata = jQuery('#verifysupplyorderform').serialize();
		jQuery.ajax({
		    data: 'action=action_verifysupplyorder&'+formdata,
		    type: 'POST',
		    url:woocommerce_params.ajax_url,
		    success: function(data) {
		        if(data){
		        	var formhtml = '<div class="modal-body">';
		        	formhtml += data;
		        	formhtml += '</div>';
		        	jQuery('#custompopuptitle').html('Supply Order Details');
					jQuery('#custompopupcontent').html(formhtml);
					jQuery('#custompopup').modal('show');
		        } else {
		        		jQuery('.errormsg').html('Something went wrong. Please try again later.').show().delay(1000).fadeOut(300);
				}
		        jQuery('.verifysupplyorder').removeClass('linkdisabled');
		        jQuery('#verifysupplyorderform')[0].reset();
		    }
		});
	}
});


jQuery('.customerfeed').on('click', function(e){
	e.preventDefault();
	var orderid = jQuery(this).attr('data-id');
	var formhtml = '<form action="" method="post" id="formcustomerfeed'+orderid+'">';
	formhtml += '<div class="modal-body">';
	formhtml += '<div class="form-group">';
	formhtml += '<label for="additionalpayreason" class="col-form-label">Feedback:</label>';
	formhtml += '<textarea class="form-control" name="customerfeedback" id="customerfeedback'+orderid+'" placeholder="Feedback" required></textarea>';
	formhtml += '</div>';
	formhtml += '</div>';
	formhtml += '<div class="modal-footer">';
	formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>';
	formhtml += '<button type="button" onclick="submit_customerfeedback('+orderid+');" class="btn btn-primary customerfeedbtn" id="customerfeedbtn'+orderid+'" data-id="'+orderid+'">Submit</button>';
	formhtml += '</div>';
	formhtml += '<div class="formsubmitmsg"><span class="errormsg errormsg'+orderid+'" style="display:none;">Something went wrong</span>';
	formhtml += '<span class="errormsg feederror'+orderid+'" style="display:none;">Feedback is required</span>';
	formhtml += '<span class="successmsg successmsg'+orderid+'" style="display:none;">Feedback is submitted.</span></div>';
	formhtml += '</form>';
	jQuery('#custompopuptitle').html('Feedback');
	jQuery('#custompopupcontent').html(formhtml);
	jQuery('#custompopup').modal('show');
});

function submit_customerfeedback(orderid=''){

	var customerfeedback = jQuery('#customerfeedback'+orderid).val();
	if(orderid == ''){
		jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
	} else if(customerfeedback == ''){
		jQuery('.feederror'+orderid).show().delay(1000).fadeOut(300);
	} else {
		jQuery('#customerfeedbtn'+orderid).addClass('linkdisabled');
		jQuery('.waitmsg').html('Feedback is saving. Please wait ');
		jQuery('.waitmsgmodal').modal('show');
		jQuery.ajax({
		    data: 'action=save_customerfeedback&id='+orderid+'&customerfeedback='+customerfeedback,
		    type: 'POST',
		    url:woocommerce_params.ajax_url,
		    success: function(data) {
		    	jQuery('.waitmsgmodal').modal('hide');
		        if(data){
		        	jQuery('.successmsg'+orderid).show().delay(1000).fadeOut(300);		        	
		        	setTimeout(function(){
		        		location.reload(true);
		        	}, 1500);
		        } else {
		            jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
		        }
		        jQuery('#customerfeedbtn'+orderid).removeClass('linkdisabled');

		    }
		});
	}
	
}



jQuery('.additionaldoc').on('click', function(e){
	e.preventDefault();
	var orderid = jQuery(this).attr('data-id');
	var formhtml = '<form action="" method="post" id="formadditionaldoc'+orderid+'">';
	formhtml += '<div class="modal-body">';
	formhtml += '<div class="form-group">';
	formhtml += '<label for="additionaldocremarks" class="col-form-label">Remarks</label>';
	formhtml += '<textarea class="form-control" name="additionaldocremarks" id="additionaldocremarks'+orderid+'" placeholder="Remarks" required></textarea>';
	formhtml += '</div>';
	formhtml += '</div>';
	formhtml += '<div class="modal-footer">';
	formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>';
	formhtml += '<button type="button" onclick="collect_additionaldoc('+orderid+');" class="btn btn-primary adddocbtn" id="adddocbtn'+orderid+'" data-id="'+orderid+'">Submit</button>';
	formhtml += '</div>';
	formhtml += '<div class="formsubmitmsg"><span class="errormsg errormsg'+orderid+'" style="display:none;">Something went wrong</span>';
	formhtml += '<span class="errormsg remarkerror'+orderid+'" style="display:none;">Remark is required</span>';
	formhtml += '<span class="successmsg successmsg'+orderid+'" style="display:none;">Additional documents are requested.</span></div>';
	formhtml += '</form>';
	jQuery('#custompopuptitle').html('Additional documents Required');
	jQuery('#custompopupcontent').html(formhtml);
	jQuery('#custompopup').modal('show');
});

function collect_additionaldoc(orderid=''){

	var additionaldocremarks = jQuery('#additionaldocremarks'+orderid).val();
	if(orderid == ''){
		jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
	} else if(additionaldocremarks == '' ){
		jQuery('.remarkerror'+orderid).show().delay(1000).fadeOut(300);
	} else {
		jQuery('#adddocbtn'+orderid).addClass('linkdisabled');
		jQuery('.waitmsg').html('Additional document details are saving. Please wait ');
		jQuery('.waitmsgmodal').modal('show');
		jQuery.ajax({
		    data: 'action=save_additionaldoc&id='+orderid+'&additionaldocremarks='+additionaldocremarks,
		    type: 'POST',
		    url:woocommerce_params.ajax_url,
		    success: function(data) {
		    	jQuery('.waitmsgmodal').modal('hide');
		        if(data){
		        	jQuery('.successmsg'+orderid).show().delay(1000).fadeOut(300);		        	
		        	setTimeout(function(){
		        		location.reload(true);
		        	}, 1500);
		        } else {
		            jQuery('.errormsg'+orderid).show().delay(1000).fadeOut(300);
		        }
		        jQuery('#adddocbtn'+orderid).removeClass('linkdisabled');
		    }
		});
	}
}

jQuery('.customeradditionaldocuments').on('click', function(e){
	e.preventDefault();
	var orderid = jQuery(this).attr('data-id');
	var remarks = jQuery(this).attr('data-remarks');
	jQuery('#customeradditionaldocuments'+orderid).addClass('linkdisabled');
	if(orderid){
		var formhtml = '<form action="" method="post" id="formadditionaldocuments'+orderid+'" enctype="multipart/form-data">';
		formhtml += '<div class="modal-body">';
		if(remarks){
			formhtml += '<div class="form-group">';
			formhtml += '<p>Remarks: '+remarks+'</p>';
			formhtml += '</div>';
		}
		formhtml += '<div class="form-group">';
		formhtml += '<input type="file" class="form-control" accept="application/pdf" name="additionaldocuments" id="additionaldocuments'+orderid+'" required>';
		formhtml += '</div>';
		formhtml += '</div>';
		formhtml += '<div class="modal-footer">';
		formhtml += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
		formhtml += '<button type="button" onclick="order_upload_extra_doc(\''+orderid+'\');" class="btn btn-primary csdoc" id="csdoc'+orderid+'" data-id="'+orderid+'">Upload</button>';
		formhtml += '</div>';
		formhtml += '<div class="formsubmitmsg"><span class="errormsg errorpay'+orderid+'" style="display:none;">Please upload file.</span>';
		formhtml += '<span class="errormsg payerrormsg'+orderid+'" style="display:none;">Something went wrong.</span>';
		formhtml += '<span class="errormsg sizeerror'+orderid+'" style="display:none;"></span>';
		formhtml += '<span class="successmsg paysuccessmsg'+orderid+'" style="display:none;">Additional document is uploaded.</span></div>';
		formhtml += '</form>';
		jQuery('#custompopuptitle').html('Additional documents Required');
		jQuery('#custompopupcontent').html(formhtml);
		jQuery('#custompopup').modal('show');
		jQuery('#customeradditionaldocuments'+orderid).removeClass('linkdisabled');
	}
});

function order_upload_extra_doc(orderid=''){
	var additionaldocuments = jQuery('#additionaldocuments'+orderid).prop('files')[0];
	if(orderid == ''){
		jQuery('.payerrormsg'+orderid).show().delay(1000).fadeOut(300);
	} else if(!additionaldocuments || additionaldocuments == '' || additionaldocuments == 'undefined'){
		jQuery('.errorpay'+orderid).show().delay(2500).fadeOut(300);
	} else {
		var additionaldocumentstype = additionaldocuments.type;
		var additionaldocumentssize = additionaldocuments.size; 
		if(additionaldocumentstype != 'application/pdf'){
			var msg = ' Only pdf files are allowed to upload.';
			jQuery('.sizeerror'+orderid).html(msg);
			jQuery('.sizeerror'+orderid).show().delay(6000).fadeOut(300);
		} else if(additionaldocumentssize > 2000000){
			var msg = name+' max file size allowed is 2MB';
			jQuery('.sizeerror'+orderid).html(msg);
			jQuery('.sizeerror'+orderid).show().delay(6000).fadeOut(300);
		} else {
			jQuery('.waitmsg').html('Document is uploading. ');
			jQuery('.waitmsgmodal').modal('show');
			jQuery('#csdoc'+orderid).addClass('linkdisabled');
			var file_data = jQuery('#additionaldocuments'+orderid).prop('files')[0];
	        var form_data = new FormData();
	        form_data.append('additionaldocuments', file_data);
	        form_data.append('filename', 'additionaldocuments');
	        form_data.append('id', orderid);
	        form_data.append('action', 'customer_upload_payment_receipt');
	        jQuery.ajax({
			    data: form_data,
			    type: 'POST',
			    contentType: false,
	            processData: false,
			    url:woocommerce_params.ajax_url,
			    success: function(data) { //alert(data);
			        if(data){
			        	jQuery('.paysuccessmsg'+orderid).show().delay(2000).fadeOut(300);
			        	setTimeout(function(){
			        		location.reload(true);
			        	}, 500);
			        
			        }  else {
			        	jQuery('.payerrormsg'+orderid).html('Something went wrong.');
			            jQuery('.payerrormsg'+orderid).show().delay(2000).fadeOut(300);
			        }

			        jQuery('.waitmsgmodal').modal('hide');
			        jQuery('#csdoc'+orderid).removeClass('linkdisabled');
			        setTimeout(function(){
		        		location.reload(true);
		        	}, 2000);
			    }
			});
	    }

	}
}

/*window.onunload = function () {
    jQuery.ajax({
        data: 'action=destroy_current_session',
        type: 'POST',
        url:woocommerce_params.ajax_url,
        success: function(data) { 
        }
    });
}*/


