<footer class="bg-dark text-neutral-300 border-t border-neutral-800" role="contentinfo">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
            <div class="col-span-2 md:col-span-1">
                <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2 mb-4" aria-label="<?php echo e(config('app.name')); ?> Home">
                    <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-xl text-white"><?php echo e(config('app.name')); ?></span>
                </a>
                <p class="text-sm text-neutral-400"><?php echo e(setting('general.site_description', 'Your marketplace for digital products')); ?></p>
            </div>

            <div>
                <h3 class="font-semibold text-white mb-4">Marketplace</h3>
                <nav aria-label="Marketplace links">
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?php echo e(route('catalog.index')); ?>" class="hover:text-secondary transition-colors">All Products</a></li>
                        <li><a href="<?php echo e(route('catalog.index')); ?>" class="hover:text-secondary transition-colors">Categories</a></li>
                        <li><a href="#" class="hover:text-secondary transition-colors">Best Sellers</a></li>
                        <li><a href="#" class="hover:text-secondary transition-colors">New Arrivals</a></li>
                        <li><a href="<?php echo e(route('seller.apply')); ?>" class="hover:text-secondary transition-colors">Become a Seller</a></li>
                    </ul>
                </nav>
            </div>

            <div>
                <h3 class="font-semibold text-white mb-4">Support</h3>
                <nav aria-label="Support links">
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-secondary transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-secondary transition-colors">Contact Us</a></li>
                        <li><a href="#" class="hover:text-secondary transition-colors">Refund Policy</a></li>
                        <li><a href="#" class="hover:text-secondary transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-secondary transition-colors">Privacy Policy</a></li>
                    </ul>
                </nav>
            </div>

            <div>
                <h3 class="font-semibold text-white mb-4">Company</h3>
                <nav aria-label="Company links">
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-secondary transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-secondary transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-secondary transition-colors">Careers</a></li>
                        <li><a href="#" class="hover:text-secondary transition-colors">Press</a></li>
                        <li><a href="#" class="hover:text-secondary transition-colors">Affiliates</a></li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="pt-8 border-t border-neutral-800">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm text-neutral-500">&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All rights reserved.</p>
                
                <div class="flex items-center gap-6">
                    <a href="#" class="text-neutral-400 hover:text-secondary transition-colors" aria-label="Twitter">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path></svg>
                    </a>
                    <a href="#" class="text-neutral-400 hover:text-secondary transition-colors" aria-label="GitHub">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"></path></svg>
                    </a>
                    <a href="#" class="text-neutral-400 hover:text-secondary transition-colors" aria-label="Discord">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.445.778-.692 1.196a19.276 19.276 0 00-5.294 0 .074.074 0 00-.079-.037 17.5 17.5 0 00-.689-1.196.077.077 0 00-.078-.037 19.736 19.736 0 00-4.885 1.515.066.066 0 00-.032.013.061.061 0 00-.015.032c-.597 1.192-.974 2.423-1.158 3.683a.083.083 0 00.031.057c1.333 2.827 3.061 5.036 5.171 6.606.073.054.154.089.238.117a.075.075 0 00.063-.032.07.07 0 00-.007-.1c-.72-1.32-1.845-2.925-3.104-4.966a.077.077 0 01-.013-.128c1.296-.062 2.842-.474 3.978-1.366a.077.077 0 01.085.028c1.162.916 2.746 1.342 4.083 1.342s2.92-.426 4.082-1.342a.077.077 0 01.086-.028c1.14.892 2.686 1.304 3.982 1.366a.077.077 0 01-.013.127 12.29 12.29 0 01-3.102 4.966.073.073 0 00-.008.1.077.077 0 00.058.08.072.072 0 00.068-.028c2.11-1.57 3.838-3.779 5.171-6.606a.083.083 0 00.032-.057c-.184-1.26-.561-2.49-1.158-3.683a.061.061 0 00-.046-.045 19.737 19.737 0 00-1.804-4.839.074.074 0 00-.106-.003l-.004.003zM8.02 15.63c-1.183-.486-2.128-1.316-2.512-2.381a.077.077 0 01.02-.11 13.277 13.277 0 01.563-2.962.077.077 0 01.121.006c.57.355 1.16.717 1.749 1.086a.074.074 0 00.07.003c1.718-.678 3.585-.964 5.412-.964s3.694.286 5.412.964a.074.074 0 00.069-.003c.59-.369 1.18-.73 1.75-1.086a.077.077 0 01.12-.006 13.279 13.279 0 01.562 2.962.077.077 0 01.021.111c-.383 1.065-1.329 1.895-2.512 2.381a.077.077 0 01-.119.016H8.14a.077.077 0 01-.12-.016zM9.97 8.22a1.25 1.25 0 11-.001-2.5 1.25 1.25 0 01.001 2.5zm5.7 0a1.25 1.25 0 11-.001-2.5 1.25 1.25 0 01.001 2.5z"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer><?php /**PATH C:\Users\User\Desktop\Ahmed\digitalfanda\resources\views\components\footer.blade.php ENDPATH**/ ?>