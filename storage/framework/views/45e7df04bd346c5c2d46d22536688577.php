<header class="sticky top-0 z-40 bg-white/95 backdrop-blur-sm border-b border-neutral-200 transition-all duration-300">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Main navigation">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2" aria-label="<?php echo e(config('app.name')); ?> Home">
                    <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-xl text-dark hidden sm:block"><?php echo e(config('app.name')); ?></span>
                </a>

                <div class="hidden md:flex md:gap-6">
                    <a href="<?php echo e(route('catalog.index')); ?>" class="text-sm font-medium text-neutral-600 hover:text-primary transition-colors">Marketplace</a>
                    <a href="<?php echo e(route('catalog.index')); ?>" class="text-sm font-medium text-neutral-600 hover:text-primary transition-colors">Categories</a>
                    <?php if(auth()->check() && auth()->user()->isSeller()): ?>
                        <a href="<?php echo e(route('seller.dashboard')); ?>" class="text-sm font-medium text-neutral-600 hover:text-primary transition-colors">Sell</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <form action="<?php echo e(route('catalog.index')); ?>" method="GET" class="hidden md:flex relative">
                    <label for="search" class="sr-only">Search products</label>
                    <input type="search" name="q" id="search" placeholder="Search digital products..."
                        value="<?php echo e(request()->get('q')); ?>"
                        class="w-64 pl-10 pr-4 py-2 text-sm bg-neutral-100 border-0 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary/20 transition-all"
                        aria-label="Search">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </form>

                <div class="hidden sm:flex items-center gap-2">
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('login')); ?>" class="text-sm font-medium text-neutral-600 hover:text-primary transition-colors">Sign in</a>
                        <a href="<?php echo e(route('register')); ?>" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-dark transition-colors">Get Started</a>
                    <?php else: ?>
                        <div class="relative group">
                            <button class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-neutral-600 hover:text-primary transition-colors rounded-lg hover:bg-neutral-100" aria-expanded="false" aria-haspopup="true">
                                <?php if(auth()->user()->avatar_path): ?>
                                    <img src="<?php echo e(asset('storage/' . auth()->user()->avatar_path)); ?>" alt="" class="w-8 h-8 rounded-full">
                                <?php else: ?>
                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-medium">
                                        <?php echo e(strtoupper(auth()->user()->name[0])); ?>

                                    </div>
                                <?php endif; ?>
                                <span class="hidden sm:block"><?php echo e(auth()->user()->name); ?></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-neutral-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 py-1">
                                <a href="<?php echo e(route('account.dashboard')); ?>" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">Dashboard</a>
                                <a href="<?php echo e(route('account.library')); ?>" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">My Library</a>
                                <a href="<?php echo e(route('account.purchases')); ?>" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">Purchases</a>
                                <a href="<?php echo e(route('account.profile')); ?>" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">Settings</a>
                                <?php if(auth()->user()->isSeller()): ?>
                                    <a href="<?php echo e(route('seller.dashboard')); ?>" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">Seller Dashboard</a>
                                <?php endif; ?>
                                <?php if(auth()->user()->isAdmin()): ?>
                                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">Admin Panel</a>
                                <?php endif; ?>
                                <hr class="my-1 border-neutral-200">
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-neutral-50">Sign Out</button>
                                </form>
                            </div>
                        </div>
                        
                        <a href="<?php echo e(route('cart.index')); ?>" class="relative p-2 text-neutral-600 hover:text-primary transition-colors rounded-lg hover:bg-neutral-100" aria-label="Shopping cart">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <?php $cartCount = collect(session()->get('cart', []))->sum('quantity'); ?>
                            <?php if($cartCount > 0): ?>
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-xs font-bold rounded-full flex items-center justify-center"><?php echo e($cartCount); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                </div>

                <button type="button" class="md:hidden p-2 text-neutral-600 hover:text-primary rounded-lg hover:bg-neutral-100" aria-label="Toggle menu" aria-expanded="false" data-mobile-menu-toggle>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <div class="md:hidden hidden border-t border-neutral-200 bg-white" id="mobile-menu" role="navigation" aria-label="Mobile menu">
        <div class="px-4 py-4 space-y-2">
            <a href="<?php echo e(route('catalog.index')); ?>" class="block px-3 py-2 text-sm font-medium text-neutral-600 hover:text-primary rounded-lg">Marketplace</a>
            <a href="<?php echo e(route('catalog.index')); ?>" class="block px-3 py-2 text-sm font-medium text-neutral-600 hover:text-primary rounded-lg">Categories</a>
            <?php if(auth()->check() && auth()->user()->isSeller()): ?>
                <a href="<?php echo e(route('seller.dashboard')); ?>" class="block px-3 py-2 text-sm font-medium text-neutral-600 hover:text-primary rounded-lg">Seller Dashboard</a>
            <?php endif; ?>
            <?php if(auth()->guard()->guest()): ?>
                <a href="<?php echo e(route('login')); ?>" class="block px-3 py-2 text-sm font-medium text-neutral-600 hover:text-primary rounded-lg">Sign in</a>
                <a href="<?php echo e(route('register')); ?>" class="block px-3 py-2 text-sm font-medium text-white bg-primary rounded-lg text-center mt-2">Get Started</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.querySelector('[data-mobile-menu-toggle]');
        const menu = document.getElementById('mobile-menu');
        if (toggle && menu) {
            toggle.addEventListener('click', function() {
                menu.classList.toggle('hidden');
                this.setAttribute('aria-expanded', menu.classList.contains('hidden') ? 'false' : 'true');
            });
        }
    });
</script><?php /**PATH C:\Users\User\Desktop\Ahmed\digitalfanda\resources\views\components\navbar.blade.php ENDPATH**/ ?>