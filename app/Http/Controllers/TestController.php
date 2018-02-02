<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use \Curl\Curl;

class TestController extends Controller
{

    protected $url = 'http://localhost/test_wordpress/wp-json/wp/v2/posts';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $curl = new Curl();
        // $curl->setHeader('Content-Type', 'application/json');
        // $curl->setHeader('Content-Type', 'UTF-8');
        // $curl->setHeader('nonce', 'a3a49a8313' );
        // $curl->setHeader('Authorization', 'Basic ' . base64_encode( 'login@example.com' . ':' . 'password'));
        $data = $curl->post('http://localhost/test_wordpress/wp-json/jwt-auth/v1/token', array(
            "username" => "damminh",
            "password" => "hanhheoheo1"
        ));

        $curl_ = new Curl();
        $curl_->setHeader('Content-Type', 'application/json');
        $curl_->setHeader('Authorization', 'Bearer '.$data->token);
        $data_ = $curl_->post('http://localhost/test_wordpress/wp-json/wp/v2/posts', array(
            "title" => "test post from laravel",
            "status" => "publish",
            "categories" => "10"
        ));
        return response()->json([
            'error' => $curl_->error,
            'reponse' => $curl_->response
        ], 200);
    }

    
    protected function getJson($url)
    {
        $response = file_get_contents($url, false);
        return json_decode( $response );
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
        //
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
        //
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

class WpBlog {
    private $curl;
    private $username;
    private $password;
    private $apiUrl;

    public function __construct(Curl $curl) { // dependency injection to enable mocking in tests
        $this->curl = $curl;

        // You place configuration variables in configuration file.
        // I have read that below is how you retrieve config variables in Laravel but it might be wrong
        $this->username = "damminh";
        $this->password = "hanhheoheo1";
        $this->apiUrl = "http://localhost/test_wordpress/wp-json/wp/v2/";
    }

    public function getLatestPosts($query) {
        $rawPosts = $this->curlGet($this->apiUrl.$query);
        $posts = [];
        foreach($rawPosts as $rawPost) {
            $posts[] = $this->formatPost($rawPost);
        }
        return response()->json($posts);
    }

    private function curlGet($url) {
        $this->curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->curl->setOpt(CURLOPT_HTTPHEADER, ["Authorization: Basic " . base64_encode($this->username . ":" . $this->password)]);
        return json_decode($this->curl->curlGet($url));
    }

    private function formatPost($rawPost) {
        $post = [
            "title" => $this->formatText($rawPost->title->rendered),
            "content" => $this->formatText($rawPost->content->rendered),
            "url" => $rawPost->link,
            "date" => $this->formatDate($rawPost->date)
        ];
        $post["abbr"] = $this->getAbbreviation($post["content"]);
        $post["thumb"] = $this->getThumb($post["content"]);
        $post["categories"] = $this->getPostCategories($rawPost);
        return $post;
    }

    private function getPostCategories($rawPost) {
        $categories = [];
        foreach($rawPost->pure_taxonomies->categories as $category) {
            $categories[] = $category->name;
        }
        return $categories;
    }

    private function getThumb($rawPost) {
        return $rawPost->better_featured_image->media_details->sizes->medium->source_url;
    }

    private function getAbbreviation($text) {
        return substr($text,0,200).'...';
    }

    private function formatText($text) {
        return trim(strip_tags($text));
    }

    private function formatDate($date) {
        return Carbon::parse($date)->formatLocalized('%d %B %Y');
    }
}
