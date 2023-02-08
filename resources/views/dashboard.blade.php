@vite(['resources/js/dashboard.chart.js'])
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fa-solid fa-gauge"></i> {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @include('components.change-year')
    
    <div class="mx-6 mt-4 -mb-3 p-2 bg-white rounded shadow">

        <div class="grid grid-cols-3 place-items-stretch">

            <div class="bg-gray-50 border-1 p-6 m-5 shadow text-center">
                <p class="text-8xl font-extrabold text-gray-800">
                    {{ $emplCount }}
                </p>
                <span class="text-gray-500 text-lg">всего сотрудников</span>
            </div>


            <div class="bg-green-50 border-1 p-6 m-5 shadow text-center">
                <p class="text-8xl font-extrabold text-green-900">
                    {{ count($emplOnVacation) }} 
                </p>
                <p class="text-lg font-extrabold text-green-900">
                    ({{ round(count($emplOnVacation) / $emplCount * 100) }}%)
                </p>
                <span class="text-gray-500 text-lg">в отпуске</span>
                <div class="mt-2">
                    <button type="button" class="inline-block px-6 py-1.5 bg-blue-500 text-white font-medium rounded shadow-md hover:bg-blue-700 transition" ease-in-out" data-bs-toggle="modal" data-bs-target="#employeeOnVacationDetail">
                        Подробнее
                    </button>
                </div>

                <!-- Modal begin -->
                <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto" id="employeeOnVacationDetail" tabindex="-1" aria-labelledby="employeeOnVacationDetailLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl relative w-auto pointer-events-none">
                        <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                            <div class="modal-header flex flex-shrink-0 items-center justify-between p-4 border-b border-gray-200 rounded-t-md">
                                <h5 class="text-xl font-medium leading-normal text-gray-800" id="employeeOnVacationDetailLabel">Сотрудники в отпуске</h5>
                                <button type="button" class="btn-close box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body relative p-4">
                                <table class="min-w-full table-sort">
                                    <thead class="bg-gray-100">
                                        <th class="border w-1/4 p-3">ФИО <i class="fa-solid fa-sort"></i></th>
                                        <th class="border p-3">Отдел <i class="fa-solid fa-sort"></i></th>
                                        <th class="border w-1/5 p-3">Период отпуска <i class="fa-solid fa-sort"></i></th>
                                    </thead>
                                    <tbody class="">
                                    @foreach($emplOnVacation as $empl)
                                        <tr>    
                                            <td class="border w-1/4 p-3">{{ $empl['fio'] }}</td>
                                            <td class="border p-3">{{ $empl['department'] }}</td>
                                            <td class="border w-1/5 p-3">
                                                {{ $empl['dateStartText'] }} - {{ $empl['dateEndText'] }}                                                
                                                <div class="w-full bg-gray-200 h-2 rounded mt-2">
                                                    <div class="bg-blue-400 h-2 rounded" style="width: <?= round(($empl['countDaysNow'] / $empl['countDays']) * 100) ?>%;"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                
                            </div>
                            <div class="modal-footer flex flex-shrink-0 flex-wrap items-center justify-end p-4 border-t border-gray-200 rounded-b-md">
                                <button type="button" class="px-6
                                    py-2.5
                                    bg-purple-600
                                    text-white
                                    font-medium
                                    text-xs
                                    leading-tight
                                    uppercase
                                    rounded
                                    shadow-md
                                    hover:bg-purple-700 hover:shadow-lg
                                    focus:bg-purple-700 focus:shadow-lg focus:outline-none focus:ring-0
                                    active:bg-purple-800 active:shadow-lg
                                    transition
                                    duration-150
                                    ease-in-out" data-bs-dismiss="modal">{{ __('Close') }}</button>                                
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Model end -->

            </div>

            <div class="bg-blue-50 border-1 p-6 m-5 shadow text-center">
                <p class="text-8xl font-extrabold text-blue-900">
                    {{ count($employeesSoonVacation) }}
                </p>
                <span class="text-gray-500 text-lg">идут в отпуск в ближайшие 7 дней</span>
                <div class="mt-2">
                    <button type="button" class="inline-block px-6 py-1.5 bg-blue-500 text-white font-medium rounded shadow-md hover:bg-blue-700 transition" ease-in-out" data-bs-toggle="modal" data-bs-target="#employeesSoonVacationDetail">
                        Подробнее
                    </button>
                </div>

                <!-- Modal begin -->
                <div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto" id="employeesSoonVacationDetail" tabindex="-1" aria-labelledby="employeesSoonVacationDetailLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl relative w-auto pointer-events-none">
                        <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none text-current">
                            <div class="modal-header flex flex-shrink-0 items-center justify-between p-4 border-b border-gray-200 rounded-t-md">
                                <h5 class="text-xl font-medium leading-normal text-gray-800" id="employeesSoonVacationDetailLabel">Сотрудники идущие в отпуск в ближайшую неделю</h5>
                                <button type="button" class="btn-close box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body relative p-4">
                                <table class="min-w-full table-sort">
                                    <thead class="bg-gray-100">
                                        <th class="border w-1/4 p-3">ФИО <i class="fa-solid fa-sort"></i></th>
                                        <th class="border p-3">Отдел <i class="fa-solid fa-sort"></i></th>
                                        <th class="border w-1/5 p-3">Период отпуска <i class="fa-solid fa-sort"></i></th>
                                    </thead>
                                    <tbody class="">
                                    @foreach($employeesSoonVacation as $empl)
                                        <tr>    
                                            <td class="border w-1/4 p-3">{{ $empl['fio'] }}</td>
                                            <td class="border p-3">{{ $empl['department'] }}</td>
                                            <td class="border w-1/5 p-3">
                                                {{ $empl['dateStartText'] }} - {{ $empl['dateEndText'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                
                            </div>
                            <div class="modal-footer flex flex-shrink-0 flex-wrap items-center justify-end p-4 border-t border-gray-200 rounded-b-md">
                                <button type="button" class="px-6
                                    py-2.5
                                    bg-purple-600
                                    text-white
                                    font-medium
                                    text-xs
                                    leading-tight
                                    uppercase
                                    rounded
                                    shadow-md
                                    hover:bg-purple-700 hover:shadow-lg
                                    focus:bg-purple-700 focus:shadow-lg focus:outline-none focus:ring-0
                                    active:bg-purple-800 active:shadow-lg
                                    transition
                                    duration-150
                                    ease-in-out" data-bs-dismiss="modal">{{ __('Close') }}</button>                                
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Model end -->

            </div>


            <div class="border col-span-3 p-10 m-5 shadow" style="position: relative;">                
                <div id="chartTest" data-values="{{ $countVacationsByDateDatas }}" data-labels="{{ $countVacationsByDateLabels }}" data-year="{{$year}}"></div>
            </div>

            
        </div>

    </div>

</x-app-layout>