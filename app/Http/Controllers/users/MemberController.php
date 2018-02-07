<?php

namespace App\Http\Controllers\users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Utils\BasicAuth;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = BasicAuth::getInstance()->getModel();
        $limit = $request->input('limit', null);
        $search_text = $request->input('search', null);
        $query = Member::where('user_id', $user->id)
            ->when($search_text, function ($q) use ($search_text) {
                return $q->where('username', 'like', '%' . $search_text . '%')
                    ->orWhere('name', 'like', '%' . $search_text . '%');
            });
        if ($limit) {
            $data = $query->paginate($limit);
        } else {
            $data = $query->get();
        }
        foreach($data as $item) {
            $item['itemName'] = $item['name'];
        }
        return response()->json($data, 200);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = $request->get('user');
            $member = new Member();
            $member->username = $request->input('username');
            $member->user_id = $user->id;
            $member->password = Hash::make($request->input('password'));
            $member->name = $request->input('name');
            $member->phone = $request->input('phone');
            $member->description = $request->input('description', null);
            $member->token = str_random(128);
            $member->save();
            return response()->json($member, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'error'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $member = Member::find($id);
        $member->username = $request->input('username');
        $reset = $request->input('reset');
        if ((int)$reset == 1) {
            $member->password = Hash::make('123456a@');
        }
        $member->name = $request->input('name');
        $member->phone = $request->input('phone');
        $member->description = $request->input('description', null);
        $member->save();
        return response()->json($member, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $member = Member::find($id);
        $member->delete();
        return response()->json($member, 200);
    }
}
