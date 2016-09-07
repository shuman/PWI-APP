@extends('header')
@section('content')

{!! HTML::Script( 'js/handlebars.js') !!}
{!! HTML::Script( 'js/user.js') !!}

<div class="user">
    <div class="user-wrpper">
        <div id="sidebar-left">
            @include('navs.sidebarLeft')
        </div>
        <div class="main-content">
            <div class="orders">
                <div  class="widget">
                    <div class="oder-history">
                        <h4>Order History</h4>
                        @if(count($order)>0)
                        <p>List of purchase made</p>
                        <table class="order_table">
                            <tbody>
                                @foreach($order as $ordervalue)
                                @if(!empty($ordervalue->order_date))
                                <tr>
                                    <td class="text-blue">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $ordervalue->order_date)->format('d/m/Y') }}</td> 
                                    <td>{{ $ordervalue->billing_full_name }}</td>
                                    <td class="text-blue">#{{$ordervalue->order_id}}</td>
                                    <td>Status: {{$ordervalue->status_value}}
                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ordervalue->updated_at)->format('d/m/Y') }}
                                    </td> 
                                    <td><button class="btn btn-green text-uppercase order-details btn-sm" data-index="{{$ordervalue->order_id}}">Details</button></td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p>Currently no orders</p>
                        @endif
                    </div>
                </div>

                <div class="oder-history-details">
                    <div class="row">
                        <div class="col-md-12 show-order-details">

                        </div>
                    </div>
                </div>
                <div class="oder-history-message">
                    <div class="widget">
                        <form id="my_message_form">
                            <input type="hidden" name="url" class="base-url" value="{{url('/')}}">
                            <span class="first-name-err"></span>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control first-name" id="first-name" <?php echo!empty(Auth::user()->user_firstname) ? 'value="' . Auth::user()->user_firstname . '"' : 'value=""'; ?> placeholder="Last Name" >
                                </div>
                                <div class="col-md-6">
                                    <span class="last-name-err"></span>
                                    <input type="text" class="form-control last-name" id="last-name" <?php echo!empty(Auth::user()->user_lastname) ? 'value="' . Auth::user()->user_lastname . '"' : 'value=""'; ?> placeholder="First Name">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <span class="order-ref-err"></span>
                                    <select class="form-control order-ref margin-top-10" name="oder_ref">
                                        <option value="">Select Order Reference Number</option>
                                        @foreach($order as $ordervalue)
                                        <option value="{{$ordervalue->order_id}}">Order #{{$ordervalue->order_id}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="margin-top-10"><p class="text-blue">Message</p></div>
                                    <span class="order-msg-err"></span>
                                    <textarea class="form-control order-msg" rows="8" placeholder="Write Message here............."></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 ">
                                    <div class="margin-top-25 margin-bottom-10">
                                        <strong class="success-message"></strong>
                                       
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-right margin-top-10">
                                        <button type="button" class="btn btn-disable bg-grey btn-sm-sw text-uppercase order-msg-cancel">Cancel</button> 
                                        <button type="button" class="btn btn-blue btn-sm-sw text-uppercase send-msg">Send Message</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="sidebar-right">
                @include('navs.sidebarRight')
            </div>
        </div>
    </div>
</div>
@endsection