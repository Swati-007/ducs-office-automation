@extends('layouts.master')
@section('body')
    <div class="container mx-auto p-4">
        <div class="bg-white p-6 h-full rounded shadow-md">
            <div class="flex items-center mb-4">
                <img src="{{ route('staff.teachers.avatar', $teacher) }}"
                    class="w-24 h-24 object-cover mr-4 border rounded shadow">
                <div>
                    <h3 class="text-2xl font-bold">{{ $teacher->name }}</h3>
                    <h5 class="text-xl text-gray-700 font-medium mb-2">
                        {{ $teacher->profile->getDesignation() }}
                        @if($teacher->isSupervisor())
                        / <span class="italic">Supervisor</span>
                        @endif
                    </h5>
                    <div class="flex items-center text-xl text-gray-700 font-medium">
                        <svg viewBox="0 0 20 20" class="h-current">
                            <g stroke="none" stroke-width="1" fill="currentColor" fill-rule="evenodd">
                                <path d="M3.33333333,8 L10,12 L20,6 L10,0 L-5.55111512e-16,6 L10,6 L10,6.5 L10,8 L3.33333333,8 L3.33333333,8 Z M1.33226763e-15,8 L1.33226763e-15,16 L2,13.7777778 L2,9.2 L2,9.2 L1.33226763e-15,8 L1.11022302e-16,8 L1.33226763e-15,8 Z M10,20 L5,16.9999998 L3,15.8 L3,9.8 L10,14 L17,9.8 L17,15.8 L10,20 L10,20 Z"></path>
                            </g>
                        </svg>
                        <h5 class="ml-2">{{ ($college = $teacher->profile->college) ? $college->name : 'Not Set' }}</h5>
                    </div>
                </div>
                <div class="ml-auto self-start">
                    @can('update', App\Teacher::class)
                        @include('staff.teachers.modals.edit', [
                            'modalName' => 'edit-teacher-modal',
                        ])
                        <button type="submit" class="p-1 hover:text-red-700 mr-2" @click="
                            $modal.show('edit-teacher-modal', {
                                Teacher: {
                                    id: {{ $teacher->id }},
                                    first_name: {{ json_encode($teacher->first_name) }},
                                    last_name: {{ json_encode($teacher->last_name) }},
                                    email: {{ json_encode($teacher->email) }},
                                },
                            })">
                        <feather-icon class="h-current" name="edit">Edit</feather-icon>
                    </button>
                    @endcan
                </div>
            </div>
            <address>
                {{ $teacher->profile->address }}
            </address>
            <p class="flex items-center">
                <feather-icon name="at-sign" class="h-current mr-2"></feather-icon>
                <a href="mailto:{{ $teacher->email }}">{{ $teacher->email }}</a>
            </p>
            <p class="flex items-center">
                <feather-icon name="phone" class="h-current mr-2"></feather-icon>
                <a href="tel:{{ $teacher->profile->phone }}">+91 {{ $teacher->profile->phone_no }}</a>
            </p>
            <div class="relative z-10 -ml-8 my-4">
                <h5 class="relative z-20 pl-8 pr-4 py-2 inline-block font-bold bg-magenta-700 text-white shadow">
                    Teaching Details
                </h5>
                <svg class="absolute left-0 w-2 text-magenta-900" viewBox="0 0 10 10">
                    <path fill="currentColor" d="M0 0 L10 0 L10 10 L0 0"></path>
                </svg>
            </div>
            @if($teacher->profile->teachingDetails->count())
            <h6 class="font-semibold mb-2">Present</h6>
            <ul>
                @foreach($teacher->profile->teachingDetails as $detail)
                <li>
                    {{ $detail->programmeRevision->programme->name }} -
                    {{ $detail->course->name }}
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-gray-600 font-bold">Nothing to show here.</p>
            @endif
        </div>

        <div class="bg-white p-6 h-full rounded shadow-md mt-4">
            <div>
                <h3 class="font-bold text-2xl underline">Teaching Records</h3>
                @foreach ($teacher->teachingRecords->groupBy(function($record) {
                return $record->valid_from->format('M, Y');
                }) as $date => $records)
                <details class="mt-6 bg-gray-200 rounded border" open>
                    <summary class="px-4 font-bold cursor-pointer outline-none py-2"> {{ $date }}</summary>
                    <ul class="px-8 list-disc ml-6">
                        @foreach($records as $record)
                        <li class="mb-3">
                            Taught <b>{{ $record->course->name }} <em>({{ $record->course->code }})</em></b>
                            under {{ $record->programmeRevision->programme->name }}
                            (w.e.f {{ $record->programmeRevision->revised_at->year }})
                            in <em class="underline">{{ $record->college->name }}</em> as
                            <strong>{{ $record->getDesignation() }}</strong> teacher.
                        </li>
                        @endforeach
                    </ul>
                </details>
                @endforeach
            </div>
        </div>
    </div>
@endsection
