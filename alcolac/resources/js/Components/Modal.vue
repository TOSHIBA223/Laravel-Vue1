<template>
    <div class="h-screen w-full fixed top-0 bottom-0 flex align-center justify-center">
        <transition leave-active-class="duration-200">
            <div class="w-full flex flex-wrap m-auto md:flex-no-wrap shadow-md border rounded overflow-hidden" :class="containerMaxWidth">
                <transition enter-active-class="ease-out duration-300"
                        enter-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="ease-in duration-200"
                        leave-class="opacity-100"
                        leave-to-class="opacity-0">
                    <div class="fixed inset-0 transform transition-all" @click="$emit('closeModal')">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                </transition>

                <transition enter-active-class="ease-out duration-300"
                        enter-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-active-class="ease-in duration-200"
                        leave-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div class="bg-white rounded-lg shadow-xl transform transition-all sm:w-full py-8 px-4" :class="[maxWidth, maxHeight, scrollBehaviour]">
                        <slot></slot>
                    </div>
                </transition>
            </div>
        </transition>
    </div>
</template>

<script>
    export default {
        props: {
            maxWidth: {
                default: 'max-w-full'
            },
            containerMaxWidth: {
                default: 'max-w-xl'
            },
            closeable: {
                default: true
            },
            maxHeight: {
                default: 'max-h-screen'
            },
            scrollBehaviour: {
                default: 'overflow-hidden'
            }
        },

        methods: {
            close() {
                if (this.closeable) {
                    this.$emit('close')
                }
            }
        },

        watch: {
            show: {
                immediate: true,
                handler: (show) => {
                    if (show) {
                        document.body.style.overflow = 'hidden'
                    } else {
                        document.body.style.overflow = null
                    }
                }
            }
        },

        created() {
            const closeOnEscape = (e) => {
                if (e.key === 'Escape' && this.show) {
                    this.close()
                }
            }

            document.addEventListener('keydown', closeOnEscape)

            this.$once('hook:destroyed', () => {
                document.removeEventListener('keydown', closeOnEscape)
            })
        },

        computed: {
            maxWidthClass() {
                return {
                    'sm': 'sm:max-w-sm',
                    'md': 'sm:max-w-md',
                    'lg': 'sm:max-w-lg',
                    'xl': 'sm:max-w-xl',
                    '2xl': 'sm:max-w-2xl',
                }[this.maxWidth]
            }
        }
    }
</script>
