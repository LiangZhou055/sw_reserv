<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Device;
use App\Models\Customer;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FullCalenderController;
use App\Services\StoreContext;

class APIController extends Controller
{
    public function listByStore($storeCode, $apiKey)
    {
        // Explicitly apply store context by route parameter first.
        $applied = StoreContext::applyForStore(strtolower(trim((string) $storeCode)));
        if (!$applied) {
            abort(404, 'Store not found or inactive');
        }

        return $this->list($apiKey, strtolower(trim((string) $storeCode)));
    }
    
     
    public function list($sn, $storeCode = null)
    {
        if ($this->validateDeivce($sn)) {
            return view('fullcalender', [
                'storeCode' => $storeCode ?? config('stores.default_store'),
                'deviceSn' => $sn,
            ]);
        }
        else
        {
            // Handle the parameter here
            $device = Device::Where('sn', $sn)->first();

            $new_device = false;
            if ($device == null) {
                $new_device = true;
            }

            return view('register', ['sn' => $sn, 'new_device' => $new_device]);
        }        
    }

    private function validateDeivce($sn)
    {
        $device = Device::Where('sn', $sn)->first();

        if ($device && $device->status == 1) {
            // Device with the given serial number exists and status is 1
            return true;
        } else {
            // Device does not exist or status is not 1
            return false;
        }

    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'sn' => ['required', 'string', 'max:50'],
        ]); 

        $sn = $request->sn;
        
        $device = Device::Where('sn', $sn)->first();

        if (!$device) {
            Device::create($data);            
            $notify[] = ['success', 'Register success, please waiting for approval'];
        }
        else{
            if ($device->status == 1) {                
                return redirect()->route('dynamic.api', ['sn' => $sn]);
            }
            else{
                $notify[] = ['success', 'Please waiting for approval'];
            }
        }
        
        return back()->withNotify($notify);
    }

}
