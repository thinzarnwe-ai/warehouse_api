@extends('admins.layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">

    <div class="py-8 px-4 sm:px-10 border-cyan-600 ">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div class="sm:flex ">
            <h1 class="text-xl font-semibold text-gray-900">Books</h1>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <button type="button" data-action="{{ route('books.store') }}" @click="addUser = true" class="inline-flex items-center justify-center rounded-md border border-transparent bg-cyan-600 p-2 text-sm font-bold text-white shadow-md hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 sm:w-auto" id="openModal">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
           
            </div>
        </div>
        <div class="mt-8 flex flex-col px-4 sm:px-6 lg:px-8">
            <div class="-my-2 -mx-4 sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle">               
                    <table class="min-w-full d-table border-separate border-spacing-y-2" id="user_table">
                        <thead class="bg-cyan-600">
                        <tr class="h-10">
                            <th scope="col" class="border-b border-gray-300  bg-opacity-75 py-2 pl-4 pr-3 text-left text-sm font-semibold text-white backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">#</th>
                            {{-- <th scope="col" class="border-b border-gray-300  bg-opacity-75 py-2 pl-4 pr-3 text-left text-sm font-semibold text-white backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">Branch</th> --}}
                            <th scope="col" class="border-b border-gray-300  bg-opacity-75 py-2 pl-4 pr-3 text-left text-sm font-semibold text-white backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">Book Name</th>
                            <th scope="col" class="border-b border-gray-300  bg-opacity-75 py-2 pl-4 pr-3 text-left text-sm font-semibold text-white backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">Content Owner</th>
                            <th scope="col" class="border-b border-gray-300  bg-opacity-75 py-2 pl-4 pr-3 text-left text-sm font-semibold text-white backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">Publisher</th>                       
                            <th scope="col" class="border-b border-gray-300  bg-opacity-75 py-2 pl-4 pr-3 text-left text-sm font-semibold text-white backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">Created Date</th>                        
                            <th scope="col" class="border-b border-gray-300  bg-opacity-75 py-2 pl-4 pr-3 text-left text-sm font-semibold text-white backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white " id="tbody">
                            @foreach ($books as $book)
                            <tr class="shadow-lg rounded-full ">
                                <td class="border-l-2 rounded-lg border-gray-300 bg-gray-50 bg-opacity-75 py-1.5 p-4 text-left text-sm text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">{{ $books->firstItem()+$loop->index }}.</td>
                                <td class=" border-gray-300 bg-gray-50 bg-opacity-75 py-1.5 p-4 text-left text-sm text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">
                                    {{ $book->bookname}}
                                </td>
                                <td class=" border-gray-300 bg-gray-50 bg-opacity-75 py-1.5 p-4 text-left text-sm text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">{{$book->content_owner->name}}</td>
                                <td class=" border-gray-300 bg-gray-50 bg-opacity-75 py-1.5 p-4 text-left text-sm text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">{{$book->publisher->name}}</td>   
                                <td class=" border-gray-300 bg-gray-50 bg-opacity-75 py-1.5 p-4 text-left text-sm text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">{{formatted_date($book->created_timetick)}}</td>   
                                <td class="border-r-2 rounded-lg border-gray-300 bg-gray-50 bg-opacity-75 py-1.5 p-4 text-left text-sm text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8">
                                    <div class="flex">
                                    <a href="{{url("admins/books/$book->id/edit")}}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-yellow-50 p-2 text-sm font-bold shadow-md hover:bg-yellow-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 sm:w-auto mr-2" >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-yellow-700 hover:text-white">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </a>
                                    
                                    <a id="btn-delete" data-id="{{$book->id}}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-rose-50 p-2 text-sm font-bold shadow-md hover:bg-rose-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 sm:w-auto mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-rose-700 hover:text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        <!-- More people... -->
                        </tbody>
                    </table>
                    <div class="flex justify-center text-xs mt-2">
                        @if($books->total()<10)Total <span class="text-red-600 px-2"> {{$books->total()}} </span>Record
                        @else
                        <span class="mb-2">{!! $books->render() !!}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="hidden relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true" id="bookModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start=" opacity-0"
        x-transition:enter-end=" opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start=" opacity-100"
        x-transition:leave-end=" opacity-0">

        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10">
          <div class="flex overflow-y-auto h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"  x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start=" opacity-0"
          x-transition:enter-end=" opacity-100"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start=" opacity-100"
          x-transition:leave-end=" opacity-0"
          >
            <div class="relative transition duration-300 overflow-y-auto ease-in-out transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl w-full sm:w-3xl sm:max-w-4xl sm:m-10 h-auto sm:p-6">
                <div class="card border border-cyan-700 rounded ">
                    <div class="card-header border-b-2">
                        <div class="flex px-4 py-2 justify-between items-center">
                            <h3 class="font-bold text-cyan-600">Create Book</h3>
                           <button class="text-rose-600 font-extrabold"  onclick="closeModal('#bookModal')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                              </svg>
                           </button>
                        </div>

                    </div>
                    <div class="card-body p-4">
                        <form class="space-y-4"  method="post" id="formData" enctype= "multipart/form-data">
                            @csrf
                            <div>
                                <div class="mt-2 grid grid-cols-4 gap-4 sm:grid-cols-6">
                                    
                                    <div class="col-span-4 sm:col-span-3">
                                        <label for="name" class="block text-sm font-medium text-gray-700"> Name</label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                        <input type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="name" class="block w-full min-w-0 flex-1 rounded-md shadow  border-cyan-600 focus:border-cyan-700 focus:ring-cyan-700 sm:text-sm" autofocus>
                                        <span class="text-rose-600 error-text "  style="font-size: 13px"></span>
                                        </div>
                                    </div>  
                                    <div class="col-span-4 sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1" for="pair">
                                           Content Owner
                                        </label>
                                        <select class="block w-full min-w-0 flex-1 rounded-md shadow  border-cyan-600 focus:border-cyan-700 focus:ring-cyan-700 sm:text-sm" name="co_id_link" id="co_id_link" style="width:100%important;" >
                                            @foreach ($content_owners as $co_owner)                                            
                                                <option value="{{ $co_owner->id }}">{{ $co_owner->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-4 sm:col-span-3">
                                      <label class="block text-sm font-medium text-gray-700 mb-1" for="pair">
                                         Publisher
                                      </label>
                                      <select class="block w-full min-w-0 flex-1 rounded-md shadow  border-cyan-600 focus:border-cyan-700 focus:ring-cyan-700 sm:text-sm" name="publisher_id" id="publisher_id" style="width:100%important;" >                                         
                                          @foreach ($publishers as $publisher)
                                              <option value="{{ $publisher->id }}">{{ $publisher->name }}</option>
                                          @endforeach
                                      </select>
                                    </div>
                                    <div class="col-span-4 sm:col-span-3">
                                        <label for="file" class="block text-sm font-medium text-gray-700"> Cover Photo</label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                        <input type="file" name="file" id="file" value="{{ old('name') }}" class="img block w-full min-w-0 flex-1 rounded-md shadow  border-cyan-600 focus:border-cyan-700 focus:ring-cyan-700 sm:text-sm"  set-to="div">
                                        <span class="text-rose-600 error-text "  style="font-size: 13px"></span>
                                        </div>
                                        <div class="col-4">
                                            <img src="" width="150px;" id="div" alt="">
                                        </div>
                                    </div>  
                                </div>
                            </div>

                          <div class="mt-5 gap-4 sm:flex">
                                <button type="submit"  class="mt-2 sm:mt-0 inline-flex items-center justify-center rounded-md border border-transparent bg-cyan-600 px-4 py-2 text-sm font-bold text-white shadow-md hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 sm:w-auto mr-2" id="btn-create" > Save</button>
                                <button type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-sm font-bold text-white shadow-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 sm:w-auto mr-2" onclick="closeModal('#bookModal')">Cancel</button>

                            </div>
                        </form>

                    </div>
                </div>

              </div>
            </div>
          </div>
</div>
@endsection
@section('script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ asset('/js/book_crud.js') }}"></script>
<script type="text/javascript">
        $(document).ready(function() {
            new TomSelect('#co_id_link');
            new TomSelect('#publisher_id');
            $('#resetBtn').on('click', function () {
                window.location.href = '/admins/books';
            });
        });
</script>


@endsection
