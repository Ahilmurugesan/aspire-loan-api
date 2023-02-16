<?php

namespace Modules\Repay\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class LoanRepaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id'            => $this->id,
            'due_amount'    => $this->due_amount,
            'amount_paid'   => $this->amount_paid,
            'due_date'      => $this->due_date,
            'paid_date'     => $this->paid_date,
            'status'        => $this->status,
        ];
    }
}
