<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
  
    public function index(Request $request)
    {
        $limit      = $request->limit;
        $offset     = $request->offset;
        $result     = Book::offset($offset)->limit($limit)->get();
        
        return response()->json([
            'success' => 1,
            'code' => 200,
            'meta' => [
                'method' => 'GET',
                'endpoint' => request()->path(),
                "limit"=> $limit,
                "offset"=> $offset,
                "total"=> $result->count()
            ],
            'data' => [$result],
            'errors' => [],
        ], 200);
    }

   
    public function store(Request $request)
    {
        //
    }

    
    public function show($id)
    {
        //
    }

   
    public function update(Request $request, $id)
    {
        //
    }

   
    public function destroy($id)
    {
        //
    }
}
