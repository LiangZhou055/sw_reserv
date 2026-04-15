@extends('layouts.dashboard')
@section('panel')
<section class="mt-3" id="contentList">
	@include('partials.list')
</section>

<div class="modal fade" id="sendNotice" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-parsley-validate=""  data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
			<form action="{{route('customer.notice')}}" method="POST">
				@csrf
				<input type="hidden" name="id">
	            <div class="modal-body">
	            	<div class="card">
	            		<div class="card-header bg--lite--violet">
	            			<div class="card-title text-center text--light">@lang('Send Notice')</div>
	            		</div>
		                <div class="card-body">
		                	<div class="mb-3 text-center">
								<div id="customer_info"></div>
								
								<input type="hidden" name="id">				
								<input type="hidden" name="contact_no">
								<input type="hidden" name="name">
							</div>
							<div class="mb-3">
								<label for="name" class="form-label">@lang('Wait Time (mins)') <sup class="text--danger">*</sup></label>
								<input type="number" class="form-control" id="wtime" name="wtime"								 
								value="10" required
								data-parsley-type="integer" 
								data-parsley-trigger="change"
								>
							</div>							
						</div>
	            	</div>
	            </div>

	            <div class="modal_button2">
	                <button type="button" class="" data-bs-dismiss="modal">@lang('Cancel')</button>
	                <button type="submit" class="bg--success btnSubmit">@lang('Submit')</button>
	            </div>
	        </form>
        </div>
    </div>
</div>


<div class="modal fade" id="dineIn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
			<form action="{{route('customer.dinein')}}" method="POST">
				@csrf
				<input type="hidden" name="id">
	            <div class="modal-body">
	            	<div class="card">
	            		<div class="card-header bg--lite--violet">
	            			<div class="card-title text-center text--light">@lang('Dine in')</div>
	            		</div>
		                <div class="card-body text-center">
		                	<div class="mb-3">
								<div id="customer_info"></div>								
								<input type="hidden" name="id">		
							</div>							
						</div>
	            	</div>
	            </div>

	            <div class="modal_button2">
	                <button type="button" class="" data-bs-dismiss="modal">@lang('Cancel')</button>
	                <button type="submit" class="bg--success" id="btnSubmitDine" >@lang('Submit')</button>
	            </div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addQueue" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-parsley-validate="" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
			<form action="{{route('customer.store')}}" method="POST">
				@csrf
	            <div class="modal-body">
	            	<div class="card">
	            		<div class="card-header bg--lite--violet">
	            			<div class="card-title text-center text--light">@lang('Add New Queue')</div>
	            		</div>
		                <div class="card-body">
							<div class="mb-3">
								<label for="name" class="form-label">@lang('Name') <sup class="text--danger">*</sup></label>
								<input type="text" class="form-control" id="name" name="name" required="">
							</div>
							
							<div class="mb-3">
								<label for="person_no" class="form-label">@lang('How Many Persons') <sup class="text--danger">*</sup></label>
								<input type="number" class="form-control" id="person_no" name="person_no"  
									data-parsley-type="integer" 
									data-parsley-max="20"
									data-parsley-trigger="change"
									required
								>
							</div>

							<div class="mb-3">
								<label class="form-label">@lang('Phone')</label><br>

								<input type="hidden" name="contact_no" id="contact_no">	
								<input type="tel" class="form-control" id="phone" name="phone">						
								<p id="output"></p>
							</div>

							<div class="mb-3" style="display:none">
								<label for="name" class="form-label">@lang('Wait Time (mins)') <sup class="text--danger">*</sup></label>
								<input type="number" class="form-control" id="wtime" name="wtime" 
								placeholder="@lang('Enter Wait Time')" value="10">
							</div>				
						</div>
	            	</div>
	            </div>

	            <div class="modal_button2">
	                <button type="button" class="" data-bs-dismiss="modal">@lang('Cancel')</button>
	                <button type="submit" class="bg--success btnSubmit" id="btnSubmitAdd">@lang('Submit')</button>
	            </div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{route('customer.delete')}}" method="POST">
				@csrf
				<input type="hidden" name="id">
				<div class="modal_body2">
					<div class="modal_icon2">
						<i class="las la-trash-alt"></i>
					</div>
					<div class="modal_text2 mt-3">
						<h6>@lang('Are you sure to cancel this customer?')</h6>
						<div id="customer_info_cancel" style="font-size:40px"></div>
					</div>
				</div>
				<div class="modal_button2">
					<button type="button" class="" data-bs-dismiss="modal">@lang('Cancel')</button>
					<button type="submit" class="bg--danger" id="btnSubmitDelete">@lang('Confirm')</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection

@push('scriptpush')
<script>
	(function($){
		"use strict";
		function register_event() {
				$('.contact').on('click', function(){

					var modal = $('#sendNotice');

					var content = "<span style='font-size:30px; font-weight:600'>Queue#: A" +$(this).data('id') 
					content += "<br>Customer: " + $(this).data('name') + " ("+ $(this).data('person_no')  + "P)</span>";

					modal.find('div[id=customer_info]').html(content);
					modal.find('input[name=id]').val($(this).data('id'));
					modal.find('input[name=name]').val($(this).data('name'));
					modal.find('input[name=contact_no]').val($(this).data('contact_no'));
				});

				$('.dine-in').on('click', function(){
					var modal = $('#dineIn');

					modal.find('input[name=id]').val($(this).data('id'));
					var content = "<span style='font-size:30px; font-weight:600'>Queue#: A" +$(this).data('id') 
					content += "<br>Customer: " + $(this).data('name') + " ("+ $(this).data('person_no')  + "P)</span>";

					modal.find('div[id=customer_info]').html(content);
				});


				$('.delete').on('click', function(){
					var modal = $('#delete');
					modal.find('input[name=id]').val($(this).data('id'));	
					var content = $(this).data('name') ;
					modal.find('div[id=customer_info_cancel]').html(content);
					modal.modal('show');
				});

				$('#addQueue').parsley().on('field:validated', function() {
					var ok = $('.parsley-error').length === 0;
				})
				.on('form:submit', function() {
					var submitBtn = $('#addQueue button[type="submit"]');
					submitBtn.prop('disabled', true);
					submitBtn.text('Submitting...'); 
				});

				
				$('#sendNotice').parsley().on('field:validated', function() {
					var ok = $('.parsley-error').length === 0;
				})
				.on('form:submit', function() {
					var submitBtn = $('#sendNotice button[type="submit"]');
					submitBtn.prop('disabled', true);
					submitBtn.text('Submitting...'); 
				});

						
				$('#btnSubmitDelete, #btnSubmitDine').on('click', function(){
					$(this).prop('disabled', true);
					$(this).text('Submitting...');  
					$(this).closest('form').submit();
				});
		}

		function refresh(){
			fetch('{{route('customer.refresh')}}')
            .then(response => response.text())
            .then(data => {
                $('#contentList').html(data);
				register_event();
            })
            .catch(error => console.error('Error:', error));

			//refresh every 1 min
			setTimeout(refresh, 60000);
		}

		setTimeout(refresh, 60000);
		register_event();


		//check phone validate
		const input = document.querySelector("#phone");
		const output = document.querySelector("#output");

		const iti = window.intlTelInput(input, {
			initialCountry: "ca",
			onlyCountries: ["ca","fr","us"],
			nationalMode: true,
			utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.2.19/build/js/utils.js" // just for formatting/placeholders etc
		});

		const handleChange = () => {
			let text = "";
			if (input.value) {
				if (iti.isValidNumber()) {
					//text = "Valid number! Full international format: " + iti.getNumber();
					$("#btnSubmitAdd").prop('disabled', false);
					$("#btnSubmitAdd").addClass('bg--success');
					$("#contact_no").val(iti.getNumber());
					
				}else{
					text = "Invalid number - please try again";
					$("#btnSubmitAdd").prop('disabled', true);
					$("#btnSubmitAdd").removeClass('bg--success');
					
					$("#btnSubmitAdd").css('background-color', 'gray');
					$("#contact_no").val("");
				}				
			} else {
				//text = "Please enter a valid number below";
				$("#btnSubmitAdd").prop('disabled', false);
				$("#btnSubmitAdd").addClass('bg--success');
				$("#btnSubmitAdd").removeClass('btnDisable');
				$("#contact_no").val("");
			}

			const textNode = document.createTextNode(text);
			output.innerHTML = "";
			output.appendChild(textNode);
		};

		// listen to "keyup", but also "change" to update when the user selects a country
		input.addEventListener('change', handleChange);
		input.addEventListener('keyup', handleChange);

	})(jQuery);
	

</script>
@endpush