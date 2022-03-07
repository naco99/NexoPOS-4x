<?php
?>
<div id="receipt">
	
	<div class="identity">
		<h1>
			Mr Flavor
			<span class="inline-block company_name">by Flavour Masters</span>
		</h1>
	</div>
	
	<div class="legal bb">
		<span class="inline-block">ICE: 098477294778</span>
		<span class="inline-block">TP: 78837R880D7R</span>
		<span class="inline-block">IF: 479893E7489</span>
	</div>
	
	<div class="order_info bb">
		<span class="inline-block">{{ now()->format('d/m/Y H:i') }}</span>
		<span class="inline-block">N°: {{ $order->code }}</span>
	</div>
	
	<div class="order_content bb">
		@foreach( Hook::filter( 'ns-receipt-products', $order->combinedProducts ) as $product )
			<div class="item">
                <span class="inline-block">
                    {{ $product->name }} <br/>
                    <span class="unit">{{ $product->unit->name }}</span>
                </span>
				@if($product->quantity > 1)
					<span class="inline-block">{{ $product->quantity }} x {{ ns()->currency->define( $product->unit_price ) }}</span>
				@endif
				<span class="inline-block">{{ ns()->currency->define( $product->total_price ) }}</span>
			</div>
		@endforeach
	
	</div>
	
	@if ( $order->discount > 0 && $order->shipping > 0)
	<div class="summary bb">
		@if ( $order->discount > 0 )
			<div class=summary_line>
			<span class="inline-block sum_label">
				{{ __( 'Discount' ) }}
				@if ( $order->discount_type === 'percentage' )
					({{ $order->discount_percentage }}%)
				@endif
				:
			</span>
				<span class="inline-block sum_value">{{ ns()->currency->define( $order->discount ) }}</span>
			</div>
		@endif
		
		@if ( $order->shipping > 0 )
			<div class=summary_line>
				<span class="inline-block sum_label">{{ __( 'Shipping' ) }}:</span>
				<span class="inline-block sum_value">{{ ns()->currency->define( $order->shipping ) }}</span>
			</div>
		@endif
	</div>
	@endif
	
	<div class="total bb">
		<span class="inline-block">{{ __( 'Total' ) }}:</span>
		<span class="inline-block">{{ ns()->currency->define( $order->total ) }}</span>
	</div>
	
	<!-- <div class="payment bb">
		<div class=payment_line>
			<span class="inline-block label">Esp:</span>
			<span class="inline-block value">200,00</span>
		</div>
		<div class=payment_line>
			<span class="inline-block label">Change:</span>
			<span class="inline-block value">23,00</span>
		</div>
	</div> -->
	
	
	<div class="footer">
		
		<div class="footer_line">
			Commandez sur notre site <span class="website">www.MrFlavor.ma</span>
		</div>
		
		<div class="footer_line">
			TEL: 05 00 12 22 78 98
		</div>
		<div class="footer_line">
			Mr Flavour vous remercie et vous souhaite un très bon appétit 😊
		</div>
	</div>
</div>


@includeWhen( request()->query( 'autoprint' ) === 'true', '/pages/dashboard/orders/templates/_autoprint' )