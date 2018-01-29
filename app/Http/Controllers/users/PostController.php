<?php

namespace App\Http\Controllers\users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utils\BasicAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Post;

class PostController extends Controller
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
        $query = Post::where('user_id', $user->id)
            ->where('is_main', 1)
            ->when($search_text, function ($q) use ($search_text) {
                return $q->where('name', 'like', '%' . $search_text . '%')
                    ->orWhere('name', 'like', '%' . $search_text . '%');
            });
        if ($limit) {
            $data = $query->paginate($limit);
        } else {
            $data = $query->get();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = BasicAuth::getInstance()->getModel();
            $ob = new Post();
            $ob->member_id = 0;
            $ob->user_id = $user->id;
            $ob->requirement_id = $request->input('requirement_id');
            $ob_requirement = Requirement::find($request->input('requirement_id'));
            $ob->domain_id = $ob_requirement->domain_id;
            $ob->name = $request->input('name');
            $ob->content = $request->input('content');
            $ob->description = $request->input('description', null);
            $ob->is_main = 1;
            $ob->save();
            return response()->json($ob, 200);
        }
        catch (\Exception $e) {
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
            $ob = Post::find($id);
            $ob->member_id = 0;
            $ob->user_id = $user->id;
            $ob->request_id = $request->input('requirement_id');
            $ob_requirement = Requirement::find('id', $request->input('requirement_id'));
            $ob->domain_id = $ob_requirement->domain_id;
            $ob->name = $request->input('name');
            $ob->content = $request->input('content');
            $ob->description = $request->input('description', null);
            $ob->is_main = 1;
            $ob->save();
            return response()->json($ob, 200);
        }
        catch (\Exception $e) {
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
        //
    }
}
