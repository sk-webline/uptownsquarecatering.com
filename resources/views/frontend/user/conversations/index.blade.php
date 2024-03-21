@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="sk-titlebar mt-20px mb-15px mb-lg-40px">
        <h1 class="fs-16 sm-fs-20 fw-500 m-0">{{ translate('Messages') }}</h1>
    </div>

    <div class="bg-account pb-25px fs-13 md-fs-16 lg-fs-12 xxl-fs-16 overflow-hidden">
        <div class="px-15px">
            <h2 class="fs-16 fw-600 border-bottom border-default-200 text-default-50 mb-25px mb-md-40px py-10px lh-1">{{translate('Our Team')}}</h2>
        </div>
        <div class="px-15px chat-messages-scroll c-scrollbar">
            <div class="chat-messages">
                <div class="message-item received">
                    <div class="row gutters-5">
                        <div class="col col-45px">
                            <div class="product-res-images rounded-50 overflow-hidden">
                                <img src="{{static_asset('assets/img/icons/logo-profile.svg')}}" alt="" class="absolute-full h-100 img-fit">
                            </div>
                        </div>
                        <div class="col col-md-grow-45px message-item-text">
                            <div class="message-item-text-content">
                                <div class="message-item-content">{{translate('How we can help you?')}}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($conversation != null)
                    @php
                        $messages = \App\Message::where('conversation_id', $conversation->id)->get();
                    @endphp
                    @foreach($messages as $message)
                        @php
                            $message_type = ($message->user_id == Auth::user()->id) ? 'send' : 'received';
                        @endphp
                        <div class="message-item {{$message_type}}">
                            <div class="row gutters-5 message-item-row">
                                <div class="col col-45px">
                                    @if($message_type=='send')
                                        <div class="product-res-images with-border rounded-50 overflow-hidden">
                                            <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}"
                                                 onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.svg') }}';"
                                                 alt=""
                                                 class="absolute-full h-100 img-fit">
                                        </div>
                                    @else
                                        <div class="product-res-images rounded-50 overflow-hidden">
                                            <img src="{{static_asset('assets/img/icons/logo-profile.svg')}}" alt="" class="absolute-full h-100 img-fit">
                                        </div>
                                    @endif
                                </div>
                                <div class="col col-md-grow-45px message-item-text">
                                    <div class="message-item-text-content">
                                        <div class="message-item-content">{{$message->message}}</div>
                                        <div class="message-item-date">{{ date('d/m/Y H:i', strtotime($message->created_at)) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="px-15px">
            <div class="mt-40px fs-14 sm-fs-18">
                @if($conversation != null)
                    <form action="{{ route('messages.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                        <div class="form-group mb-20px mb-md-30px">
                            <textarea class="form-control resize-off message-textarea border border-black-100" placeholder="{{ translate('Type your message here...') }}" rows="4" name="message"></textarea>
                            @if ($errors->has('message'))
                                <div class="invalid-feedback fs-12 d-block" role="alert">
                                    {{ $errors->first('message') }}
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-outline-secondary w-100 md-mw-400px fs-16 md-fs-18">{{ toUpper(translate('Sent Message')) }}</button>
                        </div>
                    </form>
                @else
                    <form action="{{ route('conversations.store_account_chat') }}" method="POST">
                        @csrf
                        <div class="form-group mb-20px mb-md-30px">
                            <textarea class="form-control resize-off message-textarea" rows="4" name="message" placeholder="{{ translate('Type your message here...') }}"></textarea>
                            @if ($errors->has('message'))
                                <div class="invalid-feedback fs-12 d-block" role="alert">
                                    {{ $errors->first('message') }}
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-outline-secondary w-100 md-mw-400px fs-16 md-fs-18">{{ toUpper(translate('Sent Message')) }}</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.message-item').each(function() {
            var prev = $(this).prev();
            if(prev.hasClass('send') && $(this).hasClass('send')) {
                prev.find('.message-item-date').remove();
            }
            if(prev.hasClass('received') && $(this).hasClass('received')) {
                prev.find('.message-item-date').remove();
            }
        });

        $(".chat-messages-scroll").animate({
            scrollTop: $('.chat-messages').height()
        }, 1000);
    </script>
@endsection
