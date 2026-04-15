<div class="dashboard_container">
    <div style="display:flex">
        <div style="flex:1;padding-left: 10px;">
            <img src="{{ asset('assets/images/logo.png') }}" style="height:120px; width:auto;">
         </div>
        <div style="flex:6;color:#be2727; text-align:center">
                <span class="my-3" style="font-size:3rem">
                
                Next: 
                @if($qryQueue->min('id') != 0) 
                    A{{ $qryQueue->min('id') }}
                @endif
                </span><br>
                <span class="my-3" style="font-size:2rem">Current Queue Customers: {{ $qryQueue->count() }}</span>
        </div> 

        <div style="flex:1; text-align:center; padding-top:80px;">
            <button class="btn--info text--light border-0 px-1 py-2 rounded ms-2" data-bs-toggle="modal" style="width: 150px" data-bs-target="#addQueue"><i class="las la-plus"></i> @lang('Add Queue')</button>
        </div>  
    </div>           
</div>

<div class="container-fluid p-0">
	    <div class="row">
	 		<div class="col-lg-12 p-1">
	            <div class="rounded_box">					
	                <div class="responsive-table">
		                <table class="m-0 text-center table--light">
		                    <thead>
		                        <tr>
		                            <th>@lang('Queue #')</th>
		                            <th>@lang('Name')</th>
		                            <th>@lang('Phone')</th>
		                            <th>@lang('Status')</th>
		                            <th>@lang('Waiting')</th>
		                            <th>@lang('Dine')</th>
		                            <th>@lang('Action')</th>
								</tr>
		                    </thead>
		                    @forelse($contacts as $contact)
			                    <tr class="@if($loop->even) table-light @endif">
			                    	<td data-label="@lang('A')">
									A{{__($contact->id)}}
				                    </td>

									<td data-label="@lang('Name')">
				                    	{{__($contact->name)}} ({{__($contact->person_no)}}P)
				                    </td>

									<td data-label="@lang('Phone #')">
				                    	{{__($contact->contact_no)}}
				                    </td>
				                  
				                    <td data-label="@lang('Status')">
				                    	@if($contact->status == \App\Models\Customer::STATUS_WAITING)
				                    		<span class="badge badge--success">@lang('Waiting')</span>
										@elseif($contact->status == \App\Models\Customer::STATUS_DINE)
											<span class="badge badge--warning">@lang('Dine in')</span>
										@else($contact->status == \App\Models\Customer::STATUS_CANCEL)
				                    		<span class="badge badge--danger">@lang('Cancel')</span>
				                    	@endif
				                    </td>
									
									<td data-label="@lang('Waiting')">			                    
											@php
											if($contact->status == \App\Models\Customer::STATUS_WAITING) {
												$timeDifference = $contact->created_at->diff(now());
											}
											else{
												$timeDifference = $contact->created_at->diff($contact->updated_at);
											}
											@endphp

											@if($timeDifference->h > 0)
												{{ $timeDifference->format('%h hr %i min') }}
											@else
												{{ $timeDifference->format('%i min') }}
											@endif
				                    </td>

									<td data-label="@lang('Dine')">
										@if($contact->status == \App\Models\Customer::STATUS_DINE)											                    
											{{ $contact->updated_at->format('H:i') }}
										@endif
				                    </td>

									<td data-label="@lang('Action')" style="text-align:left">									
										@if($contact->status == \App\Models\Customer::STATUS_WAITING)
											<a class="btn--primary text--light dine-in" data-bs-toggle="modal" data-bs-target="#dineIn" href="javascript:void(0)" 
												data-id="{{$contact->id}}" 
												data-name="{{$contact->name}}" 
												data-person_no="{{$contact->person_no}}">Dine in</a> &nbsp; &nbsp;
										@endif		

										@if($contact->status == \App\Models\Customer::STATUS_WAITING)	
											<a class="btn--danger text--light delete" data-bs-toggle="modal" data-bs-target="#delete" href="javascript:void(0)" 
                                            data-id="{{$contact->id}}"
											data-name="{{$contact->name}}" >
												Cancel
											</a>&nbsp; &nbsp;
										@endif

										@php
										    $sentDiff = $contact->updated_at->diffForHumans(now());											
										@endphp		

										@if($contact->contact_no != ''  && $contact->status != \App\Models\Customer::STATUS_CANCEL )
										  @if ($contact->message_sent == \App\Models\Customer::SMS_STATUS_IDLE)
											<a class="btn--info text--light contact" data-bs-toggle="modal" data-bs-target="#sendNotice" href="javascript:void(0)" 
												data-id="{{$contact->id}}" 
												data-name="{{$contact->name}}" 
												data-person_no="{{$contact->person_no}}" 
												data-contact_no="{{$contact->contact_no}}">Send Notice</a>&nbsp; &nbsp;
										  @elseif ($contact->message_sent == \App\Models\Customer::SMS_STATUS_SENT)	
											<span class="text--dark">Notice Sent, {{ $sentDiff }}</span>											
										  @elseif ($contact->message_sent == \App\Models\Customer::SMS_STATUS_CONFIRM)
											@if($contact->status == \App\Models\Customer::STATUS_DINE)
											<span class="text--dark">Customer confirm</span>	
											@else
											<span class="text--dark">Customer confirm, {{ $sentDiff }}</span>
											@endif
										  @elseif ($contact->message_sent == \App\Models\Customer::SMS_STATUS_CANCEL)
											<span class="text--dark">***Customer cancel, {{ $sentDiff }}</span>
										  @endif
										@endif													
				                    </td>
			                    </tr>
			                @empty
			                	<tr>
			                		<td class="text-muted text-center" colspan="100%">@lang('No Data Found')</td>
			                	</tr>
			                @endforelse
		                </table>
	            	</div>
	            </div>
	        </div>
	    </div>
	</div>