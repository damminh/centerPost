<?php

namespace App\Http\Controllers\users;

use Illuminate\Http\Request;
use App\Utils\BasicAuth;
use App\Models\Member;
use Carbon\Carbon;

class DashBoardController extends Controller
{
    public function report(Request $request) {
        $from_date = new Carbon($request->input('from_date'));
        $to_date = new Carbon($request->input('to_date'));
        $user = BasicAuth::getInstance()->getModel();
        $data = Member::where('user_id', $user->id)
                    ->withCount('post', function($q) use($from_date, $to_date) {
                        $q->where('is_main', 1)
                            ->whereDate('created_at', '<=', $to_date->toDateTimeString())
                            ->whereDate('created_at', '>=', $from_date->toDateTimeString());
                    })->get();
        return response()->json($data, 200);
    }
}
