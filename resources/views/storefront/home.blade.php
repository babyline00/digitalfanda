@extends('layouts.app')

@section('title', ' - Home')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-br from-primary/5 via-white to-secondary/5">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%236C5CE7%22 fill-opacity=%220.03%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-primary/10 via-transparent to-secondary/10"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-32">
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 text-primary text-sm font-medium mb-6">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                New: Courses & Templates now available
            </span>
            
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-dark tracking-tight mb-6 leading-tight">
                Digital Products
                <span class="block text-primary">That Sell Themselves</span>
            </h1>
            
            <p class="text-lg sm:text-xl text-neutral-600 mb-8 max-w-2xl">
                Build your digital empire. Sell ebooks, courses, software, templates, and more — 
                with instant delivery, secure payments, and zero technical hassle.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 mb-12">
                <a href="{{ route('catalog.index') }}" class="w-full sm:w-auto px-8 py-4 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-all duration-200 shadow-lg shadow-primary/25 text-center">
                    Explore Marketplace
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
                @guest
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary/5 transition-all duration-200 text-center">
                        Start Selling Free
                    </a>
                @else
                    <a href="{{ route('seller.dashboard') }}" class="w-full sm:w-auto px-8 py-4 border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary/5 transition-all duration-200 text-center">
                        Go to Dashboard
                    </a>
                @endguest
            </div>
            
            <!-- Trust indicators -->
            <div class="flex flex-wrap items-center gap-8 text-sm text-neutral-500">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>10K+ Creators</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Secure Payments</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>
                    <span>Instant Delivery</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Floating product cards preview -->
    <div class="absolute bottom-0 left-0 right-0 -translate-y-1/2 hidden lg:block pointer-events-none">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="grid grid-cols-3 gap-4">
                @foreach(['Ebook' => '📚', 'Course' => '🎓', 'Template' => '🎨'] as $type => $icon)
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-xl border border-neutral-200/50 transform hover:-translate-y-2 transition-transform duration-300">
                        <div class="text-4xl mb-3">{{ $icon }}</div>
                        <h3 class="font-bold text-dark">{{ $type }}s</h3>
                        <p class="text-sm text-neutral-500 mt-1">Ready to sell</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Search & Categories -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Search Bar -->
        <form action="{{ route('catalog.index') }}" method="GET" class="mb-12">
            <div class="relative max-w-3xl mx-auto">
                <label for="hero-search" class="sr-only">Search products</label>
                <input type="search" name="q" id="hero-search" placeholder="What are you looking for? Ebooks, courses, templates, software..."
                    class="w-full px-6 py-5 pl-14 text-lg bg-neutral-100 border-0 rounded-2xl focus:bg-white focus:ring-4 focus:ring-primary/20 transition-all"
                    aria-label="Search products">
                <div class="absolute left-5 top-1/2 -translate-y-1/2">
                    <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 px-6 py-2.5 bg-primary text-white font-medium rounded-xl hover:bg-primary-dark transition-colors">
                    Search
                </button>
            </div>
        </form>

        <!-- Category Pills -->
        <div class="flex flex-wrap justify-center gap-3">
            @foreach($categories as $category)
                <a href="{{ route('catalog.category', $category) }}"
                   class="px-5 py-2.5 bg-neutral-100 hover:bg-primary/10 hover:text-primary text-neutral-600 hover:border-primary border border-neutral-200 rounded-full text-sm font-medium transition-all duration-200 flex items-center gap-2">
                    @if($category->icon)
                        <span class="text-lg">{{ $category->icon }}</span>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    @endif
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products -->
@if($featuredProducts->count())
<section class="py-16 bg-neutral-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-dark">Featured Products</h2>
                <p class="text-neutral-600 mt-1">Hand-picked by our team</p>
            </div>
            <a href="{{ route('catalog.index') }}" class="text-primary font-semibold hover:underline flex items-center gap-1">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Trending Products -->
@if($trendingProducts->count())
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-dark">Trending Now</h2>
                <p class="text-neutral-600 mt-1">Most popular this week</p>
            </div>
            <a href="{{ route('catalog.index', ['sort' => 'popularity']) }}" class="text-primary font-semibold hover:underline flex items-center gap-1">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($trendingProducts as $index => $product)
                @include('components.product-card', ['product' => $product, 'rank' => $index + 1])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Categories Grid -->
@if($categories->count())
<section class="py-16 bg-neutral-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-dark">Browse by Category</h2>
            <p class="text-neutral-600 mt-1">Find exactly what you need</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('catalog.category', $category) }}"
                   class="group bg-white rounded-2xl p-6 border border-neutral-200 hover:border-primary/50 hover:shadow-xl transition-all duration-300 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-primary/10 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                        @if($category->icon)
                            <span class="text-3xl">{{ $category->icon }}</span>
                        @else
                            <svg class="w-8 h-8 text-primary group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        @endif
                    </div>
                    <h3 class="font-semibold text-dark group-hover:text-primary transition-colors">{{ $category->name }}</h3>
                    <p class="text-sm text-neutral-500 mt-1">{{ $category->products()->where('status', 'published')->count() }} products</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- New Arrivals -->
@if($newArrivals->count())
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-dark">New Arrivals</h2>
                <p class="text-neutral-600 mt-1">Fresh additions to the marketplace</p>
            </div>
            <a href="{{ route('catalog.index', ['sort' => 'newest']) }}" class="text-primary font-semibold hover:underline flex items-center gap-1">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($newArrivals as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-20 bg-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl bg-gradient-to-r from-primary to-purple-600 p-12 md:p-16 text-center overflow-hidden">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%220.03%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
            
            <div class="relative max-w-3xl mx-auto">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                    Ready to Start Selling?
                </h2>
                <p class="text-primary-100 text-lg mb-8">
                    Join thousands of creators earning passive income. 
                    Set up your store in minutes — no coding required.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @guest
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-primary font-bold rounded-xl hover:bg-neutral-100 transition-colors shadow-lg">
                            Create Free Account
                        </a>
                    @else
                        <a href="{{ route('seller.dashboard') }}" class="px-8 py-4 bg-white text-primary font-bold rounded-xl hover:bg-neutral-100 transition-colors shadow-lg">
                            Open Your Store
                        </a>
                    @endguest
                    <a href="#"
                       class="px-8 py-4 border-2 border-white text-white font-bold rounded-xl hover:bg-white/10 transition-colors">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="py-16 bg-neutral-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-dark mb-2">Stay Updated</h2>
        <p class="text-neutral-600 mb-6">Get the latest products, tips, and deals delivered to your inbox.</p>
        
        <form id="newsletter-form" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto" action="{{ route('newsletter.subscribe') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="your@email.com" required
                class="flex-1 px-5 py-3.5 text-base bg-white border border-neutral-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                aria-label="Email address">
            <button type="submit"
                class="px-8 py-3.5 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors whitespace-nowrap">
                Subscribe
            </button>
        </form>
        <p class="text-xs text-neutral-500 mt-3">No spam. Unsubscribe anytime.</p>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.getElementById('newsletter-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    
    btn.disabled = true;
    btn.textContent = 'Subscribing...';
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                email: form.email.value,
            }),
        });
        
        const data = await response.json();
        
        if (data.success) {
            btn.textContent = 'Subscribed!';
            btn.classList.remove('bg-primary', 'hover:bg-primary-dark');
            btn.classList.add('bg-secondary');
            form.reset();
        } else {
            throw new Error(data.message || 'Something went wrong');
        }
    } catch (error) {
        btn.textContent = 'Error - Try Again';
        btn.classList.remove('bg-primary', 'hover:bg-primary-dark');
        btn.classList.add('bg-red-600');
    }
    
    setTimeout(() => {
        btn.disabled = false;
        btn.textContent = originalText;
        btn.className = 'px-8 py-3.5 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition-colors whitespace-nowrap';
    }, 3000);
});
</script>
@endsection