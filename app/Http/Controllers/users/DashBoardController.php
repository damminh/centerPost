<?php

namespace App\Http\Controllers\users;

use Illuminate\Http\Request;
use App\Utils\BasicAuth;
use App\Models\Member;
use Carbon\Carbon;

class DashBoardController extends Controller
{
    public function report(Request $request)
    {
        $from_date = new Carbon($request->input('from_date'));
        $to_date = new Carbon($request->input('to_date'));
        $user = BasicAuth::getInstance()->getModel();
        $data = Member::where('user_id', $user->id)
            ->with('posts', function ($q) {
                $q->whereDate('created', '>=', $from_date->toDateTimeString())
                    ->whereDate('created', '<=', $to_date->toDateTimeString())
                    ->with('histories');
            })->get();
      
        foreach($data as $item) {
            $count_post_deleted = 0;
            $count_post_main = 0;
            $count_update = 0;
            foreach($item['posts'] as $i) {
                if($i['is_main'] == 1) {
                    $count_post_main++;
                    $count_update += count($i['histories']);
                }
                else if($i['is_main'] == 2) {
                    $count_post_deleted++;
                }
            }
            $item['count_post_delete'] = $count_post_deleted;
            $item['count_post_main'] = $count_post_main;
            $item['count_update'] = $count_update;
        }
        return response()->json($data, 200);
    }
}
