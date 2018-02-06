<?php

namespace App\Http\Controllers\users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Requirement;
use App\Utils\BasicAuth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = BasicAuth::getInstance()->getModel();
        $limit = $request->input('limit', null);
        $search_text = $request->input('search', null);
        $query = Requirement::where('user_id', $user->id)
            ->when($search_text, function ($q) use ($search_text) {
                return $q->where('name', 'like', '%' . $search_text . '%');
            });
        if ($limit) {
            $data = $query->paginate($limit);
        } else {
            $data = $query->get();
        }
        return response()->json($data, 200);
    }

    public function index_one(Request $request, $id) {
        $user = BasicAuth::getInstance()->getModel();
        $data = Requirement::where('id', $id)->where('user_id', $user->id)->first();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = BasicAuth::getInstance()->getModel();
            $ob = new Requirement();
            $ob->user_id = $user->id;
            $ob->name = $request->input('name');
            $ob->start_date = new Carbon($request->input('start_date'));
            $ob->end_date = new Carbon($request->input('end_date'));
            $ob->priority = $request->input('priority');
            $ob->type = $request->input('type');
            $ob->time_repeat = $request->input('time_repeat', null);
            $ob->description = $request->input('description', null);
            $list_member = $request->input('list_member');
            $list_domain = $request->input('list_domain');
            $ob->save();
            foreach($list_member as $item) {
                $ob->members()->attach($item['id']);                
            }
            foreach($list_domain as $item) {
                $ob->domains()->attach($item['id']);
            }
            return response()->json($ob, 200);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'error'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $user = BasicAuth::getInstance()->getModel();
            $ob = Requirement::find($id);
            $ob->name = $request->input('name');
            $ob->start_date = new Carbon($request->input('start_date'));
            $ob->end_date = new Carbon($request->input('end_date'));
            $ob->priority = $request->input('priority');
            $ob->type = $request->input('type');
            $ob->time_repeat = $request->input('time_repeat', null);
            $ob->description = $request->input('description', null);
            $ob->save();
            $list_member = $request->input('list_member');
            $list_domain = $request->input('list_domain');
            $ob->members()->detach();
            $ob->domains()->detach();
            foreach($list_member as $item) {
                $ob->members()->attach($item['id']);
            }
            foreach($list_domain as $item) {
                $ob->domains()->attach($item['id']);
            }
            return response()->json($ob, 200);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $ob = Requirement::find($id);
            $ob->members()->detach();
            $ob->domains()->detach();
            $ob->delete();
            return response()->json($ob, 200);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'error'], 500);
        }
    }
}
