<div class="flex items-center justify-center mt-4">
    <div class="inline-flex shadow-md hover:shadow-lg focus:shadow-lg">
        @for($i=0; $i<count($years); $i++)
        <a href="{{ route('set-year', ['year' => $years[$i], 'ref' => url()->full()]) }}" class="
            inline-block 
            @if($years[$i] == $year) 
                bg-blue-500 hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700
            @else 
                bg-gray-500 hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700
            @endif 
            px-6 py-2.5 text-white font-medium text-lg 
            transition duration-150 ease-in-out
            @if($i==0) rounded-l @endif
            @if($i==count($years)-1) rounded-r @endif    
        ">{{ $years[$i] }}</a>
        @endfor
    </div>
</div>