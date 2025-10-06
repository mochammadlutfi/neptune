<?php

namespace App\Services;

use App\Models\Stock\StockPicking;
use App\Models\Stock\StockMove;
use App\Models\Stock\StockQuant;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Create a stock picking and its associated stock moves.
     *
     * @param string $transactionType Model class for the transaction (e.g., PurchaseOrder::class)
     * @param int $transactionId ID of the transaction
     * @param string $operationType Type of operation (e.g., receipts, delivery, etc.)
     * @param array $lines Array of line items, each containing product_id, quantity, uom_id, source_location_id, and destination_location_id
     * @param int|null $sourceLocationId Default source location (optional, overridden by line item if provided)
     * @param int|null $destinationLocationId Default destination location (optional, overridden by line item if provided)
     * @param string|null $scheduledDate Planned date for the operation
     * @param string $status Initial status (default: 'draft')
     * @return StockPicking
     */
    public function createStockPicking(
        string $transactionType,
        int $transactionId,
        string $type,
        array $lines,
        int $partnerId = null,
        int $locationId = null,
        int $destLocationId = null,
        ?string $scheduledDate = null,
        ?string $ref = null,
        string $status = 'draft'
    ) {
        return DB::transaction(function () use (
            $transactionType,
            $transactionId,
            $type,
            $lines,
            $partnerId,
            $locationId,
            $destLocationId,
            $scheduledDate,
            $status
        ) {
            // 1. Create the Stock Picking
            $stockPicking = StockPicking::create([
                'origintable_type' => $transactionType,
                'origintable_id' => $transactionId,
                'type' => $type,
                'partner_id' => $partnerId,
                'location_id' => $locationId,
                'dest_location_id' => $destLocationId,
                'scheduled_date' => $scheduledDate,
                'status' => $status,
            ]);

            // 2. Create Stock Moves for each line
            foreach ($lines as $line) {
                $this->createStockMove(
                    $stockPicking,
                    $line['product_id'],
                    $line['quantity'],
                    $line['uom_id'],
                    $line['source_location_id'] ?? $sourceLocationId,
                    $line['destination_location_id'] ?? $destinationLocationId
                );
            }

            return $stockPicking;
        });
    }

    public function createStockMove(
        int $productId = null,
        int $variantId = null,
        int $srcLocationId = null,
        int $destLocationId = null,
        float $qty = null,
        float $orderedQty = null,
        int $unitId = null,
        ?string $state = 'draft' 
    ){

        return DB::transaction(function () use (
            $productId,
            $variantId,
            $srcLocationId,
            $destLocationId,
            $qty,
            $orderedQty,
            $unitId,
            $state
        ) {
            $stockMove = StockMove::create([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'src_locationid' => $locationId,
                'dest_location_id' => $destLocationId,
                'qty' => $qty,
                'ordered_qty' => $orderedQty,
                'unit_id' => $unitId,
                'status' => $state
            ]);

            return $stockMove;
        });
    }

    
    public function assignWaitingMoves($productId)
    {
        $availableQty = StockQuant::where('product_id', $productId)->sum('quantity');

        $waitingMoves = StockMove::where('product_id', $productId)
            ->where('status', 'waiting')
            ->whereHas('product', fn($q) => $q->where('stock_control', 1))
            ->get();

        foreach ($waitingMoves as $move) {
            if ($availableQty >= $move->quantity) {
                $move->status = 'assigned';
                $move->save();

                $availableQty -= $move->quantity;
            }
        }
    }
}
