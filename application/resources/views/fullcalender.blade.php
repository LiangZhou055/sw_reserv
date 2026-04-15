<?php 
  $version = "V3.20";
?>
@php
  $orderPrefix = \App\Services\StoreContext::getRestPrefix();
  $restName = \App\Services\StoreContext::getRestName();
  $storeCode = $storeCode ?? config('stores.default_store');
  $deviceSn = $deviceSn ?? null;
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>SMS Reservation System {{ $version }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.2.19/build/css/intlTelInput.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/fontawesome.min.css" integrity="sha512-d0olNN35C6VLiulAobxYHZiXJmq+vl+BGIgAxQtD5+kqudro/xNMvv2yIHAciGHpExsIbKX3iLg+0B6d0k4+ZA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{asset('assets/global/sb/google-font.css')}}"> 
  
    <style>
        #eventDetails {
            background-color: black; /*#f5f5f5;*/
            padding: 10px;
            border: 1px solid #ddd;
            color:white;
            margin-top: 20px;
            height: calc(100vh - 80px);
        }
        .btn-right{
          margin-right: 10px;
        }

        .fc-widget-header{
          background-color: #edede5;
        }
        .scrollable {
          overflow-y: auto;
        }

        body{    
          background: black;
        }

        .fc-day-number{
          color:white;
        }

        .fc-today .fc-day-number{
          color:black;
        }
        .fc-toolbar .fc-left {
            color: white;
        }

        .btn-transparent {
          background-color: transparent;
          color: white;
          border:1px solid white;
        }

        .event-cell {
          background-color: transparent;
          color: white;
          border:1px solid white;
          /*margin: 5px 5px 5px 5px;*/
          margin:0px;
          padding:5px;
          border-radius: 5px;
          overflow: hidden; 
          min-width:210px; 
          max-width:210px;
          position: relative;
          
        }

        .customer-source-star {
          position: absolute;
          top: 4px;
          right: 6px;
          color: #ffd54a;
          font-size: 18px;
          line-height: 1;
          pointer-events: none;
        }

        .event-cell-wrapper{
          margin:5px;
          max-width:210px;
	  margin-right:35px;
        }

        .modal-content {
   
            background-color: black;
            border: 1px solid white;
            border-radius: 0.3rem;
            outline: 1;
            color: white;
        }

        .close{
          color:white; 
          font-size:30px;
          padding:0px; 
          margin:0px
        }

        #dineInEventForm, #cancelEventForm, #deleteEventForm{
          font-size:20px;
        }

        .btn-check:focus+.btn-primary, .btn-primary:focus {
            color: #fff;
            background-color: black;
            border-color: white;
            box-shadow: none;
        }
        
        .eventTimeBox{
          margin: 5px 2px 5px 5px;
        }

        .no-margin{
          margin:0px;
        }
        .fc-toolbar h2 {
          font-size:16px;
        }
        .event-cancel{
          background-color:#b96161;
        }
        .event-dine{
          background-color:green;
        }

        .eventNumber{
          font-size:16px;
          font-weight:600px;
        }

        .txt-dinein{
          color:yellow; 
          font-weight:600
        }
        .txt-confirm{
          color:#00ff22; 
          font-weight:600
        }
        .txt-person, .txt-time{
          font-size:20px;
        }

        /* right panel*/
        .offcanvas  {
            color: white;
        }

        .offcanvas-body{
          font-size:20px;
        }
        
        .offcanvas-end {
          width: 300px;
        }

        .right-panel {
          background-color: black; 
          border-left: 1px solid white;
          box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.75); 
        }
        .memo-box-ext{
          padding-left: 5px; 
          max-width:280px;
          height:150px;
          overflow-y: scroll; 
          border: 1px solid gray
        }

        .fc-basic-view .fc-body .fc-row {
          min-height: 2em;
        }

        .btn-green{
          background-color:green; 
          border: 1px solid green;
        }
        
        .btn-green:focus, .btn-green:hover{
          background-color:green; 
          border: 1px solid green;
        }

        .sb-control {
          background-color: black;
          color: white;
          border-top-left-radius: 0; /* Remove border radius from the top left */
          border-bottom-left-radius: 0; /* Remove border radius from the bottom left */
          border-left: none; /* Remove left border */
          padding-left:0px;
        }

        .sb-control:focus {
          background-color: black;
          color: white;
          border-top-left-radius: 0; /* Remove border radius from the top left */
          border-bottom-left-radius: 0; /* Remove border radius from the bottom left */
          border-left: none; /* Remove left border */
        }
        
        .sb-control-right {
          background-color: black;
          color: white;
          border-top-right-radius: 0; /* Remove border radius from the top left */
          border-bottom-right-radius: 0; /* Remove border radius from the bottom left */
          border-right: none; /* Remove left border */
          padding-right: 1px;
        }

        .sb-control-right:focus {
          background-color: black;
          color: white;
          border-top-right-radius: 0; /* Remove border radius from the top left */
          border-bottom-right-radius: 0; /* Remove border radius from the bottom left */
          border-right: none; /* Remove left border */
        }
		
		.fc-toolbar.fc-header-toolbar {
			margin-bottom: 0.3em;
		}

    </style>
</head>
<body>

<button class="btn btn-primary" id="btnRightOff" style="display:none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"></button>

<div class="offcanvas offcanvas-end right-panel" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 id="offcanvasRightLabel">Information</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="row">
            <h5 id="detail_no"></h5>
            <hr>
            <div class="form-group d-flex align-items-center pb-2">
            <span class="material-symbols-outlined">person</span><span id="detail_person"></span></div>

            <div class="form-group d-flex align-items-center pb-2">
            <span class="material-symbols-outlined">schedule</span><span id="detail_time"></span></div>

            <div class="form-group d-flex align-items-center pb-2">
            <span class="material-symbols-outlined">call</span><span id="detail_contact_no"></span></div>

            <div class="form-group d-flex align-items-center pb-3">
            <span class="material-symbols-outlined">comment</span><span id="detail_comments"></span></div>

            <div class="form-group align-items-center pb-3" style="font-size:14px;">
            Created at:<div id="createdTime"></div></div>
            <hr>
            <div class="row justify-content-between">
              <div class="col-auto">
                <div class="form-group d-flex align-items-center pb-3">
                  <button type="button" class="btn btn-primary" id="btnDineIn" data-event-id="">
                    <div class="form-group d-flex align-items-center">
                      <span class="material-symbols-outlined" style="font-size:20px">restaurant</span>
                      Dine in
                    </div>
                  </button>
                </div>
              </div>
              <div class="col-auto">
                <div class="form-group d-flex align-items-center pb-3">
                  <button type="button" class="btn btn-warning" id="btnEdit" data-event-id="">
                    <div class="form-group d-flex align-items-center">
                      <span class="material-symbols-outlined" style="font-size:20px">edit</span>
                      Modify
                    </div>
                  </button>
                </div>
              </div>
            </div>


            <hr>              
            <div class="row justify-content-between pt-5">
              <div class="col-auto">
                <div class="form-group d-flex align-items-center pb-3">
                    <button type="button" class="btn pop-cancelEvent btn-danger" id="btnCancel" data-event-id="">
                          <div class="form-group d-flex align-items-center">
                              <span class="material-symbols-outlined" style="font-size:20px">
                              block
                              </span>
                            Cancel
                          </div>
                  </button>
                </div>
              </div>
              <div class="col-auto">
                <div class="form-group d-flex align-items-center pb-3">
                    <button type="button" class="btn btn-danger" id="btnDelete" data-event-id="">
                          <div class="form-group d-flex align-items-center">
                              <span class="material-symbols-outlined" style="font-size:20px">
                              delete
                              </span>
                            Delete
                          </div>
                  </button>
                </div>
              </div>
            </div>
      </div>
  </div>
</div>

<div class="mt-3">
    <div class="row no-margin">
        <div class="col-md-3 left-side" style="height:calc(100vh-10px); overflow-y: scroll; overflow-x: hidden;">
            <div id='calendar'></div>
            <div class="mt-2" id='left_box' style="border:1px solid white;">
              <div class="row">
                <div class="row pt-3" style="color:white; margin:0px;">                  
                  <div  style="padding-left:10px;font-weight:600;" id="morningInfor">&nbsp;</div>
                  <div  style="padding-left:10px;font-weight:600;" id="afternoonInfor">&nbsp;</div>        
                </div>

                <div class="row pt-3" style="color:white; margin:0px;">
                  <div class="col-12">                     
                    <div class="pt-2 memo-box-ext" id="memo_text_box">
                    <textarea class="form-control memo-box" 
                        style="background-color:black; color:white;border:none; outline:none; resize:none; min-height:200px; height:auto; padding-left:0px;" 
                        row=1 
                        readonly>
                      </textarea>
                  </div>

                    <div class="form-group d-flex justify-content-end pb-1 pt-2">
                      <button type="button" class="btn btn-primary btn-green" id="btnMemo" data-event-id="">
                            <div class="form-group d-flex align-items-center">
                              <span class="material-symbols-outlined" style="font-size:20px">edit</span>
                              Edit memo
                            </div>
                      </button>
                    </div>
                  </div>

                  <div class="col-12" style="margin:0px; padding-top:1px; padding:0px 0px 3px 5px; color: #ccc; font-size:12px">
                  &copySayweb.ca {{ $version }}
                  </div>
                </div>
              </div>              
            </div>
        </div>
        <div class="col-md-9" style="padding-left:1px">
            <div class="pb-1 row">
                <div class="col-md-6 d-flex">
                  <button type="button" class="btn btn-primary" id="addEventButton" data-toggle="modal" data-target="#addEventModal">
                      <div class="form-group d-flex align-items-center">
                          <span class="material-symbols-outlined">
                            person_add
                          </span>
                        <label for="person_no">{{ __('Add Reservation') }} ({{ $restName }})</label>
                    </div>  
                  </button>    
                   
                </div>
                
                <div class="col-md-6 d-flex align-items-center justify-content-end">
                  <form id="searchForm" class="d-flex align-items-center">
                      <div class="input-group-prepend">
                          <span class="input-group-text sb-control-right" id="basic-addon1">{{ $orderPrefix }}</span>
                      </div>
                      <input type="number" class="form-control sb-control" id="order_no" name="order_no" aria-describedby="basic-addon1">
                      <button type="submit" class="btn btn-primary btn-green" style="margin-left:10px; " id="btnSearch" data-toggle="modal" data-target="#addEventModal">
                          <div class="form-group d-flex align-items-center">
                              <span class="material-symbols-outlined">search</span>
                              <label for="order_no" id="spanSearch">@lang('Search')</label>
                          </div>  
                      </button>     
                      <button type="reset" class="btn btn-danger" id="btnCleanSearch"  style="margin-left:10px;" data-toggle="modal" data-target="#addEventModal">
                          <div class="form-group d-flex align-items-center">
                              <span class="material-symbols-outlined">clear</span>
                              <label for="order_no">@lang('Clean')</label>
                          </div>  
                      </button> 
                  </form>  
            </div>
             
        </div>
        
        <div id="eventDetails" class="scrollable p-1"></div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addEventModalLabel">{{ __('Add New Reservation') }} ({{ $restName }})</h5>
        <button type="button" class="btn close sb-close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addEventForm" data-parsley-validate>
      <div class="modal-body">          
					<input type="hidden" name="type" value="add">		  
          
          <div class="row">
            <div class="col-6">
              <div class="form-group d-flex align-items-center">
                  <span class="material-symbols-outlined">calendar_month</span>
                  <label for="start">Date:</label>
              </div>
            <input type="date" class="form-control ml-2" id="start" name="start" required>       
          </div>

        <div class="col-6">        
          <div class="form-group d-flex align-items-center">
            <span class="material-symbols-outlined">av_timer</span>
            <label for="time">Time:</label>
        </div>
        <div class="row">
            <div class="col-md-6">
                <select name="hour" class="form-control">
                    @foreach ([11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2] as $hour)
                        <option value="{{ $hour }}">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <select name="minute" class="form-control">
                    @for ($i = 0; $i < 60; $i += 15)
                        <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>
    </div>
    
    <div class="form-group pt-3">
      <div class="form-group d-flex align-items-center">
        <span class="material-symbols-outlined">person</span>
        <label for="title">Customer Name:</label>
      </div>
      <input type="text" class="form-control" id="title" name="title" maxlength="30" required>
    </div>        
          
    <div class="row pt-3">
      <div class="col-6">
        <div class="form-group d-flex align-items-center">
              <span class="material-symbols-outlined">group</span>
            <label for="person_no">@lang('How Many Persons')</label>
        </div>
          <input type="number" class="form-control" id="person_no" name="person_no"  
            data-parsley-type="integer" 
            data-parsley-max="100"
            data-parsley-trigger="change"
            maxlength="3"
            required
          >
      </div>

      <div class="col-6">
        <div class="form-group d-flex align-items-center">
              <span class="material-symbols-outlined">
                call
              </span>
          <label class="title">@lang('Phone')</label>
        </div>

        <input type="hidden" name="contact_no" id="contact_no">	
        <input type="tel" class="form-control iti__tel-input" id="phone" name="phone" 
            maxlength="20" required>						
        <p id="output"></p>
      </div>
    </div>

    <div class="form-group pb-3">
      <label class="form-label" for="email">@lang('Comments')</label>
      <input type="text"  class="form-control" name="comments" id="comments" maxlength="80">
    </div>

    <div class="modal-footer text-center">
      <button type="button" class="btn btn-secondary btn-right sb-close" data-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary" id="btnAdd">Add New</button>
    </div>

    </form>
    </div>
    </div>
  </div>
</div> <!-- add modal -->

<!-- edit event model -->
<div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Reservation</h5>
        <button type="button" class="btn close sb-close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editEventForm" data-parsley-validate>
      <div class="modal-body">          
					<input type="hidden" name="type" value="edit">	
          <input type="hidden" class="eventId" name="eventId">     
          <div class="row">
            <div class="col-6">
              <div class="form-group d-flex align-items-center">
                  <span class="material-symbols-outlined">calendar_month</span>
                  <label for="start">Date:</label>
              </div>
            <input type="date" class="form-control ml-2" id="edt_start" name="start" required>       
          </div>

        <div class="col-6">        
          <div class="form-group d-flex align-items-center">
            <span class="material-symbols-outlined">av_timer</span>
            <label for="time">Time:</label>
        </div>
        <div class="row">
            <div class="col-md-6">
                <select name="hour" id="edt_hour" class="form-control">
                    @foreach ([11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22,23, 0, 1, 2] as $hour)
                        <option value="{{ $hour }}">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <select name="minute" id="edt_minute" class="form-control">
                    @for ($i = 0; $i < 60; $i += 15)
                        <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>
    </div>
    
    <div class="form-group pt-3">
      <div class="form-group d-flex align-items-center">
        <span class="material-symbols-outlined">person</span>
        <label for="title">Customer Name:</label>
      </div>
      <input type="text" class="form-control" id="edt_title" name="title" maxlength="30" required>
    </div>        
          
    <div class="row pt-3">
      <div class="col-6">
        <div class="form-group d-flex align-items-center">
              <span class="material-symbols-outlined">group</span>
            <label for="person_no">@lang('How Many Persons')</label>
        </div>
          <input type="number" class="form-control" id="edt_person_no" name="person_no"  
            data-parsley-type="integer" 
            data-parsley-max="100"
            data-parsley-trigger="change"
            maxlength="3"
            required
          >
      </div>

      <div class="col-6">
        <div class="form-group d-flex align-items-center">
              <span class="material-symbols-outlined">
                call
              </span>
          <label class="title">@lang('Phone')</label>
        </div>

        <input type="hidden" name="contact_no" id="edt_contact_no">	
        <input type="tel" class="form-control iti__tel-input" id="edt_phone" name="phone" 
            maxlength="20">						
        <p id="edt_output"></p>
      </div>
    </div>

    <div class="form-group pb-3">
      <label class="form-label" for="email">@lang('Comments')</label>
      <input type="text"  class="form-control" name="comments" id="edt_comments" maxlength="80">
    </div>

    <div class="modal-footer text-center">
      <button type="button" class="btn btn-secondary btn-right sb-close" data-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary" id="btnUpdate">Update</button>
    </div>

    </form>
    </div>
    </div>
  </div>
</div> <!-- end edit modal -->


<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelConfirmModal" tabindex="-1" role="dialog" aria-labelledby="cancelConfirmModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog cancelForm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Cancel?</h5>
        <button type="button" class="btn close sb-close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="cancelEventForm">  
      <div class="modal-body">
          <input type="hidden" class="eventId" name="eventId">     
          Are you sure cancel this reservation?
          
          <div class="form-group d-flex align-items-center pt-3 pb-1">
              <span class="material-symbols-outlined">schedule</span>
              <span class="reserv-order-no"></span>
          </div>
          <div class="form-group d-flex align-items-center pb-3">
              <span class="material-symbols-outlined">person</span>
              <span class="reserv-info"></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary sb-close" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger" id="btnCancelConfirm">Confirm</button>
      </div>
        </form>
    </div>
  </div>
</div>


<!-- Delete Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog deleteForm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete?</h5>
        <button type="button" class="btn close sb-close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="deleteEventForm">   
      <div class="modal-body">
          <input type="hidden" class="eventId" name="eventId">     
          Are you sure delete this reservation?
          
          <div class="form-group d-flex align-items-center pt-3 pb-1">
              <span class="material-symbols-outlined">schedule</span>
              <span class="reserv-order-no"></span>
          </div>
          <div class="form-group d-flex align-items-center pb-3">
              <span class="material-symbols-outlined">person</span>
              <span class="reserv-info"></span>
        </div>  
        <div class="form-group pb-1">
            <div class="form-group d-flex align-items-center mb-1">
            <span class="material-symbols-outlined">
            lock
            </span>
              <label for="title">Passwords:</label>
          </div>
            <input type="password" class="form-control" id="password" name="password" maxlength="10" required>
        </div>        
          
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary sb-close" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger" id="btnDeleteConfirm">Confirm</button>
      </div>
        </form>
    </div>
  </div>
</div>

<!-- dine-in Event Modal -->
<div class="modal fade" id="dineInEventModal" tabindex="-1" role="dialog" aria-labelledby="dineInEventModal" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Customer Dine-in</h5>
        <button type="button" class="btn close  sb-close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <form id="dineInEventForm">     
      <div class="modal-body">
          <input type="hidden" class="eventId" name="eventId">      
          Are you sure you dine-in this reservation?
          
          <div class="form-group d-flex align-items-center pt-3 pb-1">
              <span class="material-symbols-outlined">schedule</span>
              <span class="reserv-order-no"></span>
          </div>

          <div class="form-group d-flex align-items-center pb-3">
              <span class="material-symbols-outlined">person</span>
              <span class="reserv-info"></span>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary sb-close" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="btnDineInConfirm">Confirm</button>
          </div>
        </form>
      </div>
      
    </div>
  </div>
</div>

<!-- edit memo modal -->
<div class="modal fade" id="addMemoModal" tabindex="-1" role="dialog" aria-labelledby="addMemoModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog cancelForm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Memo for <span id="memo_date_text"></span></h5>
        <button type="button" class="btn close sb-close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addMemoForm">  
      <div class="modal-body">
          <input type="hidden" id="memo_date" name="memo_date" value="">     
          <div class="form-group d-flex align-items-center pb-3">
              <textarea class="form-control memo-box" rows=6 name="memo_text" id="memo_text"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary sb-close" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger" id="btnSaveMemo">Save</button>
      </div>
        </form>
    </div>
  </div>
</div>

<!-- JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
.iti__selected-flag {
    height: 60%;
}

body{
  background-size: cover; /* This will make the background image cover the entire body */
  background-repeat: no-repeat; /* This will prevent the background image from repeating */
  background-attachment: fixed; 
}

</style>
<script>

$(document).ready(function () {
   
  var SITEURL = "{{ url('/') }}";
  var STORE_CODE = @json($storeCode);
  var DEVICE_SN = @json($deviceSn);
  var calendar;
  var _selectedDate;

  function calendarAuthSuffix() {
    return '&store=' + encodeURIComponent(STORE_CODE || '') + '&sn=' + encodeURIComponent(DEVICE_SN || '');
  }

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  
  var previousClickedCell = null;

  function setClickCellBackground(cell)
  {
    if (previousClickedCell !== null) {
        var cellDate = previousClickedCell.data('date');
        
        // Get all events rendered on the calendar
        var allEvents = $('#calendar').fullCalendar('clientEvents');

          // Filter events that occur on the clicked cell's date
        var eventsOnCell = allEvents.filter(function(event) {
            return event.start.format('YYYY-MM-DD') === cellDate;
        });
        
        if (eventsOnCell.length != 0) {
          previousClickedCell.css('background-color', 'green'); 
        }
        else{
          previousClickedCell.css('background-color', ''); 
        }
        
    }
    previousClickedCell = cell;
    cell.css('background-color', 'red');
  }

  
  let previousTimeButton = null;
  let previousDayCell = null;
  
  $(document).on('click', '.time-event', function() {
    
    if (previousTimeButton !== null) {
      previousTimeButton.css('background-color', '');
    }
    previousTimeButton = $(this);
    $(this).css('background-color', 'red');
    //$(".event-cell").hide();
    let time = $(this).data("time");
    let timeArray = [11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2];
    
    timeArray.forEach(function(number, index) {
      $(".time-"+number).hide();
    });
    $(".time-"+time).show();
    //console.log($(this).data("time"));
  });


  calendar = $('#calendar').fullCalendar({
    header: {
      right: 'prev,next today',
      left: 'title',
    },
    titleFormat: 'MMM YYYY',
    editable: true,
    events: function(start, end, timezone, callback) {
      $.ajax({
        url: SITEURL + '/fullcalender',
        data: {
          start: start.format('YYYY-MM-DD'),
          end: end.format('YYYY-MM-DD'),
          store: STORE_CODE || '',
          sn: DEVICE_SN || ''
        },
        dataType: 'json',
        success: function(data) {
          callback(data);
        },
        error: function() {
          callback([]);
        }
      });
    },
    displayEventTime: false,
    editable: true,
    selectable: true,
    selectHelper: true, 
    contentHeight: 220,
    dayClick: function(date, jsEvent, view) {
      showEventDetails(date, true);
      previousDayCell = $(this);
      setClickCellBackground(previousDayCell);
      previousTimeButton = null;

      $(".time-event").css("background",'');
    },
    eventClick: function(info) {
      var date = info.start;
      showEventDetails(date, true); 
      setClickCellBackground($(this));
    },    
    eventAfterAllRender: function(view) {    
      var start = $('#calendar').fullCalendar('getView').start;
        var end = $('#calendar').fullCalendar('getView').end;
        
        // Check if _selectedDate falls within the visible range of the calendar
        if (_selectedDate >= start && _selectedDate <= end) {
            showEventDetails(_selectedDate, false);
        }
        //var calendarHeight = 100; //$('.left-side').height() - 70;
       // $('#eventDetails').css('min-height', calendarHeight + 'px');
       // $('#eventDetails').css('max-height', calendarHeight + 'px');

         //$('.fc-day-grid-container').css('max-height','120px');
        //$('#eventDetails').css('height', 'calc(100vh - 80px)');

        
        var leftHeight = $("#calendar").height() +  $("#left_box").height();
        var rightHeight = $("#eventDetails").height();
  
        var delta =  $("#memo_text_box").height() +  rightHeight - leftHeight + 65;

        delta = delta < 50 ? 50 : delta;

        $("#memo_text_box").css("height", delta + "px");
        //console.log(delta);

        if (previousTimeButton != null) previousTimeButton.click();
        if (previousDayCell != null) setClickCellBackground(previousDayCell);
        
    },  
    eventRender: function(event, element, view) {      
        element.css('display', 'none');  
        var eventDate = event.start.format('YYYY-MM-DD');
      
      // Find the cell associated with the event's date 
      var cell = $('.fc-day[data-date="' + eventDate + '"]');
      
      // Set the background color of the cell
      cell.css('background-color', 'green'); // Red background color
    }   

  });

function iconLabelBegin(text)
  {    
    return `<div class="form-group d-flex align-items-center">
                        <span class="material-symbols-outlined">
                        ${text}
                        </span>
                      `;

  }

  function iconLabelEnd()
  {    
    return `</div>`;
  }

  function iconButtonBegin(text)
  {    
    return `<div class="form-group d-flex align-items-center">
                        <span class="material-symbols-outlined" style="font-size:20px">
                        ${text}
                        </span>
                      `;
  }

  function iconButtonEnd()
  {    
    return `</div>`;
  }


  function showEventDetails(date, clearSearch) {
    if (!clearSearch) {
      var search = $("#searchResult");
      var searchKeyword = $("#order_no").val().trim();

      if (searchKeyword != "" && $("#searchResult").length > 0 )  {
        console.log(search);
        return;
      }
    }

    _selectedDate = date;
    var memo_date = moment(date).format('YYYY-MM-DD');
    
    $("#memo_date_text").html(memo_date);    
    $("#memo_date").val(memo_date);

    var eventDetails = $('#eventDetails');
    var events = calendar.fullCalendar('clientEvents', function(event) {
      return moment(date).isSame(event.start, 'day');
    });

    var html = '<h5>List on ' + moment(date).format('MMMM DD, YYYY') + '</h5> ';

    var tableNoBefore3PM = 0;
    var tableNoAfter3PM = 0;
    var personNoBefore3PM = 0;
    var personNoAfter3PM = 0;

    events.forEach(function(event) {
        var eventHour = parseInt(event.time.split(":")[0]); // Extract hour from "HH:mm"
        
        if (eventHour < 15) { // Before 3:00 PM
            tableNoBefore3PM += 1;
            personNoBefore3PM += event.person_no;
        } else { // After 3:00 PM
            tableNoAfter3PM += 1;
            personNoAfter3PM += event.person_no;
        }
    });      

    $("#morningInfor").html( `<div class="d-flex">AM: <span class="material-symbols-outlined">table_bar</span>
                      ${tableNoBefore3PM} &nbsp;
                      <span class="material-symbols-outlined">person</span>
                      ${personNoBefore3PM} </div>`);
    

    $("#afternoonInfor").html( `<div class="d-flex">PM: <span class="material-symbols-outlined">table_bar</span>
                  ${tableNoAfter3PM} &nbsp;
                  <span class="material-symbols-outlined">person</span>
                  ${personNoAfter3PM} </div>`);
	  
    html += '<div class="row no-margin">';

    // Group events by time
    var groupedEvents = {};
    events.forEach(function(event) {
        var time = event.time;
        if (time) {
            if (!groupedEvents[time]) {
                groupedEvents[time] = [];
            }
            groupedEvents[time].push(event);
        }
    });
    
    // Sort keys
    var sortedKeys = Object.keys(groupedEvents).sort();

    // Create a new object with sorted keys
    var sortedGroupedEvents = {};
    sortedKeys.forEach(function(key) {
        sortedGroupedEvents[key] = groupedEvents[key];
    });

    // Iterate over each time slot
    for (var hour in sortedGroupedEvents) {
        var tclass = "time-"+hour.substr(0, 2);

        html += '<hr style="margin-top:5px">';
        //html += '<h6 class="time-event" data-time="'+ hour +'">Time: ' + hour + ':00</h6>'; // Display time slot
        //html += '<h6>Time: ' + hour + '</h6>'; // Display time slot
		
	    var totalPersonThisHour = 0;
        sortedGroupedEvents[hour].forEach(function(event) {
            if (event.person_no) {
                totalPersonThisHour += event.person_no;
            }
        });

        html += '<h6>Time: ' + hour + ' - <span style="font-weight:600;color:yellow">' + totalPersonThisHour + ' Person(s)</span></h6>';    

        sortedGroupedEvents[hour].forEach(function(event) {

          var status = event.status;
          var event_status = "";
          if (status == {{ \App\Models\Event::STATUS_DINE }}) {
            event_status = "event-dine";
          }
          else if (status == {{ \App\Models\Event::STATUS_CANCEL }}){
            event_status = "event-cancel";
          }

          var notice_status = "";
          if (event.contact_no != null && event.sms_status == {{ \App\Models\Event::SMS_STATUS_IDLE }}) {
            notice_status = " <span style='color:red'>*</span>";
          }
          
          if (event.contact_no != null && event.sms_status == {{ \App\Models\Event::SMS_STATUS_SENT }}) {
            notice_status = " <span style='color:green'>*</span>";
          }

          if (event.sms_status == {{ \App\Models\Event::SMS_STATUS_CONFIRM }}) {
            notice_status = " <span style='color:#4dd5f5'>CFM</span>";
          }

          if (status == {{ \App\Models\Event::STATUS_CANCEL }}){
            notice_status = "";
          }

          var source_badge = "";
          if (Number(event.booking_source) === {{ \App\Models\Event::SOURCE_CUSTOMER }}) {
            source_badge = '<span class="material-symbols-outlined customer-source-star" title="Customer reservation">star</span>';
          }

          console.log(event);

            html += '<div class="col-3 event-cell-wrapper ' + tclass +'">';
            html += '<div class="event-cell '+ event_status +'" data-event-id="' + event.id + '" id="event'+ event.id+'">';
            html += source_badge;

            html += '<div class="eventNumber"># {{ $orderPrefix }}' + event.order_no +  '    - <span class="txt-time">' + event.time + '</span>' +  notice_status + '</div>';

            html += '<div class="txt-person">' + iconLabelBegin('person') + ' ' + event.title + iconLabelEnd() + '</div>';

            if (event.comments) {
                html += '<div>' + iconLabelBegin('comment') + ' ' + event.comments + iconLabelEnd() + '</div>';
            }

            html += '</div>';
        html += '</div>';
        });
    }

    html += '</div>';

    eventDetails.html(html);
    getDateMemo(memo_date);
}

  //for search event only

  function showSearchEventDetails(event) {
    if (event == null) return;

    var html = '';
    html += '<div class="row no-margin p-5" id="searchResult">';
          var status = event.status;
          var event_status = "";
          
          var event_status_text = "Waiting";

          if (status == {{ \App\Models\Event::STATUS_DINE }}) {
            event_status = "event-dine";
            event_status_text = "Dine-in";
          }
          else if (status == {{ \App\Models\Event::STATUS_CANCEL }}){
            event_status = "event-cancel";
            event_status_text = "Cancel";
          }
          else if (status == {{ \App\Models\Event::STATUS_DELETE }}){
            event_status_text = "Delete";
          }

          var notice_status = "";
          if (event.contact_no != null && event.sms_status == {{ \App\Models\Event::SMS_STATUS_IDLE }}) {
            notice_status = " <span style='color:red'>*</span>";
          }
          
          if (event.contact_no != null && event.sms_status == {{ \App\Models\Event::SMS_STATUS_SENT }}) {
            notice_status = " <span style='color:green'>*</span>";
          }

          if (event.sms_status == {{ \App\Models\Event::SMS_STATUS_CONFIRM }}) {
            notice_status = " <span style='color:#4dd5f5'>CFM</span>";
          }

          if (status == {{ \App\Models\Event::STATUS_CANCEL }}){
            notice_status = "";
          }

          var source_badge = "";
          if (Number(event.booking_source) === {{ \App\Models\Event::SOURCE_CUSTOMER }}) {
            source_badge = '<span class="material-symbols-outlined customer-source-star" title="Customer reservation">star</span>';
          }

            html += '<div class="col-3 event-cell-wrapper">';
            html += '<div class="event-cell '+ event_status +'" data-event-id="' + event.id + '" id="event'+ event.id+'">';
            html += source_badge;

            html += '<div class="eventNumber" style="font-size:22px"># {{ $orderPrefix }}' + event.order_no  +  notice_status + '</div>';
           
            html += '<div>' + iconLabelBegin('calendar_month') + ' ' + event.start + iconLabelEnd() + '</div>';
            html += '<div>' + iconLabelBegin('schedule') + ' ' + event.time + iconLabelEnd() + '</div>';        
            html += '<div class="txt-person">' + iconLabelBegin('person') + ' ' + event.title + iconLabelEnd() + '</div>';
            html += '<div>' + iconLabelBegin('call') + ' ' + event.contact_no + iconLabelEnd() + '</div>';    
            if (event.comments) {
                html += '<div>' + iconLabelBegin('comment') + ' ' + event.comments + iconLabelEnd() + '</div>';
            }

            html += '<div class="txt-status">' + iconLabelBegin('contact_support') + ' ' + event_status_text + iconLabelEnd() + '</div>';

            html += '</div>';

    html += '</div>';

    $('#eventDetails').html(html);
}

  $(document).on('click', '.event-cell', eventPopUp);
  
  // Cancel button click handler for the delete confirmation modal
  $('.modal').on('click', '.sb-close', function() {
      $('.modal').modal('hide');
      $('body').removeClass('modal-open');
      $('.modal-backdrop').remove();
  });

  function closeModel()
  {
      $('.modal').modal('hide');
      $('body').removeClass('modal-open');
      $('.modal-backdrop').remove();

  }

  function setButtonDisable(id, disable){
    $("#"+id).prop("disabled", disable);
  }

  /****************************** click popup     *************************************/
  function eventPopUp(){
    var eventId = $(this).data('event-id');
    gEventId = eventId;

    var event = calendar.fullCalendar('clientEvents', eventId)[0];
    if(event == null ) 
    {
      return;
    }

    $('.eventId').val(event.id);
    
    let prefix = "{{ $orderPrefix }}";
    let eventInfo = `${event.title} - ${moment(event.start).format('MMMM DD, YYYY')} at ${event.time}`; 
    let detailTime =  `${moment(event.start).format('MMMM DD, YYYY')} at ${event.time}`;
    
    //change to add created at time
    const date = new Date(event.created_at);
    const options = {
        timeZone: 'America/Toronto',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    };

    const createdTime = new Intl.DateTimeFormat('en-CA', options).format(date);       
    $('#createdTime').text(createdTime);   
    //end
	  
    $('.reserv-comments, #detail_comments').text(event.comments);
    $('.reserv-info').text(eventInfo);    
    $('#detail_person').text(event.title);   
    $('#detail_time').text(detailTime);     
    $('.reserv-phone, #detail_contact_no').text(event.contact_no);    
    $('.reserv-order-no, #detail_no').text("#" + prefix + event.order_no);

    $("#btnDineIn").hide();
    $("#btnEdit").hide();
    $("#btnCancel").hide();        
    $("#btnDelete").hide();

    $("#edt_start").val(moment(event.start).format('YYYY-MM-DD'));
    
    $("#edt_contact_no").val(event.contact_no);
    $("#edt_phone").val(event.contact_no);    
    $("#edt_title").val(event.name);
    $("#edt_person_no").val(event.person_no);
    $("#edt_comments").val(event.comments);

    var timeParts = event.time.split(':');

    // Extracting hours and minutes
    if (timeParts.length === 2) {
      var hours = timeParts[0];
      var minutes = timeParts[1] == "00"? 0 : timeParts[1];
      $("#edt_hour").val(hours);
      $("#edt_minute").val(minutes);
    }

    moment.tz.setDefault('America/New_York');
    let eventDate = moment(event.start);
    let yestoday = moment().subtract(1, 'days');

    if (eventDate.isSameOrAfter(yestoday, 'day')) {
      
      $("#btnDelete").show();
      if (event.status == {{ \App\Models\Event::STATUS_DINE }} 
        || event.status == {{ \App\Models\Event::STATUS_CANCEL }}) {
        
      }
      else {             
        $("#btnDineIn").show();
        $("#btnEdit").show();
        $("#btnCancel").show();		  
		  
		  let event_date2 = moment(event.start).format('YYYY-MM-DD');
		  let today = moment().format('YYYY-MM-DD');

		  if (event_date2 === today) {
			  $("#btnDineIn").show();
		  } else {
			  $("#btnDineIn").hide();
		  }
      }
            

      if (event.status == {{ \App\Models\Event::STATUS_DINE }}){        
        $("#btnDelete").hide();
		$("#btnDineIn").hide();
      }
      

    }

    $("#btnRightOff").click();
    return;
  }
   

  /****************************** Add event form submission ****************************/
  let gEventId = null;
  function invokeEventClick(){
    if (gEventId != null){
      $id = $("#event"+gEventId);
      if ($id != null) $id.click();
    }
  }

  // Add Event button click handler
  $(document).on('click', '#addEventButton', function() {
    var selectedDateFormatted = moment(_selectedDate).format('YYYY-MM-DD');
    // Set the formatted date to the #start input field
    $('#start').val(selectedDateFormatted);
    $('#addEventModal').modal('show');
  });

  $('#addEventForm').submit(function(event) {
    event.preventDefault();    
    $(this).parsley().validate();

    // Check if the form is valid
    if (!$(this).parsley().isValid()) {
      return;
    }

    var formData = $(this).serialize();    

    var btn = $("#btnAdd");    
    var buttonText = btn.text();

    btn.text("process...");
    btn.prop('disabled', true);


    $.ajax({
      url: SITEURL + "/fullcalenderAjax",
      type: "POST",
      data: formData + calendarAuthSuffix(),
      success: function(data) {    
        var result = JSON.parse(data);

        $('#calendar').fullCalendar('refetchEvents');
        if (result.status) {
          toastr.success(result.message);          
          closeModel();
        }    
        else {
          toastr.error(result.message);
        }

        $('#addEventModal').modal('hide');
  
        btn.text(buttonText);
        btn.prop('disabled', false);
        $('#addEventForm').trigger("reset");
        $('#contact_no').val("");
      },
      error: function(xhr, status, error) {
        toastr.error('Error adding customer: ' + error);
        btn.text(buttonText);
        btn.prop('disabled', false);
      }
    });
  });

  //Edit click handler
  $(document).on('click', '#btnEdit', function() {    
    invokeEventClick();  
    event.preventDefault();
    $('.reserv-info').text($(this).data('event-info'));
    $('#editEventModal').modal('show');  
  });
  
  $('#editEventForm').submit(function(event) {
    event.preventDefault();    
    $(this).parsley().validate();

    // Check if the form is valid
    if (!$(this).parsley().isValid()) {
      return;
    }

    var formData = $(this).serialize();    

    var btn = $("#btnUpdate");    
    var buttonText = btn.text();

    btn.text("process...");
    btn.prop('disabled', true);


    $.ajax({
      url: SITEURL + "/fullcalenderAjax",
      type: "POST",
      data: formData + calendarAuthSuffix(),
      success: function(data) {    
        var result = JSON.parse(data);

        $('#calendar').fullCalendar('refetchEvents');
        if (result.status) {
          toastr.success(result.message);          
          closeModel();
        }    
        else {
          toastr.error(result.message);
        }
        $('#editEventModal').modal('hide');

        btn.text(buttonText);
        btn.prop('disabled', false);
        $('#editEventForm').trigger("reset");
        $('#edt_contact_no').val("");
      },
      error: function(xhr, status, error) {
        toastr.error('Error edit reservation: ' + error);
        btn.text(buttonText);
        btn.prop('disabled', false);
      }
    });
  });

  // Dine in event button click handler
  $(document).on('click', '#btnDineIn', function() {    
    invokeEventClick();  
    event.preventDefault();
    $('.reserv-info').text($(this).data('event-info'));
    $('#dineInEventModal').modal('show');
  });

  $('#dineInEventForm').submit(function(event) {
    event.preventDefault();
    var formData = $(this).serialize();
    
    var btn = $("#btnDineInConfirm");    
    var buttonText = btn.text();
    btn.text("process...");
    btn.prop('disabled', true);

    $.ajax({
      url: SITEURL + "/fullcalenderAjax",
      type: "POST",
      data: formData + '&type=dineIn' + calendarAuthSuffix(),
      success: function(data) {

        var result = JSON.parse(data);

        $('#calendar').fullCalendar('refetchEvents');
        if (result.status) {
          toastr.success(result.message);          
          closeModel();
        }    
        else {
          toastr.error(result.message);
        }

        $('#dineInEventModal').modal('hide');
        btn.text(buttonText);
        btn.prop('disabled', false);
      },
      error: function(xhr, status, error) {
        toastr.error('Error dine in reservation: ' + error);
        btn.text(buttonText);
        btn.prop('disabled', false);
    
      }
    });
  });

  // Cancel event button click handler
  $(document).on('click', '#btnCancel', function() {    
    invokeEventClick();
    $("#reserv-info").text($(this).data('event-info'));
    $('#cancelConfirmModal').modal('show');
  });

  // Confirm delete event button click handler
  $('#cancelEventForm').submit(function(event) {
    event.preventDefault();
    var btn = $("#btnCancelConfirm");    
    var buttonText = btn.text();
    btn.text("process...");
    btn.prop('disabled', true);

    var formData = $(this).serialize();
    
    $.ajax({
      url: SITEURL + "/fullcalenderAjax",
      type: "POST",
      data: formData + '&type=cancel' + calendarAuthSuffix(),
      success: function(data) {
        var result = JSON.parse(data);

        $('#calendar').fullCalendar('refetchEvents');
        if (result.status) {
          toastr.success(result.message);          
          closeModel();
        }    
        else {
          toastr.error(result.message);
        }
        
        $('#cancelConfirmModal').modal('hide');
        
        btn.text(buttonText);
        btn.prop('disabled', false);
      },
      error: function(xhr, status, error) {
        toastr.error('Error cancel reservation: ' + error);
        
        btn.text(buttonText);
        btn.prop('disabled', false);
      }
    });
});


  // Delete event button click handler
  $(document).on('click', '#btnDelete', function() {    
    invokeEventClick();
    $("#reserv-info").text($(this).data('event-info'));
    $('#deleteConfirmModal').modal('show');
  });

  // Confirm delete event button click handler
  $('#deleteEventForm').submit(function(event) {
    event.preventDefault();
    var btn = $("#btnDeleteConfirm");    
    var buttonText = btn.text();
    btn.text("process...");
    btn.prop('disabled', true);

    var formData = $(this).serialize();
    
    $.ajax({
      url: SITEURL + "/fullcalenderAjax",
      type: "POST",
      data: formData + '&type=delete' + calendarAuthSuffix(),
      success: function(data) {
        var result = JSON.parse(data);

        $('#calendar').fullCalendar('refetchEvents');
        if (result.status) {
          toastr.success(result.message);    
          $("#password").val("");
          closeModel();
        }    
        else {
          toastr.error(result.message);
        }
        
        $('#cancelConfirmModal').modal('hide');
        
        btn.text(buttonText);
        btn.prop('disabled', false);
      },
      error: function(xhr, status, error) {
        toastr.error('Error delete reservation: ' + error);
        
        btn.text(buttonText);
        btn.prop('disabled', false);
      }
    });
});

/*********** search form */
$('#searchForm').submit(function(event) {
    event.preventDefault();
    if ($("#order_no").val().trim() == ""){
      return;
    }

    var btn = $("#spanSearch");    
    var buttonText = btn.text();
    btn.text("process...");
    btn.prop('disabled', true);

    var formData = $(this).serialize();
    
    $.ajax({
      url: SITEURL + "/search",
      type: "POST",
      data: formData + calendarAuthSuffix(),
      success: function(data) {
        var result = JSON.parse(data);
        if (result.status) {
          showSearchEventDetails(result.event);  
        }    
        else {
          toastr.error(result.message);
        }
                
        btn.text(buttonText);
        btn.prop('disabled', false);
      },
      error: function(xhr, status, error) {
        toastr.error('Error in search: ' + error);
        
        btn.text(buttonText);
        btn.prop('disabled', false);
      }
    });
});


/* register add memo */
$(document).on('click', '#btnMemo', function() {    
  //$("#reserv-info").text($(this).data('event-info'));
  $('#addMemoModal').modal('show');
});

$('#addMemoForm').submit(function(event) {
    event.preventDefault();
    var btn = $("#btnSaveMemo");    
    var buttonText = btn.text();
    btn.text("process...");
    btn.prop('disabled', true);

    var formData = $(this).serialize();
    
    $.ajax({
      url: SITEURL + "/saveMemo",
      type: "POST",
      data: formData + calendarAuthSuffix(),
      success: function(data) {
        var result = JSON.parse(data);
        if (result.status) {
          toastr.success(result.message);    
          
          $(".memo-box").html($("#memo_text").val());      
          closeModel();
        }    
        else {
          toastr.error(result.message);
        }
        
        $('#addMemoModal').modal('hide');
        
        btn.text(buttonText);
        btn.prop('disabled', false);
      },
      error: function(xhr, status, error) {
        toastr.error('Error add memo: ' + error);
        
        btn.text(buttonText);
        btn.prop('disabled', false);
      }
    });
});

//get date memo
function getDateMemo(date){    
    $.ajax({
      url: SITEURL + "/getMemo",
      type: "POST",
      data: { memo_date: date, store: STORE_CODE || '', sn: DEVICE_SN || '' },
      success: function(data) {
        $(".memo-box").html(data);
      },
      error: function(xhr, status, error) {
        $(".memo-box").html("");
      }
    });
};

/*****************************end event modal*********************************** */
  function registerAddPhoneInput() {
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
		input.addEventListener('blur', handleChange);
  }

  //register edit phone input

  function registerEditPhoneInput() {
		//check phone validate
		const input = document.querySelector("#edt_phone");
		const output = document.querySelector("#edt_output");

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
					$("#btnUpdate").prop('disabled', false);
					$("#btnUpdate").addClass('bg--success');
					$("#edt_contact_no").val(iti.getNumber());
					
				}else{
					text = "Invalid number - please try again";
					$("#btnUpdate").prop('disabled', true);
					$("#btnUpdate").removeClass('bg--success');
					
					$("#btnUpdate").css('background-color', 'gray');
					$("#edt_contact_no").val("");
				}				
			} else {
          $("#btnUpdate").prop('disabled', false);
          $("#btnUpdate").addClass('bg--success');
          $("#btnUpdate").removeClass('btnDisable');
          $("#edt_contact_no").val("");
			}

			const textNode = document.createTextNode(text);
			output.innerHTML = "";
			output.appendChild(textNode);
		};

		// listen to "keyup", but also "change" to update when the user selects a country
		input.addEventListener('change', handleChange);
		input.addEventListener('blur', handleChange);
  }
  
  //call register
    registerAddPhoneInput();
    registerEditPhoneInput();

    function refresh(){
      $('#calendar').fullCalendar('refetchEvents');

			//refresh every 1 min
			setTimeout(refresh, 60000);
		}

		setTimeout(refresh, 60000);
});

</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="{{asset('assets/dashboard/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/dashboard/js/parsley.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.2.19/build/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone.min.js"></script>
</body>
</html>
