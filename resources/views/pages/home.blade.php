<!-- Start::page-header -->
<div class="md:flex block items-center justify-between my-6 page-header-breadcrumb">
    <div>
        <h4 class="mb-0 text-defaulttextcolor font-medium">
            Hi, {{ auth()->user()->name ?? 'User' }}!
        </h4>
        <p class="-mt-[0.2rem] mb-0 text-textmuted">
            Logged in as <strong>{{ auth()->user()->role ?? 'User' }}</strong>
            • {{ auth()->user()->email }}
        </p>
    </div>

    <div class="main-dashboard-header-right">
    
    <div>
        <a href="" class="ti-btn ti-btn-primary-full">
    <i class="bi bi-person-circle"></i>
    Profile
</a>

        
    </div>
</div>

</div>
<!-- End::page-header -->


                    <!-- row -->
                    <div class="grid grid-cols-12 gap-x-6">
    <div class="xl:col-span-3 lg:col-span-6 md:col-span-6 sm:col-span-12 col-span-12">
        <div class="box overflow-hidden sales-box bg-primary-gradient !rounded-sm">
            <div class="px-4 pt-4 pb-2">
                <div>
                    <h6 class="mb-3 text-[.75rem] font-medium text-fixed-white">TOTAL USERS</h6>
                </div>
                <div class="pb-0 mt-0">
                    <div class="flex">
                        <div>
                            <h4 class="font-bold text-[1.25rem] text-fixed-white">{{ $stats['users'] }}</h4>
                            <p class="mb-0 text-[.75rem] text-fixed-white opacity-[0.7]">System-wide registered users</p>
                        </div>
                        <span class="float-end my-auto ms-auto">
                            <i class="fas fa-users text-fixed-white"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div id="compositeline" class="!-mb-[2px]"></div>
        </div>
    </div>

    <div class="xl:col-span-3 lg:col-span-6 md:col-span-6 sm:col-span-12 col-span-12">
        <div class="box overflow-hidden sales-card bg-danger-gradient !rounded-sm">
            <div class="px-4 pt-4 pb-2">
                <div>
                    <h6 class="mb-3 text-[.75rem] font-medium text-fixed-white">TOTAL DEPARTMENTS</h6>
                </div>
                <div class="pb-0 mt-0">
                    <div class="flex">
                        <div>
                            <h4 class="text-[1.25rem] font-bold text-fixed-white">{{ $stats['departments'] }}</h4>
                            <p class="mb-0 text-[.75rem] text-fixed-white opacity-[0.7]">Across all units</p>
                        </div>
                        <span class="float-end my-auto ms-auto">
                            <i class="fas fa-building text-fixed-white"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div id="compositeline2" class="!-mb-[2px]"></div>
        </div>
    </div>

    <div class="xl:col-span-3 lg:col-span-6 md:col-span-6 sm:col-span-12 col-span-12">
        <div class="box overflow-hidden sales-card bg-success-gradient !rounded-sm">
            <div class="px-4 pt-4 pb-2">
                <div>
                    <h6 class="mb-3 text-[.75rem] font-medium text-fixed-white">TOTAL PROJECTS</h6>
                </div>
                <div class="pb-0 mt-0">
                    <div class="flex">
                        <div>
                            <h4 class="text-[1.25rem] font-bold text-fixed-white">{{ $stats['projects'] }}</h4>
                            <p class="mb-0 text-[.75rem] text-fixed-white opacity-[0.7]">Monitored projects</p>
                        </div>
                        <span class="float-end my-auto ms-auto">
                            <i class="fas fa-briefcase text-fixed-white"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div id="compositeline3" class="!-mb-[2px]"></div>
        </div>
    </div>

    <div class="xl:col-span-3 lg:col-span-6 md:col-span-6 sm:col-span-12 col-span-12">
        <div class="box overflow-hidden sales-card bg-warning-gradient !rounded-sm">
            <div class="px-4 pt-4 pb-2">
                <div>
                    <h6 class="mb-3 text-[.75rem] font-medium text-fixed-white">TOTAL UNITS</h6>
                </div>
                <div class="pb-0 mt-0">
                    <div class="flex">
                        <div>
                            <h4 class="text-[1.25rem] font-bold text-fixed-white">{{ $stats['units'] }}</h4>
                            <p class="mb-0 text-[.75rem] text-fixed-white opacity-[0.7]">Across departments</p>
                        </div>
                        <span class="float-end my-auto ms-auto">
                            <i class="fas fa-cubes text-fixed-white"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div id="compositeline4" class="!-mb-[2px]"></div>
        </div>
    </div>
</div>


                    
                