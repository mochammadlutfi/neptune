<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Order - {{ $data->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }
        
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .header-table td {
            vertical-align: top;
            padding: 0;
        }
        
        .company-info {
            width: 50%;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 10px;
            color: #666;
            line-height: 1.4;
        }
        
        .document-info {
            width: 50%;
            text-align: right;
        }
        
        .document-title {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        
        .document-number {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .document-date {
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-confirmed {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-done {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-cancel {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .parties-section {
            width: 100%;
            margin-bottom: 30px;
        }
        
        .parties-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .parties-table td {
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        
        .parties-table td:last-child {
            padding-right: 0;
        }
        
        .party-title {
            font-size: 12px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .party-details {
            font-size: 11px;
            line-height: 1.5;
        }
        
        .order-details {
            background: #f8fafc;
            padding: 15px;
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }
        
        .order-details-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .order-details-table td {
            width: 33.33%;
            vertical-align: top;
            padding: 5px 10px 5px 0;
        }
        
        .detail-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 3px;
            display: block;
        }
        
        .detail-value {
            font-size: 11px;
            font-weight: 500;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }
        
        .items-table th {
            background: #2563eb;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #1e40af;
        }
        
        .items-table td {
            padding: 10px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            vertical-align: top;
        }
        
        .items-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .product-info {
            font-weight: 500;
        }
        
        .product-sku {
            font-size: 9px;
            color: #666;
            margin-bottom: 2px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals-section {
            width: 100%;
            margin-top: 20px;
        }
        
        .totals-wrapper {
            width: 100%;
        }
        
        .totals-table {
            width: 300px;
            margin-left: auto;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 8px 12px;
            font-size: 11px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .totals-table .label {
            text-align: right;
            color: #666;
            font-weight: 500;
            width: 60%;
        }
        
        .totals-table .value {
            text-align: right;
            font-weight: bold;
            width: 40%;
        }
        
        .total-final {
            border-top: 2px solid #2563eb;
            border-bottom: 2px solid #2563eb;
            font-size: 13px;
            background: #f8fafc;
        }
        
        .total-final td {
            font-weight: bold;
            font-size: 13px;
        }
        
        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background: #fefce8;
            border-left: 4px solid #eab308;
        }
        
        .notes-title {
            font-size: 12px;
            font-weight: bold;
            color: #a16207;
            margin-bottom: 8px;
        }
        
        .notes-content {
            font-size: 10px;
            color: #a16207;
            line-height: 1.5;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        .delivery-info {
            margin-top: 20px;
            padding: 15px;
            background: #f0f9ff;
            border-left: 4px solid #0284c7;
        }
        
        .delivery-title {
            font-size: 12px;
            font-weight: bold;
            color: #0369a1;
            margin-bottom: 8px;
        }
        
        .delivery-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .delivery-table td {
            padding: 5px 0;
            font-size: 10px;
            border-bottom: 1px solid #e0f2fe;
        }
        
        .delivery-table .label {
            font-weight: bold;
            color: #0369a1;
            width: 30%;
        }
        
        @page {
            margin: 15mm;
            size: A4;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        /* Utility classes for better spacing */
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        .mb-4 { margin-bottom: 20px; }
        
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }
        .mt-4 { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="company-info">
                        <div class="company-name">{{ config('app.name', 'Your Company') }}</div>
                        <div class="company-details">
                            {{ config('app.address', 'Company Address') }}<br>
                            Phone: {{ config('app.phone', 'Phone Number') }}<br>
                            Email: {{ config('app.email', 'email@company.com') }}
                        </div>
                    </td>
                    <td class="document-info">
                        <div class="document-title">SALES ORDER</div>
                        <div class="document-number">{{ $data->name }}</div>
                        <div class="document-date">
                            Date: {{ \Carbon\Carbon::parse($data->date)->format('d M Y') }}
                        </div>
                        <div class="status-badge status-{{ $data->state }}">
                            {{ ucfirst($data->state) }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Parties Information -->
        <div class="parties-section">
            <table class="parties-table">
                <tr>
                    <td>
                        <div class="party-title">Bill To:</div>
                        <div class="party-details">
                            <strong>{{ $data->partner->name }}</strong><br>
                            @if($data->partner->email)
                                Email: {{ $data->partner->email }}<br>
                            @endif
                            @if($data->partner->phone)
                                Phone: {{ $data->partner->phone }}<br>
                            @endif
                            @if($data->partner->address)
                                {{ $data->partner->address }}
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="party-title">Ship To:</div>
                        <div class="party-details">
                            <strong>{{ $data->location->completed_name ?? $data->location->name }}</strong><br>
                            Warehouse Location
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Order Details -->
        <div class="order-details">
            <table class="order-details-table">
                <tr>
                    <td>
                        <span class="detail-label">Payment Terms</span>
                        <span class="detail-value">{{ $data->payment_term }} Days</span>
                    </td>
                    <td>
                        <span class="detail-label">Shipping Status</span>
                        <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $data->shipping_status)) }}</span>
                    </td>
                    <td>
                        <span class="detail-label">Invoice Status</span>
                        <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $data->invoice_status)) }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 35%;">Product</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 10%;" class="text-center">Unit</th>
                    <th style="width: 12%;" class="text-right">Unit Price</th>
                    <th style="width: 8%;" class="text-center">Disc</th>
                    <th style="width: 10%;" class="text-center">Tax</th>
                    <th style="width: 15%;" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data->lines as $line)
                <tr>
                    <td>
                        <div class="product-info">
                            @if($line->variant && $line->variant->sku)
                                <div class="product-sku">[{{ $line->variant->sku }}]</div>
                            @endif
                            {{ $line->product->name }}
                            @if($line->variant && $line->variant->name)
                                <br><small>({{ $line->variant->name }})</small>
                            @endif
                        </div>
                    </td>
                    <td class="text-center">{{ number_format($line->qty, 0) }}</td>
                    <td class="text-center">
                        {{ $line->unit->name }}
                        @if($line->unit->short_name)
                            <br>({{ $line->unit->short_name }})
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($line->price_unit, 2) }}</td>
                    <td class="text-center">
                        @if($line->disc_type == 'fixed')
                            {{ number_format($line->disc_value, 0) }}
                        @else
                            {{ $line->disc_value }}%
                        @endif
                    </td>
                    <td class="text-center">
                        @if($line->tax)
                            {{ $line->tax->name }}<br>({{ $line->tax->rate }}%)
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($line->price_subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Delivery Information (if confirmed) -->
        @if($data->state == 'confirmed')
        <div class="delivery-info">
            <div class="delivery-title">Delivery Information:</div>
            <table class="delivery-table">
                <tr>
                    <td class="label">Shipping Status:</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $data->shipping_status)) }}</td>
                </tr>
                <tr>
                    <td class="label">Total Items:</td>
                    <td>{{ $data->lines->sum('qty') }} items</td>
                </tr>
                <tr>
                    <td class="label">Items Delivered:</td>
                    <td>{{ $data->lines->sum('qty_delivered') }} items</td>
                </tr>
                <tr>
                    <td class="label">Items Invoiced:</td>
                    <td>{{ $data->lines->sum('qty_invoiced') }} items</td>
                </tr>
            </table>
        </div>
        @endif

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-wrapper">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="value">{{ number_format($data->amount_untaxed, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tax:</td>
                        <td class="value">{{ number_format($data->amount_tax, 2) }}</td>
                    </tr>
                    <tr class="total-final">
                        <td class="label">Total:</td>
                        <td class="value">{{ number_format($data->amount_total, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Notes Section -->
        @if($data->note)
        <div class="notes-section">
            <div class="notes-title">Notes:</div>
            <div class="notes-content">
                {{ $data->note }}
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>This document was generated on {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
            <p>{{ config('app.name') }} - Sales Order #{{ $data->name }}</p>
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>