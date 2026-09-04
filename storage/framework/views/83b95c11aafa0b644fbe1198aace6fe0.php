<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product', 'rank' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['product', 'rank' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<article class="group bg-white rounded-2xl border border-neutral-200 overflow-hidden hover:shadow-xl hover:border-primary/30 transition-all duration-300 relative">
    <?php if($rank !== null && $rank <= 3): ?>
        <div class="absolute top-3 left-3 z-10">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary text-white font-bold text-sm">
                <?php echo e($rank); ?>

            </span>
        </div>
    <?php endif; ?>
    
    <?php if($product->is_featured): ?>
        <div class="absolute top-3 right-3 z-10">
            <span class="px-2 py-0.5 text-xs font-semibold text-white bg-secondary rounded-full">
                Featured
            </span>
        </div>
    <?php endif; ?>
    
    <a href="<?php echo e(route('product.show', $product)); ?>" class="block">
        <?php if($product->thumbnail_path): ?>
            <div class="aspect-video relative overflow-hidden bg-neutral-100">
                <img src="<?php echo e(asset('storage/' . $product->thumbnail_path)); ?>"
                     alt="<?php echo e($product->title); ?>"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            </div>
        <?php else: ?>
            <div class="aspect-video relative overflow-hidden bg-neutral-100 flex items-center justify-center">
                <svg class="w-16 h-16 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        <?php endif; ?>
    </a>
    
    <div class="p-5">
        <div class="flex items-center gap-2 mb-2">
            <?php if($product->category): ?>
                <a href="<?php echo e(route('catalog.category', $product->category)); ?>"
                   class="text-xs px-2 py-0.5 bg-primary/10 text-primary rounded-full hover:bg-primary/20 transition-colors">
                    <?php echo e($product->category->name); ?>

                </a>
            <?php endif; ?>
            <span class="text-xs text-neutral-400 capitalize"><?php echo e($product->type); ?></span>
        </div>
        
        <h3 class="font-bold text-dark group-hover:text-primary transition-colors line-clamp-2 mb-2">
            <a href="<?php echo e(route('product.show', $product)); ?>"><?php echo e($product->title); ?></a>
        </h3>
        
        <?php if($product->seller): ?>
            <p class="text-sm text-neutral-500 mb-3 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <?php echo e($product->seller->store_name); ?>

            </p>
        <?php endif; ?>
        
        <div class="flex items-center justify-between">
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-dark"><?php echo e($product->getFormattedPrice()); ?></span>
                <?php if($product->getFormattedCompareAtPrice()): ?>
                    <span class="text-lg text-neutral-400 line-through"><?php echo e($product->getFormattedCompareAtPrice()); ?></span>
                    <span class="text-sm font-semibold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">
                        -<?php echo e($product->getDiscountPercent()); ?>%
                    </span>
                <?php endif; ?>
            </div>
            
            <?php if($product->rating_count > 0): ?>
                <div class="flex items-center gap-1 text-sm text-neutral-500">
                    <svg class="w-4 h-4 text-yellow-400 fill-current" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <span><?php echo e(number_format($product->rating_avg, 1)); ?></span>
                    <span class="text-neutral-400">(<?php echo e($product->rating_count); ?>)</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</article><?php /**PATH C:\Users\User\Desktop\Ahmed\digitalfanda\resources\views\components\product-card.blade.php ENDPATH**/ ?>