
HTML CSS:(TAILWIND)
    //el welcome de jetstream

        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
            <div>
                <x-jet-application-logo class="block h-12 w-auto" />
            </div>

            <div class="mt-8 text-2xl">
                Welcome to your Jetstream application!
            </div>

            <div class="mt-6 text-gray-500">
                Laravel Jetstream provides a beautiful, robust starting point for your next Laravel application. Laravel is designed
                to help you build your application using a development environment that is simple, powerful, and enjoyable. We believe
                you should love expressing your creativity through programming, so we have spent time carefully crafting the Laravel
                ecosystem to be a breath of fresh air. We hope you love it.
            </div>
        </div>

        // <!-- ocultar cel y mostrar en landscape -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
            <div class="-mr-2 flex items-center sm:hidden">


            // centrado (y dividido correctamente)
            <body class="antialiased">
                <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
                    <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                        <div class="flex items-center pt-8 sm:justify-start sm:pt-0">
                            <div class="px-4 text-lg text-gray-500 border-r border-gray-400 tracking-wider">404                    </div>
                            <div class="ml-4 text-lg text-gray-500 uppercase tracking-wider">Not Found                    </div>
                        </div>
                    </div>
                </div>
            </body>
// laravel 10 block
<a href="https://laravel.com/docs" class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
    <div>
        <div class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-7 h-7 stroke-red-500"> <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /> </svg>
        </div>
        <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">Documentation</h2>
        <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
        </p>
    </div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="self-center shrink-0 stroke-red-500 w-6 h-6 mx-6"> <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" /> </svg>
</a>


BLADE
    // <!-- datatable -->
        <div class="p-2 sm:px-10 bg-white border-b border-gray-200">
            <div class="p-2 sm:px-10 bg-white border-b border-gray-200">
                <livewire:datatable model="App\Models\OrdenCompra" exclude="created_at, updated_at,aprobado,adjunto,empresa_id,tarea_id,clasificacion_id,municipio_id" />
            </div>
        </div>






