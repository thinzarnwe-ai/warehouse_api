<div class="relative z-40 lg:hidden" role="dialog" aria-modal="true" x-show="!openMenu">
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75"
    ></div>

    <div class="fixed inset-0 z-40 flex" >
      <div class="relative flex w-full max-w-xs flex-1 flex-col bg-[#1a936f] pt-5 pb-4"
      x-transition:enter="ease-in-out duration-300"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition ease-in-out duration-300 transform"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0" >
        <div class="absolute top-0 right-0 -mr-12 pt-2" >
          <button type="button" x-on:click.prevent="openMenu = !openMenu"
          class="hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white z-20 inline-flex items-center justify-center p-2 text-gray-400 transition duration-300 ease-in-out rounded-sm" >
            <span class="sr-only">Close sidebar</span>
            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

       
        <nav class="mt-5 h-full flex-shrink-0 divide-y divide-[#1a936f] overflow-y-auto" aria-label="Sidebar" >
          <div class="space-y-1 px-2">
            <a href="#" class="bg-[#1a936f] text-white group flex items-center px-2 py-2 text-base font-medium rounded-md" aria-current="page">
              <svg class="mr-4 h-6 w-6 flex-shrink-0 text-cyan-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              </svg>
              Home
            </a>              
          </div>
            <div class="space-y-1 px-2">
            <a href="#" class="bg-[#1a936f] text-white group flex items-center px-2 py-2 text-base font-medium rounded-md" aria-current="page">
              <svg class="mr-4 h-6 w-6 flex-shrink-0 text-cyan-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              </svg>
              Branches
            </a>              
          </div>
            <div class="space-y-1 px-2">
            <a href="#" class="bg-[#1a936f] text-white group flex items-center px-2 py-2 text-base font-medium rounded-md" aria-current="page">
              <svg class="mr-4 h-6 w-6 flex-shrink-0 text-cyan-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              </svg>
              Positions
            </a>              
          </div>
            <div class="space-y-1 px-2">
            <a href="#" class="bg-[#1a936f] text-white group flex items-center px-2 py-2 text-base font-medium rounded-md" aria-current="page">
              <svg class="mr-4 h-6 w-6 flex-shrink-0 text-cyan-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              </svg>
              Users
            </a>              
          </div>
             <div class="space-y-1 px-2">
            <a href="#" class="bg-[#1a936f] text-white group flex items-center px-2 py-2 text-base font-medium rounded-md" aria-current="page">
              <svg class="mr-4 h-6 w-6 flex-shrink-0 text-cyan-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              </svg>
              Roles
            </a>              
          </div>
             <div class="space-y-1 px-2">
            <a href="#" class="bg-[#1a936f] text-white group flex items-center px-2 py-2 text-base font-medium rounded-md" aria-current="page">
              <svg class="mr-4 h-6 w-6 flex-shrink-0 text-cyan-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
              </svg>
              Permissions
            </a>              
          </div>
          <div class="mt-6 pt-6">
            <div class="space-y-1 px-2">
              <a href="#" class="group flex items-center rounded-md px-2 py-2 text-base font-medium text-cyan-100 hover:bg-[#1a936f] hover:text-white">
                <svg class="mr-4 h-6 w-6 text-cyan-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077l1.41-.513m14.095-5.13l1.41-.513M5.106 17.785l1.15-.964m11.49-9.642l1.149-.964M7.501 19.795l.75-1.3m7.5-12.99l.75-1.3m-6.063 16.658l.26-1.477m2.605-14.772l.26-1.477m0 17.726l-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205L12 12m6.894 5.785l-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864l-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495" />
                </svg>
                Settings
              </a>
             
            </div>
          </div>
        </nav>
      </div>

      <div class="w-14 flex-shrink-0" aria-hidden="true" >
      </div>
    </div>
  </div>