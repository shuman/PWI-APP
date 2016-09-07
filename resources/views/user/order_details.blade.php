@if(!empty($data))
<div class="widget order-data">
    <h2>Order #{{$order_data->order_id}}</h2>
    <p>Status: {{$order_data->status_value}} {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order_data->updated_at)->format('d/m/Y') }}</p>
    <div class="row">
        <div class="col-md-4">
            <p>Ordered From:<br>
                @if(count($data)>0)
                <?php $od = 0; ?>
                @foreach($data as $order)
                @if($od==0)
                <strong>{{$order->org_name}}</strong><br>
                {{$order->org_addressline1.''.$order->org_addressline2}}<br>
                {{$order->org_city}},{{$order->org_state}}</p>
            @endif
            <?php $od ++ ?>
            @endforeach
            @endif
            <p>Shipped To:<br>
                {{$order_data->shipping_full_name}}<br>
                {{$order_data->shipping_address_line1}}<br>
                {{$order_data->shipping_city}},{{$order_data->shipping_state}}</p>
        </div>
        <div class="col-md-8">
            <table class="order-details-table">
                <thead>
                    <tr>
                        <th>Itemized Order</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-right">Price</th>
                    </tr>
                </thead>
                @if(count($data)>0)
                <tbody>
                    <?php $price_total = 0 ?>
                    @foreach($data as $order_details)
                    <tr>
                        <td>{{$order_details->product_name}}</td>
                        <td class="text-center">{{$order_details->quantity}}</td>
                        <td class="text-right">${{number_format($order_details->product_price, 2)}}</td>
                        <?php $price_total = $price_total + $order_details->product_price ?>
                    </tr>
                    @endforeach
                    <tr>
                        <td>&nbsp;</td>
                        <td class="text-right">Tax</td>
                        <td class="text-right">${{ number_format($order_data->order_tax, 2) }}</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="text-right">Shipping</td>
                        <td class="text-right">${{ number_format($order_data->order_shipping_cost, 2) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Total</th>
                        <th class="text-right">${{ number_format($price_total+$order_data->order_shipping_cost+$order_data->order_tax,2 )}}</th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    <button class="btn btn-blue btn-sm-w text-uppercase contact-message">Contact</button>
</div>
@endif