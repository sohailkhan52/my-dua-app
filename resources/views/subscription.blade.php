@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @foreach($plans as  $plan)
            <div class="col-md-4 mb-4">
                <div class="card h-100 {{ $currentSubscription === $plan['price_id'] ? 'border-primary' : '' }}">
                    <div class="card-body text-center">
                        <h3 class="card-title">{{ $plan['name'] }}</h3>
                        <div class="display-4 mb-3">
                            ${{ $plan['price'] }}
                            <small class="text-muted">/{{ $plan['interval'] }}</small>
                        </div>
                        <ul class="list-unstyled mt-3 mb-4">
                            @foreach($plan['features'] as $feature)
                                <li>✓ {{ $feature }}</li>
                            @endforeach
                        </ul>

                        @auth
                            @if($currentSubscription === $plan['price_id'])
                                <!-- Currently subscribed -->
                                <button class="btn btn-success w-100" disabled>
                                    Current Plan
                                </button>
                            @elseif($currentSubscription)
                                <!-- User has a different plan - show upgrade/downgrade -->
                                <form action="{{ route('subscription.change-plan') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="new_price_id" value="{{ $plan['price_id'] }}">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Switch to {{ $plan['name'] }}
                                    </button>
                                </form>
                            @else
                                <!-- No subscription - show subscribe button -->
                                <form action="{{ route('subscription.checkout') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="price_id" value="{{ $plan['price_id'] }}">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Subscribe
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100">
                                Sign Up to Subscribe
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @auth
        @php
            $subscription = auth()->user()->subscription('default');
        @endphp
        
        @if(auth()->user()->subscribed('default'))
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Your Subscription Status</h4>
                        </div>
                        <div class="card-body">
                            @if($subscription->onTrial())
                                <div class="alert alert-info">
                                    You're on a trial period until {{ $subscription->trial_ends_at->format('F j, Y') }}.
                                </div>
                            @endif
                            
                            @if($subscription->cancelled())
                                <div class="alert alert-warning">
                                    Your subscription has been cancelled and will end on 
                                    {{ $subscription->ends_at->format('F j, Y') }}.
                                    
                                    <form action="{{ route('subscription.resume') }}" method="POST" class="mt-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            Resume Subscription
                                        </button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('subscription.cancel') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Are you sure? You will lose access at the end of your billing period.')">
                                        Cancel Subscription
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth
</div>
@endsection