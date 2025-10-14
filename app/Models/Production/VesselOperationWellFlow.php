<?php
namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Well;

class VesselOperationWellFlow extends Model
{
    protected $table = 'vessel_operation_well_flows';

    protected $fillable = [
        'vessel_operation_id',
        'well_id',
        'gas_flow_rate_mmscfd',
    ];

    protected $casts = [
        'gas_flow_rate_mmscfd' => 'decimal:4',
    ];

    /**
     * Relationship: Vessel Operation
     */
    public function vesselOperation(): BelongsTo
    {
        return $this->belongsTo(VesselOperation::class, 'vessel_operation_id');
    }

    /**
     * Relationship: Well
     */
    public function well(): BelongsTo
    {
        return $this->belongsTo(Well::class, 'well_id');
    }
}