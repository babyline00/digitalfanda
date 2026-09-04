<?php $__env->startSection('title', ' - Marketplace'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-neutral-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-2xl border border-neutral-200 p-6 sticky top-24">
                    <h3 class="font-bold text-dark mb-4">Filters</h3>
                    
                    <form method="GET" action="<?php echo e(route('catalog.index')); ?>" class="space-y-6">
                        <!-- Search -->
                        <input type="hidden" name="q" value="<?php echo e(request()->get('q')); ?>">
                        
                        <!-- Categories -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-3">Categories</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="category" value="" class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary/20" <?php echo e(!request()->has('category') ? 'checked' : ''); ?>>
                                    <span class="text-sm text-neutral-700">All Categories</span>
                                </label>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="category" value="<?php echo e($category->slug); ?>" class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary/20" <?php echo e(request()->get('category') === $category->slug ? 'checked' : ''); ?>>
                                        <span class="text-sm text-neutral-700"><?php echo e($category->name); ?></span>
                                        <span class="text-xs text-neutral-400 ml-auto"><?php echo e($category->products()->where('status', 'published')->count()); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        
                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-3">Price Range</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="min_price" placeholder="Min" value="<?php echo e(request()->get('min_price')); ?>" class="w-full px-3 py-2 text-sm border border-neutral-300 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20">
                                <span class="text-neutral-400">-</span>
                                <input type="number" name="max_price" placeholder="Max" value="<?php echo e(request()->get('max_price')); ?>" class="w-full px-3 py-2 text-sm border border-neutral-300 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20">
                            </div>
                        </div>
                        
                        <!-- Product Type -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-3">Type</label>
                            <div class="space-y-2">
                                <?php $__currentLoopData = ['download' => 'Download', 'stream' => 'Stream', 'course' => 'Course', 'license_key' => 'License Key']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="type" value="<?php echo e($type); ?>" class="w-4 h-4 text-primary border-neutral-300 rounded focus:ring-primary/20" <?php echo e(request()->get('type') === $type ? 'checked' : ''); ?>>
                                        <span class="text-sm text-neutral-700"><?php echo e($label); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        
                        <!-- Sort -->
                        <div>
                            <label for="sort" class="block text-sm font-medium text-dark mb-2">Sort By</label>
                            <select name="sort" id="sort" class="w-full px-3 py-2 text-sm border border-neutral-300 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20 bg-white">
                                <option value="popularity" <?php echo e(request()->get('sort') === 'popularity' ? 'selected' : ''); ?>>Popularity</option>
                                <option value="newest" <?php echo e(request()->get('sort') === 'newest' ? 'selected' : ''); ?>>Newest</option>
                                <option value="price_asc" <?php echo e(request()->get('sort') === 'price_asc' ? 'selected' : ''); ?>>Price: Low to High</option>
                                <option value="price_desc" <?php echo e(request()->get('sort') === 'price_desc' ? 'selected' : ''); ?>>Price: High to Low</option>
                                <option value="rating" <?php echo e(request()->get('sort') === 'rating' ? 'selected' : ''); ?>>Highest Rated</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="w-full px-4 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors">
                            Apply Filters
                        </button>
                        
                        <?php if(request()->hasAny(['category', 'min_price', 'max_price', 'type', 'sort'])): ?>
                            <a href="<?php echo e(route('catalog.index')); ?>" class="block w-full text-center text-sm text-primary hover:underline">
                                Clear All Filters
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </aside>
            
            <!-- Product Grid -->
            <main class="flex-1 min-w-0">
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-dark"><?php echo e($categories->where('slug', request()->get('category'))->first()?->name ?? 'All Products'); ?></h1>
                        <p class="text-neutral-600 mt-1"><?php echo e($products->total()); ?> products found</p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 border border-neutral-300 rounded-xl overflow-hidden bg-white">
                            <button class="px-4 py-2 text-primary font-semibold border-r border-neutral-300 bg-primary/5" data-view="grid">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            </button>
                            <button class="px-4 py-2 text-neutral-500 hover:text-primary" data-view="list">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <?php if($products->isEmpty()): ?>
                    <div class="text-center py-20">
                        <svg class="w-16 h-16 mx-auto text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 class="text-xl font-semibold text-dark mb-2">No products found</h3>
                        <p class="text-neutral-500 mb-6">Try adjusting your filters or search terms</p>
                        <a href="<?php echo e(route('catalog.index')); ?>" class="px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors inline-block">
                            Clear Filters
                        </a>
                    </div>
                <?php else: ?>
                    <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" data-view="grid">
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('components.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php echo e($products->links()); ?>

                <?php endif; ?>
            </main>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Desktop\Ahmed\digitalfanda\resources\views\storefront\catalog\index.blade.php ENDPATH**/ ?>