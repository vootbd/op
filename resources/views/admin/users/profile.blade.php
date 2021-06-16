@extends('dashboard.layouts.app')

@section('content')
<div class="row user-profile">
    <div class="col-lg-6">
        <div class="d-flex flex-row user-block">
            <div class="img-block shadow">
                <img src="{{ asset('dashboard/image/icons/user-profile.svg') }}" alt="user profile" class="user-image">
            </div>
            <div class="content-block d-flex flex-column justify-content-center">
                <h1 class="name">Alamgir Kabir</h1>
                <p class="mail">
                    <img src="{{ asset('dashboard/image/icons/mail.svg') }}" alt="">
                    <span>kabir@gmail.com</span>
                </p>
                <p class="phone">
                    <img src="{{ asset('dashboard/image/icons/smartphone.svg') }}" alt="">
                    <span>+00 00 42524 255555</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="profile-completer d-flex justify-content-between align-items-center">
            <div id="profileCircle">
            </div>
            <div class="text">
                <p>Profile Complitation</p>
                <a href="" class="btn">complete</a>
            </div>
        </div>
    </div>
</div>

<div class="row user-content">
    <div class="profile-nav">
        <a href="" class="btn btn-link active">Profile info</a>
        <a href="" class="btn btn-link">Update profile</a>
        <a href="" class="btn btn-link">Data information</a>
        <a href="" class="btn btn-link">Data information</a>
        <a href="" class="btn btn-link">Data information</a>
        <a href="" class="btn btn-link">Data information</a>
    </div>
</div>
@endsection

@push('custom-style')
<link href="{{ asset('dashboard/css/profile.css') }}" rel="stylesheet">
@endpush

@push('custom-scripts')
<script src="{{ asset('vendor/progressbar/progressbar.js') }}"></script>
<script>
    var bar = new ProgressBar.Circle(profileCircle, {
    color: '#ffc107',
    strokeWidth: 6,
    trailWidth: 6,
    easing: 'easeInOut',
    duration: 1400,
    text: {
        autoStyleContainer: false
    },
    from: { color: '#fff', width: 6 },
    to: { color: '#ffc107', width: 6 },
    // Set default step function for all animate calls
    step: function(state, circle) {
        circle.path.setAttribute('stroke', state.color);
        circle.path.setAttribute('stroke-width', state.width);

        var value = Math.round(circle.value() * 100);
        if (value === 0) {
        circle.setText('');
        } else {
        circle.setText(value + '%');
        }

    }
    });
    // bar.text.style.fontFamily = '"Raleway", Helvetica, sans-serif';
    // bar.text.style.fontSize = '2rem';

    bar.animate(.5);  // Number from 0.0 to 1.0
</script>
@endpush