<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fa-solid fa-table-list"></i> {{ __('Schedule') }}
        </h2>
    </x-slot>
       
    @include('components.change-year')

    <div class="mt-4 -mb-3 z-0">
        <div class="not-prose relative bg-slate-50 rounded-xl dark:bg-slate-800/25 m-5 z-0">
            <div class="absolute inset-0 bg-grid-slate-100 [mask-image:linear-gradient(0deg,#fff,rgba(255,255,255,0.6))] dark:bg-grid-slate-700/25 dark:[mask-image:linear-gradient(0deg,rgba(255,255,255,0.1),rgba(255,255,255,0.5))]" style="background-position: 10px 10px;"></div>
            <div class="relative rounded-xl z-0">
                <div class="shadow-sm my-8 z-0">
                    <table class="border-collapse w-full text-sm overflow-auto">
                        <thead class="text-base">
                            <tr>
                                <th class="bg-gray-50 z-10 border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-left sticky left-0" style="min-width: 25rem;" rowspan="2">
                                    Наименование
                                </th>
                                @foreach($calendar as $month)
                                <th class="bg-gray-50 z-0 border-b border-l border-r dark:border-slate-600 font-medium p-4 text-center sticky top-0" colspan="{{ $month['days'] }}">
                                    {{ __($month['name']) }}
                                </th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($calendar as $month)
                                    @foreach ($month['daysData'] as $day)
                                        <th class="bg-blue-50 z-0 border p-1 font-medium text-center sticky top-10 @if($day['date']->format('N') >= 6) text-red-500 @endif">
                                            <span data-bs-toggle="tooltip" data-bs-html="true" title="{{ __($day['date']->format('l')) }}">
                                                {{ $day['day'] }}
                                            </span>
                                        </th>
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 accordion">

                            @foreach($data as $department => $departmentData)
                            
                                <tr class="odd:bg-white even:bg-slate-50 hover:bg-slate-300 accordion-item">
                                    <td class="text-base border-b border-slate-100 bg-white dark:border-slate-700 p-4 pl-8 dark:text-slate-400 sticky left-0">                                    
                                        <div class="">                              
                                            <button class="
                                                accordion-button
                                                collapsed
                                                relative
                                                flex
                                                items-center
                                                w-full
                                                py-4
                                                px-5
                                                text-gray-800 text-left
                                                bg-white
                                                border-0
                                                rounded-none
                                                transition
                                                focus:outline-none
                                            " type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ md5($department) }}" aria-expanded="false"
                                                aria-controls="collapse_{{ md5($department) }}">
                                                {{ $department }}
                                                <span class="py-1 px-1.5 leading-none text-center whitespace-nowrap font-bold bg-gray-400 text-white rounded ml-2 mr-2">
                                                {{ count($departmentData) }}
                                                </span>
                                            </button>
                                        </div>
                                    </td>  

                                    @foreach($calendar as $month)             
                                        @foreach($month['daysData'] as $day)
                                            <?php
                                                $count = 0;
                                                foreach($data[$department] as $deps) {
                                                    foreach($deps as $empl) {
                                                        if ($day['date'] >= $empl['dateStart']  && $day['date'] <= $empl['dateEnd']) {
                                                            $count++;
                                                        }
                                                    }
                                                }                                                                     
                                                $bgIndex = $count ? (round(($count / count($departmentData)) * 100, -1)) : false;
                                            ?>
                                            <td class="text-gray-600 z-5 border p-1 @if($bgIndex !== false) bg-green-500/{{ $bgIndex }} @else @if($day['date']->format('N') >= 6) bg-red-50 @else bg-yellow-50 @endif @endif font-mono text-center font-bold">                                             
                                                @if($count > 0)
                                                    <span class="text-xs inline-block py-1 px-2 leading-none text-center font-bold text-white rounded bg-green-500"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-html="true" title="<strong>{{ round(($count / count($departmentData)) * 100) }}%</strong> сотрудников в отпуске<hr /><i class='far fa-calendar'></i> {{ $day['date']->format('d.m.Y') }}<br />{{ __($day['date']->format('l')) }}"
                                                    >
                                                        {{ $count }} 
                                                    </span><br /><br />                                                
                                                @endif
                                            </td>
                                        @endforeach
                                    @endforeach                                    
                                </tr>

                                <tbody id="collapse_{{ md5($department) }}" class="accordion-collapse collapse">
                                @foreach($departmentData as $fio => $empl)
                                <tr class="hover:bg-slate-300">                                                                        
                                    <td class="bg-white z-10 border-b border-slate-100 pl-20 py-3 text-left text-base text-gray-600 sticky left-0">
                                        <i class="fa-regular fa-circle-user"></i> {{ $fio }}
                                    </td>   
                                    <?php $lastDay = 0; ?>
                                    @foreach($calendar as $month)             
                                        @foreach($month['daysData'] as $day)
                                            <?php
                                                $tooltipTitle = '';                                                
                                                $bgColor = 'text-yellow-50/[.7]';
                                                foreach($empl as $period) {
                                                    if($day['date'] >= $period['dateStart']  && $day['date'] <= $period['dateEnd']
                                                        && isset($typesVacation[$period['type']])) {
                                                        $bgColor = $typesVacation[$period['type']];
                                                        $tooltipTitle = $period['type'] 
                                                            . '<br /><b>' . $day['date']->format('d.m.Y') . '</b>';                                                        
                                                    }
                                                }

                                                if ($tooltipTitle) {
                                                    $lastDay++;
                                                }
                                                else {
                                                    $lastDay = 0;
                                                }
                                            ?>

                                            <td class="text-gray-600 z-5 border p-0 text-center @if($day['date']->format('N') >= 6) bg-red-50/[.7] @else bg-yellow-50/[.7] @endif">
                                                @if($tooltipTitle)
                                                    <span class="text-xs inline-block py-1 px-2 leading-none text-center font-bold text-white rounded {{ $bgColor }}" 
                                                        data-bs-toggle="tooltip"
                                                        data-bs-html="true" title="{{ $tooltipTitle }}<hr />{{ __($day['date']->format('l')) }}">{{ $lastDay }}</span>                                                    
                                                @endif
                                            </td>
                                        @endforeach
                                    @endforeach    
                                </tr>
                                @endforeach        
                                </tbody>
                            
                            @endforeach

                        </tbody>
                    </table> 
                </div>
            </div>
        </div>
        <!-- <div class="absolute inset-0 pointer-events-none border border-black/5 rounded-xl dark:border-white/5"></div> -->    
    </div>

</x-app-layout>
