@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
@endphp

<div class="xxl:col-span-4 xl:col-span-12 col-span-12">
    <div class="box overflow-hidden">
        <div class="box-body !p-0">
            <div class="sm:flex items-start !py-6 px-4 main-profile-cover">
                <div>
                    <span class="avatar avatar-xxl avatar-rounded online me-4">
                        <img src="{{ $user->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" alt="{{ $user->name }}">
                    </span>
                </div>
                <div class="flex-grow main-profile-info">
                    <div class="flex items-center !justify-between">
                        <h6 class="font-semibold mb-1 text-white text-[1rem]">{{ $user->name }}</h6>
  <div class="flex items-center space-x-2">
                            <span class="text-xs text-yellow-400 bg-yellow-100 dark:bg-yellow-700/20 px-2 py-1 rounded-md">Edit Disabled</span>
                            <button class="ti-btn ti-btn-light !font-medium !gap-0 opacity-50 cursor-not-allowed" disabled>
                                <i class="ri-edit-line me-1 align-middle inline-block"></i>Edit Profile
                            </button>
                        </div>

                    </div>
                    <p class="mb-1 !text-white opacity-[0.7]">{{ $user->designation ?? 'No designation set' }}</p>
                    <p class="text-[0.75rem] text-white mb-6 opacity-[0.5]">
                        <span class="me-4 inline-flex"><i class="ri-building-line me-1 align-middle"></i>{{ $user->organization ?? 'NITRA Technical Campus' }}</span>
                        <span class="inline-flex"><i class="ri-map-pin-line me-1 align-middle"></i>{{ $user->location ?? 'Ghaziabad, India' }}</span>
                    </p>
                    
                </div>
            </div>

            {{-- Bio Section --}}
            <div class="p-6 border-b border-dashed dark:border-defaultborder/10">
                <div class="mb-6">
                    <p class="text-[.9375rem] mb-2 font-semibold">Professional Bio :</p>
                    <p class="text-[0.75rem] text-textmuted opacity-[0.7] mb-0">
                        {{ $user->bio ?? 'No bio provided yet.' }}
                    </p>
                </div>
            </div>

            {{-- Contact Section --}}
            <div class="p-6 border-b dark:border-defaultborder/10 border-dashed">
                <p class="text-[.9375rem] mb-2 me-6 font-semibold">Contact Information :</p>
                <div class="text-textmuted">
                    <p class="mb-2"><i class="ri-mail-line me-1"></i> {{ $user->email }}</p>
                    <p class="mb-2"><i class="ri-phone-line me-1"></i> {{ $user->phone ?? '+91-0000000000' }}</p>
                    <p class="mb-0"><i class="ri-map-pin-line me-1"></i> {{ $user->address ?? 'Not set' }}</p>
                </div>
            </div>

           
        </div>
    </div>
</div>
