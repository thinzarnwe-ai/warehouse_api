<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\ContentOwner;
use App\Models\Publisher;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class BookController extends Controller
{
  
    public function index()
    {
        $books              = Book::latest()->paginate(30); 
        // dd($books); 
        $content_owners     = ContentOwner::all();    
        $publishers         = Publisher::all(); 
        return view('admins.books.index',compact('books','content_owners','publishers'));
    }

   public function book_search(Request $request)
   {
  
   }
    public function create()
    {
        //
    }

  
    public function store(Request $request){
            $input = $this->validate($request, [
                'name'              => 'required',
            ]);
            // dd($request->all());
            try{
                DB::beginTransaction();
                $book_uniq_id           = Str::uuid();
                $cover_photo            =saveCoverPhoto($request->file);
                $book                   = new Book();
                $book->book_uniq_id     = $book_uniq_id;
                $book->bookname         = $request->name;
                $book->co_id_link       = $request->co_id_link;
                $book->publisher_id     = $request->publisher_id;
                $book->cover_photo      = $request->has('file')?$cover_photo:'test.png';
                $book->created_timetick = Carbon::now();
                $book->save();
                DB::commit();
                // return redirect()->route('books.index')
                //                 ->with('success','Book created successfully');
                return response()->json([
                    'success' => true,
                    'message' => 'Success Created book',
                    ]);
                }
            catch(Exception $e)
            {
            DB::rollBack();
            return $e->getMessage();
            }
    }

 
    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $book               = Book::where('id',$id)->first();
        $content_owners     = ContentOwner::all();    
        $publishers         = Publisher::all(); 
         return view('admins.books.edit',compact('book','content_owners','publishers'));
    }

 
    public function update(Request $request, $id)
    {
        $input = $this->validate($request, [
            'name'              => 'sometimes|required',
        ]);
        // dd($request->all());
        try{
            DB::beginTransaction();
            $cover_photo            = saveCoverPhoto($request->file);
            $request['cover_photo'] = $request->has('file')?$cover_photo:$book->cover_photo;
            $book                   = Book::find($id);
            $book->update($request->all());            
            DB::commit();
            return redirect('admins/books');
            }
        catch(Exception $e)
        {
        DB::rollBack();
        return $e->getMessage();
        }
    }

 
    public function destroy($id)
    {
        Book::find($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Success Deleted book',
            ]);
    }
}
