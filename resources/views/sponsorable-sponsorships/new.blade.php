@extends('layouts.app')

@section('body')
    <div class="bg-grey-light min-h-screen">
        <div class="max-w-md mx-auto px-6 pt-16 pb-24">
            <h1 class="text-2xl text-center font-semibold mb-8 ">Which episodes would you like to sponsor</h1>
            <ul class="list-reset">
                @foreach (range(1, 4) as $i)
                <li class="mb-4">
                    <div class="bg-white rounded-lg shadow p-4 flex group border-2 border-transparent hover:border-indigo-light select-none cursor-pointer">
                        <div class="w-3/4 flex items-center justify-between">
                            <div class="flex items-center">
                                <img class ="block h-16 w-16 mr-4 rounded" src="https://media.simplecast.com/podcast/image/279/small_1413649662-artwork.jpg">
                                <div>
                                    <div class="text-lg font-bold">Full Stack Radio: Episode {{ 89 + $i }}</div>
                                    <div class="text-sm text-grey-dark">June 6, 2018</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold">$500</div>
                                <div class="text-sm text-grey-dark">USD</div>
                            </div>
                        </div>
                        <div class="w-1/4  flex justify-end items-center">
                            {{-- <span class="block group-hover:hidden h-6 w-6 text-green"><i class="fas fa-check"></i></span> --}}
                            <span class="hidden group-hover:block h-6 w-6 text-indigo"><i class="fas fa-plus-circle"></i></span>
                            <span class="block group-hover:hidden h-6 w-6 text-grey"><i class="fas fa-plus-circle"></i></span>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="bg-white border-t-2 border-grey fixed pin-b pin-x">
            <div class="max-w-md mx-auto px-6 py-6">
                <div class="flex justify-between items-center">
                    <div class="w-3/4 flex justify-between items-center">
                        <div class="text-lg">1 episode selected</div>
                        <div class="text-right">
                            <div class="text-lg font-bold">$500</div>
                            <div class="text-sm text-grey-dark">USD</div>
                        </div>
                    </div>
                    <div class="w-1/4 flex justify-end items-center">
                        <button class="rounded px-5 py-2 text-lg leading-normal font-bold text-white rounded bg-indigo hover:bg-indigo-light" type="button">Pay Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
