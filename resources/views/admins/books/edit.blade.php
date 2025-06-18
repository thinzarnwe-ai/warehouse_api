@extends('admins.layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">

    <div class="py-8 px-4 sm:px-10 border-cyan-600 ">
        <div class="mt-4 sm:mt-0 sm:flex-none">
            <a href="{{ url()->previous()}}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-cyan-600 px-4 py-2 text-sm font-bold text-white shadow-md hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 sm:w-auto hover:shadow-xl" ><< Back</a>
        </div>
        <div class="mt-4 flex flex-col px-4 sm:px-6 lg:px-8">
            <div class="-my-2 -mx-4 sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle">
                <form class="space-y-4"  method="post" id="formData" action="{{ url("/admins/books/$book->id") }}" enctype= "multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <fieldset class="border rounded-md border-cyan-600 xl:p-8 p-3 shadow-md bg-white">
                        <legend class="text-lg font-semibold ml-5 px-2">Edit Book</legend>
                        @csrf
                        <div>
                            <div class="mt-2 grid grid-cols-4 gap-4 sm:grid-cols-6">
                               
                                <div class="col-span-4 sm:col-span-3">
                                    <label for="name" class="block text-sm font-medium text-gray-700"> Name</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" name="name" id="name" value="{{$book->bookname}}" autocomplete="name" class="block w-full min-w-0 flex-1 rounded-md shadow  border-cyan-600 focus:border-cyan-700 focus:ring-cyan-700 sm:text-sm" autofocus>
                                    <span class="text-rose-600 error-text "  style="font-size: 13px"></span>
                                    </div>
                                </div>  
                                <div class="col-span-4 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1" for="pair">
                                       Content Owner
                                    </label>
                                    <select class="block w-full min-w-0 flex-1 rounded-md shadow  border-cyan-600 focus:border-cyan-700 focus:ring-cyan-700 sm:text-sm" name="co_id_link" id="co_id_link" style="width:100%important;" >
                                        @foreach ($content_owners as $co_owner)                                            
                                            <option value="{{ $co_owner->id }}" {{$co_owner->id==$book->co_id_link?'selected' :''}}>{{ $co_owner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-4 sm:col-span-3">
                                  <label class="block text-sm font-medium text-gray-700 mb-1" for="pair">
                                     Publisher
                                  </label>
                                  <select class="block w-full min-w-0 flex-1 rounded-md shadow  border-cyan-600 focus:border-cyan-700 focus:ring-cyan-700 sm:text-sm" name="publisher_id" id="publisher_id" style="width:100%important;" >                                         
                                      @foreach ($publishers as $publisher)
                                          <option value="{{ $publisher->id }}" {{$publisher->id==$book->publisher_id?'selected' :''}}>{{ $publisher->name }}</option>
                                      @endforeach
                                  </select>
                                </div>
                                <div class="col-span-4 sm:col-span-3">
                                    <label for="file" class="block text-sm font-medium text-gray-700"> Cover Photo</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="file" name="file" id="file" value="{{ $book->cover_photo }}" class="img block w-full min-w-0 flex-1 rounded-md shadow  border-cyan-600 focus:border-cyan-700 focus:ring-cyan-700 sm:text-sm"  set-to="div">
                                    <span class="text-rose-600 error-text "  style="font-size: 13px"></span>
                                    </div>
                                    <div class="col-4">
                                        <img src="{{asset("storage/uploads/coverphoto/$book->cover_photo")}}" width="150px;" id="div" alt="">
                                    </div>
                                </div>  
                            </div>
                        </div>
                    </fieldset>

                  <div class="mt-5 gap-4 sm:flex">
                        <button type="submit"  class="mt-2 sm:mt-0 inline-flex items-center justify-center rounded-md border border-transparent bg-cyan-600 px-4 py-2 text-sm font-bold text-white shadow-md hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 sm:w-auto mr-2" id="btn-create" > Save</button>
                        <button type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-sm font-bold text-white shadow-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 sm:w-auto mr-2" onclick="closeModal('#userModal')">Cancel</button>

                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript">
    $(document).ready(function() {
        new TomSelect('#co_id_link');
        new TomSelect('#publisher_id');
    });
    function readURL(input) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();
          var div_id  = $(input).attr('set-to');
          console.log(div_id);
          reader.onload = function (e) {
              $('#'+div_id).attr('src','');
              $('#'+div_id).attr('src', e.target.result);
 
          }
          reader.readAsDataURL(input.files[0]);
      }
  }
  $(".img").change(function(){
      readURL(this);
  });
</script>


@endsection
