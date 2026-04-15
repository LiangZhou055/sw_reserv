<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Twilio\Rest\Client;
use App\Models\SMSlog;
use Carbon\Carbon;
use App\Models\Event;
use App\Services\StoreContext;
use App\Services\SmsTemplateService;

class SMSController extends Controller
{
    public static function sendSMS($event, $message)
    { 
        //return true;
        $ret = true;

        $contact_no = $event->contact_no;

        $log = new SMSlog();
        $log->to = $contact_no;
        $log->event_id = $event->id;
        $log->initiated_time = Carbon::now();
        $log->message = $message;

        try{               
               
            $sid = config('services.twilio.account_sid');
            $token = config('services.twilio.auth_token');
            $twilioNumber = self::resolveTwilioFromNumber();

            $client = new Client($sid, $token);

            $log->status = 1; 
            $client->messages->create(
                // Phone number to send the message to
                $contact_no,
                array(
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => $twilioNumber,
                    // The body of the text message you'd like to send
                    'body' => $message
                )
            );      
            
        }
        catch(\Throwable $ex)
        {
            $ret = false;   
            $log->status = 2;  
            
            Log::error('Exception sendSMS: ' . $ex->getMessage(). ' phone no: '. $contact_no);
        }
        finally {
            $log->save();
        }
        
        return $ret;
    }

    public function receiveSMS(Request $request)
    {        
        // Get the incoming message body
        $body = $request->input('Body');
        $from = $request->input('From'); // Retrieve the sender's phone number
        $to = $request->input('To'); // Twilio number that received the message

        // Resolve store context by inbound Twilio number for correct sender/profile.
        if (!empty($to)) {
            $storeRow = DB::connection('central')
                ->table('stores')
                ->where('is_active', 1)
                ->where('twilio_from', $to)
                ->first();

            if (!empty($storeRow)) {
                StoreContext::applyForStore((string) $storeRow->code);
            }
        }
        
        $contact = Event::whereDate('start', '=', Carbon::today())  
                        ->where('contact_no', $from) 
                        ->where('sms_status', Event::SMS_STATUS_NOTICED)
                        ->first();

		
        $log = new SMSlog();
        $log->to = $from;
        $log->type = 2;
        $log->event_id = $contact ? (int) $contact->id : 0;
        $log->initiated_time = Carbon::now();
        $log->message = $body;
        $log->save();
		
        //send reply only status = 1
        if ($contact != null) {       

            if (strtolower($body) == '1') {

                $response = app(SmsTemplateService::class)->render(SmsTemplateService::TYPE_CONFIRM, $contact);
				
                $data['sms_status'] = Event::SMS_STATUS_CONFIRM;
                
                $contact->update($data);
                SMSController::sendSMS($contact, $response);

            } else if (strtolower($body) == '2') {
				
                $response = app(SmsTemplateService::class)->render(SmsTemplateService::TYPE_CANCEL, $contact);
				
                $data['status'] = Event::STATUS_CANCEL;
                $data['sms_status'] = Event::SMS_STATUS_CANCELED;
                
                $contact->update($data);
                SMSController::sendSMS($contact, $response);
            }
            
        }  
    }

    private static function resolveTwilioFromNumber(): string
    {
        $fallback = (string) config('services.twilio.from');
        $storeCode = app()->bound('store.code') ? (string) app('store.code') : '';
        if ($storeCode === '') {
            return $fallback;
        }

        $store = DB::connection('central')
            ->table('stores')
            ->whereRaw('LOWER(code) = ?', [strtolower($storeCode)])
            ->first();

        return (string) (!empty($store->twilio_from) ? $store->twilio_from : $fallback);
    }
}
