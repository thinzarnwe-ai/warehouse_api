<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WMS</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css')}}">
    <link href="{{ asset('css/tom-select.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <style>
        body{
            font-family: sans-serif;
        }
        .solid {
            border: 2px red solid!important;
            border-radius: 10px!important;
        }
        table.dataTable tbody tr{
            font-size: 12px!important;
        }
        .tab-active
        {
            border-bottom: 2px #0e7490 solid!important;
        }
        .ts-control{
            overflow: auto!important;
            height: 50px!important;
        }

    </style>
</head>
<body class="h-full"  x-data="{ openMenu: false,sideBarMenu:true, addDept:false,addPosition:false,addBranch:false, addRole:false, addPermission:false,addUser:false,addTeam:false }"
>
<div class="min-h-full font-roboto">
    @include('admins.layouts.nav')
    <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col" x-show="sideBarMenu">
      <div class="flex flex-grow flex-col overflow-y-auto bg-[#ffff] pt-5 pb-4">
        <nav class="mt-3 flex flex-1 flex-col divide-y divide-[#ffff] bg-[#ffff] overflow-y-auto" aria-label="Sidebar">
          <div class="space-y-1 px-2">
            {{-- <a href="{{ url('admins') }}" class="{{ request()->is('admins') ? 'bg-[#1a936f] text-white' : 'text-[#1a936f] hover:text-white hover:bg-[#1a936f]' }} group flex items-center px-2 py-2 text-sm leading-6 font-bold rounded-md">
              <svg class="mr-4 h-6 w-6 flex-shrink-0 " xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              </svg>
              Dashboard
            </a> --}}

            {{-- <a href="{{ route('books.index') }}" class="{{ Route::currentRouteNamed('books.index') ? 'bg-cyan-800 text-white' : 'text-cyan-100 hover:text-white hover:bg-cyan-600' }} group flex items-center px-2 py-2 text-sm leading-6 font-medium rounded-md">
              <svg class="mr-4 h-6 w-6 flex-shrink-0 text-cyan-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Books
            </a>
               --}}
            <a href="{{ route('branches.index') }}" class="{{ Route::currentRouteNamed('branches.index') ? 'bg-[#1a936f] text-white' : 'text-[#1a936f] hover:text-white hover:bg-[#1a936f]' }} group flex items-center px-2 py-2 text-sm leading-6 font-bold rounded-md">
            <svg class="mr-4 h-6 w-6 flex-shrink-0 transition-colors duration-200"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
            </svg>
            Branches
          </a>


            {{-- <a href="{{ route('departments.index') }}" class="{{ Route::currentRouteNamed('departments.index') ? 'bg-[#1a936f] text-white' : 'text-[#1a936f] hover:text-white hover:bg-[#1a936f]' }} group flex items-center px-2 py-2 text-sm leading-6 font-medium rounded-md">
              <svg class="mr-4 h-6 w-6 flex-shrink-0 transition-colors duration-200" 
              xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
              </svg>
              Departments
            </a>   --}}

            {{-- <a href="{{ route('positions.index') }}" class="{{ Route::currentRouteNamed('positions.index') ? 'bg-[#1a936f] text-white' : 'text-[#1a936f] hover:text-white hover:bg-[#1a936f]' }} group flex items-center px-2 py-2 text-sm leading-6 font-medium rounded-md">
              <svg class="mr-4 h-6 w-6 flex-shrink-0 transition-colors duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path strokeLinecap="round" strokeLinejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
              </svg>
              Positions
            </a>   --}}

            <a href="{{ route('users.index') }}" class="{{ Route::currentRouteNamed('users.index') ? 'bg-[#1a936f] text-white' : 'text-[#1a936f] hover:text-white hover:bg-[#1a936f]' }} group flex items-center px-2 py-2 text-sm leading-6 font-medium rounded-md">
              <svg class="mr-4 h-6 w-6 flex-shrink-0 transition-colors duration-200" xmlns="http://www.w3.org/2000/svg"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path strokeLinecap="round" strokeLinejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              </svg>

              Users 
            </a> 
            <a href="{{ route('roles.index') }}" class="{{ Route::currentRouteNamed('roles.index') ? 'bg-[#1a936f] text-white' : 'text-[#1a936f] hover:text-white hover:bg-[#1a936f]' }} group flex items-center px-2 py-2 text-sm leading-6 font-medium rounded-md">
              <svg class="mr-4 h-6 w-6 flex-shrink-0 transition-colors duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
              </svg>
              Roles
            </a> 

            <a href="{{ route('permissions.index') }}" class="{{ Route::currentRouteNamed('permissions.index') ? 'bg-[#1a936f] text-white' : 'text-[#1a936f] hover:text-white hover:bg-[#1a936f]' }} group flex items-center px-2 py-2 text-sm leading-6 font-medium rounded-md">
              <svg class="mr-4 h-6 w-6 flex-shrink-0 transition-colors duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Permission
            </a> 

          </div>         
        </nav>
      </div>
    </div>

    <div class="flex flex-1 flex-col lg:pl-64">
      <div class="flex h-20 flex-shrink-0 border-b border-[#74bba6] shadow-md ">
        <!-- Search bar -->

        <div class="flex flex-1 justify-between px-4 sm:px-6 lg:mx-auto lg:max-w-6xl lg:px-8">
            <div class="flex items-center">
              <h1 class=" font-bold text-[#1a936f]">Warehouse Management</h2>
                <img class="h-12 lg:hidden" src="{{ asset('img/wms.jpg') }}" alt="">
            </div>
          <div class="ml-4 flex justify-end items-center md:ml-6">
            <button type="button" class="block lg:hidden bg-[#1a936f] p-1 text-gray-400 hover:text-gray-500" x-on:click.prevent="openMenu = !openMenu">
                <span class="sr-only">Mobile Menu</span>
                <!-- Heroicon name: outline/menu -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5" />
                  </svg>
              </button>

            <!-- Profile dropdown -->
            <div class="relative ml-3" x-data="{admindropdownMenu: false}"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95" >
              <div>
                <button type="button" @click="admindropdownMenu = ! admindropdownMenu" class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 lg:rounded-md lg:p-2 lg:hover:bg-gray-50" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                  <img class="h-8 w-8 rounded-full" src="{{asset('img/profile.png')}}" alt="">
                  <span class="ml-3 hidden text-sm font-medium text-gray-700 lg:block"><span class="sr-only">Open user menu for </span>{{ getAuthUser()->name}}</span>
                  <!-- Heroicon name: mini/chevron-down -->
                  <svg class="ml-1 hidden h-5 w-5 flex-shrink-0 text-gray-400 lg:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                  </svg>
                </button>
              </div>
              <div x-show="admindropdownMenu" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">               
                <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Logout</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <main class="flex-1 pb-8 mt-4">
        <!-- Page header -->
        {{-- {{ Auth::user()->name }} --}}
       @yield('content')
      </main>
    </div>
  </div>

  <script src="{{asset('js/jquery-3.6.0.js')}}"></script>

  <script src="{{asset('js/jquery.dataTables.min.js')}}" type="text/javascript"></script>
  <script src="{{asset('js/select2.min.js')}}"></script> 
  <script src="{{asset('js/3.4.16')}}"></script>
  <script src="{{asset('js/tom-select.complete.min.js')}}"></script>
  <script src="{{ asset('js/app.js') }}"></script>
 
<script src="{{asset('js/sweetalert2@10.js')}}"></script>
@stack('addon-script')
<script>
    function closeModal(modal)
       {
           $(modal).hide();
       }
       function openModal(modal)
       {
           $(modal).show();
       }
</script>
@yield('script')
</body>
</html>
