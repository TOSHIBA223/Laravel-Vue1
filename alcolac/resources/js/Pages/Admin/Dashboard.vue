<template>
    <app-layout>
	<!-- <transition enter-active-class="ease-out duration-300"
                    enter-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-class="opacity-100"
                    leave-to-class="opacity-0">
            <div class="bg-red-500 p-4 fixed left-0 right-0 top-0 text-center" v-if="errors.systemFail" @click="errors = false">
                <div class="max-w-xl w-full mx-auto">
                    <span class="text-white">
                        {{errors.systemFail}} x
                    </span>
                </div>
            </div>
            <div class="bg-green-400 p-4 fixed left-0 right-0 top-0 text-center" v-if="systemSuccess" @click="systemSuccess = false">
                <div class="max-w-xl w-full mx-auto">
                    <span class="text-white">
                        {{systemSuccess}} 
                    </span>
                </div>
            </div>
        </transition> -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="container mt-5 mb-5">
            <div class="row ">

                <div class="d-flex col-md-4">
                      <div class="flex-fill card">
                        <div class=" py-4 card-body">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h1 class="mb-4 display-4">100</h1>
                                    <h6 class="mb-0">
                                        <span class="text-black-50">How many Declarations sent today?</span>
                                    </h6>
                                </div>
                                <div class="d-inline-block ms-3">
                                    <div class="stat">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-exclamation-triangle text-primary" width="35" height="35" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5H3z"/>
                                            <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
                                            <line x1="12" y1="1" x2="12" y2="23"></line>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex col-md-4">
                      <div class="flex-fill card">
                        <div class=" py-4 card-body">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h1 class="mb-4 display-4">100</h1>
                                        <h6 class="mb-0">
                                            <span class="text-black-50">How many Test Messages sent today?</span>
                                        </h6>
                                </div>
                                <div class="d-inline-block ms-3">
                                    <div class="stat">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-exclamation-triangle text-success" width="35" height="35" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-2.5a2 2 0 0 0-1.6.8L8 14.333 6.1 11.8a2 2 0 0 0-1.6-.8H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2.5a1 1 0 0 1 .8.4l1.9 2.533a1 1 0 0 0 1.6 0l1.9-2.533a1 1 0 0 1 .8-.4H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                                            <path d="M5 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                            <line x1="3" y1="6" x2="21" y2="6"></line>
                                        </svg>
                                            
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex col-md-4">
                      <div class="flex-fill card">
                        <div class=" py-4 card-body">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h1 class="mb-4 display-4">10/8</h1>
                                        <h6 class="mb-0">
                                            <span class="text-black-50">How many Lock Files created True/false?</span>
                                        </h6>
                                </div>
                                <div class="d-inline-block ms-3">
                                    <div class="stat">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-exclamation-triangle text-success" width="35" height="35" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>      
                                            <line x1="12" y1="1" x2="12" y2="23"></line>
                                        </svg>                                         
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from 'Layout/AppLayout'

    export default {
        components: {
            AppLayout,
        },
        props: [
            'test',
			'errors',
            'systemSuccess',
            'systemFail'
        ]
    }
</script>
