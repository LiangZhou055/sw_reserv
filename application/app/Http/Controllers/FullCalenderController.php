<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Services\StoreContext;
use App\Services\SmsTemplateService;
  
class FullCalenderController extends Controller
{
    private const ORDER_NO_MIN = 10000;
    private const ORDER_NO_MAX = 99999;
    private const ORDER_NO_RETRY_LIMIT = 30;

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(Request $request)
    {
  
        if($request->ajax()) {
            $data = Event::whereDate('start', '>=', $request->start)
                       ->whereDate('end', '<=', $request->end)
                       ->where('status',  '!=', Event::STATUS_DELETE)
                       ->get([
                           'id',
                           'title',
                           'title as name',
                           'created_at',
                           'updated_at',
                           'start',
                           'end',
                           'person_no',
                           'contact_no',
                           'time',
                           'order_no',
                           'status',
                           'sms_status',
                           'source as booking_source',
                           'comments'
                       ]);
                   
            // Modify the title of each event to include person_no
            $data->transform(function ($event) {
                $event->title = $event->title . " (" . $event->person_no . "P)";
                return $event;
            });

            return response()->json($data);
        }
  
        return view('fullcalender', [
            'storeCode' => $request->input('store') ?? config('stores.default_store'),
            'deviceSn' => $request->input('sn'),
        ]);
    }

    
    public function search(Request $request)
    {  
        $response = array();
        $data = Event::where('order_no',  '=', $request->order_no)
                    ->get([
                        'id',
                        'title',
                        'title as name',
                        'start',
                        'end',
                        'person_no',
                        'contact_no',
                        'time',
                        'order_no',
                        'status',
                        'sms_status',
                        'source as booking_source',
                        'comments'
                    ]);
        
        if ($data->first()) {
            // Modify the title of each event to include person_no
            $data->transform(function ($event) {
                $event->title = $event->title . " (" . $event->person_no . "P)";
                return $event;
            });
            $response["status"] = true;
            $response["message"] = "Find the record";
            $response["event"] = $data->first();
        }
        else {
            $response["status"] = false;
            $response["message"] = "Can not find the record";
        }
        
        return json_encode($response);
    }
 
    public function getCalendar()
    {
        return view('fullcalender', [
            'storeCode' => config('stores.default_store'),
            'deviceSn' => null,
        ]);
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function ajax(Request $request)
    { 
        $response = array();

        switch ($request->type) {
           case 'add':
              $start = $request->start;

              $nextOrderNo = $this->generateUniqueOrderNo();

              $minute = $request->minute == 0 ? "00" : $request->minute;
              $slotTime = $request->hour.":".$minute;

              $payload = [
                  'title' => $request->title,
                  'start' => $start,
                  'end' => $start,
                  'order_no' => $nextOrderNo,
                  'comments' => $request->comments,
                  'contact_no' => $request->contact_no,
                  'person_no' => $request->person_no,
                  'status' => Event::STATUS_WAITING,
                  'sms_status' => Event::SMS_STATUS_IDLE,
                  'time' => $slotTime,
              ];

              if (Schema::hasColumn('events', 'source')) {
                  $payload['source'] = Event::SOURCE_STORE;
              }

              $result = Event::create($payload);
                
              $response["status"] = $result;
              if($result) {
                  $response["message"] = "Add success";
              }
              else{                    
                  $response["message"] = "Failed in add";
              }
             break;

        
             case 'edit':
                $start = $request->start;
                $minute = $request->minute == 0 ? "00" : $request->minute;
                $update_time = $request->hour.":".$minute;

                $event = Event::find($request->eventId);

                $sms_status = $event->sms_status;

                if ($event->start != $start 
                        || $event->time != $update_time
                        || $event->contact_no != $request->contact_no){
                    $sms_status = Event::SMS_STATUS_IDLE;
                }

                $result = $event->update([
                    'title' => $request->title,
                    'start' => $start,
                    'end' => $start,
                    'comments' => $request->comments,
                    'contact_no' => $request->contact_no,
                    'person_no' => $request->person_no,
                    'time' => $update_time,
                    'sms_status' => $sms_status,
                ]);
                
                $response["status"] = $result;
                if($result) {
                    $response["message"] = "Update success";
                }
                else{                    
                    $response["message"] = "Failed in update";
                }

                break;
  
           case 'dineIn':
                $event = Event::find($request->eventId);
                $result = $event->update([
                    'status' => Event::STATUS_DINE,
                    'sms_status' => Event::SMS_STATUS_VOID,                    
                ]);
                
                $response["status"] = $result;
                if($result) {
                    $response["message"] = "Dine-in success";
                }
                else{                    
                    $response["message"] = "Failed in Dine-in";
                }
                break;

           case 'cancel':
                $event = Event::find($request->eventId);
                $result = $event->update([
                  'status' => Event::STATUS_CANCEL,
                  'sms_status' => Event::SMS_STATUS_CANCEL,
                ]);
                
                $response["status"] = $result;
                if($result) {
                    $response["message"] = "Cancel success";
                }
                else{                    
                    $response["message"] = "Failed in cancel";
                }

                break;
  
           case 'delete':
              $event = Event::find($request->eventId);

              if($request->password != "1234") {
                $response["status"] = false;
                $response["message"] = "password error";
              }
              else {
                $result = $event->update([
                                            'status' => Event::STATUS_DELETE,
                                            'sms_status' => Event::SMS_STATUS_VOID,  
                                        ]);

                $response["status"] = $result;
                if($result) {
                    $response["message"] = "Delete success";
                }
                else{                    
                    $response["message"] = "Failed in delete";
                }
             }
             break;

             
           default:
             # code...
             break;
        }
        return json_encode($response);
    }

    private function generateUniqueOrderNo(): int
    {
        for ($i = 0; $i < self::ORDER_NO_RETRY_LIMIT; $i++) {
            $candidate = random_int(self::ORDER_NO_MIN, self::ORDER_NO_MAX);
            $exists = Event::where('order_no', $candidate)->exists();
            if (!$exists) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Failed to generate unique 5-digit order number');
    }
    
    public function sendWelcomeSMS($event)
    {
        if ($event) { 
            if ($event->sms_time_welcome > 1) 
            {
                return false;
            }

            $messages = app(SmsTemplateService::class)->render(SmsTemplateService::TYPE_WELCOME, $event);

            if ($event->sms_status == Event::SMS_STATUS_IDLE) {
                $event->update([
                    'sms_status' => Event::SMS_STATUS_SENT,
                    'sms_time_welcome' => $event->sms_time_welcome + 1
                ]);
                $ret = SMSController::sendSMS($event, $messages);
            }
            else{
                $ret = true;
            }

            return $ret;
        } else {
            return false;
        }
    }
    
    public function sendCancelSMS($event)
    {
        if ($event) {           
            if ($event->sms_time_cancel > 1) 
            {
                return false;
            } 
            $messages = app(SmsTemplateService::class)->render(SmsTemplateService::TYPE_CANCEL, $event);
            if ($event->sms_status == Event::SMS_STATUS_CANCEL) {               
                $event->update([
                    'sms_status' => Event::SMS_STATUS_CANCELED,
                    'sms_time_cancel' => $event->sms_time_cancel + 1
                ]);
                $ret = SMSController::sendSMS($event, $messages);
            }
            else{
                $ret = true;
            }

            return $ret;
        } else {
            return false;
        }
    }

    public function sendNoticeSMS($event)
    { 
        if ($event) {     
            if ($event->sms_time_notice > 1) 
            {
                return false;
            }

            $messages = app(SmsTemplateService::class)->render(SmsTemplateService::TYPE_NOTICE, $event);

            $ret = false;
            if ($event->sms_status == Event::SMS_STATUS_SENT) {               
                $event->update([
                    'sms_status' => Event::SMS_STATUS_NOTICED,
                    'sms_time_notice' => $event->sms_time_notice + 1
                ]);
                $ret = SMSController::sendSMS($event, $messages);
            }
            return $ret;
        } else {
            return false;
        }        
    }   

    
    public function sendNoticeBatch(string $storeCode)
    { 
        if (! StoreContext::applyForStore($storeCode)) {
            return response('Invalid store code', 404);
        }
        if (!$this->isWorkTime()) return false;

        $today = Date::today();

        $events = Event::whereDate('start', '=', $today)
                    ->where('status', '=', Event::STATUS_WAITING)
                    ->where('sms_status', '=', Event::SMS_STATUS_SENT)
                    ->get();
    
         // Get the current time in Toronto timezone
         $now = Carbon::now('America/Toronto');

         foreach ($events as $event) {
             if ($event->contact_no != "") {
                 $eventTime = Carbon::parse($event->time);
 
                 // Calculate time difference in hours between current time and event time
                 $timeDifference = $now->diffInHours($eventTime);
 
                 // Check if the time difference is less than 1.5 hours
                 if ($timeDifference < 1.5) {
                     $this->sendNoticeSMS($event);
                     sleep(2);
                 }
             }
         }
    }

    public function sendWelcomeBatch(string $storeCode)
    { 
        if (! StoreContext::applyForStore($storeCode)) {
            return response('Invalid store code', 404);
        }
        if (!$this->isWorkTime()) return false;

        //send notice very 3 minutes
        $today = Date::today();

        $events = Event::whereDate('start', '>=', $today)
                    ->where('status', '=', Event::STATUS_WAITING)
                    ->where('sms_status',  '=', Event::SMS_STATUS_IDLE)
                    ->get();
    
        foreach ($events as $event) {
            if ($event->contact_no != "") {
                $this->sendWelcomeSMS($event);
                sleep(2);
            }
        }
    }

    public function sendCancelBatch(string $storeCode)
    { 
        if (! StoreContext::applyForStore($storeCode)) {
            return response('Invalid store code', 404);
        }
        if (!$this->isWorkTime()) return false;

        $today = Date::today();

        $events = Event::whereDate('start', '>=', $today)
                    ->where('status', '=', Event::STATUS_CANCEL)
                    ->where('sms_status',  '=', Event::SMS_STATUS_CANCEL)
                    ->get();
    
        foreach ($events as $event) {
            if ($event->contact_no != "") {
                $this->sendCancelSMS($event);
                sleep(2);
            }
        }
    } 
    
    public function isWorkTime()
    {
        $now = Carbon::now('America/Toronto');

        // Check if the current time is between 10:00 AM and 10:00 PM
        if ($now->hour >= 10 && $now->hour <= 22) 
        {
            return true;
        }
		else if ($now->hour >= 0 && $now->hour <=2) 
        {
            return true;
        }
        else{
            return false;
        }
    }

}