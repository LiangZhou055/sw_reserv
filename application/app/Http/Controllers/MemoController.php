<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;

class MemoController extends Controller
{
    //
    public function save(Request $request)
    { 
        $response = array();
        if ($request->memo_date == ""){
            $response["status"] = false;
            $response["message"] = "Please select the date";
            return json_encode($response);
        }

        $memo =  Memo::where('memo_date', $request->memo_date)->first();

        if ($memo){
            $result = $memo->update([
                'memo_text' => $request->memo_text,
            ]);
        }
        else{
            $result = Memo::create([
                'memo_date' => $request->memo_date,
                'memo_text' => $request->memo_text,
            ]);  
        }
        
        $response["status"] = $result;
        if($result) {
            $response["message"] = "Update memo success";
        }
        else{                    
            $response["message"] = "Failed in memo update";
        }

        return json_encode($response);
    }

    public function get(Request $request)
    { 
        $memo =  Memo::where('memo_date', $request->memo_date)->first();

        if ($memo){
            return $memo->memo_text;
        }
        else{
            return "";
        }
    }
}
